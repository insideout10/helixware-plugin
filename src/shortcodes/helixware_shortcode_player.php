<?php

function hewa_shortcode_player_ajax() {
	
	echo('STOCAZZO');
	
	wp_die();
}
add_action('wp_ajax_hewa_player', 'hewa_shortcode_player_ajax');
add_action('wp_ajax_nopriv_hewa_player', 'hewa_shortcode_player_ajax');

/**
 * This file contains the code to handle the [hewa-player ...] shortcode.
 */

/**
 * Creates the code to display an HTML5 player for the specified file.
 *
 * @param array $atts An array of parameters, including the *path* value.
 * @return string An HTML coe fragment.
 */
function hewa_shortcode_player( $atts ){
	
	//Extract attributes and set default values
    $hewa_atts = shortcode_atts(array(
        'width'      => '100%',
        'height'     => '300px',
        'path' => '000'
    ), $atts);

    // TODO: code this method.
    $path = '...';

    $urls = hewa_get_clip_urls( $path );
	
	// RTMP
	//OK $sources = '<source src="rtmp://cp67126.edgefcs.net/ondemand/&mp4:mediapm/ovp/content/test/video/spacealonehd_sounas_640_300.mp4" type="rtmp/mp4">';
	//OK $sources = '<source src="rtmp://streamer.a1.net/&mp4:cdn/A1TAAdmin/VendorAdm/tests/test-signal-3.mp4" type="rtmp/mp4">';	
	
	// HLS
	//OK $sources = '<source src="http://solutions.brightcove.com/jwhisenant/hls/apple/bipbop/bipbopall.m3u8" type="application/x-mpegURL">';
	//NI $sources = '<source src="http://streamer.a1.net/m3ugen/iPhone-src/cdn/A1TAAdmin/VendorAdm/tests/test-signal-3.mp4" type="application/x-mpegURL">';
	
	$sources = '<source src="http://solutions.brightcove.com/jwhisenant/hls/apple/bipbop/bipbopall.m3u8" type="application/x-mpegURL">';
	
	
	// Load scripts and css on page
	$bower_path = '../bower_components/';
	
	// Videojs
	wp_enqueue_style( 'videojs-css', plugins_url( $bower_path . 'videojs/dist/video-js/video-js.css', __FILE__ ) );
	wp_enqueue_script( 'videojs', plugins_url( $bower_path . 'videojs/dist/video-js/video.js', __FILE__ ) );

	// Videojs HLS plugin
	wp_enqueue_script( 'videojs-contrib-media-sources', plugins_url( $bower_path . 'videojs-contrib-media-sources/src/videojs-media-sources.js', __FILE__ ) );
	wp_enqueue_script( 'videojs-contrib-hls', plugins_url( $bower_path . 'videojs-contrib-hls/src/videojs-hls.js', __FILE__ ) );
	wp_enqueue_script( 'videojs-hls-flv-tag', plugins_url( $bower_path . 'videojs-contrib-hls/src/flv-tag.js', __FILE__) );
	wp_enqueue_script( 'videojs-hls-exp-golomb', plugins_url( $bower_path . 'videojs-contrib-hls/src/exp-golomb.js', __FILE__) );
	wp_enqueue_script( 'videojs-hls-h264-stream', plugins_url( $bower_path . 'videojs-contrib-hls/src/h264-stream.js', __FILE__) );
	wp_enqueue_script( 'videojs-hls-aac-stream', plugins_url( $bower_path . 'videojs-contrib-hls/src/aac-stream.js', __FILE__) );
	wp_enqueue_script( 'videojs-hls-segment-parser', plugins_url( $bower_path . 'videojs-contrib-hls/src/segment-parser.js', __FILE__) );
	wp_enqueue_script( 'videojs-hls-stream', plugins_url( $bower_path . 'videojs-contrib-hls/src/stream.js', __FILE__) );
	wp_enqueue_script( 'videojs-hls-m3u8-parser', plugins_url( $bower_path . 'videojs-contrib-hls/src/m3u8/m3u8-parser.js', __FILE__) );
	wp_enqueue_script( 'videojs-hls-playlist-loader', plugins_url( $bower_path . 'videojs-contrib-hls/src/playlist-loader.js', __FILE__) );

	// Our js
	wp_enqueue_script( 'helixwarejs', plugins_url( '../js/helixware.js', __FILE__ ) );
	wp_localize_script( 'helixwarejs', 'videojs_params', array(
            'id' => 'hewa_video_' . get_the_ID(),	// Not clean YEEEEET
            'swfurl' => plugins_url( $bower_path . 'videojs/dist/video-js/video-js.swf', __FILE__ ),
			'ajax_url'   => admin_url('admin-ajax.php'),
            'action'     => 'hewa_player'
        )
    );
	
	
	// Escaping atts.
    $esc_class  = esc_attr( 'hewa_video' );
    $esc_id     = esc_attr( 'hewa_video_' . get_the_ID() );
	$esc_width  = esc_attr( $hewa_atts['width'] );
	$esc_height = esc_attr( $hewa_atts['height'] );
	$esc_videojs_swf_url = plugins_url($videojs_path . 'video-js.swf', __FILE__);
	
	// Return HTML template
    return <<<EOF
<video id=$esc_id class="video-js vjs-default-skin $esc_class"
		controls preload="auto" width="$esc_width" height="$esc_height" >
		$sources
	<p class="vjs-no-js">To view this video consider upgrading to a web browser that <a href="http://videojs.com/html5-video-support/" target="_blank">supports HTML5 video</a></p>
</video>
EOF;
}
add_shortcode( HELIXWARE_SHORTCODE_PREFIX . 'player', 'hewa_shortcode_player' );