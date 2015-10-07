<?php

/**
 * This class manages Fragments for assets.
 *
 * @since      1.0.0
 * @package    Helixware_Mico
 * @subpackage Helixware_Mico/includes
 * @author     David Riccitelli <david@insideout.io>
 */
class Helixware_Mico_Fragment_Service {

	const FIND_BY_ASSET_GUID_PATH = '/fragments/search/findByAssetGUID?guid=%s';

	/**
	 * A HAL client.
	 *
	 * @since 1.0.0
	 * @access private
	 * @var \HelixWare_HAL_Client $hal_client A HAL client.
	 */
	private $hal_client;

	/**
	 * The MICO server URL.
	 *
	 * @since 1.0.0
	 * @access private
	 * @var string $server_url The MICO server URL.
	 */
	private $server_url;

	/**
	 * Create an instance of the MICO Fragment service.
	 *
	 * @since 1.0.0
	 *
	 * @param \HelixWare_HAL_Client $hal_client A HAL client.
	 * @param string $server_url The server URL.
	 */
	public function __construct( $hal_client, $server_url ) {

		$this->hal_client = $hal_client;
		$this->server_url = $server_url;

	}

	/**
	 * Get the fragments for the specified GUID.
	 *
	 * @since 1.0.0
	 *
	 * @param string $guid The asset GUID.
	 */
	public function get_fragments( $guid ) {

		$path    = sprintf( self::FIND_BY_ASSET_GUID_PATH, urlencode( $guid ) );
		$request = new HelixWare_HAL_Request( 'GET', $this->server_url . $path );

		$response = $this->hal_client->execute( $request );

		var_dump($response);

		do {
			foreach ( $response->get_embedded( 'fragments' ) as $fragment ) {

				var_dump( $fragment );
			}
		} while ( $response->has_next() && $response = $response->get_next() );

		wp_die();

	}

}
