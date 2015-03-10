<?php
/**
 */

function hewa_admin_ajax_live_assets() {

    $request_body = file_get_contents( 'php://input' );
    $path         = ( isset( $_GET['p'] ) ? $_GET['p'] : '' );
    $content      = hewa_admin_live_assets( $path, $_SERVER['REQUEST_METHOD'], $request_body );

    echo $content;

    wp_die();

}
add_action( 'wp_ajax_hewa_live_assets', 'hewa_admin_ajax_live_assets' );

function hewa_admin_live_assets( $path = '', $method = 'GET', $request_body = '' ) {

    $response = hewa_server_request( '/4/users/lives' . ( empty( $path ) ? '' : '/' . $path ), $method, $request_body );

    return $response['body'];

}