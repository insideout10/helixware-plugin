<?php

/**
 */
class HelixWare_Playlist_RSS_JWPlayer {

	/**
	 *  The Stream service.
	 *
	 * @since 1.2.0
	 * @access private
	 * @var \HelixWare_Stream_Service $stream_service The Stream service.
	 */
	private $stream_service;

	/**
	 * The Asset image service.
	 *
	 * @since 1.2.0
	 * @access private
	 * @var \HelixWare_Asset_Image_Service $asset_image_service The Asset Image service.
	 */
	private $asset_image_service;

	/**
	 * Create an instance of the HelixWare_Playlist_RSS_JWPlayer.
	 *
	 * @since 1.2.0
	 *
	 * @param \HelixWare_Stream_Service $stream_service The Stream service.
	 * @param \HelixWare_Asset_Image_Service $asset_image_service The Asset Image service.
	 */
	public function __construct( $stream_service, $asset_image_service ) {

		$this->stream_service      = $stream_service;
		$this->asset_image_service = $asset_image_service;

	}

	public function ajax_rss_jwplayer() {

		// Check if a post id has been provided.
		if ( ! isset( $_GET['id'] ) || ! is_numeric( $_GET['id'] ) ) {
			wp_die( 'An id is required.' );
		}

		// Get the post.
		if ( NULL === ( $post = get_post( $_GET['id'] ) ) ) {
			wp_die( 'Attachment not found' );
		}


		header( 'Content-Type: application/rss+xml' );

		$thumbnail_url = $this->asset_image_service->get_local_image_url_by_id( $post->ID );

		$this->_print_header( $post->post_title, $post->post_content, $thumbnail_url, admin_url( "admin-ajax.php?action=hw_vtt_chapters&id=$post->ID" ) );
		array_walk( $this->stream_service->get_streams( $post->ID ), function ( $stream ) {
			echo( "<jwplayer:source file=\"$stream->url\" label=\"$stream->label\" />\n" );
		} );
		$this->_print_footer();

		wp_die();

	}

	private function _print_header( $title = NULL, $description = NULL, $thumbnail_url = NULL, $chapters_url = NULL ) {

		echo( "<rss version=\"2.0\" xmlns:jwplayer=\"http://rss.jwpcdn.com/\">\n" );
		echo( "<channel>\n" );
		echo( "<item>\n" );

		if ( isset( $title ) ) {
			echo( sprintf( "<title>%s</title>\n", htmlentities( $title ) ) );
		}

		if ( ! empty( $description ) ) {
			echo( sprintf( "<description>%s</description>\n", htmlentities( $description ) ) );
		}

		if ( isset( $thumbnail_url ) ) {
			echo( sprintf( "<jwplayer:image>%s</jwplayer:image>\n", htmlentities( $thumbnail_url ) ) );
		}

		if ( isset( $chapters_url ) ) {
			echo( '<jwplayer:track file="' . htmlentities( $chapters_url ) . '" kind="chapters" />' . "\n" );
		}

	}

	private function _print_footer() {

		echo( "</item>\n" );
		echo( "</channel>\n" );
		echo( "</rss>\n" );

	}

	private function _print_item( $title = NULL, $description = NULL, $thumbnail_url = NULL ) {


	}

}