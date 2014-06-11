<?php
/**
 * This file provides AJAX methods to access remote M3U8 files. The basic motivation of this file is that Helix Server
 * returns 302 Redirects to .m3u8 files that Android devices do not know how to handle. The AJAX call will handle the
 * redirects and return the file content to the player.
 */

/**
 * Load an m3u8 file from remote.
 */
function hewa_ajax_load_m3u8() {

    if ( ! isset( $_GET['file'] ) ) {
        wp_die( 'The file parameter is not set.' );
    }

    // Construct the URL using the configured *server* configuration parameter and the *file* parameter in the GET
    // request.
    // TODO: move this to a configuration setting.
    $server = 'http://streamer.a1.net/m3ugen/';
    $file   = $_GET['file'];
    $url    = $server . $file;

    $re      = '/' . str_replace( '/', '\/', $server ) . '(.+)\/[^\/]+$/';
    $matches = array();
    if ( 1 !== preg_match( $re, $url, $matches ) ) {
        wp_die( "Cannot parse the URL [ re :: $re ][ url :: $url ]." );
    }
    $path    = 'http://streamer.a1.net:80/Segments/HLS_TS/' . $matches[1] . '/';


    // Make the request.
    $response  = wp_remote_get( $url );

    if ( is_wp_error( $response ) ) {

        hewa_write_log( "hewa_ajax_load_m3u8 : error [ response :: " . var_export( $response ) . " ]" );
        wp_die('An error occurred while request the remote resource.');
    }

    // Send the output.
    $content_type = $response['headers']['content-type'];
    header( "Content-Type: $content_type" );
    foreach( explode( "\n", $response['body'] ) as $line ) {
        if ( empty( $line ) || '#' === substr( $line, 0, 1 ) ) {
            echo $line . "\n";
        } else {
            echo $path . $line . "\n";
        }
    }
    wp_die();

}
add_action( 'wp_ajax_hewa_m3u8', 'hewa_ajax_load_m3u8' );
add_action( 'wp_ajax_nopriv_hewa_m3u8', 'hewa_ajax_load_m3u8' );