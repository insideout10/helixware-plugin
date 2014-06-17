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
 * @param string $path The clip path.
 *
 * @return A structure with the clip data.
 */
function hewa_get_clip_urls( $path ) {

    // create a sample JSON.
    $json = <<<EOF
{
    "flash-direct": {
        "caption": "Direct RTMP URL for Flash players.",
        "file": "cdn/A1TAAdmin/VendorAdm/tests/test-signal-3.mp4",
        "protocol": "rtmp",
        "streamer": "rtmp://streamer.a1.net/",
        "url": "rtmp://streamer.a1.net/cdn/A1TAAdmin/VendorAdm/tests/test-signal-3.mp4"
    },
    "flash-redirector": {
        "caption": "Redirector Flash/SMIL URL for Flash players.",
        "file": "flashgen/cdn/A1TAAdmin/VendorAdm/tests/test-signal-3.mp4",
        "protocol": "http",
        "streamer": "http://streamer.a1.net/",
        "url": "http://streamer.a1.net/flashgen/cdn/A1TAAdmin/VendorAdm/tests/test-signal-3.mp4"
    },
    "m3u8-redirector": {
        "caption": "Redirector M3U8 URL for HLS players (iOS).",
        "file": "m3ugen/iPhone-src/cdn/A1TAAdmin/VendorAdm/tests/test-signal-3.mp4",
        "protocol": "http",
        "streamer": "http://streamer.a1.net/",
        "url": "http://streamer.a1.net/m3ugen/iPhone-src/cdn/A1TAAdmin/VendorAdm/tests/test-signal-3.mp4"
    },
    "rtsp-direct": {
        "caption": "Direct RTSP URL.",
        "file": "cdn/A1TAAdmin/VendorAdm/tests/test-signal-3.mp4",
        "protocol": "rtsp",
        "streamer": "rtsp://streamer.a1.net/",
        "url": "rtsp://streamer.a1.net/cdn/A1TAAdmin/VendorAdm/tests/test-signal-3.mp4"
    },
    "sdp-redirector": {
        "caption": "Redirector URL using SDP protocol.",
        "file": "sdpgen/cdn/A1TAAdmin/VendorAdm/tests/test-signal-3.mp4",
        "protocol": "http",
        "streamer": "http://streamer.a1.net/",
        "url": "http://streamer.a1.net/sdpgen/cdn/A1TAAdmin/VendorAdm/tests/test-signal-3.mp4"
    }
}
EOF;

    // Return an object instance.
    return json_decode( $json );
}

/**
 * Get the value for a setting or return a default value.
 *
 * @param string $name The setting name.
 * @param mixed $default The default value if the setting is not found (null if not provided).
 * @return mixed The setting value, or the default value.
 */
function hewa_get_option( $name, $default = null ) {

    $settings = (array) get_option( HEWA_SETTINGS );
    return ( isset( $settings[$name] ) ? esc_attr( $settings[$name] ) : $default );

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