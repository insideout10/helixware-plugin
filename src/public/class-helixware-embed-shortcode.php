<?php

/**
 * Provides the _hw_embed_ shortcode.
 *
 * @package    HelixWare
 * @subpackage HelixWare/includes
 * @author     David Riccitelli <david@insideout.io>
 */
class HelixWare_Embed_Shortcode {

	/**
	 * The 'hw_embed' shortcode.
	 *
	 * @since 1.2.0
	 */
	const HANDLE_NAME = 'hw_embed';

	/**
	 * The Asset service.
	 *
	 * @since 1.2.0
	 * @access private
	 * @var \HelixWare_Asset_Service $asset_service The Asset service.
	 */
	private $asset_service;

	/**
	 * The Asset Image service.
	 * @since 1.2.0
	 * @access private
	 * @var \HelixWare_Asset_Image_Service The Asset Image service.
	 */
	private $asset_image_service;

	/**
	 * A player rendering class.
	 *
	 * @since 1.2.0
	 * @access private
	 * @var \HelixWare_Player $player A player rendering class
	 */
	private $player;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.1.0
	 *
	 * @param \HelixWare_Asset_Service $asset_service The Asset service.
	 * @param \HelixWare_Asset_Image_Service $asset_image_service The Asset Image service.
	 * @param \HelixWare_Player $player A player.
	 */
	public function __construct( $asset_service, $asset_image_service, $player ) {

		$this->asset_service       = $asset_service;
		$this->asset_image_service = $asset_image_service;
		$this->player              = $player;

		add_shortcode( self::HANDLE_NAME, array( $this, 'render' ) );

	}

	/**
	 * Render the _hw_embed_ shortcode. Internally this method will rely to
	 * {@see _render_compat} which calls the existing _hw_player_ shortcode.
	 *
	 * @since 1.1.0
	 *
	 * @param array $atts An array of shortcode attributes.
	 *
	 * @return string The HTML code.
	 */
	public function render( $atts ) {

		// We need a post ID.
		if ( ! isset( $atts['id'] ) || ! is_numeric( $atts['id'] ) ) {
			return '';
		}

		// The attachment ID.
		$id = $atts['id'];

		// Check that the attachment exists, otherwise return a *video not found* message.
		if ( FALSE === wp_get_attachment_url( $id ) ) {
			return "Video not found";
		}

		return $this->player->render( $id, 640, 360, $this->asset_image_service->get_local_image_url_by_id( $id, 5 ) );

	}

}
