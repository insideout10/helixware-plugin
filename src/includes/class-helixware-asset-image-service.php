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

		// Only process HelixWare assets.
		if ( ! HelixWare_Asset_Service::is_helixware_mime_type( $response['mime'] ) ) {
			return $response;
		}

		// Get the thumbnail URL.
		$thumbnail_url = get_post_meta( $attachment->ID, HelixWare_Asset_Service::META_THUMBNAIL_URL, TRUE );

		// Add a thumbnail URL if available.
		if ( ! empty( $thumbnail_url ) ) {
			$thumbnail_path    = urlencode( substr( $thumbnail_url, strlen( $this->server_url ) ) );
			$response['image'] = array( 'src' => admin_url( "admin-ajax.php?action=hw_asset_image&path=$thumbnail_path" ) );
		}

		return $response;
	}

	/**
	 * Creates the _hw_embed_ shortcode to embed an asset from HelixWare.
	 *
	 * @since 1.1.0
	 *
	 * @param string $html
	 * @param int $id
	 * @param string $attachment
	 *
	 * @return string A _hw_embed_ shortcode.
	 */
	public function media_send_to_editor( $html, $id, $attachment ) {

		$post = get_post( $id );

		if ( ! HelixWare_Asset_Service::is_helixware_mime_type( $post->post_mime_type ) ) {
			return $html;
		}

		return "[hw_embed id='$post->guid']";
	}

}