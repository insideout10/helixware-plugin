<?php
/**
 * General functions bound to the admin screen.
 */

// Add support for the *live asset* ajax method.
require_once( 'ajax/helixware_admin_ajax_live_assets.php' );


/**
 * Load scripts for the admin screen.
 */
function hewa_admin_scripts() {

    // Get the configuration options.
    $options = array(
        'url'         => hewa_get_option( HEWA_SETTINGS_SERVER_URL ) . '/4/user/ondemand',
        'key'         => hewa_get_option( HEWA_SETTINGS_APPLICATION_KEY ),
        'secret'      => hewa_get_option( HEWA_SETTINGS_APPLICATION_SECRET ),
        'extensions'  => hewa_get_option( HEWA_SETTINGS_FILE_EXTENSIONS ),
        'form_action' => admin_url( 'admin-ajax.php' ),
        'ajax_action' => 'hewa_create_post',
        'max_file_size' => HEWA_SETTINGS_MAX_FILE_SIZE,
        'post_types'  => array_map(
            function( $item ) {
                return array(
                    'title' => $item->labels->singular_name
                );
            },
            get_post_types( array( 'public'   => true ), 'objects' )
        ),
        'labels'     => array (
            'title' => __( 'Title', HEWA_LANGUAGE_DOMAIN ),
            'tags'  => __( 'Tags', HEWA_LANGUAGE_DOMAIN ),
            'save'  => __( 'Save', HEWA_LANGUAGE_DOMAIN )
        ),
    );

    // Enqueue the admin script.
    wp_enqueue_script( 'hewa_admin_js', plugins_url( 'js/helixware.admin.js', __FILE__ ) );

    // Set the options.
    wp_localize_script( 'hewa_admin_js', 'hewa_admin_options', $options );

    // Enqueue the stylesheet.
    wp_enqueue_style( 'hewa_admin_css', plugins_url( 'css/helixware.admin.css', __FILE__ ) );

}
add_action( 'admin_enqueue_scripts', 'hewa_admin_scripts' );


/**
 * Add the maximum upload file for HelixWare uploads.
 */
function hewa_admin_media_post_upload_ui() {

?>
    <span class="hewa-max-upload-size"><?php
        printf( __( 'HelixWare maximum upload file size: ' . HEWA_SETTINGS_MAX_FILE_SIZE . '.', HEWA_LANGUAGE_DOMAIN ) ); ?></span>
<?php

}
add_action( 'post-upload-ui', 'hewa_admin_media_post_upload_ui' );


/**
 * Create a post for the specified asset Id.
 *
 * @uses hewa_admin_request_still_image to create a still of the video.
 *
 * @param int $asset_id      The asset Id.
 * @param string $post_type  The post type.
 * @param string $post_title The post title.
 * @param string $post_tags  A comma separated list of tags.
 * @return int The post Id.
 */
function hewa_admin_create_post( $asset_id, $post_type, $post_title, $post_tags ) {

    hewa_write_log( '[ asset-id :: ' . $asset_id . ' ][ post-type :: ' . $post_type . ' ][ post-title :: ' . $post_title
        . ' ][ post-tags :: ' . $post_tags . ' ]' );

    // Set the post content to the player shortcode to the loaded asset Id.
    $post_content = apply_filters( HEWA_FILTERS_CREATE_POST_CONTENT, $asset_id, $post_type, $post_title, $post_tags );

    // Create the post with the following parameters.
    $args = array(
        'post_content' => $post_content,
        'post_title'   => $post_title,
        'post_type'    => $post_type,
        'tags_input'   => $post_tags
    );

    // Create the post.
    $post_id = wp_insert_post( $args, true );

    // If it's an error die with the error message.
    if ( is_wp_error( $post_id ) ) {

        hewa_write_log( 'An error occurred while creating a post [ error-message :: ' .
            $post_id->get_error_message() . ' ]' );
        wp_die( $post_id->get_error_message() );

    }

    // Set the post thumbnail.
    hewa_admin_set_post_thumbnail( $asset_id, $post_id );

    // Return the post id.
    echo (string)$post_id;

}


/**
 * Create the content for the video post using a template if any has been provided.
 *
 * @param int $asset_id      The video asset Id.
 * @param string $post_type  The post type.
 * @param string $post_title The post title.
 * @param string $post_tags   A comma separated list of tags.
 * @return string The post content.
 */
function hewa_admin_filters_create_post_content( $asset_id, $post_type, $post_title, $post_tags ) {

    // If a template Id is not specified then return the plain shortcode with the asset Id.
    if ( null === ( $template_id = hewa_get_option( HEWA_SETTINGS_TEMPLATE_ID ) ) // check that the option is set.
        || empty( $template_id ) // check that the value is set.
        || null === ( $template = get_post( $template_id ) ) ) { // check that the template exists.
        // Set the player shortcode.
        return '[' . HEWA_SHORTCODE_PREFIX . 'player asset_id="' . $asset_id . '"]';
    }

    // Else get the post content and replace the markers.
    $content = $template->post_content;
    $content = str_replace( '{asset_id}', $asset_id, $content );
    $content = str_replace( '{post_type}', $post_type, $content );
    $content = str_replace( '{post_title}', $post_title, $content );
    $content = str_replace( '{post_tags}', $post_tags, $content );

    return $content;

}
add_filter( HEWA_FILTERS_CREATE_POST_CONTENT, 'hewa_admin_filters_create_post_content', 10, 4 );

/**
 * Set the post thumbnail for the specified post Id by getting a thumbnail of the specified asset.
 *
 * @param int $asset_id      The asset Id.
 * @param int $post_id       The post Id.
 */
function hewa_admin_set_post_thumbnail( $asset_id, $post_id ) {

    // Get the post title.
    $post         = get_post( $post_id );
    $post_title   = $post->post_title;

    // Get the image from the remote server, then save it locally.
    $response     = hewa_admin_request_still_image( $asset_id, HEWA_STILL_IMAGE_WIDTH, HEWA_STILL_IMAGE_TIMECODE_SECONDS );
    $image_name   = 'a' . $asset_id . '-w' . HEWA_STILL_IMAGE_WIDTH . '-s' . HEWA_STILL_IMAGE_TIMECODE_SECONDS . '.png';
    $image        = hewa_admin_save_response_body( $response['body'], $image_name );

    // Create an attachment.
    $attachment = array(
        'guid'           => $image['guid'],
        // post_title, post_content (the value for this key should be the empty string), post_status and post_mime_type
        'post_title'     => $post_title, // Set the title to the post title.
        'post_content'   => '',
        'post_status'    => 'inherit',
        'post_mime_type' => HEWA_STILL_IMAGE_CONTENT_TYPE
    );

    // Create the attachment in WordPress and generate the related metadata.
    $attachment_id   = wp_insert_attachment( $attachment, $image['path'], $post_id );
    $attachment_data = wp_generate_attachment_metadata( $attachment_id, $image['path'] );
    wp_update_attachment_metadata( $attachment_id, $attachment_data );

    // Set it as the featured image.
    set_post_thumbnail( $post_id, $attachment_id );

}

/**
 * Get an HTTP response instance with the image data.
 *
 * @param int $asset_id         The asset Id.
 * @param int $width            The image width.
 * @param int $timecode_seconds The timecode in seconds.
 * @return object An HTTP response instance.
 */
function hewa_admin_request_still_image( $asset_id, $width, $timecode_seconds ) {

    // Format the timecode seconds to xx:xx:xx
    $timecode = gmdate( 'H:i:s', (int)$timecode_seconds );

    // Get the response instance.
    return hewa_server_request(
        '/4/pub/asset/' . $asset_id . '/image?tc=' . $timecode . '&w=' . $width, 'GET', '', 'text/plain', 'image/png'
    );

}


/**
 * Save the provided data to the local storage.
 *
 * @param object $data     The data to save.
 * @param string $filename The output filename, will be appended to the *upload dir*.
 * @return array An array with the *path* of the file and the *guid* (aka the URL).
 */
function hewa_admin_save_response_body( $data, $filename )
{

    $upload_dir  = wp_upload_dir();
    $upload_path = $upload_dir['path'] . '/' . $filename;

    hewa_write_log( "Saving data [ filename :: $filename ][ data :: $data ]" );

    // Store the data locally.
    file_put_contents( $upload_path, $data );

    // Return the path.
    return array(
        'path' => $upload_path,
        'guid' => $upload_dir['url'] . '/' . $filename
    );

}