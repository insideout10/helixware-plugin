<?php

/**
 * This file contains the code to handle the [hewa-player ...] shortcode.
 */

/**
 * Creates the code to display an HTML5 player for the specified file.
 *
 * @uses hewa_get_clip_urls to load the clip URLs from the remote HelixWare server.
 *
 * @param array $atts An array of parameters, including the *path* value.
 * @return string An HTML coe fragment.
 */
function hewa_shortcode_player( $atts ) {
	
	// Extract attributes and set default values
    // TODO: the default path might point to a custom video that invites the user to select a video.
    // TODO: default width and height ratio should be calculated from the video.
    $params = shortcode_atts( array(
        'width'  => 480,
        'height' => 270,
        'path'   => 'cdn/A1TAAdmin/VendorAdm/tests/test-signal-3.mp4'
    ), $atts);

    // Videojs
    wp_enqueue_style(
        'videojs-css',
        plugins_url( 'bower_components/video.js-dist/dist/video-js/video-js.min.css', __FILE__ )
    );
    wp_enqueue_script(
        'videojs',
        plugins_url( 'bower_components/video.js-dist/dist/video-js/video.js', __FILE__ )
    );

    // Our js
    wp_enqueue_script( 'helixwarejs', plugins_url( 'js/helixware.js', __FILE__ ) );
    wp_localize_script( 'helixwarejs', 'videojs_params', array(
            'class'  => 'hewa-player',
            'swfurl' => plugins_url( 'bower_components/video.js-dist/dist/video-js/video-js.swf', __FILE__ )
        )
    );

    // Retrieving sources
    $clips    = hewa_get_clip_urls( $params['path'] );

    // TODO: the above call might return an error, handle it here and display a friendly message.

    // Setting width and height
	$width_e  = esc_attr( $params['width'] );
	$height_e = esc_attr( $params['height'] );
	
	// Return HTML template
    echo <<<EOF
        <video class='video-js vjs-default-skin hewa-player' controls preload='auto' data-setup='{ "techOrder": ["html5", "flash"] }'
            width="$width_e" height="$height_e">
EOF;

    // Print the streaming sources, we only need to:
    //  * HLS streaming (m3u8) for Safari, iOS, Android
    //  * Flash for all the others (Chrome, Internet Explorer, Safari)
    foreach( $clips as $key => $clip ) {
        switch ( $key ) {

            // HLS streaming.
            case 'm3u8-redirector':

                hewa_player_print_source_tag(
                    admin_url('admin-ajax.php') . '?action=hewa_m3u8&file=' . urlencode( $clip->file ),
                    'application/x-mpegURL'
                );
                break;

            // Flash streaming.
            case 'flash-direct':

                hewa_player_print_source_tag( $clip->url, 'rtmp/mp4' );
                break;

        }
    }

    echo <<<EOF
	        <p class="vjs-no-js">To view this video consider upgrading to a web browser that <a
	            href="http://videojs.com/html5-video-support/" target="_blank">supports HTML5 video</a></p>
        </video>
EOF;
}
add_shortcode( HELIXWARE_SHORTCODE_PREFIX . 'player', 'hewa_shortcode_player' );

/**
 * Print a *source* tag with the provided *src* and *type* attributes.
 *
 * @param string $source The URL source of the stream.
 * @param string $type The type of the stream.
 */
function hewa_player_print_source_tag( $source, $type ) {

    $source_e = esc_attr( $source );
    $type_e   = esc_attr( $type );
    echo "<source src='$source_e' type='$type_e'>";
}