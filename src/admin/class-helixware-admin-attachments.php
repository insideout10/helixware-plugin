<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://insideout.io
 * @since      1.0.0
 *
 * @package    HelixWare
 * @subpackage HelixWare/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    HelixWare
 * @subpackage HelixWare/admin
 * @author     David Riccitelli <david@insideout.io>
 */
class HelixWare_Admin_Attachments {

	/**
	 * The Asset service enables assets syncing.
	 *
	 * @since 1.3.0
	 * @access private
	 * @var \HelixWare_Asset_Service $asset_service The Asset service.
	 */
	private $asset_service;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 *
	 * @param \HelixWare_Asset_Service $asset_service The Asset service.
	 */
	public function __construct( $asset_service ) {

		$this->asset_service = $asset_service;

	}

	/**
	 * A filter (ajax_query_attachments_args) that intercepts the AJAX calls to
	 * display the list of media items in WordPress. In case the request filters
	 * by videos, we add the HelixWare mimetypes.
	 *
	 * @since 1.1.0
	 *
	 * @param array $query An array with query parameters.
	 *
	 * @return array The original query or the updated one if it is filtered by videos.
	 */
	public function ajax_query_attachments_args( $query ) {

		// Synchronize the library.
		$this->asset_service->sync();

		// If it has been requested to filter by video, add the HelixWare mime types.
		if ( isset( $query['post_mime_type'] ) && 'video' === $query['post_mime_type'] ) {
			$query['post_mime_type'] .= ',' . HelixWare_Asset_Service::MIME_TYPE_ONDEMAND .
			                            ',' . HelixWare_Asset_Service::MIME_TYPE_LIVE .
			                            ',' . HelixWare_Asset_Service::MIME_TYPE_BROADCAST .
			                            ',' . HelixWare_Asset_Service::MIME_TYPE_CHANNEL .
			                            ',' . HelixWare_Asset_Service::MIME_TYPE_UNKNOWN;
		}

		return $query;

	}

}
