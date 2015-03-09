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
 *
 * @return string An HTML code fragment.
 */
function hewa_shortcode_player( $atts ) {

	// Extract attributes and set default values
	// TODO: the default path might point to a custom video that invites the user to select a video.
	// TODO: default width and height ratio should be calculated from the video.
	$params = shortcode_atts( array(
		'width'        => '100%', // by default we stretch the full width of the containing element.
		'asset_id'     => null,
		'live_id'      => null,
		'aspectratio'  => '5:3',
		'listbar'      => null,
		'listbar_size' => 240,
		'listbar_cat'  => 'for-you',
		'autostart'    => true,
		'max'          => 5,
		'skin'         => hewa_get_option( HEWA_SETTINGS_JWPLAYER_DEFAULT_SKIN, '' ),
		'logo_url'     => hewa_get_option( HEWA_SETTINGS_JWPLAYER_LOGO_URL, '' ),
		'logo_link'    => hewa_get_option( HEWA_SETTINGS_JWPLAYER_LOGO_LINK, '' )
	), $atts );

	// Queue the scripts.
	// wp_enqueue_script( 'jwplayer', plugins_url( 'js/jwplayer-6.11/jwplayer.js', __FILE__ ) );

	// Get the player key.
	$jwplayer_key = hewa_get_option( HEWA_SETTINGS_JWPLAYER_ID, '' );

	// Get the asset Id.
	$player_id = uniqid( 'hewa-player-' );
	$is_live   = ! empty( $params['live_id'] );
	$asset_id  = ( $is_live ? $params['live_id'] : $params['asset_id'] );
	$title_u   = urlencode( get_the_title() );

	// Get the thumbnail URL.
	// TODO: get the thumbnail of the right size.
	$attachment_url = wp_get_attachment_url( get_post_thumbnail_id() );
	$image_u        = urlencode( $attachment_url );

	// Build the player array which will then be translated to JavaScript for JWPlayer initialization.
	$player                = array();
	$player['flashplayer'] = plugins_url( 'js/jwplayer-6.11/jwplayer.flash.swf', __FILE__ );
	$player['html5player'] = plugins_url( 'js/jwplayer-6.11/jwplayer.html5.js', __FILE__ );
	$player['androidhls']  = true;
	$player['autostart']   = ( $params['autostart'] && is_singular() ? 'true' : 'false' );
	$player['playlist']    = ( $is_live || null === $params['listbar']
		? hewa_get_option( HEWA_SETTINGS_SERVER_URL, false ) . "/4/pub/asset/$asset_id/streams.xml"
		: apply_filters( HEWA_FILTERS_PLAYER_PLAYLIST_URL,
			admin_url( 'admin-ajax.php?action=hewa_rss&id=' . $asset_id .
			           '&t=' . $title_u . // set the title
			           '&i=' . $image_u . // set the image
			           '&max=' . $params['max'] . // set the maximum number of elements
			           ( null !== $params['listbar'] ? '&cat=' . $params['listbar_cat'] : '' ) // add the category if we have the listbar.
			)
		) );
	$player['width']       = $params['width'];
	$player['aspectratio'] = $params['aspectratio'];
	$player['ga']          = array(
		// playlist title or mediaid
		'idstring'    => ( null != $params['ga_id_string'] ? $params['ga_id_string'] : 'mediaid' ),
		'universalga' => ( null != $params['ga_tracking_object'] ? $params['ga_tracking_object'] : '__gaTracker' ),
		// mediaid or title
		'label'       => ( null != $params['ga_media_id'] ? $params['ga_media_id'] : 'title' )
	);

	// Add the logo and the link if provided.
	if ( ! empty( $params['logo_url'] ) ) {

		$player['logo'] = array( 'file' => $params['logo_url'] );

		if ( ! empty( $params['logo_link'] ) ) {
			$player['logo']['link'] = $params['logo_link'];
		}

	}

	// Add the skin if specified.
	if ( ! empty( $params['skin'] ) ) {
		$player['skin'] = $params['skin'];
	}

	// The loading string.
	$loading = esc_html__( 'Loading player...', HEWA_LANGUAGE_DOMAIN );

	// Build the *responsive* listbar.
	if ( null !== $params['listbar'] && 'responsive' === $params['listbar'] ) {
		wp_enqueue_style( 'helixware-player-css', plugins_url( 'css/helixware.player.css', dirname( __FILE__ ) ) );

		$listbar_id = uniqid();
		// Print the responsive listbar player DIV.
		$result = "<div class=\"hewa-container\" id=\"hewa-container-$listbar_id\">" .
		          "<div class=\"hewa-player-container\" id=\"hewa-player-container-$listbar_id\"><div id=\"$player_id\">$loading</div></div>" .
		          "<div class=\"hewa-listbar-container\"><ul id=\"hewa-listbar-$listbar_id\" class=\"hewa-listbar\"></ul></div>" .
		          '</div>';

	} else {

		$result = "<div id='$player_id' " . apply_filters( 'hewa_player_start_element', $asset_id ) . '>' .
		          apply_filters( 'hewa_player_in_element', $asset_id ) .
		          $loading . '</div>';

	}

	// Build a standard listbar.
	if ( null !== $params['listbar'] && 'responsive' !== $params['listbar'] ) {

		$player['listbar'] = array(
			'position' => $params['listbar'],
			'size'     => $params['listbar_size']
		);

	}

	// Create the JSON version of the player.
	$player_json = json_encode( $player, JSON_PRETTY_PRINT );

	// Start printing out the player javascript.
	$jwplayer_url = plugins_url( 'js/jwplayer-6.11/jwplayer.js', __FILE__ );
	$result .= <<<EOF
        <script type="text/javascript">
            jQuery( function( $ ) {
				$.getScript('$jwplayer_url', function() {

                jwplayer.key = '$jwplayer_key';
                jwplayer('$player_id')
                    .setup($player_json);

EOF;

	// If the listbar Id is set, then print-out related events.
	if ( isset( $listbar_id ) ) {

		$result .= <<<EOF
            jwplayer('$player_id')
                .onReady( function () {
                    var html     = '';
                    var player   = jwplayer('$player_id');
                    var playlist = player.getPlaylist();

                    for (var i = 0; i < playlist.length; i++) {

                        html += '<li><a href="javascript:jwplayer(\'$player_id\').playlistItem(' + i + ');">';

                        if ( undefined != playlist[i].image ) {
                            html += '<img height="75" width="120" src="' + playlist[i].image + '" />';
                        }

                        html += '<div class="hewa-listbar-title">' + playlist[i].title + '</div></a>';

                        if ( undefined != playlist[i].description ) {
                            html += '<div class="hewa-listbar-description">' + description + '</div>';
                        }

                        html += '</li>';

                        $('#hewa-listbar-$listbar_id').html( html );

                    }


                    setTimeout( function() {

                        // Give the player some time to load.
                        if ( 600 < $(document).width() ) {

                            $( '#hewa-listbar-$listbar_id' ).height( 0 < $('#${player_id}_wrapper').length
                                ? $('#${player_id}_wrapper').height()
                                : $('#${player_id}').height()
                            );

                        } else {
                            $( '#hewa-listbar-$listbar_id' ).height( 88 );
                        }
                    }, 2000);

                })
                .onResize(function (event) {

                    if ( 600 < $(document).width() )
                        $( '#hewa-listbar-$listbar_id' ).height( event.height );
                    else
                        $( '#hewa-listbar-$listbar_id' ).height( 88 );

                });

EOF;
	}

	// Close the script and return the results.
	return $result . '}); });</script>';

}

add_shortcode( HEWA_SHORTCODE_PREFIX . 'player', 'hewa_shortcode_player' );


/**
 * Send the shortcode output as echo. This appears to be required if we're in a slider.
 *
 * @param array $atts The shortcode attributes.
 */
function hewa_shortcode_player_echo( $atts ) {

	echo hewa_shortcode_player( $atts );

}

add_shortcode( HEWA_SHORTCODE_PREFIX . 'player_echo', 'hewa_shortcode_player_echo' );
