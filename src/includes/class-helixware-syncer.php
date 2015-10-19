<?php

/**
 * Performs synchronization of assets from HelixWare to WordPress.
 *
 * @package    HelixWare
 * @subpackage HelixWare/includes
 * @author     David Riccitelli <david@insideout.io>
 */
class HelixWare_Syncer {

	/**
	 * The Log service.
	 *
	 * @since 1.3.0
	 * @access private
	 * @var \HelixWare_Log_Service $log_service The Log service.
	 */
	private $log_service;

	/**
	 * The Asset service.
	 *
	 * @since 1.1.0
	 * @access private
	 * @var \HelixWare_Asset_Service $asset_service The Asset service.
	 */
	private $asset_service;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.1.0
	 *
	 * @param \HelixWare_Asset_Service $asset_service The Asset Service.
	 */
	public function __construct( $asset_service ) {

		$this->log_service = HelixWare_Log_Service::get_logger( 'HelixWare_Syncer' );

		$this->asset_service = $asset_service;
	}

	/**
	 * Synchronize the assets with the remote HelixWare.
	 *
	 * @since 1.1.0
	 *
	 * @param bool $incremental Whether to do incremental sync, by default yes.
	 */
	public function sync( $incremental = TRUE ) {

		// If incremental, we ask to HelixWare only assets changed after the most recent last modified date.
		$last_modified_date = ( $incremental ?
			$this->asset_service->get_most_recent_last_modified_date() :
			HelixWare_Asset_Service::MIN_LAST_MODIFIED_DATE );

		$this->log_service->info( "Syncing [ incremental :: " . ( $incremental ? 'TRUE' : 'FALSE' ) . " ][ last modified date :: $last_modified_date ]" );

		// Get a HAL response from the Asset service.
		$response = $this->asset_service->get_assets_where_last_modified_date_greater_than( $last_modified_date );

		// Sync each asset.
		do {
			foreach ( $response->get_embedded( 'assets' ) as $asset ) {

				$this->_sync( $asset );
			}
		} while ( $response->has_next() && $response = $response->get_next() );

	}

	/**
	 * Internal function to perform the actual synchronization.
	 *
	 * @since 1.1.0
	 *
	 * @param $asset
	 */
	private function _sync( $asset ) {
		global $wpdb;

		$self = $asset->_links->self->href;

		// Get the mime type according to the asset type.
		$mime_type = HelixWare_Asset_Service::get_mime_type( $asset->type );

		$attachment = array(
			'guid'           => $self,
			'post_title'     => $asset->title,
			'post_content'   => '', // must be an empty string.
			'post_status'    => 'inherit',
			'post_mime_type' => $mime_type
		);

		// Check if attachment already exists for this guid.
		if ( NULL !== ( $attachment_id = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE guid=%s", $self ) ) ) ) {
			$attachment['ID'] = $attachment_id;
		}

		$this->log_service->trace( "Syncing [ " . str_replace( "\n", '', var_export( $attachment, TRUE ) ) . " ]" );

		if ( 0 === ( $attachment_id = wp_insert_attachment( $attachment ) ) ) {
			return;
		};

		// Set the additional fields.
		$this->asset_service->set_thumbnail_url( $attachment_id, isset( $asset->_links->thumbnail->href ) ? $asset->_links->thumbnail->href : NULL );
		$this->asset_service->set_type( $attachment_id, $asset->type );
		$this->asset_service->set_last_modified_date( $attachment_id, $asset->lastModifiedDate );

		// Set the duration if available.
		if ( isset( $asset->duration ) ) {
			$this->asset_service->set_duration( $attachment_id, $asset->duration );
		}

	}

}