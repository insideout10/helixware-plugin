<?php
/**
 * This file provides AJAX methods to access remote smil files. The basic motivation of this file is that Helix Server
 * returns 302 Redirects to .smil files that Android devices do not know how to handle. The AJAX call will handle the
 * redirects and return the file content to the player.
 */

/**
 * Load an smil file from a remote Helix Server install.
 *
 * The remote file may express the stream location relative to the server, therefore it won't work because we're
 * proxying the request. This requires us to prepend the server name and base path to the file path in the remote
 * .smil file.
 *
 * This method is available via AJAX as *admin-ajax.php?action=hewa_smil&file=<filename>
 */
function hewa_ajax_load_smil() {

	// Check if the asset id has been provided.
	if ( ! isset( $_GET['id'] ) || empty( $_GET['id'] ) ) {
		wp_die( __( 'The id parameter is required.', HEWA_LANGUAGE_DOMAIN ) );
	}

	$asset_id = $_GET['id'];
	$streams  = hewa_get_clip_urls( $asset_id );

	$ratio = $streams->ratio;
	$flash = $streams->formats->{'application/x-fcs'};

	ob_start();
	header( 'Content-Type: application/smil' );

	// Get the base address, e.g. rtmp://example.org
	$base = $flash->streamer;

	// If set, force to another protocol (rtmpt or rtmps).
	if ( 'default' !== ( $protocol = hewa_get_option( HEWA_SETTINGS_STREAMING_PROTOCOL, 'default' ) ) ) {
		$base = $protocol . substr( $base, strpos( $base, '://' ) );
	}

	echo <<<EOF
<smil>
	<head>
		<meta base="$base"/>
	</head>
	<body>
		<switch>
EOF;

	// Sort the bitrates.
	$bitrates = $flash->bitrates;
	usort( $bitrates, function ( $a, $b ) {
			return $a->bitrate - $b->bitrate;
		} );

	for ( $i = 0; $i < sizeof( $bitrates ); $i ++ ) {

		$bitrate   = $bitrates[ $i ];
		$width     = $bitrate->width;
		$bandwidth = $bitrate->bitrate;
		$height    = intval( $width / $ratio );
		$file      = $bitrate->file;

		echo "<video src=\"$file\" system-bitrate=\"$bandwidth\" width=\"$width\" height=\"$height\" />\n";

	}

	echo <<<EOF
        </switch>
    </body>
</smil>
EOF;

	wp_die();

}

add_action( 'wp_ajax_hewa_smil', 'hewa_ajax_load_smil' );
add_action( 'wp_ajax_nopriv_hewa_smil', 'hewa_ajax_load_smil' );