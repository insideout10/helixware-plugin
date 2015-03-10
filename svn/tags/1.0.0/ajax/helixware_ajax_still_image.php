<?php
/**
 * This file provides the AJAX call to retrieve still image from the server.
 */

/**
 * Get a still image from the server.
 */
function hewa_ajax_still_image() {

    // We need an asset id, timecode and a width.

    if ( ! isset( $_GET['id'] ) || empty( $_GET['id'] ) ) {

        wp_die( __( 'The id parameter is required.', HEWA_LANGUAGE_DOMAIN  ) );

    }

    if ( ! is_numeric( $_GET['id'] ) || 1 > $_GET['id'] ) {

        wp_die( __( 'The id parameter is invalid.', HEWA_LANGUAGE_DOMAIN  ) );

    }

    if ( ! isset( $_GET['tc'] ) || empty( $_GET['tc'] ) ) {

        wp_die( __( 'The tc parameter is required.', HEWA_LANGUAGE_DOMAIN  ) );

    }

    if ( ! is_numeric( $_GET['tc'] ) || 0 > $_GET['tc'] ) {

        wp_die( __( 'The tc parameter is invalid.', HEWA_LANGUAGE_DOMAIN  ) );

    }

    if ( ! isset( $_GET['w'] ) || empty( $_GET['w'] ) ) {

        wp_die( __( 'The w parameter is required.', HEWA_LANGUAGE_DOMAIN  ) );

    }

    if ( ! is_numeric( $_GET['w'] ) || 1 > $_GET['w'] ) {

        wp_die( __( 'The w parameter is invalid.', HEWA_LANGUAGE_DOMAIN  ) );

    }

    $asset_id = $_GET['id'];
    $tc_secs  = $_GET['tc'];
    $width    = $_GET['w'];

    // Get the response instance.
    $response = hewa_admin_request_still_image( $asset_id, $width, $tc_secs );

    ob_clean();
    header( 'Content-Type: image/png' );
    echo $response['body'];

    wp_die();

}
add_action( 'wp_ajax_' . HEWA_SHORTCODE_PREFIX . 'still_image', 'hewa_ajax_still_image' );
