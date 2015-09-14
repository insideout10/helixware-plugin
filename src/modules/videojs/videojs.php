<?php

function hewa_videojs_shortcode( $atts ) {

	$params = shortcode_atts( array(
		'asset_id' => null,
	), $atts );

	wp_enqueue_style( 'videojs-css', plugins_url( 'helixware/modules/videojs/video-js.min.css' ) );
	wp_enqueue_style( 'videojs-resolutions-css', plugins_url( 'helixware/modules/videojs/video-js-resolutions.css' ) );
	wp_enqueue_script( 'videojs-js', plugins_url( 'helixware/modules/videojs/video.js' ) );
	wp_enqueue_script( 'videojs-resolutions-js', plugins_url( 'helixware/modules/videojs/video-js-resolutions.js' ) );

	$streams = hewa_get_clip_urls( $params['asset_id'] );
	$poster  = hewa_get_server_url() . '/4/pub/asset/' . $params['asset_id'] . '/image?w=640&tc=00:00:03';

//	var_dump( $streams );
//	wp_die();
	$m3u8          = $streams->formats->{'application/x-mpegurl'};
	$ratio         = $streams->ratio;
	$player_width  = 640;
	$player_height = $player_width / $ratio;

	$bitrates = $m3u8->bitrates;
	usort( $bitrates, function ( $a, $b ) {
		return $a->bitrate - $b->bitrate;
	} );

	$content = '<video id="vid1" class="video-js vjs-default-skin" controls preload="auto" width="' . $player_width . '" height="' . $player_height . '" poster="' . $poster . '" data-setup="{}">';

	for ( $i = 0; $i < sizeof( $bitrates ); $i ++ ) {

		$bitrate  = $bitrates[ $i ];
		$height_p = $bitrates[ $i ]->height . 'p';
		$url      = $bitrate->url;

//		var_dump( $bitrate );
		$content .= "<source src='$url' type='video/mp4' data-res='$height_p' />";

	}

	$content .= '<p>Video Playback Not Supported</p>';
	$content .= '</video>';
	$content .= <<<EOF
<script type="text/javascript">

	jQuery( function($) {
		videojs('vid1', {
			plugins: {
				resolutions: {}
			}
		});
    });
</script>

EOF;


	return $content;
}

add_shortcode( 'hewa_videojs', 'hewa_videojs_shortcode' );
