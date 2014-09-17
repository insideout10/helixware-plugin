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
function hewa_ajax_rss_live() {

    // Check if the asset id has been provided.
    if ( ! isset( $_GET['id'] ) || empty( $_GET['id'] ) ) {
        wp_die( __( 'The id parameter is required.', HEWA_LANGUAGE_DOMAIN ) );
    }

    $asset_id  = $_GET['id'];
    $title     = ( isset( $_GET['t'] ) ? $_GET['t'] : '' );
    $image_url = ( isset( $_GET['i'] ) ? $_GET['i'] : '' );
    $content   = hewa_admin_live_assets( $asset_id );

    $live_asset = json_decode( $content );

    ob_start();
    header( "Content-Type: application/rss+xml" );

    echo <<<EOF
<rss version="2.0" xmlns:jwplayer="http://rss.jwpcdn.com/">
 <channel>

EOF;

	$streaming_server = hewa_get_option( HEWA_SETTINGS_STREAMING_SERVER );
	$path = $live_asset->username . '/' . $live_asset->path;

	// Get the protocol configuration.
	$protocol = hewa_get_option( HEWA_SETTINGS_STREAMING_PROTOCOL, 'default' );
	$protocol = ( $protocol === 'default' ? 'rtmp' : $protocol );

    $rtmp = $protocol . '://' . $streaming_server . '/rtmplive/' . $path;
    $hls  = 'http://' . $streaming_server . '/m3ugen/rtmplive/' . $path . '?ext.m3u8';

    hewa_echo_rss_live_item( $rtmp, $hls, $title, $image_url );

    echo <<<EOF
 </channel>
</rss>
EOF;


    wp_die();

}
add_action( 'wp_ajax_hewa_rss_live', 'hewa_ajax_rss_live' );
add_action( 'wp_ajax_nopriv_hewa_rss_live', 'hewa_ajax_rss_live' );


function hewa_echo_rss_live_item( $rtmp, $hls, $title = null, $image_url = null ) {

    echo "  <item>\n";

    if ( null !== $title ) {

        // Escape the title.
        $title_h = esc_html( $title );
        echo "   <title>$title_h</title>\n";

    }

    if ( null !== $image_url && ! empty( $image_url ) ) {
        echo "   <jwplayer:image>$image_url</jwplayer:image>\n";
    }

    echo "   <jwplayer:source file=\"$rtmp\" label=\"Auto\" type=\"rtmp\" />\n";
    echo "   <jwplayer:source file=\"$hls\" label=\"Auto\" default=\"true\" type=\"hls\" />\n";

    echo "</item>";

}