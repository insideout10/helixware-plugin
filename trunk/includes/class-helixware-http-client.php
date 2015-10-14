<?php

/**
 * An HTTP client to perform remote requests.
 *
 * @package    HelixWare
 * @subpackage HelixWare/includes
 * @author     David Riccitelli <david@insideout.io>
 */
class HelixWare_HTTP_Client {

	/**
	 * The authentication strategy.
	 *
	 * @since 1.1.0
	 * @access private
	 * @var \HelixWare_HTTP_Client_Authentication $authentication
	 */
	private $authentication;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.1.0
	 *
	 * @param \HelixWare_HTTP_Client_Authentication $authentication The
	 */
	public function __construct( $authentication ) {

		$this->authentication = $authentication;

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

		// Set the headers.
		$headers = array();

		if ( ! empty( $content_type ) ) {
			$headers['Content-Type'] = $content_type;
		}

		if ( ! empty( $accept ) ) {
			$headers['Accept'] = $accept;
		}

		// Prepare the default arguments.
		$args = $this->authentication->get_args( array_merge_recursive( unserialize( HEWA_API_HTTP_OPTIONS ), array(
			'method'  => $method,
			'body'    => ( is_array( $body ) ? json_encode( $body ) : $body ),
			'headers' => $headers
		) ) );

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