<?php

/**
 * Provides Asset Image Services.
 *
 * @package    HelixWare
 * @subpackage HelixWare/includes
 * @author     David Riccitelli <david@insideout.io>
 */
class HelixWare_Asset_Image_Service {

	const ACCEPT = 'image/png';

	/**
	 * @var HelixWare_HTTP_Client $http_clent An HTTP client.
	 */
	private $http_client;

	private $server_url;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.1.0
	 *
	 * @param HelixWare_HTTP_Client $http_client An HTTP client.
	 * @param string $server_url The HelixWare server URL.
	 */
	public function __construct( $http_client, $server_url ) {

		$this->http_client = $http_client;
		$this->server_url  = $server_url;
	}

	/**
	 * Get the image at the specified path. The server URL is prepended to the path.
	 *
	 * @since 1.1.0
	 *
	 * @param $path
	 */
	public function get_image( $path ) {

		$url      = $this->server_url . $path;
		$response = $this->http_client->execute( 'GET', $url, NULL, NULL, self::ACCEPT );

		if ( is_wp_error( $response ) ) {
			wp_die( 'An error occurred.' );
		}

		// dump out the image.
		$content_type = $response['headers']['content-type'];
		header( "Content-Type: $content_type" );
		echo( $response['body'] );

		wp_die();

	}

	/**
	 * Hooked to the _hw_asset_image_ AJAX action, it'll get the path from the
	 * request parameters and call {@see get_image}.
	 *
	 * @since 1.1.0
	 */
	public function wp_ajax_get_image() {

		if ( ! isset( $_GET['path'] ) ) {
			wp_die( 'The path parameter is required.' );
		}

		$this->get_image( $_GET['path'] );

	}

	public function wp_prepare_attachment_for_js( $response, $attachment, $meta ) {

		$guid              = get_the_guid( $attachment );
		$path              = substr( $guid, strlen( $this->server_url ) ) . urlencode( '/images/0/0/5?width=200' );
		$response['image'] = array( 'src' => admin_url( "admin-ajax.php?action=hw_asset_image&path=$path" ) );

		return $response;
	}

}