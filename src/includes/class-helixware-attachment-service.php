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
	 * The Asset service.
	 *
	 * @since 1.3.0
	 * @access private
	 * @var \HelixWare_Asset_Service $asset_service The Asset service.
	 */
	private $asset_service;

	/**
	 * Create an instance of the Asset service.
	 *
	 * @since 1.3.0
	 *
	 * @param \HelixWare_Asset_Service $asset_service The Asset service.
	 */
	public function __construct( $asset_service ) {

		$this->log_service = HelixWare_Log_Service::get_logger( 'HelixWare_Attachment_Service' );

		$this->asset_service = $asset_service;

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
		if ( FALSE === $this->asset_service->push( $post_id, $data ) ) {
			// TODO: send an error message somewhere to the UI here?
			wp_die( 'Cannot update HelixWare: halting update.' );
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
