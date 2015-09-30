<?php

/**
 * An asset.
 *
 * @package    HelixWare
 * @subpackage HelixWare/includes
 * @author     David Riccitelli <david@insideout.io>
 */
class HelixWare_Asset_Service {

	// Meta Keys for attachment posts.
	const META_THUMBNAIL_URL = '_hw_thumbnail_url';
	const META_TYPE = '_hw_type';
	const META_LAST_MODIFIED_DATE = '_hw_last_modified_date';

	// The minimum last modified date to get all assets.
	const MIN_LAST_MODIFIED_DATE = '1970-01-01T00:00:00.000Z';

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.1.0
	 */
	public function __construct() {

	}

	/**
	 * Get the most recent last modified date.
	 *
	 * @since 1.1.0
	 *
	 * @return string The most recent last modified date among HelixWare attachments.
	 *                If none found the MIN_LAST_MODIFIED_DATE is returned.
	 */
	public function get_most_recent_last_modified_date() {

		$posts = get_posts( array(
			'post_type'      => 'attachment',
			'meta_key'       => HelixWare_Asset_Service::META_LAST_MODIFIED_DATE,
			'order'          => 'DESC',
			'order_by'       => 'meta_value',
			'posts_per_page' => 1,
			'fields'         => 'ids',
			'post_status'    => 'any'
		) );

		// No posts found, return the default last modified date.
		if ( 0 === sizeof( $posts ) ) {
			return self::MIN_LAST_MODIFIED_DATE;
		}

		// If the last modified date is not set on the post, return the default last modified date.
		if ( FALSE === ( $last_modified_date = get_post_meta( $posts[0], HelixWare_Asset_Service::META_LAST_MODIFIED_DATE, TRUE ) ) ) {
			return self::MIN_LAST_MODIFIED_DATE;
		}

		// Finally return the last modified date.
		return $last_modified_date;

	}

}
