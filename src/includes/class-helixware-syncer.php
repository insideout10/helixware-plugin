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

	/**
	 * A HAL client.
	 *
	 * @since 1.1.0
	 * @access private
	 * @var HelixWare_HAL_Client $hal_client A HAL client.
	 */
	private $hal_client;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.1.0
	 *
	 * @param HelixWare_HAL_Client $hal_client
	 */
	public function __construct( $hal_client ) {

		$this->hal_client = $hal_client;

	}

	/**
	 * Synchronize the assets with the remote HelixWare.
	 *
	 * @since 1.1.0
	 */
	public function sync() {

		$request  = new HelixWare_HAL_Request( 'GET', hewa_get_server_url() . '/api/assets' );
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

		// Save a reference to the thumbnail if it exists.
		if ( isset( $asset->_links->thumbnail ) ) {
			update_post_meta( $attachment_id, HelixWare_Asset_Service::META_THUMBNAIL_URL, $asset->_links->thumbnail->href );
		} else {
			delete_post_meta( $attachment_id, HelixWare_Asset_Service::META_THUMBNAIL_URL );
		}

		// Save the type (Live, OnDemand, Broadcast, Channel).
		update_post_meta( $attachment_id, HelixWare_Asset_Service::META_TYPE, $asset->type );

	}

}