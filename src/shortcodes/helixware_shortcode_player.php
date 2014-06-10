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

    return 'I am the player';
}
add_shortcode( HELIXWARE_SHORTCODE_PREFIX . 'player', 'hewa_shortcode_player' );