<?php
/**
 * General functions bound to the admin screen.
 */


/**
 * Load scripts for the admin screen.
 */
function hewa_admin_scripts() {

    // Get the configuration options.
    $options = array(
        'url'         => hewa_get_option( HEWA_SETTINGS_SERVER_URL ) . '/4/users/assets',
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