<?php

/**
 * Represents a HAL response.
 *
 * @since      1.1.0
 * @package    HelixWare
 * @subpackage HelixWare/includes
 * @author     David Riccitelli <david@insideout.io>
 */
class HelixWare_HAL_Response {

	/**
	 * @var HelixWare_HAL_Client $hal_client
	 */
	private $hal_client;

	/**
	 * @var array $response
	 */
	private $response;

	private $body_json;
	private $links;
	private $embedded;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.1.0
	 *
	 * @param HelixWare_HAL_Client $hal_client to perform other requests such as next or previous.
	 * @param array $response
	 */
	public function __construct( $hal_client, $response ) {


		$this->hal_client = $hal_client;
		$this->response   = $response;
		$this->body_json  = json_decode( $response['body'] );
		$this->links      = ( isset( $this->body_json->_links ) ? $this->body_json->_links : NULL );
		$this->embedded   = ( isset( $this->body_json->_embedded ) ? $this->body_json->_embedded : NULL );

	}

	/**
	 * @since 1.1.0
	 * @return bool True if the response has a next link.
	 */
	public function has_next() {

		return isset( $this->links->next ) && isset( $this->links->next->href );

	}

	/**
	 * @since 1.1.0
	 * @return HelixWare_HAL_Response
	 */
	public function get_next() {

		return $this->hal_client->execute( new HelixWare_HAL_Request( 'GET', $this->links->next->href ) );

	}

	public function has_embedded() {

		return ( isset( $this->embedded ) );

	}

	public function get_embedded( $key = NULL ) {

		if ( NULL === $key ) {
			return $this->embedded;
		}

		return $this->embedded->{$key};

	}

}