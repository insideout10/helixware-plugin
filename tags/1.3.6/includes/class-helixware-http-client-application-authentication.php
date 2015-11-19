<?php

/**
 * An HTTP client strategy to authenticate remote requests using X-Application-Key and X-Application-Secret headers.
 *
 * @package    HelixWare
 * @subpackage HelixWare/includes
 * @author     David Riccitelli <david@insideout.io>
 */
class HelixWare_HTTP_Client_Application_Authentication implements HelixWare_HTTP_Client_Authentication {

	private $application_key;
	private $application_secret;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.2.0
	 *
	 * @param string $application_key The application key.
	 * @param string $application_secret The application secret.
	 */
	public function __construct( $application_key, $application_secret ) {

		if ( FALSE === $application_key || FALSE === $application_secret ) {
			hewa_write_log( __( 'The plugin is not configured.', HEWA_LANGUAGE_DOMAIN ) );
		}

		$this->application_key    = $application_key;
		$this->application_secret = $application_secret;

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

		// Set the Application Key and Secret.
		$args['headers']['X-Application-Key']    = $this->application_key;
		$args['headers']['X-Application-Secret'] = $this->application_secret;

		return $args;

	}

}
