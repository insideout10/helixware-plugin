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

    hewa_write_log( '[ asset-id :: ' + $asset_id + ' ][ post-type :: ' + $post_type + ' ][ post-title :: ' + $post_title
        + ' ][ post-tags :: ' + $post_tags + ' ]' );

    // Set the player shortcode.
    $player_shortcode = HEWA_SHORTCODE_PREFIX . 'player';

    // Set the post content to the player shortcode to the loaded asset Id.
    $post_content = "[$player_shortcode asset_id=$asset_id]";

    // Create the post with the following parameters.
    $args = array(
        'post_content' => $post_content,
        'post_title'   => $post_title,
        'post_type'    => $post_type,
        'tags_input'   => $post_tags
    );

    // Create the post.
    $post_id = wp_insert_post( $args, true );

    // If it's an error die with the error message.
    if ( is_wp_error( $post_id ) ) {

        hewa_write_log( 'An error occurred while creating a post [ error-message :: ' .
            $post_id->get_error_message() . ' ]' );
        wp_die( $post_id->get_error_message() );

    }

    // Return the post id.
    echo $post_id;

    wp_die();

}
add_action( 'wp_ajax_hewa_create_post', 'hewa_create_post' );

function hewa_edit_post() {

    $post_id = $_GET['id'];

    wp_redirect( html_entity_decode( get_edit_post_link( $post_id ) ) );
    exit;

}
add_action( 'wp_ajax_hewa_edit_post', 'hewa_edit_post' );