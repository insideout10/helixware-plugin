<?php

/**
 * An asset.
 *
 * @package    HelixWare
 * @subpackage HelixWare/includes
 * @author     David Riccitelli <david@insideout.io>
 */
class HelixWare_Asset_Service {

	// The mime types for the HelixWare attachments.
	const MIME_TYPE_ONDEMAND = 'application/x-helixware-ondemand';
	const MIME_TYPE_LIVE = 'application/x-helixware-live';
	const MIME_TYPE_BROADCAST = 'application/x-helixware-broadcast';
	const MIME_TYPE_CHANNEL = 'application/x-helixware-channel';
	const MIME_TYPE_UNKNOWN = 'application/x-helixware';

	const TYPE_ONDEMAND = 'ondemand';
	const TYPE_LIVE = 'live';
	const TYPE_BROADCAST = 'broadcast';
	const TYPE_CHANNEL = 'channel';

	// Meta Keys for attachment posts.
	const META_THUMBNAIL_URL = '_hw_thumbnail_url';
	const META_TYPE = '_hw_type';
	const META_LAST_MODIFIED_DATE = '_hw_last_modified_date';
	const META_DURATION = '_hw_duration';

	// The key to store a flag whether the asset is being synchronized.
	const META_SYNCHRONIZING = '_hw_synchronizing';

	// The minimum last modified date to get all assets.
	const MIN_LAST_MODIFIED_DATE = '1970-01-01T00:00:00.000Z';

	// The API path to the assets and the search paths.
	const ASSETS_PATH = '/api/assets';
	const FIND_BY_LAST_MODIFIED_DATE_GREATER_THAN_PATH = '/search/findByLastModifiedDateGreaterThan?date=%s';

	/**
	 * The Log service.
	 *
	 * @since 1.3.0
	 * @access private
	 * @var \HelixWare_Log_Service $log_service The Log service.
	 */
	private $log_service;

	/**
	 * A HAL client.
	 *
	 * @since 1.1.0
	 * @access private
	 * @var \HelixWare_HAL_Client $hal_client A HAL client.
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
	 * Initialize the class and set its properties.
	 *
	 * @since    1.1.0
	 *
	 * @param \HelixWare_HAL_Client $hal_client A HAL client.
	 * @param string $server_url The server URL.
	 */
	public function __construct( $hal_client, $server_url ) {

		$this->log_service = HelixWare_Log_Service::get_logger( 'HelixWare_Asset_Service' );

		$this->hal_client = $hal_client;
		$this->server_url = $server_url;

	}

	/**
	 * Get the mime-type for the specified HelixWare type.
	 *
	 * @since 1.1.0
	 *
	 * @param string $type A HelixWare type (ondemand, live, broadcast, channel).
	 *
	 * @return string The related mime-type or a default mime type if the type is not recognized.
	 */
	public static function get_mime_type( $type ) {

		switch ( strtolower( $type ) ) {

			case self::TYPE_ONDEMAND:
				return self::MIME_TYPE_ONDEMAND;

			case self::TYPE_LIVE:
				return self::MIME_TYPE_LIVE;

			case self::TYPE_BROADCAST:
				return self::MIME_TYPE_BROADCAST;

			case self::TYPE_CHANNEL:
				return self::MIME_TYPE_CHANNEL;

			default:
				return self::MIME_TYPE_UNKNOWN;
		}

	}

	/**
	 * Check whether the provided mime-type belongs to HelixWare.
	 *
	 * @since 1.1.0
	 *
	 * @param string $mime_type The mime type.
	 *
	 * @return bool True if it belongs to HelixWare otherwise false.
	 */
	public static function is_helixware_mime_type( $mime_type ) {

		return ( self::MIME_TYPE_ONDEMAND === $mime_type
		         || self::MIME_TYPE_LIVE === $mime_type
		         || self::MIME_TYPE_BROADCAST === $mime_type
		         || self::MIME_TYPE_CHANNEL === $mime_type
		         || self::MIME_TYPE_UNKNOWN === $mime_type );

	}

	/**
	 * Get all the assets modified after the specified date.
	 *
	 * @since 1.3.0
	 *
	 * @param string $last_modified_date A last modified date in ISO8601 format.
	 *
	 * @return \HelixWare_HAL_Response The HAL response.
	 */
	public function get_assets_where_last_modified_date_greater_than( $last_modified_date ) {

		// Set the path to the /api/assets and append the path to filter by date.
		$path = self::ASSETS_PATH
		        . sprintf( self::FIND_BY_LAST_MODIFIED_DATE_GREATER_THAN_PATH, $last_modified_date );

		// Create a HAL request.
		$request = new HelixWare_HAL_Request( 'GET', $this->server_url . $path );

		return $this->hal_client->execute( $request );

	}

	/**
	 * Return the GUID for the specified post ID.
	 *
	 * @since 1.2.0
	 *
	 * @param int $id The post ID.
	 *
	 * @return null|string NULL if post not found or the guid.
	 */
	public function get_guid( $id ) {

		if ( NULL === ( $post = get_post( $id ) ) ) {
			return NULL;
		};

		// Return the GUID.
		return $post->guid;

	}

	/**
	 * Return the asset ID for the specified post ID.
	 *
	 * @since 1.2.0
	 *
	 * @param int $id The post ID.
	 *
	 * @return null|string NULL if post not found or the guid.
	 */
	public function get_asset_id( $id ) {

		// Get the guid in the format of http://server/assets/$asset_id
		if ( NULL === ( $post = get_post( $id ) ) ) {
			return NULL;
		}

		$parts = explode( '/', $post->guid );

		return $parts[ sizeof( $parts ) - 1 ];

	}

	/**
	 * Get a HelixWare asset URL given a post id.
	 *
	 * @since 1.3.0
	 *
	 * @param int $post_id The post id.
	 *
	 * @return string The HelixWare asset URL.
	 */
	public function get_asset_url( $post_id ) {

		return $this->server_url . self::ASSETS_PATH . '/' . $this->get_asset_id( $post_id );

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

	/**
	 * Get the duration for the specified post.
	 *
	 * @since 1.2.0
	 *
	 * @param int $id The attachment id.
	 *
	 * @return int|FALSE The duration or FALSE if not found.
	 */
	public function get_duration( $id ) {

		if ( FALSE === ( $value = get_post_meta( $id, self::META_DURATION, TRUE ) ) ) {
			return FALSE;
		}

		return (int) $value;

	}

	/**
	 * Set the last modified date.
	 *
	 * @since 1.1.0
	 *
	 * @param int $id The attachment id.
	 * @param string $value The last modified date value.
	 */
	public function set_last_modified_date( $id, $value ) {

		$this->_update_post_meta( $id, HelixWare_Asset_Service::META_LAST_MODIFIED_DATE, $value );

	}

	/**
	 * Set the type.
	 *
	 * @since 1.1.0
	 *
	 * @param int $id The attachment id.
	 * @param string $value The type (OnDemand, Live, Broadcast, Channel).
	 */
	public function set_type( $id, $value ) {

		// Save the type (Live, OnDemand, Broadcast, Channel).
		$this->_update_post_meta( $id, HelixWare_Asset_Service::META_TYPE, $value );

	}

	/**
	 * Set the thumbnail URL. The default width is 230 which is 130 (the WP Media Library tile width) / 9 * 16 (inverse of 16:9).
	 *
	 * @since 1.1.0
	 *
	 * @param int $id The attachment id.
	 * @param string $value The thumbnail URL.
	 * @param int $width The requested width (if not provided, it'll be set to 230 by default).
	 */
	public function set_thumbnail_url( $id, $value = NULL, $width = 230 ) {

		// Save a reference to the thumbnail if it exists.
		$this->_update_post_meta( $id, HelixWare_Asset_Service::META_THUMBNAIL_URL, ( isset( $value ) ? "$value?width=$width" : NULL ) );

	}

	/**
	 * Set the duration for the specified post id.
	 *
	 * @since 1.2.0
	 *
	 * @param int $id The attachment id.
	 * @param double $value The duration.
	 */
	public function set_duration( $id, $value ) {

		$this->_update_post_meta( $id, HelixWare_Asset_Service::META_DURATION, ( is_numeric( $value ) ? intval( $value ) : NULL ) );

	}


	/**
	 * Pull an asset from HelixWare.
	 *
	 * @since 1.3.0
	 *
	 * @param object $asset An Asset representation.
	 *
	 * @return bool TRUE if successful otherwise FALSE.
	 */
	public function pull( $asset ) {

		global $wpdb;

		$self = $asset->_links->self->href;

		// Get the mime type according to the asset type.
		$mime_type = self::get_mime_type( $asset->type );

		$attachment = array(
			'guid'           => $self,
			'post_title'     => $asset->title,
			'post_content'   => '', // must be an empty string.
			'post_status'    => 'inherit',
			'post_mime_type' => $mime_type,
		);

		// Check if attachment already exists for this guid.
		if ( NULL !== ( $attachment_id = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE guid=%s", $self ) ) ) ) {
			$attachment['ID'] = $attachment_id;

			// Set the sync flag for existing attachments.
			$this->set_syncing( $attachment_id, TRUE );
		}

		$this->log_service->trace( "Syncing [ " . str_replace( "\n", '', var_export( $attachment, TRUE ) ) . " ]" );

		if ( 0 === ( $attachment_id = wp_insert_attachment( $attachment ) ) ) {
			return FALSE;
		};

		// Clear the syncing flag.
		$this->set_syncing( $attachment_id, FALSE );

		// Set the additional fields.
		$this->set_thumbnail_url( $attachment_id, isset( $asset->_links->thumbnail->href ) ? $asset->_links->thumbnail->href : NULL );
		$this->set_type( $attachment_id, $asset->type );
		$this->set_last_modified_date( $attachment_id, $asset->lastModifiedDate );

		// Set the duration if available.
		if ( isset( $asset->duration ) ) {
			$this->set_duration( $attachment_id, $asset->duration );
		}

		return TRUE;

	}

	/**
	 * Push attachment data to HelixWare.
	 *
	 * @since 1.3.0
	 *
	 * @param int $post_id The attachment id.
	 * @param array $data The fields' data.
	 *
	 * @return bool|NULL FALSE in case in issues, TRUE if successful, or NULL if the synchronization wasn't performed.
	 */
	public function push( $post_id, $data ) {

		// If the mime type is not set or it's not HelixWare, do nothing.
		if ( ! isset( $data['post_mime_type'] ) || ! HelixWare_Asset_Service::is_helixware_mime_type( $data['post_mime_type'] ) ) {
			return NULL;
		}

		// If the post is being synchronized, do nothing.
		if ( $this->is_syncing( $post_id ) ) {
			$this->log_service->trace( "Post is synchronizing [ post id :: $post_id ]" );

			return NULL;
		}

		// If the asset id is not found, fail.
		if ( NULL === ( $asset_id = $this->get_asset_id( $post_id ) ) ) {
			$this->log_service->error( "Asset id not found [ post id :: $post_id ]" );

			return FALSE;
		}

		$payload = array(
			'title'       => $data['post_title'],
			'description' => $data['post_content']
		);

		// Create the request and execute.
		$request  = new HelixWare_HAL_Request( 'PATCH', $this->get_asset_url( $post_id ), $payload, HelixWare_HAL_Request::CONTENT_TYPE_APPLICATION_JSON );
		$response = $this->hal_client->execute( $request );

		return ( is_numeric( $response->get_status_code() ) && 2 === intval( $response->get_status_code() / 100 ) );

	}

	/**
	 * Delete the asset on HelixWare.
	 *
	 * @since 1.3.0
	 *
	 * @param int $post_id The post id.
	 * @param array $data The post fields.
	 *
	 * @return bool TRUE if the operation was successful otherwise FALSE.
	 */
	public function delete( $post_id, $data ) {

		// If the mime type is not set or it's not HelixWare, do nothing.
		if ( ! isset( $data['post_mime_type'] ) || ! HelixWare_Asset_Service::is_helixware_mime_type( $data['post_mime_type'] ) ) {
			return NULL;
		}

		// Create the request and execute.
		$request  = new HelixWare_HAL_Request( 'DELETE', $this->get_asset_url( $post_id ) );
		$response = $this->hal_client->execute( $request );

		return ( is_numeric( $response->get_status_code() ) && 2 === intval( $response->get_status_code() / 100 ) );

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
			$this->get_most_recent_last_modified_date() :
			self::MIN_LAST_MODIFIED_DATE );

		$this->log_service->info( "Syncing [ incremental :: " . ( $incremental ? 'TRUE' : 'FALSE' ) . " ][ last modified date :: $last_modified_date ]" );

		// Get a HAL response from the Asset service.
		$response = $this->get_assets_where_last_modified_date_greater_than( $last_modified_date );

		// Sync each asset.
		do {
			foreach ( $response->get_embedded( 'assets' ) as $asset ) {

				$this->pull( $asset );
			}
		} while ( $response->has_next() && $response = $response->get_next() );

	}

	/**
	 * Convenience method called by the set methods.
	 *
	 * @since 1.1.0
	 *
	 * @param int $id The post id.
	 * @param string $key The key name.
	 * @param string|int|null $value The value to set for the key. If the value is NULL, the key will be removed.
	 */
	private function _update_post_meta( $id, $key, $value = NULL ) {

		if ( isset( $value ) ) {
			update_post_meta( $id, $key, $value );
		} else {
			delete_post_meta( $id, $key );
		}

	}

	/**
	 * Set the syncing flag.
	 *
	 * @since 1.3.0
	 *
	 * @param int $post_id The post id.
	 * @param bool $value The syncing flag (default TRUE).
	 *
	 * @return array The array of fields with the syncing flag.
	 */
	private function set_syncing( $post_id, $value = TRUE ) {

		if ( $value ) {
			update_post_meta( $post_id, self::META_SYNCHRONIZING, 1 );
		} else {
			delete_post_meta( $post_id, self::META_SYNCHRONIZING );
		}

	}

	/**
	 * Check whether the data fields contain a syncing flag (and it is set to TRUE).
	 *
	 * @since 1.3.0
	 *
	 * @param int $post_id The post id.
	 *
	 * @return bool TRUE if syncing, otherwise FALSE.
	 */
	private function is_syncing( $post_id ) {

		return ( '' !== get_post_meta( $post_id, self::META_SYNCHRONIZING, TRUE ) );

	}

}
