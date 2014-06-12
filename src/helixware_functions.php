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
        "file": "iPhone-src/cdn/A1TAAdmin/VendorAdm/tests/test-signal-3.mp4",
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
    $urls = json_decode( $json );
	
	// Builod html <source> attributes
	$sources = array();
	foreach( $urls as $u ) {
		// rtmp
		if( $u->protocol == 'rtmp' ) {
			$sources[] = array(
				'src' => $u->url,
				'type' => 'rtmp/mp4'
			);
		}
		// m3u8
		if( strpos( $u->caption, 'M3U8' ) ){	// Don't know how to check for the m3u8
			$sources[] = array(
				'src' => admin_url('admin-ajax.php') . '?action=hewa_m3u8&file=' . $u->file,
				'type' => 'application/x-mpegURL'
			);
		}	
	}
	
	return $sources;
}

/**
 * Take a list of objects (with url and type)
 * and build the <source> tags to write into the video tag
 * 
 * @param structure outputted by *hewa_get_clip_urls*.
 *
 * @return HTML markup with a list of <source> tags, to put into the <video> tag.
 */
 function hewa_build_video_sources( $urls ){
 	
	$sources = '';
	foreach( $urls as $u ){
		$sources = $sources . '<source src= "' . $u['src'] . '" type="' . $u['type'] . '" >';
	}
	return $sources;
}