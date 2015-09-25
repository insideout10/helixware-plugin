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
	 * Initialize the class and set its properties.
	 *
	 * @since    1.1.0
	 */
	public function __construct() {

	}

	public function sync() {

		$hal_client = new HelixWare_HAL_Client();
		$request    = new HelixWare_HAL_Request( 'GET', hewa_get_server_url() . '/api/assets' );
		$response   = $hal_client->execute( $request );

		do {
			foreach ( $response->get_embedded( 'assets' ) as $asset ) {

				$this->_sync( $asset );
			}
		} while ( $response->has_next() && $response = $response->get_next() );

//		$this->http_client   = new HelixWare_Http_Client();
//		$this->remote_assets = new HelixWare_Remote_Assets( $this->http_client );
//
//		$response = $this->remote_assets->get_all();
//		$json = json_decode( $response['body'] );
//		var_dump( $json->_embedded->assets );
//		wp_die();


	}

	private function _sync( $asset ) {
		global $wpdb;

//		var_dump( $asset );

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

//			echo( "[ existing id :: $attachment_id ]\n" );
		}

		wp_get_attachment_url();

		if ( 0 === ( $attachment_id = wp_insert_attachment( $attachment, $filename ) ) ) {
//			echo( "error\n" );

			return;
		};

//		echo( "attachment id :: $attachment_id\n" );
	}

}