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
	 * @param int $id The attachment id.
	 * @param int|string $width The player width (default 640).
	 * @param int|string $height The player height (default 360).
	 * @param bool $autoplay Whether to autoplay the clip (default true).
	 * @param string $thumbnail_url The URL of the thumbnail.
	 * @param string $title The asset's title.
	 * @param string $description The asset's description.
	 *
	 * @return string The HTML code for the player.
	 */
	public function render( $id, $width = 640, $height = 360, $autoplay = TRUE, $thumbnail_url = NULL, $title = NULL, $description = NULL );

}