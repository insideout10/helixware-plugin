<?php

/**
 * Performs synchronization of assets from HelixWare to WordPress.
 *
 * @package    HelixWare
 * @subpackage HelixWare/includes
 * @author     David Riccitelli <david@insideout.io>
 */
class HelixWare_Syncer {

	const MIME_TYPE = 'application/x-helixware';

	const ASSETS_PATH = '/api/assets';
	const FIND_BY_LAST_MODIFIED_DATE_GREATER_THAN_PATH = '/search/findByLastModifiedDateGreaterThan?date=%s';

	/**
	 * A HAL client.
	 *
	 * @since 1.1.0
	 * @access private
	 * @var HelixWare_HAL_Client $hal_client A HAL client.
	 */
	private $hal_client;

	/**
	 * The HelixWare server URL.
	 *
	 * @since 1.1.0
	 * @access private
	 * @var string $server_url The server URL.
	 */
	private $server_url;

	/**
	 * The Asset service.
	 *
	 * @since 1.1.0
	 * @access private
	 * @var HelixWare_Asset_Service $asset_service The Asset service.
	 */
	private $asset_service;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.1.0
	 *
	 * @param HelixWare_HAL_Client $hal_client A HAL client.
	 * @param string $server_url The server URL.
	 * @param HelixWare_Asset_Service $asset_service The Asset Service.
	 */
	public function __construct( $hal_client, $server_url, $asset_service ) {

		$this->hal_client    = $hal_client;
		$this->server_url    = $server_url;
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

		// Set the path to the /api/assets.
		$path = self::ASSETS_PATH;

		// If incremental, we ask to HelixWare only assets changed after the most recent last modified date.
		if ( $incremental ) {
			$last_modified_date = $this->asset_service->get_most_recent_last_modified_date();
			$path .= sprintf( self::FIND_BY_LAST_MODIFIED_DATE_GREATER_THAN_PATH, $last_modified_date );
		}

		$request  = new HelixWare_HAL_Request( 'GET', $this->server_url . $path );
		$response = $this->hal_client->execute( $request );

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

		$self     = $asset->_links->self->href;
		$filename = ( isset( $asset->relativePath ) ? $asset->relativePath : '' );

		$attachment_id = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE guid=%s", $self ) );

		$attachment = array(
			'guid'           => $self,
			'post_title'     => $asset->title,
			'post_content'   => '', // must be an empty string.
			'post_status'    => 'inherit',
			'post_mime_type' => self::MIME_TYPE
		);

		if ( NULL !== $attachment_id ) {
			$attachment['ID'] = $attachment_id;
		}

		if ( 0 === ( $attachment_id = wp_insert_attachment( $attachment, $filename ) ) ) {
			return;
		};

		// Set the additional fields.
		$this->asset_service->set_thumbnail_url( $attachment_id, isset( $asset->_links->thumbnail->href ) ? $asset->_links->thumbnail->href : NULL );
		$this->asset_service->set_type( $attachment_id, $asset->type );
		$this->asset_service->set_last_modified_date( $attachment_id, $asset->lastModifiedDate );

	}

}