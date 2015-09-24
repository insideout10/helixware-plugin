<?php

/**
 * Provides access to the remote assets.
 *
 * @package    HelixWare
 * @subpackage HelixWare/includes
 * @author     David Riccitelli <david@insideout.io>
 */
class HelixWare_Remote_Assets {

	/**
	 * The http client.
	 *
	 * @since    1.1.0
	 * @access   private
	 * @var      string $http_client The http client.
	 */
	private $http_client;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.1.0
	 *
	 * @param HelixWare_Http_Client $http_client The HTTP client to perform remote requests.
	 */
	public function __construct( $http_client ) {

		$this->http_client = $http_client;

	}

}
