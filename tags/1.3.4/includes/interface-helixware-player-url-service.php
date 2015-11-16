<?php

/**
 * Provides the URLs to play a clip.
 *
 * @since 1.3.0
 */
interface HelixWare_Player_URL_Service {

	/**
	 * Get a URL for the specified post id.
	 *
	 * @since 1.3.0
	 *
	 * @param int $id The post id.
	 *
	 * @return string A URL.
	 */
	public function get_url( $id );

}