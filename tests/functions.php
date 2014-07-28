<?php

function hewa_configure_wordpress_test() {

    do_action('activate_helixware/helixware.php');

    // Set the plugin options.
    update_option( HEWA_SETTINGS, array(
        HEWA_SETTINGS_SERVER_URL   => getenv('HEWA_SERVER_URL'),
        HEWA_SETTINGS_APPLICATION_KEY      => getenv('HEWA_APPLICATION_KEY'),
        HEWA_SETTINGS_APPLICATION_SECRET   => getenv('HEWA_APPLICATION_SECRET')
    ) );
}