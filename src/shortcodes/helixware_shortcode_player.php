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
 * @return string An HTML code fragment.
 */
function hewa_shortcode_player( $atts ) {

    // Extract attributes and set default values
    // TODO: the default path might point to a custom video that invites the user to select a video.
    // TODO: default width and height ratio should be calculated from the video.
    $params = shortcode_atts( array(
        'width'        => '100%', // by default we stretch the full width of the containing element.
        'asset_id'     => 5,
        'aspectratio'  => '5:3',
        'listbar'      => null,
        'listbar_size' => 240,
        'listbar_cat'  => 'for-you',
        'autostart'    => true,
        'max'          => 5,
        'skin'         => hewa_get_option( HEWA_SETTINGS_JWPLAYER_DEFAULT_SKIN, '' ),
        'logo_url'     => hewa_get_option( HEWA_SETTINGS_JWPLAYER_LOGO_URL, '' ),
        'logo_link'    => hewa_get_option( HEWA_SETTINGS_JWPLAYER_LOGO_LINK, '' ),
        'ga_idstring'  => 'title'
    ), $atts);

    // Queue the scripts.
    wp_enqueue_script( 'jwplayer', plugins_url('js/jwplayer-6.9/jwplayer.js', __FILE__ ) );

    // Get the player key.
    $jwplayer_key = hewa_get_option( HEWA_SETTINGS_JWPLAYER_ID, '' );

    // Get the asset Id.
    $player_id = uniqid( 'hewa-player-');
    $asset_id  = $params['asset_id'];
    $title_u   = urlencode( get_the_title() );

    // Get the thumbnail URL.
    // TODO: get the thumbnail of the right size.
    $attachment_url = wp_get_attachment_url( get_post_thumbnail_id() );
    $image_u  = urlencode( $attachment_url );

    // Build the player array which will then be translated to JavaScript for JWPlayer initialization.
    $player                = array();
    $player['androidhls']  = true;
    $player['autostart']   = ( $params['autostart'] ? 'true' : 'false' );
    $player['playlist']    = apply_filters(
        HEWA_FILTERS_PLAYER_PLAYLIST_URL,
        admin_url( 'admin-ajax.php?action=hewa_rss&id=' . $asset_id .
            '&t=' . $title_u . // set the title
            '&i=' . $image_u . // set the image
            '&max=' . $params['max'] . // set the maximum number of elements
            ( null !== $params['listbar'] ? '&cat=' . $params['listbar_cat'] : '' ) // add the category if we have the listbar.
        )
    );
    $player['width']       = $params['width'];
    $player['aspectratio'] = $params['aspectratio'];

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
    $loading  = esc_html__( 'Loading player...', HEWA_LANGUAGE_DOMAIN );

    // Prepare an empty result variable.
    $result  = '';

    // Build the *responsive* listbar.
    if ( null !== $params['listbar'] && 'responsive' === $params['listbar'] ) {
        wp_enqueue_style( 'helixware-player-css', plugins_url( 'css/helixware.player.css', dirname( __FILE__ ) ) );

        $listbar_id = uniqid( 'hewa-listbar-');;
        $result     = '<div class="hewa-container">' .
            "<div class=\"hewa-player-container\"><div id=\"$player_id\">$loading</div></div>" .
            "<div class=\"hewa-listbar-container\"><ul id=\"$listbar_id\" class=\"hewa-listbar\"></ul></div>" .
            '</div>';

    }

    // Build a standard listbar.
    if ( null !== $params['listbar'] && 'responsive' !== $params['listbar'] ) {
        $player['listbar'] = array(
            'position' => $params['listbar'],
            'size'     => $params['listbar_size']
        );

        $result .= "<div id=\"$player_id\">$loading</div>";
    }

    // Set the GA setting.
    $player['ga'] = array( 'idstring' => $params['ga_idstring'] );

    // Create the JSON version of the player.
    $player_json = json_encode( $player );

    // Start printing out the player javascript.
    $result .= <<<EOF
        <script type="text/javascript">
            jQuery( function( $ ) {
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

                        $('#$listbar_id').html( html );

                    }

                    $('#$listbar_id').css('height', player.getHeight() + 'px');

                })
                .onResize(function (event) {

                    $('#$listbar_id').css('height', event.height + 'px');

                });

EOF;
    }

        // Close the script and return the results.
        return $result . '});</script>';

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
