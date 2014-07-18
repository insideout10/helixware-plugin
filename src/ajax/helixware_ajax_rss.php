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

    $asset_id = $_GET['id'];
    $streams  = hewa_get_clip_urls( $asset_id );

    $ratio    = $streams->ratio;
    $m3u8     = $streams->formats->{'m3u8-redirector'};


    ob_start();
    header( "Content-Type: application/rss+xml" );

    echo <<<EOF
<rss version="2.0" xmlns:jwplayer="http://rss.jwpcdn.com/">
    <channel>
        <item>
            <jwplayer:source file="/wp-admin/admin-ajax.php?action=hewa_m3u8&amp;id=$asset_id" label="Auto" default="true" type="hls" />

EOF;

    for ( $i = 0; $i < sizeof( $m3u8->bitrates ); $i++ ) {

        $bitrate    = $m3u8->bitrates[$i];
        $width_p    = $bitrate->width . 'p';
        $url        = $bitrate->url;

        echo "<jwplayer:source file=\"$url\" label=\"$width_p\" type=\"hls\" />\n";

    }

    echo <<<EOF
            <jwplayer:source file="/wp-admin/admin-ajax.php?action=hewa_smil&amp;id=$asset_id" label="Auto" type="rtmp" />
        </item>
    </channel>
</rss>
EOF;


    wp_die();

}
add_action( 'wp_ajax_hewa_rss', 'hewa_ajax_load_rss' );
add_action( 'wp_ajax_nopriv_hewa_rss', 'hewa_ajax_load_rss' );