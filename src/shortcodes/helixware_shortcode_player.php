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
        'autostart'    => true
    ), $atts);

    // Queue the scripts.
    wp_enqueue_script( 'jwplayer', plugins_url('js/jwplayer-6.9/jwplayer.js', __FILE__ ) );

    // Get the player key.
    $jwplayer_key = hewa_get_option( HEWA_SETTINGS_JWPLAYER_ID, '' );

    // Get the asset Id.
    $id       = uniqid( 'hewa-player-');
    $asset_id = $params['asset_id'];
    $title_u  = urlencode( get_the_title() );

    // Get the thumbnail URL.
    // TODO: get the thumbnail of the right size.
    $attachment_url = wp_get_attachment_url( get_post_thumbnail_id() );
    $image_u  = urlencode( $attachment_url );

    // Build the player array which will then be translated to JavaScript for JWPlayer initialization.
    $player                = array();
    $player['androidhls']  = true;
    $player['autostart']   = ( $params['autostart'] ? 'true' : 'false' );
    $player['playlist']    = admin_url( 'admin-ajax.php?action=hewa_rss&id=' . $asset_id .
        '&t=' . $title_u . // set the title
        '&i=' . $image_u . // set the image
        ( null !== $params['listbar'] ? '&cat=' . $params['listbar_cat'] : '' ) ); // add the category if we have the listbar.
    $player['width']       = $params['width'];
    $player['aspectratio'] = $params['aspectratio'];

    // Build the playlist object.
    if ( null !== $params['listbar'] ) {
        $player['listbar'] = array(
            'position' => $params['listbar'],
            'size'     => $params['listbar_size']
        );
    }

    // Create the JSON version of the player.
    $player_json = json_encode( $player );

    $loading  = esc_html__( 'Loading player...', HEWA_LANGUAGE_DOMAIN );

    $result = <<<EOF
        <div id="$id">$loading</div>
        <script type="text/javascript">
            jQuery( function( $ ) {
                jwplayer.key = '$jwplayer_key';
                jwplayer('$id').setup($player_json);
            } );
        </script>
EOF;

    return $result;

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

///**
// * Print a *source* tag with the provided *src* and *type* attributes.
// *
// * @param string $source The URL source of the stream.
// * @param string $type   The type of the stream.
// * @param string $width  The width of the encoded stream.
// * @return string The html fragment.
// */
//function hewa_player_print_source_tag( $source, $type, $width ) {
//
//    // Escape del params.
//    $source_e = esc_attr( $source );
//    $type_e   = esc_attr( $type );
//    $res_e    = esc_attr( $width );
//
//    return "<source src='$source_e' type='$type_e' data-res='$res_e'>";
//
//}