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
        'width'    => '100%', // by default we stretch the full width of the containing element.
        'asset_id' => 5
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
    
    // Videojs persist volume
    wp_enqueue_script(
        'videojs-persistvolume',
        plugins_url('bower_components/videojs-persistvolume/videojs.persistvolume.js', __FILE__ ) );

    // Our js
    wp_enqueue_script( 'helixwarejs', plugins_url( 'js/helixware.js', __FILE__ ) );
    wp_localize_script( 'helixwarejs', 'videojs_params', array(
            'class'  => 'hewa-player',
            'swfurl' => plugins_url( 'bower_components/video.js-dist/dist/video-js/video-js.swf', __FILE__ )
        )
    );

    // Retrieving sources
    $streams    = hewa_get_clip_urls( $params['asset_id'] );

    // TODO: the above call might return an error, handle it here and display a friendly message.

    // Establish video ratio, to be used client-side* to determine height
    // *necessary because if width is a percentage, we can't know width in pixels here.
    if( ! isset( $streams->ratio ) || is_null( $streams->ratio ) || ! is_numeric( $streams->ratio ) )
        $streams->ratio = 1.77;

    // Setting width and height
    $id      = esc_attr( 'hewa_player_' . get_the_id() );
	$width_e = esc_attr( $params['width'] );
    $ratio_e = esc_attr( $streams->ratio );
	
	// Return HTML template
    echo <<<EOF
        <video id='$id' class='video-js vjs-default-skin hewa-player' controls preload='auto'
            width="$width_e" data-ratio="$ratio_e">
EOF;

    // Print the streaming sources, we only need to:
    //  * HLS streaming (m3u8) for Safari, iOS, Android
    //  * Flash for all the others (Chrome, Internet Explorer, Safari)
    foreach( $streams->formats as $key => $format ) {
        switch ( $key ) {

            // HLS streaming.
            case 'm3u8-redirector':
                foreach( $format->bitrates as $version ) {
                    hewa_player_print_source_tag(
                        $version->url,
                        // The following line is for streaming servers that do not provide a cross domain xml.
                        // admin_url('admin-ajax.php') . '?action=hewa_m3u8&file=' . urlencode( $version->file ),
                        'application/x-mpegURL',
                        $version->width
                    );
                }
                break;

            // Flash streaming.
            case 'flash-direct':
                foreach( $format->bitrates as $version ) {
                    hewa_player_print_source_tag( $version->url, 'rtmp/mp4', $version->width );
                }
                break;

        }
    }

    echo <<<EOF
	        <p class="vjs-no-js">To view this video consider upgrading to a web browser that <a
	            href="http://videojs.com/html5-video-support/" target="_blank">supports HTML5 video</a></p>
        </video>
EOF;
}
add_shortcode( HEWA_SHORTCODE_PREFIX . 'player', 'hewa_shortcode_player' );

/**
 * Print a *source* tag with the provided *src* and *type* attributes.
 *
 * @param string $source The URL source of the stream.
 * @param string $type   The type of the stream.
 * @param string $width  The width of the encoded stream.
 */
function hewa_player_print_source_tag( $source, $type, $width ) {

    // Escape del params.
    $source_e = esc_attr( $source );
    $type_e   = esc_attr( $type );
    $res_e    = esc_attr( $width );

    echo "<source src='$source_e' type='$type_e' data-res='$res_e'>";

}