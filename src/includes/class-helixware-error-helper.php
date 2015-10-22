<?php

/**
 * The Error helper class.
 *
 * @since 1.3.0
 */
class HelixWare_Error_Helper {

	const ERR_CODE_ASSET_PUSH = 'hewa_err_asset_push';

	public function __construct() {

	}

	/**
	 * Create a WP_Error instance.
	 *
	 * @since 1.3.0
	 *
	 * @param string|int $code Error code
	 * @param string $message Error message
	 * @param mixed $data Optional. Error data.
	 *
	 * @return \WP_Error A WP_Error instance.
	 */
	public static function create( $code = '', $message = '', $data = '' ) {

		return new WP_Error( $code, $message, $data );

	}

}
