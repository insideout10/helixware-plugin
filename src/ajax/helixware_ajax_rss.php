<?php
/**
 * This file provides AJAX methods to access remote rss files. The basic motivation of this file is that Helix Server
 * returns 302 Redirects to .rss files that Android devices do not know how to handle. The AJAX call will handle the
 * redirects and return the file content to the player.
 */

/**
 * Load an rss file from a remote Helix Server install.
 *
 * The remote file may express the stream location relative to the server, therefore it won't work because we're
 * proxying the request. This requires us to prepend the server name and base path to the file path in the remote
 * .rss file.
 *
 * This method is available via AJAX as *admin-ajax.php?action=hewa_rss&file=<filename>
 */
function hewa_ajax_load_rss() {

    // Check if the asset id has been provided.
    if ( ! isset( $_GET['id'] ) || empty( $_GET['id'] ) ) {
        wp_die( __( 'The id parameter is required.', HEWA_LANGUAGE_DOMAIN ) );
    }

    $asset_id  = $_GET['id'];
    $title     = ( isset( $_GET['t'] ) ? $_GET['t'] : '' );
    $image_url = ( isset( $_GET['i'] ) ? $_GET['i'] : '' );
    $streams   = hewa_get_clip_urls( $asset_id );

//    $ratio    = $streams->ratio;
    $m3u8      = $streams->formats->{'m3u8-redirector'};


    ob_start();
    header( "Content-Type: application/rss+xml" );

    echo <<<EOF
<rss version="2.0" xmlns:jwplayer="http://rss.jwpcdn.com/">
 <channel>

EOF;

    hewa_echo_rss_item( $asset_id, $m3u8, $title, $image_url );

    // Add a list for the listbar if we have a category.
    if ( isset( $_GET['cat'] ) ) {

        // Get the category Id.
        $category_id = ( is_numeric( $_GET['cat'] ) ? $_GET['cat'] : get_category_by_slug( $_GET['cat'] )->cat_ID );
        $posts_count = ( isset( $_GET['max'] ) && is_numeric( $_GET['max'] ) ? $_GET['max'] : 5 );

        // Query for posts.
        $posts       = get_posts( array(
            'category'       => $category_id,
            'posts_per_page' => $posts_count,
            'numberposts'    => $posts_count
        ) );

        foreach ( $posts as $post ) {

            $matches = array();
            if ( 1 === preg_match( '/ asset_id=(\d+)/', $post->post_content, $matches ) ) {

                $this_asset_id = $matches[1];

                // Don't add the same asset.
                if ( $asset_id === $this_asset_id ) {
                    continue;
                }

                $thumbnail_id   = get_post_thumbnail_id( $post->ID );
                // TODO: get attachment of the required size.
                $attachment_url = wp_get_attachment_url( $thumbnail_id );
                hewa_echo_rss_item( $this_asset_id, null, $post->post_title, $attachment_url );

            }
        }

    }

    echo <<<EOF
 </channel>
</rss>
EOF;


    wp_die();

}
add_action( 'wp_ajax_hewa_rss', 'hewa_ajax_load_rss' );
add_action( 'wp_ajax_nopriv_hewa_rss', 'hewa_ajax_load_rss' );


function hewa_echo_rss_item( $asset_id, $m3u8 = null, $title = null, $image_url = null ) {

    // Get the ajax URL.
    $ajax_url = admin_url( 'admin-ajax.php' );

    echo "  <item>\n";

    if ( null !== $title ) {

        // Escape the title.
        $title_h = esc_html( $title );
        echo "   <title>$title_h</title>\n";

    }

    if ( null !== $image_url && ! empty( $image_url ) ) {
        echo "   <jwplayer:image>$image_url</jwplayer:image>\n";
    }

    echo "   <jwplayer:source file=\"$ajax_url?action=hewa_smil&amp;id=$asset_id\" label=\"Auto\" type=\"rtmp\" />\n";
    echo "   <jwplayer:source file=\"$ajax_url?action=hewa_m3u8&amp;id=$asset_id\" label=\"Auto\" default=\"true\" type=\"hls\" />\n";

    // TODO: make the following URL parametric and use the authenticated PHP call.
    $server_url = hewa_get_option( HEWA_SETTINGS_SERVER_URL, '' );
    echo "   <jwplayer:track file=\"$server_url/4/users/assets/$asset_id/vtt?w=95&amp;i=5\" kind=\"thumbnails\" />\n";

    // Is the request from an Android?
    $ua = strtolower( $_SERVER['HTTP_USER_AGENT'] );
    $is_android = stripos( $ua, 'android') !== false;

    if ( null !== $m3u8  && !$is_android ) {
        for ( $i = 0; $i < sizeof( $m3u8->bitrates ); $i++ ) {

            $bitrate    = $m3u8->bitrates[$i];
            $width_p    = $bitrate->width . 'p';
            $url        = $bitrate->url;

            echo "   <jwplayer:source file=\"$url\" label=\"$width_p\" type=\"hls\" />\n";

        }
    }

    echo "</item>";

}