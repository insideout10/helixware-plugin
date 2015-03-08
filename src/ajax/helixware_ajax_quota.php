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
	$response = hewa_server_call( '/me' );
	$quota    = json_decode( $response );

	// Check that the response from the server is correct.
	if ( ! isset( $quota->account ) || ! isset( $quota->account->maxQuota ) || ! isset( $quota->account->currentQuota ) ) {

		hewa_write_log( 'Invalid response [ response :: {response} ]', array( 'response' => $response ) );

		echo json_encode( array( 'message' => __( 'An error occurred, check your settings', HEWA_LANGUAGE_DOMAIN ) ) );
		wp_die();
	}

	hewa_write_log( 'Response received [ response :: {response} ]', array( 'response' => $response ) );

	// Build the message.
	$max_quota    = $quota->account->maxQuota;
	$used_quota   = $quota->account->currentQuota;
	$free_quota   = $max_quota - $used_quota;
	$percent_free = round( ( $free_quota / $max_quota ) * 100, 0 );

	$message        = ( $free_quota >= 0
		? __( 'You have %s%% of free space (%s out of %s total).', HEWA_LANGUAGE_DOMAIN )
		: __( 'You are using %2$s over quota (%3$s total).', HEWA_LANGUAGE_DOMAIN ) );
	$quota->message = sprintf( $message, $percent_free, hewa_format_bytes( abs( $free_quota ) ), hewa_format_bytes( $max_quota ) );

	echo json_encode( $quota );

	wp_die();

}

add_action( 'wp_ajax_' . HEWA_SHORTCODE_PREFIX . 'quota', 'hewa_ajax_quota' );
