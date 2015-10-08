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

	/**
	 * The HelixWare server URL.
	 *
	 * @since 1.1.0
	 * @access private
	 * @var string $server_url The HelixWare server URL.
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
	 * Initialize the class and set its properties.
	 *
	 * @since    1.1.0
	 *
	 * @param \HelixWare_HTTP_Client $http_client An HTTP client.
	 * @param string $server_url The HelixWare server URL.
	 * @param \HelixWare_Asset_Service $asset_service The Asset service.
	 */
	public function __construct( $http_client, $server_url, $asset_service ) {

		$this->http_client   = $http_client;
		$this->server_url    = $server_url;
		$this->asset_service = $asset_service;

	}

	/**
	 * Print the image at the specified path to the response output.
	 *
	 * @since 1.1.0
	 *
	 * @param int $id The attachment id.
	 * @param int $seconds The timecode of the image.
	 * @param int $width The width of the image (default 640).
	 */
	public function send_image( $id, $seconds, $width = 640 ) {

		$url      = $this->get_remote_image_url_by_id( $id, $seconds, $width );
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
	 * Get the image URL on HelixWare.
	 *
	 * @since 1.2.0
	 *
	 * @param int $id The post id.
	 * @param int $seconds The timecode of the image.
	 * @param int $width The width of the image (default 640).
	 *
	 * @return string A relative path to the image.
	 */
	public function get_remote_image_url_by_id( $id, $seconds, $width = 640 ) {

		$guid = $this->asset_service->get_guid( $id );
		$url  = $guid . '/images/' . date( 'H/i/s', $seconds ) . '?width=' . $width;

		return $url;
	}

	/**
	 * Get the WordPress URL to the image.
	 *
	 * @since 1.2.0
	 *
	 * @param int $id The post id.
	 * @param int $seconds The timecode of the image (default 5).
	 * @param int $width The width of the image (default 640).
	 *
	 * @return string The local URL to the image.
	 */
	public function get_local_image_url_by_id( $id, $seconds = 5, $width = 640 ) {

		return admin_url( "admin-ajax.php?action=hw_asset_image&id=$id&seconds=$seconds&width=$width" );

	}

	/**
	 * Hooked to the _hw_asset_image_ AJAX action, it'll get the path from the
	 * request parameters and call {@see get_image}.
	 *
	 * @since 1.1.0
	 */
	public function wp_ajax_get_image() {

		// The post ID is required.
		if ( ! isset( $_GET['id'] ) && ! is_numeric( $_GET['id'] ) ) {
			wp_die( 'The path id parameter are required.' );
		}

		// The attachment id.
		$id = $_GET['id'];

		// Get the seconds or use 5 as default.
		$seconds = ( isset( $_GET['seconds'] ) && is_numeric( $_GET['seconds'] ) ? $_GET['seconds'] : 5 );

		// Get the width or use 640 as default.
		$width = ( isset( $_GET['width'] ) && is_numeric( $_GET['width'] ) ? $_GET['width'] : 640 );

		$this->send_image( $id, $seconds, $width );

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

		return "[hw_embed id='$post->ID']";
	}

}