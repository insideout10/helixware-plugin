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
	const MEDIA_LIBRARY_THUMBNAIL_WIDTH = 230;
	const DEFAULT_TIMECODE = 5;
	const DEFAULT_WIDTH = 640;
	const DEFAULT_THUMBNAIL_WIDTH = 100;

	/**
	 * @var \HelixWare_HTTP_Client $http_clent An HTTP client.
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
	 * @param int $height The height of the image (default NULL), used for cropping.
	 * @param int $x The x coordinate of the image (default NULL), used for cropping.
	 * @param int $y The y coordinate of the image (default NULL), used for cropping.
	 */
	public function send_image( $id, $seconds, $width = self::DEFAULT_WIDTH, $height = NULL, $x = NULL, $y = NULL ) {

		$url      = $this->get_remote_image_url_by_id( $id, $seconds, $width, $height, $x, $y );
		$response = $this->http_client->execute( 'GET', $url, NULL, NULL, self::ACCEPT );

		if ( is_wp_error( $response ) ) {
			wp_die( 'An error occurred.' );
		}

		// dump out the image.
		if ( isset( $response['headers']['content-type'] ) ) {
			$content_type = $response['headers']['content-type'];
			header( "Content-Type: $content_type" );
		}

		echo( isset( $response['body'] ) ? $response['body'] : '' );

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
	 * @param int $height The height of the image (default NULL), used for cropping.
	 * @param int $x The x coordinate of the image (default NULL), used for cropping.
	 * @param int $y The y coordinate of the image (default NULL), used for cropping.
	 *
	 * @return string A relative path to the image.
	 */
	public function get_remote_image_url_by_id( $id, $seconds, $width = self::DEFAULT_WIDTH, $height = NULL, $x = NULL, $y = NULL ) {

		$guid = $this->asset_service->get_guid( $id );

		// If we have been provided with valid parameters for height, x and y then
		// we set the parameters for cropping, otherwise for scaling.
		$parameters = ( isset( $height ) && is_numeric( $height ) && isset( $x ) && is_numeric( $x ) && isset( $y ) && is_numeric( $y )
			? "w=$width&h=$height&x=$x&y=$y"
			: "width=$width" );

		$url = $guid . '/images/' . date( 'H/i/s', $seconds ) . '?' . $parameters;

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
	 * @param int $height The height of the image (default NULL), used for cropping.
	 * @param int $x The x coordinate of the image (default NULL), used for cropping.
	 * @param int $y The y coordinate of the image (default NULL), used for cropping.
	 *
	 * @return string The local URL to the image.
	 */
	public function get_local_image_url_by_id( $id, $seconds = self::DEFAULT_TIMECODE, $width = self::DEFAULT_WIDTH, $height = NULL, $x = NULL, $y = NULL ) {

		$path = "admin-ajax.php?action=hw_asset_image&id=$id&seconds=$seconds&width=$width";

		if ( isset( $height ) && is_numeric( $height ) ) {
			$path .= "&height=$height";
		}

		if ( isset( $x ) && is_numeric( $x ) ) {
			$path .= "&x=$x";
		}

		if ( isset( $y ) && is_numeric( $y ) ) {
			$path .= "&y=$y";
		}

		return admin_url( $path );

	}

	/**
	 * Get the URL to the VTT images file pointing to a list of thumbnails for the asset.
	 *
	 * @since 1.2.0
	 *
	 * @param int $id The post id.
	 * @param int $width The image width.
	 *
	 * @return string|void
	 */
	public function get_vtt_thumbnails_url( $id, $width = self::DEFAULT_THUMBNAIL_WIDTH ) {

		return admin_url( "admin-ajax.php?action=hw_vtt_thumbnails&id=$id&width=$width" );

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
		$seconds = ( isset( $_GET['seconds'] ) && is_numeric( $_GET['seconds'] ) ? $_GET['seconds'] : self::DEFAULT_TIMECODE );

		// Get the width or use 640 as default.
		$width = ( isset( $_GET['width'] ) && is_numeric( $_GET['width'] ) ? $_GET['width'] : self::DEFAULT_WIDTH );

		$height = ( isset( $_GET['width'] ) && is_numeric( $_GET['width'] ) ? $_GET['width'] : self::DEFAULT_WIDTH );
		$x      = ( isset( $_GET['x'] ) && is_numeric( $_GET['x'] ) ? $_GET['x'] : NULL );
		$y      = ( isset( $_GET['y'] ) && is_numeric( $_GET['y'] ) ? $_GET['y'] : NULL );

		$this->send_image( $id, $seconds, $width, $height, $x, $y );

	}

	/**
	 * Intercept the filter to prepare attachments for the JavaScript function (WP Media Library)
	 * and add thumbnail images for HelixWare assets.
	 *
	 * @since 1.1.0
	 *
	 * @param array $response A response which will be serialized to JSON.
	 * @param WP_Post $attachment A post instance.
	 * @param array $meta An array of meta for the attachment.
	 *
	 * @return array The enriched response array.
	 */
	public function wp_prepare_attachment_for_js( $response, $attachment, $meta ) {

		// Only process HelixWare on-demand assets.
		if ( HelixWare_Asset_Service::MIME_TYPE_ONDEMAND !== $response['mime'] ) {
			return $response;
		}

		$response['image'] = array( 'src' => $this->get_local_image_url_by_id( $attachment->ID, self::DEFAULT_TIMECODE, self::MEDIA_LIBRARY_THUMBNAIL_WIDTH ) );

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

	/**
	 * Outputs a VTT file defining the images for the attachment with the provided id.
	 *
	 * @since 1.2.0
	 */
	public function ajax_vtt_thumbnails() {

		// Check that a post ID has been provided.
		if ( ! isset( $_GET['id'] ) || ! is_numeric( $_GET['id'] ) ) {
			wp_die( 'A numeric id is required.' );
		}

		// Get the ID and width parameters.
		$id    = $_GET['id'];
		$width = ( isset( $_GET['width'] ) && is_numeric( $_GET['width'] ) ? (int) $_GET['width'] : 100 );

		if ( FALSE === ( $duration = $this->asset_service->get_duration( $id ) ) ) {
			wp_die( 'Duration unknown' );
		};

		echo( "WEBVTT\n\n" );

		// Show a thumbnail every second.
		for ( $seconds = 0; $seconds < $duration; $seconds ++ ) {
			echo( HelixWare_Helper::milliseconds_to_timecode( $seconds * 1000 ) . ' --> ' . HelixWare_Helper::milliseconds_to_timecode( ( $seconds + 1 ) * 1000 ) . "\n" );
			// We add ext.png as hack otherwise JWPlayer 7 doesn't show the image.
			echo( $this->get_local_image_url_by_id( $id, $seconds, $width ) . "&ext.png\n" );
			echo( "\n" );
		}

		wp_die();

	}

}