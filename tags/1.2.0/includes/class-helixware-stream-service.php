<?php

/**
 * Created by PhpStorm.
 * User: david
 * Date: 7/10/15
 * Time: 17:45
 */
class HelixWare_Stream_Service {

	const PATH = '/api/assets/%d/streams';

	/**
	 * The HTTP client.
	 *
	 * @since 1.2.0
	 * @access private
	 * @var \HelixWare_HTTP_Client $http_client The HTTP client.
	 */
	private $http_client;

	/**
	 * The server URL.
	 *
	 * @since 1.2.0
	 * @access private
	 * @var string $server_url The server URL.
	 */
	private $server_url;

	/**
	 * The Asset service.
	 *
	 * @since 1.2.0
	 * @access private
	 * @var \HelixWare_Asset_Service $asset_service The Asset service.
	 */
	private $asset_service;

	/**
	 * Create an instance of the HelixWare_Stream_Service.
	 *
	 * @since 1.2.0
	 *
	 * @param \HelixWare_HTTP_Client $http_client The HTTP client.
	 * @param string $server_url The server URL.
	 * @param $asset_service
	 */
	public function __construct( $http_client, $server_url, $asset_service ) {

		$this->http_client   = $http_client;
		$this->server_url    = $server_url;
		$this->asset_service = $asset_service;

	}

	/**
	 * Get the streams for the specified asset ID.
	 *
	 * @since 1.2.0
	 *
	 * @param int $id The post ID.
	 *
	 * @return array An array of streams.
	 */
	public function get_streams( $id ) {

		$asset_id = $this->asset_service->get_asset_id( $id );
		$url      = $this->server_url . sprintf( self::PATH, $asset_id );
		$response = $this->http_client->execute( 'GET', $url, NULL, NULL, 'application/json' );

		return json_decode( $response['body'] );

	}

}
