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
function hewa_get_clip_urls_and_types( $path ) {

    // TODO: provide here the actual functions.

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

    // return an object instance.
    return json_decode( $json );

}

/**
 * Take a list of objects (with url and type)
 * and build the <source> tags to write into the video tag
 * 
 * @param structure outputted by *hewa_get_clip_urls*.
 *
 * @return HTML markup with a list of <source> tags, to put into the <video> tag.
 */
 function hewa_build_video_sources( $urls_and_type ){
 	
	$src='rtmp://streamer.a1.net/cdn/&amp;mp4:A1TAAdmin/VendorAdm/tests/test-signal-3.mp4';
    $type = 'rtmp/mp4';
	$sources = '<source src="' . $src . '" type="' . $type . '" >';
	

	$action = 'hewa_m3u8';
	$src = admin_url('admin-ajax.php') . '?action=' . $action . 
	 		'&file=' . 'iPhone-src%2Fcdn%2FA1TAAdmin%2FVendorAdm%2Ftests%2Ftest-signal-3.mp4';
	$type = 'application/x-mpegURL';
	$sources = $sources . '<source src="' . $src . '" type="' . $type . '" >';
	
	return $sources;
}