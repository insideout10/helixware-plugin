<?php

/**
 * This class manages Fragments for assets.
 *
 * @since      1.0.0
 * @package    Helixware_Mico
 * @subpackage Helixware_Mico/includes
 * @author     David Riccitelli <david@insideout.io>
 */
class Helixware_Mico_Fragment_Service {

	const FIND_BY_ASSET_GUID_PATH = '/fragments/search/findByAssetGUID?guid=%s';

	/**
	 * A HAL client.
	 *
	 * @since 1.0.0
	 * @access private
	 * @var \HelixWare_HAL_Client $hal_client A HAL client.
	 */
	private $hal_client;

	/**
	 * The MICO server URL.
	 *
	 * @since 1.0.0
	 * @access private
	 * @var string $server_url The MICO server URL.
	 */
	private $server_url;

	/**
	 * The Asset service.
	 *
	 * @since 1.2.0
	 * @access private
	 * @var \HelixWare_Asset_Service The Asset service.
	 */
	private $asset_service;

	/**
	 * Create an instance of the MICO Fragment service.
	 *
	 * @since 1.0.0
	 *
	 * @param \HelixWare_HAL_Client $hal_client A HAL client.
	 * @param string $server_url The server URL.
	 * @param \HelixWare_Asset_Service $asset_service The Asset service.
	 */
	public function __construct( $hal_client, $server_url, $asset_service ) {

		$this->hal_client    = $hal_client;
		$this->server_url    = $server_url;
		$this->asset_service = $asset_service;
	}

	/**
	 * Get the fragments for the specified GUID.
	 *
	 * @since 1.0.0
	 *
	 * @param string $guid The asset GUID.
	 *
	 * @return array An array of fragments.
	 */
	public function get_fragments( $guid ) {

		$path    = sprintf( self::FIND_BY_ASSET_GUID_PATH, urlencode( $guid ) );
		$request = new HelixWare_HAL_Request( 'GET', $this->server_url . $path );

		$response = $this->hal_client->execute( $request );

		$fragments = array();
		do {
			$fragments = array_merge( $fragments, $response->get_embedded( 'fragments' ) );
		} while ( $response->has_next() && $response = $response->get_next() );

		return $fragments;

	}

	/**
	 * Get the fragments for the specified post ID.
	 *
	 * @since 1.2.0
	 *
	 * @param int $id The post ID.
	 *
	 * @return array An array of fragments.
	 */
	public function get_fragments_by_id( $id ) {

		return $this->get_fragments( $this->asset_service->get_guid( $id ) );

	}

	/**
	 * Get the VTT chapters URL.
	 *
	 * @since 1.2.0
	 *
	 * @param int $id The post ID.
	 *
	 * @return string The local URL to the VTT chapters URL.
	 */
	public function get_vtt_chapters_url( $id ) {

		return admin_url( "admin-ajax.php?action=hw_vtt_chapters&id=$id" );
	}

	/**
	 * Echo a jwplayer:track line linking to the chapters file.
	 *
	 * @since 1.2.0
	 *
	 * @param WP_Post $post A post instance.
	 */
	public function playlist_rss_jwplayer_header( $post ) {

		echo( '<jwplayer:track file="' . htmlentities( $this->get_vtt_chapters_url( $post->ID ) ) . '" kind="chapters" />' . "\n" );

	}

	/**
	 * Outputs a VTT file defining the chapters for the attachment with the provided id.
	 *
	 * @since 1.2.0
	 */
	public function ajax_vtt_chapters() {

		// Check that a post ID has been provided.
		if ( ! isset( $_GET['id'] ) || ! is_numeric( $_GET['id'] ) ) {
			wp_die( 'A numeric id is required.' );
		}

		echo( "WEBVTT\n\n" );

		$chapter_no = 0;
		$fragments  = $this->get_fragments_by_id( $_GET['id'] );
		array_walk( $fragments, function ( $fragment ) use ( &$chapter_no ) {

			echo( 'chapter_' . ( ++ $chapter_no ) . "\n" );
			echo( HelixWare_Helper::milliseconds_to_timecode( $fragment->start ) . " --> " . HelixWare_Helper::milliseconds_to_timecode( $fragment->end ) . "\n" );
			echo( 'Chapter ' . $chapter_no . "\n" );
			echo( "\n" );

		} );

		wp_die();

	}

}
