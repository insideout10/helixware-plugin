<?php

/**
 * Synchronizes WordPress attachments towards HelixWare.
 *
 * @since 1.3.0
 */
class HelixWare_Attachment_Sync_Service extends HelixWare_Sync_Service {

	/**
	 * The Log service.
	 *
	 * @since 1.3.0
	 * @access private
	 * @var \HelixWare_Log_Service $log_service The Log service.
	 */
	private $log_service;

	/**
	 * Create an instance of the Attachment Sync service.
	 *
	 * @since 1.3.0
	 */
	public function __construct() {

		$this->log_service = HelixWare_Log_Service::get_logger( 'HelixWare_Attachment_Sync_Service' );

	}

	public function push( $post_id, $data ) {

		$this->log_service->info( "syncing..." );

		// If data is result of a sync, do nothing.
		if ( $this->is_syncing( $post_id ) ) {
			return TRUE;
		}

		return FALSE;

	}

}