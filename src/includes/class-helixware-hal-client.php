<?php

/**
 * An HTTP client to perform remote requests.
 *
 * @package    HelixWare
 * @subpackage HelixWare/includes
 * @author     David Riccitelli <david@insideout.io>
 */
class HelixWare_HAL_Client {

	const ACCEPT = 'application/hal+json, application/json';

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.1.0
	 */
	public function __construct() {

	}


	/**
	 * @param HelixWare_HAL_Request $request
	 *
	 * @return HelixWare_HAL_Response
	 */
	public function execute( $request ) {

		return $this->_execute( $request->get_method(), $request->get_url(), $request->get_body(), $request->get_content_type(), self::ACCEPT );

	}

	private function _execute( $method, $url, $body = NULL, $content_type = NULL, $accept = NULL ) {

		// Get the configuration settings and die if not set.
		$app_key    = hewa_get_option( HEWA_SETTINGS_APPLICATION_KEY, FALSE );
		$app_secret = hewa_get_option( HEWA_SETTINGS_APPLICATION_SECRET, FALSE );

		if ( FALSE === $url || FALSE === $app_key || FALSE === $app_secret ) {
			wp_die( __( 'The plugin is not configured.', HEWA_LANGUAGE_DOMAIN ) );
		}

		// Set the headers.
		$headers = array(
			'X-Application-Key'    => $app_key,
			'X-Application-Secret' => $app_secret
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
		return new HelixWare_HAL_Response( $this, $response );
	}

}
