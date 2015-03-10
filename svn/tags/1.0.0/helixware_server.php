<?php
/**
 * This file provides functions to call and interact with the remote HelixWare Server.
 */

/**
 * Call the remote HelixServer API, signing the request with the Application Key and Secret configured in WordPress.
 *
 * @uses hewa_get_option to get the configuration settings.
 *
 * @param string $endpoint The endpoint (will be appended to the server URL, must start with a trailing slash).
 * @param string $method The HTTP method.
 * @param string $body The request payload.
 * @param string $content_type The request body content type (default: 'application/json; charset=UTF-8').
 * @param string $accept The accept header content type (default: 'application/json; charset=UTF-8').
 *
 * @return string The response.
 */
function hewa_server_request(
	$endpoint, $method = 'GET', $body = '', $content_type = 'application/json; charset=UTF-8', $accept = 'application/json; charset=UTF-8'
) {

	// Get the configuration settings and die if not set.
	$server_url = hewa_get_server_url();
	$app_key    = hewa_get_option( HEWA_SETTINGS_APPLICATION_KEY, false );
	$app_secret = hewa_get_option( HEWA_SETTINGS_APPLICATION_SECRET, false );

	if ( false === $server_url || false === $app_key || false === $app_secret ) {
		wp_die( __( 'The plugin is not configured.', HEWA_LANGUAGE_DOMAIN ) );
	}

	// Set the full URL.
	$url = $server_url . $endpoint;

	// Prepare the default arguments.
	$args = array_merge_recursive( unserialize( HEWA_API_HTTP_OPTIONS ), array(
		'method'  => $method,
		'body'    => ( is_array( $body ) ? json_encode( $body ) : $body ),
		'headers' => array(
			'Content-Type'         => $content_type,
			'Accept'               => $accept,
			'X-Application-Key'    => $app_key,
			'X-Application-Secret' => $app_secret
		)
	) );

	//
	hewa_write_log( 'Performing a request [ url :: ' . $url . ' ][ end-point :: ' . $endpoint . ' ]' );

	// Perform the request.
	$response = wp_remote_request( $url, $args );

	// If an error occurs, print the error and exit.
	if ( is_wp_error( $response ) || 200 !== (int) $response['response']['code'] ) {
		hewa_write_log(
			'An error occurred while calling the remote server ( ' .
			( is_wp_error( $response ) ? $response->get_error_message() : $response['body'] ) . ' )'
		);

		return ( __(
			'An error occurred while calling the remote server ( ' .
			( is_wp_error( $response ) ? $response->get_error_message() : $response['body'] ) . ' )',
			HEWA_LANGUAGE_DOMAIN
		) );

	}

	// Return the response as a string.
	return $response;

}

/**
 * Call the remote HelixServer API, signing the request with the Application Key and Secret configured in WordPress.
 *
 * @uses hewa_get_option to get the configuration settings.
 *
 * @param string $endpoint The endpoint (will be appended to the server URL, must start with a trailing slash).
 * @param string $content_type The request body content type (default: 'application/json; charset=UTF-8').
 * @param string $accept The accept header content type (default: 'application/json; charset=UTF-8').
 *
 * @return string The response body.
 */
function hewa_server_call(
	$endpoint, $content_type = 'application/json; charset=UTF-8', $accept = 'application/json; charset=UTF-8'
) {

	$response = hewa_server_request( $endpoint, 'GET', '', $content_type, $accept );

	// Return the response as a string.
	return $response['body'];

}


/**
 * Get the HelixWare server URL.
 *
 * @since 4.0.0
 * @return string
 */
function hewa_get_server_url() {

	return hewa_get_option( HEWA_SETTINGS_SERVER_URL, false );
}