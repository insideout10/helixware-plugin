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

    // TODO: code this method.
    $path = '...';

    $urls = hewa_get_clip_urls( $path );

    return 'code the HTML5 player';
}
add_shortcode( HELIXWARE_SHORTCODE_PREFIX . 'player', 'hewa_shortcode_player' );