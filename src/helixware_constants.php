<?php
/**
 * This file contains HelixWare constants.
 */

// Define the prefix for shortcodes.
define( 'HEWA_SHORTCODE_PREFIX', 'hewa_' );

// Define the language domain for the plugin.
define( 'HEWA_LANGUAGE_DOMAIN', 'helixware' );

// Define the settings name for get_option/set_option calls.
define( 'HEWA_SETTINGS', 'helixware_settings' );

// Define the configuration settings names.
define( 'HEWA_SETTINGS_SERVER_URL', 'hewa_server_url' );
define( 'HEWA_SETTINGS_APPLICATION_KEY', 'hewa_app_key' );
define( 'HEWA_SETTINGS_APPLICATION_SECRET', 'hewa_app_secret' );
define( 'HEWA_SETTINGS_FILE_EXTENSIONS', 'hewa_file_extensions' );

// Define the clip custom post name.
define( 'HEWA_POST_TYPE_CLIP', 'hewa_clip' );

define( 'HEWA_API_HTTP_OPTIONS', serialize( array(
    'timeout' => 60,
    'redirection' => 5,
    'httpversion' => '1.0',
    'blocking' => true,
    'cookies' => array(),
    'sslverify' => false
) ) );

// The max file size for HelixWare-related uploads. This information is used with the uploader as well as for display
// to the user.
define( 'HEWA_SETTINGS_MAX_FILE_SIZE', '5Gb' );

// TODO: move these options to the plugin configuration.
// Define the options for the still image.
define( 'HEWA_STILL_IMAGE_WIDTH', 1200 );
define( 'HEWA_STILL_IMAGE_TIMECODE_SECONDS', 15 );
define( 'HEWA_STILL_IMAGE_CONTENT_TYPE', 'image/png' );