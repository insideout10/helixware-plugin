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
	 * @var HelixWare_HTTP_Client $http_clent An HTTP client.
	 */
	private $http_client;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.1.0
	 *
	 * @param HelixWare_HTTP_Client $http_client An HTTP client.
	 */
	public function __construct( $http_client ) {

		$this->http_client = $http_client;

	}

	/**
	 * @param HelixWare_HAL_Request $request
	 *
	 * @return HelixWare_HAL_Response
	 */
	public function execute( $request ) {

		$response = $this->http_client->execute( $request->get_method(), $request->get_url(), $request->get_body(), $request->get_content_type(), self::ACCEPT );

		return new HelixWare_HAL_Response( $this, $response );

	}

}
