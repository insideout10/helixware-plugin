<?php
/**
 */

function hewa_admin_ajax_live_assets() {

    $request_body = file_get_contents( 'php://input' );
    $path         = '/4/users/lives' . ( isset( $_GET['p'] ) ? '/' . $_GET['p'] : '' );
    $response     = hewa_server_request( $path, $_SERVER['REQUEST_METHOD'], $request_body );

    echo $response['body'];

    wp_die();

}
add_action( 'wp_ajax_hewa_live_assets', 'hewa_admin_ajax_live_assets' );