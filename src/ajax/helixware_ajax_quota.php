<?php
/**
 * This file provides the AJAX call to retrieve quota information using the configured Application Key and Secret.
 */

/**
 * Provide the quota information about the currently configured *Application*.
 */
function hewa_ajax_quota() {

    ob_clean();
    header( 'Content-Type: application/json; charset=UTF-8' );
    $quota      = json_decode( hewa_server_call( '/me' ) );

    // Build the message.
    $max_quota  = $quota->account->maxQuota;
    $used_quota = $quota->account->currentQuota;
    $free_quota = $max_quota - $used_quota;
    $percent_free = round( ( $free_quota / $max_quota ) * 100, 0 );
    $message    = __( 'You have %s%% of free space (%s out of %s total).', HEWA_LANGUAGE_DOMAIN );
    $quota->message = sprintf( $message, $percent_free, hewa_format_bytes( $free_quota ), hewa_format_bytes( $max_quota ) );

    echo json_encode( $quota );

    wp_die();
    
}
add_action( 'wp_ajax_' . HEWA_SHORTCODE_PREFIX . 'quota', 'hewa_ajax_quota' );
