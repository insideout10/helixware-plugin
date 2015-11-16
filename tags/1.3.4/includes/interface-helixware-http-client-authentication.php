<?php

/**
 * An interface for authenticating HTTP requests.
 *
 * @package    HelixWare
 * @subpackage HelixWare/includes
 * @author     David Riccitelli <david@insideout.io>
 */
interface HelixWare_HTTP_Client_Authentication {

	/**
	 * @param array $args The arguments for a WordPress HTTP call.
	 *
	 * @return mixed
	 */
	public function get_args( $args );

}