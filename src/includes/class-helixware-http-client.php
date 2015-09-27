<?php

/**
 * An HTTP client to perform remote requests.
 *
 * @package    HelixWare
 * @subpackage HelixWare/includes
 * @author     David Riccitelli <david@insideout.io>
 */
class HelixWare_HTTP_Client {

	private $application_key;
	private $application_secret;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.1.0
	 */
	public function __construct() {

		$this->application_key    = hewa_get_option( HEWA_SETTINGS_APPLICATION_KEY, FALSE );
		$this->application_secret = hewa_get_option( HEWA_SETTINGS_APPLICATION_SECRET, FALSE );

	}

	/**
	 * Performs an authenticated HTTP request.
	 * @since 1.1.0
	 *
	 * @param $method
	 * @param $url
	 * @param null $body
	 * @param null $content_type
	 * @param null $accept
	 *
	 * @return array|string|void|\WP_Error
	 */
	public function execute( $method, $url, $body = NULL, $content_type = NULL, $accept = NULL ) {

		if ( FALSE === $url || FALSE === $this->application_key || FALSE === $this->application_secret ) {
			wp_die( __( 'The plugin is not configured.', HEWA_LANGUAGE_DOMAIN ) );
		}

		// Set the headers.
		$headers = array(
			'X-Application-Key'    => $this->application_key,
			'X-Application-Secret' => $this->application_secret
		);

		if ( ! empty( $content_type ) ) {
			$headers['Content-Type'] = $content_type;
		}

		if ( ! empty( $accept ) ) {
			$headers['Accept'] = $accept;
		}

		// Prepare the default arguments.
		$args = array_merge_recursive( unserialize( HEWA_API_HTTP_OPTIONS ), array(
			'method'  => $method,
			'body'    => ( is_array( $body ) ? json_encode( $body ) : $body ),
			'headers' => $headers
		) );

		//
		hewa_write_log( "Performing a request [ method :: $method ][ url :: $url ]" );

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
}