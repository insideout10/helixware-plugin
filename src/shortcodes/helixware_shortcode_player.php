<?php

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
	
	// Extract attributes and set default values
    $hewa_atts = shortcode_atts(array(
        'width'      => '100%',
        'height'     => '300px',
        'path' => 'cdn/A1TAAdmin/VendorAdm/tests/test-signal-3.mp4'
    ), $atts);
	
	// Retrieving sources
    $urls = hewa_get_clip_urls( $hewa_atts['path'] );
	// Formatting sources as <source> tags for the <video>
	$sources = hewa_build_video_sources( $urls );
	
	// Css class of the video
	$hewa_css_class = HELIXWARE_SHORTCODE_PREFIX . 'player';
	
	// Load scripts and css on page
	$bower_path = 'bower_components/';
	
	// Videojs
	wp_enqueue_style( 'videojs-css', plugins_url( $bower_path . 'videojs/dist/video-js/video-js.css', __FILE__ ) );
	wp_enqueue_script( 'videojs', plugins_url( $bower_path . 'videojs/dist/video-js/video.js', __FILE__ ) );
	
	// Our js
	wp_enqueue_script( 'helixwarejs', plugins_url( 'js/helixware.js', __FILE__ ) );
	wp_localize_script( 'helixwarejs', 'videojs_params', array(
            'class' => $hewa_css_class,
            'swfurl' => plugins_url( $bower_path . 'videojs/dist/video-js/video-js.swf', __FILE__ )
        )
    );
	
	// Escaping atts.
    $esc_class  = esc_attr( $hewa_css_class );
    $esc_id     = esc_attr( $hewa_css_class . '_' . get_the_ID() );
	$esc_data_id = esc_attr( get_the_ID() );
	$esc_width  = esc_attr( $hewa_atts['width'] );
	$esc_height = esc_attr( $hewa_atts['height'] );
	
	// Return HTML template
    return <<<EOF
<video id=$esc_id class="video-js vjs-default-skin $esc_class" data-id="$esc_data_id"
		controls preload="auto" width="$esc_width" height="$esc_height" >
		$sources
	<p class="vjs-no-js">To view this video consider upgrading to a web browser that <a href="http://videojs.com/html5-video-support/" target="_blank">supports HTML5 video</a></p>
</video>
EOF;
}
add_shortcode( HELIXWARE_SHORTCODE_PREFIX . 'player', 'hewa_shortcode_player' );