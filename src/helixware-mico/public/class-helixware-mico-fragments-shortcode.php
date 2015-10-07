<?php

/**
 * Provides the _hw_fragments_ shortcode.
 *
 * @package    HelixWare
 * @subpackage HelixWare/includes
 * @author     David Riccitelli <david@insideout.io>
 */
class HelixWare_Mico_Fragments_Shortcode {

	const HANDLE_NAME = 'hw_fragments';

	/**
	 * The Fragments service.
	 *
	 * @since 1.2.0
	 * @access private
	 * @var \Helixware_Mico_Fragment_Service $fragments_service The Fragments service.
	 */
	private $fragments_service;

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
	 *
	 * @since 1.2.0
	 * @access private
	 * @var \HelixWare_Asset_Image_Service $asset_image_service The Asset Image service.
	 */
	private $asset_image_service;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.1.0
	 *
	 * @param \Helixware_Mico_Fragment_Service $fragments_service The Fragments service.
	 * @param \HelixWare_Asset_Service $asset_service The Asset service.
	 * @param \HelixWare_Asset_Image_Service $asset_image_service The Asset Image service.
	 */
	public function __construct( $fragments_service, $asset_service, $asset_image_service ) {

		$this->fragments_service   = $fragments_service;
		$this->asset_service       = $asset_service;
		$this->asset_image_service = $asset_image_service;

		// Register itself as handler for the hw_fragments shortcode.
		add_shortcode( self::HANDLE_NAME, array( $this, 'render' ) );

	}

	/**
	 * Render the _hw_fragments_ shortcode.
	 *
	 * @since 1.2.0
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

		// The attachment GUID (http://cloud.helixware...).
		$guid = $this->asset_service->get_guid( $id );

		$html = "<ul>";
		foreach ( $this->fragments_service->get_fragments_by_id( $id ) as $fragment ) {
			$html .= '<li style="float:left;"><img width="200" src="' . $this->asset_image_service->get_image_url( $guid, $fragment->start / 1000 ) . '" />' . $fragment->start . '</li>';
		}
		$html .= "</ul>";

		return $html;

	}

}
