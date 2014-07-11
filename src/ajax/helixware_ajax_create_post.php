<?php
/**
 * This file provides function to create a post via AJAX.
 */

/**
 * Create a post using the provided data.
 */
function hewa_create_post() {

    // TODO: move the actual method to another function.

    ob_clean();
    header( 'Content-Type: text/plain' );

//    // Get the request body and convert it to a json onbject.
//    $body       = @file_get_contents('php://input');
//    $json       = json_decode( $body );
//
//    // Get the data.
//    $asset_id   = $json->assetId;
//    $post_type  = $json->postType;
//    $post_title = $json->postTitle;
//    $post_tags  = $json->postTags;

    $asset_id   = $_POST['assetId'];

    $post_type  = $_POST['postType'];
    $post_title = $_POST['postTitle'];
    $post_tags  = $_POST['postTags'];

    // Create the post and print the resulting post Id.
    echo hewa_admin_create_post( $asset_id, $post_type, $post_title, $post_tags);

    wp_die();

}
add_action( 'wp_ajax_hewa_create_post', 'hewa_create_post' );
