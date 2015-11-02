<?php

/**
 * Provides playlist URLs to players.
 *
 * @since 1.3.0
 */
class HelixWare_HLS_Player_URL_Service implements HelixWare_Player_URL_Service {

	/**
	 *  The Stream service.
	 *
	 * @since 1.2.0
	 * @access private
	 * @var \HelixWare_Stream_Service $stream_service The Stream service.
	 */
	private $stream_service;

	/**
	 * Create an instance of the HelixWare_HLS_Player_URL_Service.
	 *
	 * @since 1.2.0
	 *
	 * @param \HelixWare_Stream_Service $stream_service The Stream service.
	 * @param \HelixWare_Asset_Image_Service $asset_image_service The Asset Image service.
	 */
	public function __construct( $stream_service ) {

		$this->stream_service = $stream_service;

	}

	/**
	 * Get the URL to the RSS JWPlayer playlist.
	 *
	 * @since 1.2.0
	 *
	 * @param int $id The post id.
	 *
	 * @return string The URL to the RSS JWPlayer.
	 */
	public function get_url( $id ) {

		$streams = $this->stream_service->get_streams( $id );

		// Get the stream which is HLS and ABR.
		$url = array_reduce( $streams, function ( $carry, $item ) {

			if ( 'application/x-mpegurl' === $item->mimeType && 'Auto' === $item->label ) {
				return $item->url;
			}

			return $carry;

		} );

		return $url;

	}

	/**
	 * An AJAX method that returns the HLS ABR URL for an asset id.
	 *
	 * @since 1.3.0
	 */
	public function ajax_hls_url() {

		// Check that the id parameter has been provided.
		if ( ! isset( $_REQUEST['id'] ) || ! is_numeric( $_REQUEST['id'] ) ) {

			wp_send_json_error( 'The id parameter is required.' );

		}

		// Get the URL and return it as JSON.
		$url = $this->get_url( (int) $_REQUEST['id'] );

		wp_send_json_success( $url );

	}

}
