<?php
/**
 * Functions related to the hewa_set_post_thumbnail AJAX action.
 */

/**
 * Binds an asset thumbnail to the post.
 */
function hewa_ajax_set_post_thumbnail() {

    if ( ! isset( $_GET['post'] ) || empty( $_GET['post'] ) ) {
        wp_die( __( 'The post parameter is required.', HEWA_LANGUAGE_DOMAIN ) );
    }

    if ( ! is_numeric( $_GET['post'] ) ) {
        wp_die( __( 'The post parameter is invalid.', HEWA_LANGUAGE_DOMAIN ) );
    }

    if ( ! isset( $_GET['asset'] ) || empty( $_GET['asset'] ) ) {
        wp_die( __( 'The asset parameter is required.', HEWA_LANGUAGE_DOMAIN ) );
    }

    if ( ! is_numeric( $_GET['asset'] ) ) {
        wp_die( __( 'The asset parameter is invalid.', HEWA_LANGUAGE_DOMAIN ) );
    }

    $asset_id = $_GET['asset'];
    $post_id  = $_GET['post'];

    hewa_admin_set_post_thumbnail( $asset_id, $post_id );

    wp_die();

}
add_action( 'wp_ajax_hewa_set_post_thumbnail', 'hewa_ajax_set_post_thumbnail' );