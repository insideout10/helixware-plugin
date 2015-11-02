<?php

/**
 * An HTTP client strategy to authenticate remote requests using Basic authentication.
 *
 * @package    HelixWare
 * @subpackage HelixWare/includes
 * @author     David Riccitelli <david@insideout.io>
 */
class HelixWare_HTTP_Client_Basic_Authentication implements HelixWare_HTTP_Client_Authentication {

	private $username;
	private $password;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.2.0
	 *
	 * @param string $username The username used for authentication.
	 * @param string $password The password used for authentication.
	 */
	public function __construct( $username, $password ) {

		$this->username = $username;
		$this->password = $password;

	}

	/**
	 * Enrich the HTTP arguments with the authentication headers.
	 *
	 * @since 1.2.0
	 *
	 * @param array $args The arguments for a WordPress HTTP call.
	 *
	 * @return array The args enriched with the authentication headers.
	 */
	public function get_args( $args ) {

		// Create the headers array if not-existent.
		if ( ! isset( $args['headers'] ) ) {
			$args['headers'] = array();
		}

		// Set the Basic authentication header.
		$args['headers']['Authorization'] = 'Basic ' . base64_encode( "$this->username:$this->password" );

		return $args;

	}

}
