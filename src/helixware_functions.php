<?php
/**
 * This file contains general functions.
 */
 
/**
 * Change *plugins_url* response to return the correct path of WordLift files when working in development mode.
 * @param $url The URL as set by the plugins_url method.
 * @param $path The request path.
 * @param $plugin The plugin folder.
 * @return string The URL.
 */
function hewa_plugins_url($url, $path, $plugin)
{
    hewa_write_log("hewa_plugins_url [ url :: $url ][ path :: $path ][ plugin :: $plugin ]");

    // Check if it's our pages calling the plugins_url.
    if (1 !== preg_match('/\/helixware[^.]*.php$/i', $plugin)) {
        return $url;
    };

    // Set the URL to plugins URL + helixware, in order to support the plugin being symbolic linked.
    $plugin_url = plugins_url() . '/helixware/' . $path;

    hewa_write_log("hewa_plugins_url [ match :: yes ][ plugin url :: $plugin_url ][ url :: $url ][ path :: $path ][ plugin :: $plugin ]");

    return $plugin_url;
}
add_filter('plugins_url', 'hewa_plugins_url', 10, 3);

/**
 * Retrieve the URLs for a clip with the specified path.
 *
 * @param integer $asset_id The asset Id.
 *
 * @return A structure with the clip data.
 */
function hewa_get_clip_urls( $asset_id ) {

    return json_decode( hewa_server_call( '/4/users/streams/' . $asset_id ) );

}

/**
 * Get the value for a setting or return a default value.
 *
 * @param string $name The setting name.
 * @param mixed $default The default value if the setting is not found (null if not provided).
 * @return mixed The setting value, or the default value.
 */
function hewa_get_option( $name, $default = null ) {

    // Set the default supported extensions.
    if ( HEWA_SETTINGS_FILE_EXTENSIONS === $name ) {
        return 'mp4,mpg,mpeg,mov,avi,wmv,mp3,aac';
    }

    $settings = (array) get_option( HEWA_SETTINGS );
    return ( isset( $settings[$name] ) ? esc_attr( $settings[$name] ) : $default );

}

/**
 * Set the option with the specified name, to the specified value.
 *
 * @param string $name  The option name.
 * @param string $value The option value.
 * @return bool The result from the *add_option/update_option* call.
 */
function hewa_set_option( $name, $value ) {

    // If no settings are saved yet, create them.
    if ( false === ( $settings = get_option( HEWA_SETTINGS ) ) ) {
        return add_option( HEWA_SETTINGS, array( $name => $value ) );
    }

    // Else update the settings.
    $settings[$name] = $value;
    return update_option( HEWA_SETTINGS, $settings );

}

/**
 * Format the specified value in bytes.
 *
 * @param int $size The size in bytes.
 * @param int $precision The number of decimals.
 * @return string The formatted string.
 */
function hewa_format_bytes( $size, $precision = 2) {

    $base = log( $size ) / log( 1024 );
    $suffixes = array( '', 'kb', 'Mb', 'Gb', 'Tb' );

    return round( pow( 1024, $base - floor( $base ) ), $precision ) . $suffixes[floor( $base )];

}