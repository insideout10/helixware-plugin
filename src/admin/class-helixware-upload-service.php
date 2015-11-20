<?php

/**
 * The HelixWare Upload service loads a JavaScript into the Media New page to intercept
 * uploads of video files and direct them to HelixWare.
 *
 * The JavaScript is loaded only on the media-new.php page in order to avoid potential
 * conflicts with other plugins (see https://github.com/insideout10/helixware-plugin/issues/37).
 */
class HelixWare_Upload_Service {

	/**
	 * The Log service.
	 *
	 * @since 1.3.7
	 * @access private
	 * @var \HelixWare_Log_Service $log_service The Log service.
	 */
	private $log_service;

	/**
	 * Create an instance of the Upload service.
	 *
	 * @since 1.3.7
	 */
	public function __construct() {

		$this->log_service = HelixWare_Log_Service::get_logger( 'HelixWare_Upload_Service' );

	}

	/**
	 * Enqueue the admin scripts.
	 *
	 * @since 1.3.7
	 */
	private function enqueue_scripts() {

		$version = HelixWare::get_instance()->get_version();

		wp_enqueue_script( 'helixware-admin-media-new', plugin_dir_url( __FILE__ ) . 'js/helixware-admin-media-new.js', array(
			'wp-plupload',
			'plupload-handlers'
		), $version, TRUE );

		// Get the configuration options.
		$options = array(
			'url'           => HELIXWARE_CLIENT_URL . '/4/user/ondemand',
			'key'           => hewa_get_option( HEWA_SETTINGS_APPLICATION_KEY ),
			'secret'        => hewa_get_option( HEWA_SETTINGS_APPLICATION_SECRET ),
			'extensions'    => hewa_get_option( HEWA_SETTINGS_FILE_EXTENSIONS ),
			'form_action'   => admin_url( 'admin-ajax.php' ),
			'ajax_action'   => 'hewa_create_post',
			'max_file_size' => HEWA_SETTINGS_MAX_FILE_SIZE,
			'post_types'    => array_map(
				function ( $item ) {
					return array(
						'title' => $item->labels->singular_name
					);
				},
				get_post_types( array( 'public' => TRUE ), 'objects' )
			),
			'labels'        => array(
				'title' => __( 'Title', HEWA_LANGUAGE_DOMAIN ),
				'tags'  => __( 'Tags', HEWA_LANGUAGE_DOMAIN ),
				'save'  => __( 'Save', HEWA_LANGUAGE_DOMAIN )
			),
		);

		// Set the options.
		wp_localize_script( 'helixware-admin-media-new', 'hewa_admin_options', $options );

	}

	/**
	 * Intercept calls to the media-new.php page.
	 *
	 * @since 1.3.7
	 */
	public function admin_head_media_new() {

		$this->enqueue_scripts();

	}

}
