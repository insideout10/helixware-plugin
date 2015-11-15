<?php

/**
 * Provides the methods to load a VideoJS.
 *
 * @since 1.3.0
 */
class HelixWare_Player_VideoJS implements HelixWare_Player {

	const LIBRARY_URL = '//vjs.zencdn.net/5.0/video.min.js';
	const CSS_URL = '//vjs.zencdn.net/5.0/video-js.min.css';

	/**
	 * The URL to the videojs-media-sources JavaScript library.
	 *
	 * @since 1.3.0
	 * @access private
	 * @var string $media_sources_library_url The URL to the videojs-media-sources JavaScript library.
	 */
	private $media_sources_library_url;

	/**
	 * The URL to the HLS JavaScript library.
	 *
	 * @since 1.3.0
	 * @access private
	 * @var string $hls_library_url The URL to the HLS JavaScript library.
	 */
	private $hls_library_url;

	/**
	 * A URL service which generates playable URLs.
	 *
	 * @since 1.3.0
	 * @access private
	 * @var \HelixWare_Player_URL_Service $player_url_service A URL service which generates playable URLs.
	 */
	private $player_url_service;

	/**
	 * Create an instance of the HelixWare_Player_VideoJS class.
	 *
	 * @since 1.3.0
	 *
	 * @param \HelixWare_Player_URL_Service $player_url_service A URL service which generates playable URLs.
	 */
	public function __construct( $player_url_service ) {

		$this->media_sources_library_url = plugin_dir_url( dirname( __FILE__ ) ) . 'public/js/videojs-media-sources.min.js';
		$this->hls_library_url           = plugin_dir_url( dirname( __FILE__ ) ) . 'public/js/videojs.hls.min.js';

		$this->player_url_service = $player_url_service;

	}

	/**
	 * Queue the scripts and styles required by VideoJS.
	 *
	 * @since 1.3.0
	 */
	public function queue_scripts() {

		$version = HelixWare::get_instance()->get_version();

		wp_enqueue_script( 'videojs', self::LIBRARY_URL, array(), '1.3.1', $version );
		wp_enqueue_script( 'videojs-media-sources', $this->media_sources_library_url, array( 'videojs' ), $version, TRUE );
		wp_enqueue_script( 'videojs-hls', $this->hls_library_url, array( 'videojs-media-sources' ), $version, TRUE );
		wp_enqueue_style( 'videojs', self::CSS_URL, array(), $version );

	}

	/**
	 * Render the HTML code for the player.
	 *
	 * @since 1.3.0
	 *
	 * @param int $id The attachment id.
	 * @param int $width The player width (default 640).
	 * @param int $height The player height (default 360).
	 * @param string $thumbnail_url The URL of the thumbnail.
	 * @param string $title The asset's title.
	 * @param string $description The asset's description.
	 *
	 * @return string The HTML code for the player.
	 */
	public function render( $id, $width = 640, $height = 360, $thumbnail_url = NULL, $title = NULL, $description = NULL ) {

		$url = $this->player_url_service->get_url( $id );

		// Queue the required scripts and styles.
		$this->queue_scripts();

		// Generate a unique player id to avoid clashes with other potentially instantiated players.
		$element_id = uniqid( 'videojs' );

		// Preset the initial configuration.
		$args = array();

		// The other configuration parameters if provided.
		// The thumbnail URL is also set in the playlist.
		if ( isset( $thumbnail_url ) ) {
			$args['poster'] = $thumbnail_url;
		}

		// Encode the arguments in JSON format.
		$args_js = json_encode( $args, JSON_PRETTY_PRINT );

		return <<<EOF

<video id="$element_id" width="$width" height="$height" class="video-js vjs-default-skin" controls>Loading the player...
	<source src="$url" type="application/x-mpegURL" />
</video>

<script type="text/javascript">
	( jQuery( function( $ ) {
		videojs( '$element_id', $args_js, function() {} );
	} ) );
</script>

EOF;

	}

}
