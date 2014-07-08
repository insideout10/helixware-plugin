<?php
/**
 * This file provides functions to call and interact with the remote HelixWare Server.
 */

/**
 * Call the remote HelixServer API, signing the request with the Application Key and Secret configured in WordPress.
 *
 * @uses hewa_get_option to get the configuration settings.
 *
 * @param string $endpoint The endpoint (will be appended to the server URL, must start with a trailing slash).
 * @return string The response body.
 */
function hewa_server_call( $endpoint ) {

    // Get the configuration settings and die if not set.
    $server_url = hewa_get_option( HEWA_SETTINGS_SERVER_URL, false );
    $app_key    = hewa_get_option( HEWA_SETTINGS_APPLICATION_KEY, false );
    $app_secret = hewa_get_option( HEWA_SETTINGS_APPLICATION_SECRET, false );

    if ( false === $server_url || false === $app_key || false === $app_secret ) {
        wp_die( __( 'The plugin is not configured.', HEWA_LANGUAGE_DOMAIN ) );
    }

    // Set the full URL.
    $url        = $server_url . $endpoint;

    // Prepare the default arguments.
    $args = array_merge_recursive( unserialize( HEWA_API_HTTP_OPTIONS ), array(
        'method'  => 'GET',
        'headers' => array(
            'Content-Type'         => 'application/json; charset=UTF-8',
            'Accept'               => 'application/json; charset=UTF-8',
            'X-Application-Key'    => $app_key,
            'X-Application-Secret' => $app_secret
        )
    ) );

    // Perform the request.
    $response = wp_remote_request( $url, $args );

    // If an error occurs, print the error and exit.
    if ( is_wp_error( $response ) || 200 !== $response['response']['code'] ) {
        wp_die( __( 'An error occurred while calling the remote server (' . $response->get_error_message() . ')', HEWA_LANGUAGE_DOMAIN ) );
    }

    // Return the response as a string.
    return $response['body'];

}