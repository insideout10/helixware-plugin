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
define( 'HEWA_SETTINGS_TEMPLATE_ID', 'hewa_template_id' );
define( 'HEWA_SETTINGS_JWPLAYER_ID', 'hewa_jwplayer_key' );
define( 'HEWA_SETTINGS_JWPLAYER_LOGO_URL', 'hewa_jwplayer_logo_url' );
define( 'HEWA_SETTINGS_JWPLAYER_LOGO_LINK', 'hewa_jwplayer_logo_link' );
define( 'HEWA_SETTINGS_JWPLAYER_DEFAULT_SKIN', 'hewa_jwplayer_default_skin' );


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