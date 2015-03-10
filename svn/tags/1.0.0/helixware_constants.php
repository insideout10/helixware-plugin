<?php
/**
 * This file contains HelixWare constants.
 */

// Define the prefix for shortcodes.
define( 'HEWA_SHORTCODE_PREFIX', 'hewa_' );

// Define the language domain for the plugin.
define( 'HEWA_LANGUAGE_DOMAIN', 'helixware' );

// Define the setting screen tabs.
define( 'HEWA_OPTIONS_PAGE', 'hewa_options' );
define( 'HEWA_OPTIONS_SETTINGS_SERVER', 'hewa_options_settings_server' );
define( 'HEWA_OPTIONS_SETTINGS_PLAYER', 'hewa_options_settings_player' );
define( 'HEWA_OPTIONS_SETTINGS_LIVE', 'hewa_options_settings_live' );


// Define the settings name for get_option/set_option calls.
// define( 'HEWA_SETTINGS', 'helixware_settings' );

// Define the configuration settings names.
define( 'HEWA_SETTINGS_STREAMING_SERVER', HEWA_OPTIONS_SETTINGS_SERVER . '>hewa_streaming_server' );
define( 'HEWA_SETTINGS_STREAMING_PROTOCOL', HEWA_OPTIONS_SETTINGS_SERVER . '>hewa_streaming_protocol' );
define( 'HEWA_SETTINGS_SERVER_URL', HEWA_OPTIONS_SETTINGS_SERVER . '>hewa_server_url' );
define( 'HEWA_SETTINGS_APPLICATION_KEY', HEWA_OPTIONS_SETTINGS_SERVER . '>hewa_app_key' );
define( 'HEWA_SETTINGS_APPLICATION_SECRET', HEWA_OPTIONS_SETTINGS_SERVER . '>hewa_app_secret' );
define( 'HEWA_SETTINGS_TEMPLATE_ID', HEWA_OPTIONS_SETTINGS_PLAYER . '>hewa_template_id' );
define( 'HEWA_SETTINGS_JWPLAYER_ID', HEWA_OPTIONS_SETTINGS_PLAYER . '>hewa_jwplayer_key' );
define( 'HEWA_SETTINGS_JWPLAYER_LOGO_URL', HEWA_OPTIONS_SETTINGS_PLAYER . '>hewa_jwplayer_logo_url' );
define( 'HEWA_SETTINGS_JWPLAYER_LOGO_LINK', HEWA_OPTIONS_SETTINGS_PLAYER . '>hewa_jwplayer_logo_link' );
define( 'HEWA_SETTINGS_JWPLAYER_DEFAULT_SKIN', HEWA_OPTIONS_SETTINGS_PLAYER . '>hewa_jwplayer_default_skin' );

// The value is handled in the code in *hewa_get_option*.
define( 'HEWA_SETTINGS_FILE_EXTENSIONS', 'hewa_file_extensions' );


// Define the clip custom post name.
define( 'HEWA_POST_TYPE_CLIP', 'hewa_clip' );

define( 'HEWA_API_HTTP_OPTIONS', serialize( array(
    'timeout' => 300,
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

// This filter is used to get the post content for new posts.
define( 'HEWA_FILTERS_CREATE_POST_CONTENT', 'hewa_filters_create_post_content' );

// This filter can be used to append parameters to the playlist URL.
define( 'HEWA_FILTERS_PLAYER_PLAYLIST_URL', 'hewa_filters_player_playlist_url' );