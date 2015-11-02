<?php

/**
 * Provides the _hw_embed_ shortcode.
 *
 * @package    HelixWare
 * @subpackage HelixWare/includes
 * @author     David Riccitelli <david@insideout.io>
 */
class HelixWare_Embed_Shortcode {

	const HANDLE_NAME = 'hw_embed';

	/**
	 * The Asset service.
	 *
	 * @since 1.2.0
	 * @access private
	 * @var \HelixWare_Asset_Service $asset_service The Asset service.
	 */
	private $asset_service;

	/**
	 * The Asset Image service.
	 * @since 1.2.0
	 * @access private
	 * @var \HelixWare_Asset_Image_Service The Asset Image service.
	 */
	private $asset_image_service;

	/**
	 * A player rendering class.
	 *
	 * @since 1.2.0
	 * @access private
	 * @var \HelixWare_Player $player A player rendering class
	 */
	private $player;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.1.0
	 *
	 * @param \HelixWare_Asset_Service $asset_service The Asset service.
	 * @param \HelixWare_Asset_Image_Service $asset_image_service The Asset Image service.
	 * @param \HelixWare_Player $player A player.
	 */
	public function __construct( $asset_service, $asset_image_service, $player ) {

		$this->asset_service       = $asset_service;
		$this->asset_image_service = $asset_image_service;
		$this->player              = $player;

		add_shortcode( self::HANDLE_NAME, array( $this, 'render' ) );

	}

	/**
	 * Render the _hw_embed_ shortcode. Internally this method will rely to
	 * {@see _render_compat} which calls the existing _hw_player_ shortcode.
	 *
	 * @since 1.1.0
	 *
	 * @param array $atts An array of shortcode attributes.
	 *
	 * @return string The HTML code.
	 */
	public function render( $atts ) {

		// We need a post ID.
		if ( ! isset( $atts['id'] ) || ! is_numeric( $atts['id'] ) ) {
			return '';
		}

		return $this->_render( $atts );

	}

//	/**
//	 * Renders the media player using the _compatible_ shortcode _hw_player_.
//	 *
//	 * @since 1.1.0
//	 *
//	 * @param array $atts The shortcode attributes.
//	 *
//	 * @return string The HTML fragment.
//	 */
//	private function _render_compat( $atts ) {
//
//		// TODO: this ID can be either an on-demand or a live, check by reading the metadata.
//		$asset_id = $this->asset_service->get_asset_id( $atts['id'] );
//
//		// Unset the *id* and set the *asset_id*. We pass through other attributes
//		// the use may have set.
//		unset( $atts['id'] );
//		$atts['asset_id'] = $asset_id;
//
//		return hewa_shortcode_player( $atts );
//	}

	/**
	 * Render the player.
	 *
	 * @since 1.2.0
	 *
	 * @param array $atts
	 *
	 * @return string The player HTML code.
	 */
	private function _render( $atts ) {

//		// The attachment ID.
		$id = $atts['id'];

		return $this->player->render( $id, 640, 360, $this->asset_image_service->get_local_image_url_by_id( $id, 5 ) );

	}

//	/**
//	 * Alternative method to use video.js to render a video.
//	 *
//	 * @since 1.1.0
//	 *
//	 * @param array $atts Shortcode attributes.
//	 *
//	 * @return string The HTML fragment.
//	 */
//	private function _render_videojs( $atts ) {
//
//		wp_enqueue_style( 'videojs', plugin_dir_url( __FILE__ ) . 'public/lib/videojs/video-js.min.css' );
//
//		// video.js 5
//		// wp_enqueue_script( 'videojs-ie8', plugin_dir_url( __FILE__ ) . 'public/lib/videojs/ie8/videojs-ie8.min.js' );
//		// wp_enqueue_script( 'videojs', plugin_dir_url( __FILE__ ) . 'public/lib/videojs/video.min.js' );
//
//		wp_enqueue_script( 'videojs', plugin_dir_url( __FILE__ ) . 'public/lib/videojs/video.dev.js' );
//		wp_enqueue_script( 'videojs-media-sources', plugin_dir_url( __FILE__ ) . 'public/lib/videojs/videojs-media-sources.js' );
//
//		wp_enqueue_script( 'videojs-hls', plugin_dir_url( __FILE__ ) . 'public/lib/videojs/hls/videojs-hls.js' );
//
//		wp_enqueue_script( 'videojs-hls-xhr', plugin_dir_url( __FILE__ ) . 'public/lib/videojs/hls/xhr.js' );
//		wp_enqueue_script( 'videojs-hls-flv-tag', plugin_dir_url( __FILE__ ) . 'public/lib/videojs/hls/flv-tag.js' );
//		wp_enqueue_script( 'videojs-hls-stream', plugin_dir_url( __FILE__ ) . 'public/lib/videojs/hls/stream.js' );
//		wp_enqueue_script( 'videojs-hls-exp-golomb', plugin_dir_url( __FILE__ ) . 'public/lib/videojs/hls/exp-golomb.js' );
//		wp_enqueue_script( 'videojs-hls-h264-extradata', plugin_dir_url( __FILE__ ) . 'public/lib/videojs/hls/h264-extradata.js' );
//		wp_enqueue_script( 'videojs-hls-h264-stream', plugin_dir_url( __FILE__ ) . 'public/lib/videojs/hls/h264-stream.js' );
//		wp_enqueue_script( 'videojs-hls-aac-stream', plugin_dir_url( __FILE__ ) . 'public/lib/videojs/hls/aac-stream.js' );
//		wp_enqueue_script( 'videojs-hls-metadata-stream', plugin_dir_url( __FILE__ ) . 'public/lib/videojs/hls/metadata-stream.js' );
//		wp_enqueue_script( 'videojs-hls-segment-parser', plugin_dir_url( __FILE__ ) . 'public/lib/videojs/hls/segment-parser.js' );
//		wp_enqueue_script( 'videojs-hls-m3u8-parser', plugin_dir_url( __FILE__ ) . 'public/lib/videojs/hls/m3u8/m3u8-parser.js' );
//		wp_enqueue_script( 'videojs-hls-playlist', plugin_dir_url( __FILE__ ) . 'public/lib/videojs/hls/playlist.js' );
//		wp_enqueue_script( 'videojs-hls-playlist-loader', plugin_dir_url( __FILE__ ) . 'public/lib/videojs/hls/playlist-loader.js' );
//
//		wp_enqueue_script( 'pkcs7-unpad', plugin_dir_url( __FILE__ ) . 'public/lib/pkcs7/pkcs7.unpad.js' );
//
//		wp_enqueue_script( 'videojs-hls-decrypter', plugin_dir_url( __FILE__ ) . 'public/lib/videojs/hls/decrypter.js' );
//		wp_enqueue_script( 'videojs-hls-bin-utils', plugin_dir_url( __FILE__ ) . 'public/lib/videojs/hls/bin-utils.js' );
//
//		// DASH support
//		// wp_enqueue_script( 'dash', plugin_dir_url( __FILE__ ) . 'public/lib/dash/dash.all.js' );
//		// wp_enqueue_script( 'videojs-dash', plugin_dir_url( __FILE__ ) . 'public/lib/dash/videojs-dash.min.js' );
//
//		wp_localize_script( 'videojs', 'videojs.options.flash.swf', plugin_dir_url( __FILE__ ) . 'public/videojs/video.min.swf' );
//
//		return <<<EOF
//  <video id="video" class="video-js vjs-default-skin" controls preload="none" width="640" height="264"
//      poster="http://video-js.zencoder.com/oceans-clip.png"
//      data-setup="{}">
//      <source src="..." type="application/dash+xml" />
//    <p class="vjs-no-js">To view this video please enable JavaScript, and consider upgrading to a web browser that <a href="http://videojs.com/html5-video-support/" target="_blank">supports HTML5 video</a></p>
//  </video>
//EOF;
//
//
//	}

}
