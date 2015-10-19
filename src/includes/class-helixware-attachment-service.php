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
	 * Create an instance of the Attachment service.
	 *
	 * @since 1.3.0
	 */
	public function __construct() {

		$this->log_service = HelixWare_Log_Service::get_logger( 'HelixWare_Attachment_Service' );

	}

	/**
	 * Called when the _wp_insert_attachment_data_ filter is raised.
	 *
	 * @since 1.3.0
	 *
	 * @param array $data An array of sanitized attachment post data.
	 * @param array $postarr An array of unsanitized attachment post data.
	 *
	 * @return array An array of sanitized attachment post data.
	 */
	public function wp_insert_attachment_data( $data, $postarr ) {

		$this->log_service->trace( "[ data :: " . str_replace( "\n", '', var_export( $data, TRUE ) ) . " ]" );

		// If the mime type is not set or it's not HelixWare, do nothing.
		if ( ! isset( $data['post_mime_type'] ) || ! HelixWare_Asset_Service::is_helixware_mime_type( $data['post_mime_type'] ) ) {
			return $data;
		}

		// Update HelixWare.
		return $data;
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
