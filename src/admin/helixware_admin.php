<?php
/**
 * General functions bound to the admin screen.
 */


/**
 * Load scripts for the admin screen.
 */
function hewa_admin_scripts() {

    // Get the configuration options.
    $options = array(
        'url'    => hewa_get_option( HEWA_SETTINGS_SERVER_URL ) . '/4/users/assets',
        'key'    => hewa_get_option( HEWA_SETTINGS_APPLICATION_KEY ),
        'secret' => hewa_get_option( HEWA_SETTINGS_APPLICATION_SECRET )
    );

    // Enqueue the admin script.
    wp_enqueue_script( 'hewa_admin_js', plugins_url( 'js/helixware.admin.js', __FILE__ ) );

    // Set the options.
    wp_localize_script( 'hewa_admin_js', 'hewa_admin_options', $options );

}
add_action( 'admin_enqueue_scripts', 'hewa_admin_scripts' );
