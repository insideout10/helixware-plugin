<?php
/**
 * This file provides the AJAX call to retrieve quota information using the configured Application Key and Secret.
 */

/**
 * Provide the quota information about the currently configured *Application*.
 */
function hewa_ajax_quota() {

    echo hewa_server_call( '/me' );

    wp_die();

}
add_action( 'wp_ajax_hewa_quota', 'hewa_ajax_quota' );