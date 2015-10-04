<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       http://insideout.io
 * @since      1.0.0
 *
 * @package    HelixWare
 * @subpackage HelixWare/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    HelixWare
 * @subpackage HelixWare/includes
 * @author     David Riccitelli <david@insideout.io>
 */
class HelixWare {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      HelixWare_Loader $loader Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string $plugin_name The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string $version The current version of the plugin.
	 */
	protected $version;

	/**
	 * An HTTP client to perform requests towards HelixWare.
	 *
	 * @since 1.1.0
	 * @access private
	 * @var HelixWare_HTTP_Client $http_client An HTTP client.
	 */
	private $http_client;

	/**
	 * A HAL client to perform requests towards HelixWare.
	 *
	 * @since 1.1.0
	 * @access private
	 * @var HelixWare_HAL_Client $hal_client A HAL client.
	 */
	private $hal_client;

	/**
	 * The Asset Service.
	 *
	 * @since 1.1.0
	 * @access private
	 * @var HelixWare_Asset_Service $asset_service The Asset Service.
	 */
	private $asset_service;

	/**
	 * The Asset Image Service.
	 *
	 * @since 1.1.0
	 * @access private
	 * @var HelixWare_Asset_Image_Service $asset_image_service The Asset Image Service.
	 */
	private $asset_image_service;

	/**
	 * An instance of the syncer which synchronizes the local library with the remote one.
	 *
	 * @since 1.1.0
	 * @access private
	 * @var HelixWare_Syncer $syncer The syncer instance.
	 */
	private $syncer;

	/**
	 * The Admin Attachments class handles requests for attachments from WordPress
	 * Media Library.
	 *
	 * @since 1.1.0
	 * @access private
	 * @var HelixWare_Admin_Attachments $admin_attachments
	 */
	private $admin_attachments;

	/**
	 * @since 1.1.0
	 * @var HelixWare_Embed_Shortcode $embed_shortcode
	 */
	private $embed_shortcode;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {

		$this->plugin_name = 'helixware';
		$this->version     = '1.1.0';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - HelixWare_Loader. Orchestrates the hooks of the plugin.
	 * - HelixWare_i18n. Defines internationalization functionality.
	 * - HelixWare_Admin. Defines all hooks for the admin area.
	 * - HelixWare_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-helixware-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-helixware-i18n.php';

		/**
		 * The class responsible for making HTTP requests.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-helixware-http-client.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-helixware-hal-response.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-helixware-hal-request.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-helixware-hal-client.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-helixware-syncer.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-helixware-asset-service.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-helixware-asset-image-service.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-helixware-admin.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-helixware-admin-attachments.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-helixware-public.php';

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-helixware-embed-shortcode.php';


		$this->loader = new HelixWare_Loader();

		// Instantiate all the classes.
		$this->http_client         = new HelixWare_HTTP_Client();
		$this->hal_client          = new HelixWare_HAL_Client( $this->http_client );
		$this->asset_service       = new HelixWare_Asset_Service();
		$this->asset_image_service = new HelixWare_Asset_Image_Service( $this->http_client, hewa_get_server_url() );
		$this->syncer              = new HelixWare_Syncer( $this->hal_client, hewa_get_server_url(), $this->asset_service );
		$this->admin_attachments   = new HelixWare_Admin_Attachments( $this->syncer );
		$this->embed_shortcode     = new HelixWare_Embed_Shortcode();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the HelixWare_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new HelixWare_i18n();
		$plugin_i18n->set_domain( $this->get_helixware() );

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new HelixWare_Admin( $this->get_helixware(), $this->get_version() );

		$this->loader->add_filter( 'wp_prepare_attachment_for_js', $this->asset_image_service, 'wp_prepare_attachment_for_js', 1000, 3 );
		$this->loader->add_filter( 'media_send_to_editor', $this->asset_image_service, 'media_send_to_editor', 1000, 3 );

		// When the media library requests attachments, we filter the query arguments to include also HelixWare assets.
		$this->loader->add_filter( 'ajax_query_attachments_args', $this->admin_attachments, 'ajax_query_attachments_args', 1000, 1 );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
		$this->loader->add_action( 'wp_ajax_hw_asset_image', $this->asset_image_service, 'wp_ajax_get_image' );

	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new HelixWare_Public( $this->get_helixware(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );
		$this->loader->add_action( 'wp_ajax_nopriv_hw_asset_image', $this->asset_image_service, 'wp_ajax_get_image' );

	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_helixware() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    HelixWare_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}
