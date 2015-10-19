<?php

/**
 * Allows extending class to pass a syncing flag along with the data fields, in
 * order to avoid circular loops when synchronizing data.
 *
 * @since 1.3.0
 */
abstract class HelixWare_Sync_Service {

	const SYNCHRONIZING = '_hewa_synchronizing';

	/**
	 * Push a single data item.
	 *
	 * @since 1.3.0
	 *
	 * @param object|array $data Data to push.
	 *
	 * @return bool TRUE if successful otherwise FALSE.
	 */
	public abstract function push( $post_id, $data );

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
	protected function set_syncing( $post_id, $value = TRUE ) {

		if ( $value ) {
			update_post_meta( $post_id, self::SYNCHRONIZING, 1 );
		} else {
			delete_post_meta( $post_id, self::SYNCHRONIZING );
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
	protected function is_syncing( $post_id ) {

		return ( '' !== get_post_meta( $post_id, self::SYNCHRONIZING, TRUE ) );

	}

}