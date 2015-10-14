<?php

/**
 * Represents a HAL request.
 *
 * @since      1.1.0
 * @package    HelixWare
 * @subpackage HelixWare/includes
 * @author     David Riccitelli <david@insideout.io>
 */
class HelixWare_HAL_Request {

	private $method;
	private $url;
	private $body;
	private $content_type;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.1.0
	 *
	 * @param string $method The HTTP method.
	 * @param string $body The request body.
	 * @param string $content_type The request content type.
	 */
	public function __construct( $method, $url, $body = '', $content_type = '' ) {

		$this->method       = $method;
		$this->url          = $url;
		$this->body         = $body;
		$this->content_type = $content_type;

	}

	/**
	 * @return string
	 */
	public function get_method() {
		return $this->method;
	}

	/**
	 * @return mixed
	 */
	public function get_url() {
		return $this->url;
	}

	/**
	 * @return string
	 */
	public function get_body() {
		return $this->body;
	}

	/**
	 * @return string
	 */
	public function get_content_type() {
		return $this->content_type;
	}

}
