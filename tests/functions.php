<?php

function hewa_configure_wordpress_test() {

    do_action('activate_helixware/helixware.php');

    // Set the plugin options.
    hewa_set_option( HEWA_SETTINGS_SERVER_URL, getenv('HEWA_SERVER_URL') );
    hewa_set_option( HEWA_SETTINGS_APPLICATION_KEY, getenv('HEWA_APPLICATION_KEY') );
    hewa_set_option( HEWA_SETTINGS_APPLICATION_SECRET, getenv('HEWA_APPLICATION_SECRET') );
}