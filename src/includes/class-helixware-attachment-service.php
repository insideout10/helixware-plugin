<?php

/**
 * Handles events about attachments (post and postmeta updates).
 *
 * @since 1.3.0
 */
class HelixWare_Attachment_Service {

	/**
	 * The Log service.
	 *
	 * @since 1.3.0
	 * @access private
	 * @var \HelixWare_Log_Service $log_service The Log service.
	 */
	private $log_service;

	/**
	 * The Attachment Sync service.
	 *
	 * @since 1.3.0
	 * @access private
	 * @var \HelixWare_Attachment_Sync_Service $attachment_sync_service The Attachment Sync service.
	 */
	private $attachment_sync_service;

	/**
	 * Create an instance of the Attachment service.
	 *
	 * @since 1.3.0
	 *
	 * @param \HelixWare_Attachment_Sync_Service $attachment_sync_service The Attachment Sync service.
	 */
	public function __construct( $attachment_sync_service ) {

		$this->log_service = HelixWare_Log_Service::get_logger( 'HelixWare_Attachment_Service' );

		$this->attachment_sync_service = $attachment_sync_service;

	}

	/**
	 * Called when the pre_post_update filter is raised.
	 *
	 * @since 1.3.0
	 *
	 * @param int $post_id Post ID.
	 * @param array $data Array of unslashed post data.
	 */
	public function pre_post_update( $post_id, $data ) {

		$this->log_service->trace( "A post is being updated [ post id :: $post_id ][ data :: " . str_replace( "\n", '', var_export( $data, TRUE ) ) . " ]" );

		// If it's not an attachment, do nothing.
		if ( ! $this->is_attachment( $post_id ) ) {
			return;
		}

		// If the mime type is not set or it's not HelixWare, do nothing.
		if ( ! isset( $data['post_mime_type'] ) || ! HelixWare_Asset_Service::is_helixware_mime_type( $data['post_mime_type'] ) ) {
			return;
		}

		// Try updating HelixWare, if it fails, fail the local update as well.
		if ( ! $this->attachment_sync_service->push( $post_id, $data ) ) {
			// TODO: handle
			wp_die( 'need to return a proper error message' );
		}
	}

	/**
	 * Check if a post is an attachment.
	 *
	 * @since 1.3.0
	 *
	 * @param int $post_id The post id.
	 *
	 * @return bool TRUE if it's attachment otherwise FALSE.
	 */
	public function is_attachment( $post_id ) {

		return ( 'attachment' === get_post_type( $post_id ) );

	}

}
