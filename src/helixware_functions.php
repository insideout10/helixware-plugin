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

    $clip_data = hewa_server_call( '/4/users/streams/' . $asset_id );
    return json_decode( $clip_data );

    // SAMPLE JSON:
    // {
    //    "title":"test-signal.mp4",
    //    "duration":10.01,
    //    "ratio":1.3333333333333333,
    //    "formats":{
    //       "sdp-redirector":{
    //          "caption":"Redirector URL using SDP protocol.",
    //          "protocol":"http",
    //          "streamer":"http://totalerg.insideout.io/",
    //          "bitrates":[
    //             {
    //                "bitrate":1294336.0,
    //                "width":640,
    //                "file":"sdpgen/helixware/tests/test/2-test-signal.mp4",
    //                "url":"http://totalerg.insideout.io/sdpgen/helixware/tests/test/2-test-signal.mp4"
    //             },
    //             {
    //                "bitrate":2625536.0,
    //                "width":960,
    //                "file":"sdpgen/helixware/tests/test/3-test-signal.mp4",
    //                "url":"http://totalerg.insideout.io/sdpgen/helixware/tests/test/3-test-signal.mp4"
    //             },
    //             {
    //                "bitrate":475136.0,
    //                "width":480,
    //                "file":"sdpgen/helixware/tests/test/1-test-signal.mp4",
    //                "url":"http://totalerg.insideout.io/sdpgen/helixware/tests/test/1-test-signal.mp4"
    //             }
    //          ]
    //       },
    //       "m3u8-redirector":{
    //          "caption":"Redirector M3U8 URL for HLS players (iOS).",
    //          "protocol":"http",
    //          "streamer":"http://totalerg.insideout.io/",
    //          "bitrates":[
    //             {
    //                "bitrate":1294336.0,
    //                "width":640,
    //                "file":"m3ugen/helixware/tests/test/2-test-signal.mp4",
    //                "url":"http://totalerg.insideout.io/m3ugen/helixware/tests/test/2-test-signal.mp4"
    //             },
    //             {
    //                "bitrate":2625536.0,
    //                "width":960,
    //                "file":"m3ugen/helixware/tests/test/3-test-signal.mp4",
    //                "url":"http://totalerg.insideout.io/m3ugen/helixware/tests/test/3-test-signal.mp4"
    //             },
    //             {
    //                "bitrate":475136.0,
    //                "width":480,
    //                "file":"m3ugen/helixware/tests/test/1-test-signal.mp4",
    //                "url":"http://totalerg.insideout.io/m3ugen/helixware/tests/test/1-test-signal.mp4"
    //             }
    //          ]
    //       },
    //       "rtsp-direct":{
    //          "caption":"Direct RTSP URL.",
    //          "protocol":"rtsp",
    //          "streamer":"rtsp://totalerg.insideout.io/",
    //          "bitrates":[
    //             {
    //                "bitrate":1294336.0,
    //                "width":640,
    //                "file":"helixware/tests/test/2-test-signal.mp4",
    //                "url":"rtsp://totalerg.insideout.io/helixware/tests/test/2-test-signal.mp4"
    //             },
    //             {
    //                "bitrate":2625536.0,
    //                "width":960,
    //                "file":"helixware/tests/test/3-test-signal.mp4",
    //                "url":"rtsp://totalerg.insideout.io/helixware/tests/test/3-test-signal.mp4"
    //             },
    //             {
    //                "bitrate":475136.0,
    //                "width":480,
    //                "file":"helixware/tests/test/1-test-signal.mp4",
    //                "url":"rtsp://totalerg.insideout.io/helixware/tests/test/1-test-signal.mp4"
    //             }
    //          ]
    //       },
    //       "flash-redirector":{
    //          "caption":"Redirector Flash/SMIL URL for Flash players.",
    //          "protocol":"http",
    //          "streamer":"http://totalerg.insideout.io/",
    //          "bitrates":[
    //             {
    //                "bitrate":1294336.0,
    //                "width":640,
    //                "file":"flashgen/helixware/tests/test/2-test-signal.mp4",
    //                "url":"http://totalerg.insideout.io/flashgen/helixware/tests/test/2-test-signal.mp4"
    //             },
    //             {
    //                "bitrate":2625536.0,
    //                "width":960,
    //                "file":"flashgen/helixware/tests/test/3-test-signal.mp4",
    //                "url":"http://totalerg.insideout.io/flashgen/helixware/tests/test/3-test-signal.mp4"
    //             },
    //             {
    //                "bitrate":475136.0,
    //                "width":480,
    //                "file":"flashgen/helixware/tests/test/1-test-signal.mp4",
    //                "url":"http://totalerg.insideout.io/flashgen/helixware/tests/test/1-test-signal.mp4"
    //             }
    //          ]
    //       },
    //       "flash-direct":{
    //          "caption":"Direct RTMP URL for Flash players.",
    //          "protocol":"rtmp",
    //          "streamer":"rtmp://totalerg.insideout.io/",
    //          "bitrates":[
    //             {
    //                "bitrate":1294336.0,
    //                "width":640,
    //                "file":"helixware/tests/test/2-test-signal.mp4",
    //                "url":"rtmp://totalerg.insideout.io/helixware/tests/test/2-test-signal.mp4"
    //             },
    //             {
    //                "bitrate":2625536.0,
    //                "width":960,
    //                "file":"helixware/tests/test/3-test-signal.mp4",
    //                "url":"rtmp://totalerg.insideout.io/helixware/tests/test/3-test-signal.mp4"
    //             },
    //             {
    //                "bitrate":475136.0,
    //                "width":480,
    //                "file":"helixware/tests/test/1-test-signal.mp4",
    //                "url":"rtmp://totalerg.insideout.io/helixware/tests/test/1-test-signal.mp4"
    //             }
    //          ]
    //       }
    //    }
    // }
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