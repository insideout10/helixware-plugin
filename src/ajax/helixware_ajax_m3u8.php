<?php
/**
 * This file provides AJAX methods to access remote M3U8 files. The basic motivation of this file is that Helix Server
 * returns 302 Redirects to .m3u8 files that Android devices do not know how to handle. The AJAX call will handle the
 * redirects and return the file content to the player.
 */

/**
 * Load an m3u8 file from a remote Helix Server install.
 *
 * The remote file may express the stream location relative to the server, therefore it won't work because we're
 * proxying the request. This requires us to prepend the server name and base path to the file path in the remote
 * .m3u8 file.
 *
 * This method is available via AJAX as *admin-ajax.php?action=hewa_m3u8&file=<filename>
 */
function hewa_ajax_load_m3u8() {

    // Check if the asset id has been provided.
    if ( ! isset( $_GET['id'] ) || empty( $_GET['id'] ) ) {
        wp_die( __( 'The id parameter is required.', HEWA_LANGUAGE_DOMAIN ) );
    }

    $asset_id = $_GET['id'];
    $streams  = hewa_get_clip_urls( $asset_id );

    $ratio    = $streams->ratio;
    $m3u8     = $streams->formats->{'m3u8-redirector'};

    ob_start();
    header( "Content-Type: application/vnd.apple.mpegurl" );

    // Sort the bitrates.
    $bitrates = $m3u8->bitrates;
    usort( $bitrates, function( $a, $b ) { return $a->bitrate - $b->bitrate; } );

    echo "#EXTM3U\n";
    for ( $i = 0; $i < sizeof( $bitrates ); $i++ ) {

        $bitrate    = $bitrates[$i];
        $bandwidth  = $bitrate->bitrate;
        $width_p    = $bitrate->width . 'p';
        $resolution = $bitrate->width . 'x' . intval( $bitrate->width / $ratio );
        $url        = $bitrate->url;
        $chunklist_url = hewa_get_chunklist_url( $url );

        echo "#EXT-X-STREAM-INF:PROGRAM-ID=1,BANDWIDTH=$bandwidth,RESOLUTION=$resolution,NAME=\"$width_p\"\n";
        echo "$chunklist_url\n";

    }

    wp_die();

}
add_action( 'wp_ajax_hewa_m3u8', 'hewa_ajax_load_m3u8' );
add_action( 'wp_ajax_nopriv_hewa_m3u8', 'hewa_ajax_load_m3u8' );


/**
 * Get the chunklist URL from a playlist file. The URL is determined as the first line without the pound.
 *
 * @since 3.0.0
 *
 * @param $url The playlist file URL.
 * @return string|null The chunklist URL or null if not found.
 */
function hewa_get_chunklist_url( $url ) {

    $response = wp_remote_get( $url );

    $lines    = explode( "\n", $response['body'] );

    foreach ( $lines as $line ) {
        if ( '#' !== substr( $line, 0, 1 ) )
            return $line;
    }

    return null;

}