<?php
/**
 * Provides the function for the *hewa_edit_post* ajax action.
 */

/**
 * Redirect to the edit post page.
 */
function hewa_ajax_edit_post() {

    $post_id = $_GET['id'];

    wp_redirect( html_entity_decode( get_edit_post_link( $post_id ) ) );
    exit;

}
add_action( 'wp_ajax_hewa_edit_post', 'hewa_ajax_edit_post' );