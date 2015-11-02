<?php

/**
 * Provides the methods to load a JWPlayer 7.
 *
 * @since 1.2.0
 */
class HelixWare_Player_JWPlayer7 implements HelixWare_Player {

	const LIBRARY_URL = '//content.jwplatform.com/libraries/%s.js';

	/**
	 * The Player URL service.
	 *
	 * @since 1.3.0
	 * @access private
	 * @var \HelixWare_Player_URL_Service $player_url_service The Player URL service.
	 */
	private $player_url_service;

	/**
	 * The JWPlayer 7 key.
	 *
	 * @since 1.2.0
	 * @access private
	 * @var string $key The JWPlayer 7 key.
	 */
	private $key;

	/**
	 * Create an instance of the HelixWare_JWPlayer7 class.
	 *
	 * @since 1.2.0
	 *
	 * @param \HelixWare_Player_URL_Service $player_url_service The Player URL service.
	 * @param string $key The player key.
	 */
	public function __construct( $player_url_service, $key ) {

		// Check that the key is set.
		if ( FALSE === $key ) {
			hewa_write_log( 'The JWPlayer key is not set.' );
		}

		$this->player_url_service = $player_url_service;
		$this->key                = $key;
	}

	/**
	 * Queue the scripts required by JWPlayer 7.
	 *
	 * @since 1.2.0
	 */
	private function queue_scripts() {

		wp_enqueue_script( 'jwplayer7', sprintf( self::LIBRARY_URL, $this->key ) );

	}

	/**
	 * Render the HTML code for the player.
	 *
	 * @since 1.2.0
	 *
	 * @param int $id The post id.
	 * @param int $width The player width (default 640).
	 * @param int $height The player height (default 360).
	 * @param string $thumbnail_url The URL of the thumbnail.
	 * @param string $title The asset's title.
	 * @param string $description The asset's description.
	 *
	 * @return string The HTML code for the player.
	 * @internal param string $url The URL of the video.
	 */
	public function render( $id, $width = 640, $height = 360, $thumbnail_url = NULL, $title = NULL, $description = NULL ) {

		$url = $this->player_url_service->get_url( $id );

		// Queue the required scripts.
		$this->queue_scripts();

		// Generate a unique player id to avoid clashes with other potentially instantiated players.
		$element_id = uniqid( 'jwplayer' );

		// Preset the initial configuration.
		$args = array(
			'playlist' => $url,
			'width'    => $width,
			'height'   => $height
		);

		// The other configuration parameters if provided.
		// The thumbnail URL is also set in the playlist.
		if ( isset( $thumbnail_url ) ) {
			$args['image'] = $thumbnail_url;
		}

		if ( isset( $title ) ) {
			$args['title'] = $title;
		}

		if ( isset( $description ) ) {
			$args['description'] = $description;
		}

		if ( isset( $chapters_url ) ) {

			$args['tracks'] = array(
				array(
					'file' => $chapters_url,
					'kind' => 'chapters'
				)
			);

		}

		// Encode the arguments in JSON format.
		$args_js = json_encode( $args, JSON_PRETTY_PRINT );

		return <<<EOF

<div id="$element_id">Loading the player...</div>

<script type="text/javascript">
(jQuery(function($) {

	jwplayer('$element_id').setup($args_js);

}));

</script>

EOF;

	}

}