<?php

/**
 * A standard interface for rendering players.
 *
 * @since 1.2.0
 */
interface HelixWare_Player {

	/**
	 * Render the HTML code for the player.
	 *
	 * @since 1.2.0
	 *
	 * @param string $url The URL of the video.
	 * @param int $width The player width (default 640).
	 * @param int $height The player height (default 360).
	 * @param string $thumbnail_url The URL of the thumbnail.
	 * @param string $title The asset's title.
	 * @param string $description The asset's description.
	 * @param string $chapters_url The URL pointing to the video's chapters.
	 *
	 * @return string The HTML code for the player.
	 */
	public function render( $url, $width = 640, $height = 360, $thumbnail_url = NULL, $title = NULL, $description = NULL, $chapters_url = NULL );

}