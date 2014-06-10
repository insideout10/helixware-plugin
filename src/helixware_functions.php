<?php
/**
 * This file contains general functions.
 */

/**
 * Retrieve the URLs for a clip with the specified path.
 *
 * @param string $path The clip path.
 *
 * @return A structure with the clip data.
 */
function hewa_get_clip_urls( $path ) {

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