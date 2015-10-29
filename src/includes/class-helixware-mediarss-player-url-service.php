<?php

/**
 * Generates Media RSS/JWPlayer playlists.
 *
 * @since 1.2.0
 */
class HelixWare_MediaRSS_Player_URL_Service implements HelixWare_Player_URL_Service {

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
	 * Create an instance of the HelixWare_MediaRSS_Player_URL_Service.
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

		return admin_url( "admin-ajax.php?action=hw_rss_jwplayer&id=$id" );

	}

	/**
	 * Handles the _hw_rss_jwplayer_ AJAX action, by outputting the RSS JWPlayer
	 * playlist.
	 *
	 * @since 1.2.0
	 */
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

		$thumbnail_url  = $this->asset_image_service->get_local_image_url_by_id( $post->ID );
		$thumbnails_url = $this->asset_image_service->get_vtt_thumbnails_url( $post->ID );

		$this->_print_header( $post, $post->post_title, $post->post_content, $thumbnail_url, $thumbnails_url );
		$streams = $this->stream_service->get_streams( $post->ID );

		if ( is_array( $streams ) ) {

			// Print each stream in the streams array (if it's not an RTSP link).
			array_walk( $streams, function ( $stream ) {

				// JWPlayer 6 tries to play rtsp links, therefore we remove them.
				if ( 0 !== strpos( $stream->url, 'rtsp://' ) ) {
					echo( "<jwplayer:source file=\"$stream->url\" label=\"$stream->label\" />\n" );
				}

			} );

		}

		$this->_print_footer();

		wp_die();

	}

	/**
	 * Print the RSS/JWPlayer header.
	 *
	 * @since 1.2.0
	 *
	 * @param WP_Post $post The attachment.
	 * @param string|null $title The attachment's title.
	 * @param string|null $description The attachment's description.
	 * @param string|null $thumbnail_url A link to a thumbnail image.
	 * @param string|null $thumbnails_url A link to a VTT files with thumbnails.
	 */
	private function _print_header( $post, $title = NULL, $description = NULL, $thumbnail_url = NULL, $thumbnails_url = NULL ) {

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

		if ( isset( $thumbnails_url ) ) {
			echo( '<jwplayer:track file="' . htmlentities( $thumbnails_url ) . '" kind="thumbnails" />' . "\n" );
		}

		// We delegate adding other information here, e.g. chapters are added by the MICO
		// extensions by hooking to this action.
		do_action( 'hewa_playlist_rss_jwplayer_header', $post );

	}

	private function _print_footer() {

		echo( "</item>\n" );
		echo( "</channel>\n" );
		echo( "</rss>\n" );

	}

}
