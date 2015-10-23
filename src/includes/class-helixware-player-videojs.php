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
	 * @var string $mediaSourcesLibraryURL The URL to the videojs-media-sources JavaScript library.
	 */
	private $mediaSourcesLibraryURL;

	/**
	 * The URL to the HLS JavaScript library.
	 *
	 * @since 1.3.0
	 * @access private
	 * @var string $hlsLibraryURL The URL to the HLS JavaScript library.
	 */
	private $hlsLibraryURL;

	/**
	 * Create an instance of the HelixWare_Player_VideoJS class.
	 *
	 * @since 1.3.0
	 */
	public function __construct() {

		$this->mediaSourcesLibraryURL = plugin_dir_url( dirname( __FILE__ ) ) . 'public/js/videojs-media-sources.min.js';
		$this->hlsLibraryURL          = plugin_dir_url( dirname( __FILE__ ) ) . 'public/js/videojs.hls.min.js';

	}

	/**
	 * Queue the scripts and styles required by VideoJS.
	 *
	 * @since 1..0
	 */
	private function queue_scripts() {

		wp_enqueue_script( 'videojs', self::LIBRARY_URL );
		wp_enqueue_script( 'videojs-media-sources', $this->mediaSourcesLibraryURL, array( 'videojs' ) );
		wp_enqueue_script( 'videojs-hls', $this->hlsLibraryURL, array( 'videojs-media-sources' ) );
		wp_enqueue_style( 'videojs', self::CSS_URL );

	}

	/**
	 * Render the HTML code for the player.
	 *
	 * @since 1.3.0
	 *
	 * @param string $url The URL of the video.
	 * @param int $width The player width (default 640).
	 * @param int $height The player height (default 360).
	 * @param string $thumbnail_url The URL of the thumbnail.
	 * @param string $title The asset's title.
	 * @param string $description The asset's description.
	 *
	 * @return string The HTML code for the player.
	 */
	public function render( $url, $width = 640, $height = 360, $thumbnail_url = NULL, $title = NULL, $description = NULL ) {

		// Queue the required scripts and styles.
		$this->queue_scripts();

		// Generate a unique player id to avoid clashes with other potentially instantiated players.
		$element_id = uniqid( 'videojs' );

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

<video id="$element_id" width=600 height=300 class="video-js vjs-default-skin" controls>Loading the player...

<source
     src="http://cloud.helixware.localhost/4/pub/asset/104/streams.m3u8"
     type="application/x-mpegURL">
</video>

<script type="text/javascript">
(jQuery(function($) {

	videojs('$element_id', { /* Options */ }, function() {
	});

}));

</script>

EOF;

	}

}