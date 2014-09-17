<?php
/**
 * Testing ajax response class
 */

require_once 'functions.php';

class AjaxSmilTest extends WP_UnitTestCase {

	/**
	 * Saved error reporting level
	 * @var int
	 */
	protected $_error_level = 0;

	/**
	 * Set up the test fixture.
	 * Override wp_die(), pretend to be ajax, and suppress E_WARNINGs
	 */
	public function setUp() {
		if ( false === ( getenv( 'HEWA_SERVER_URL' ) ) || false === getenv( 'HEWA_APPLICATION_KEY' ) || false === getenv( 'HEWA_APPLICATION_SECRET' ) ) {
			$this->markTestSkipped( 'The required environment settings for this test are not set.' );
		}

		parent::setUp();

		hewa_configure_wordpress_test();

		// Suppress warnings from "Cannot modify header information - headers already sent by"
		$this->_error_level = error_reporting();
		error_reporting( $this->_error_level & ~E_WARNING );

		add_filter( 'wp_die_ajax_handler', array( $this, 'getDieHandler' ), 1, 1 );
		if ( ! defined( 'DOING_AJAX' ) ) {
			define( 'DOING_AJAX', true );
		}

		// Disable the *wl_write_log* as it can create issues with AJAX tests.
		add_filter( 'hewa_write_log_handler', array( $this, 'get_write_log_handler' ), 1, 1 );
	}

	/**
	 * Tear down the test fixture.
	 * Remove the wp_die() override, restore error reporting
	 */
	public function tearDown() {
		parent::tearDown();

		remove_filter( 'wp_die_ajax_handler', array( $this, 'getDieHandler' ), 1, 1 );
		remove_filter( 'hewa_write_log_handler', array( $this, 'get_write_log_handler' ), 1, 1 );
		error_reporting( $this->_error_level );
	}


	public function get_write_log_handler() {

		return array( $this, 'write_log_handler' );
	}

	public function write_log_handler( $log ) {

	}

	/**
	 * Return our callback handler
	 * @return callback
	 */
	public function getDieHandler() {
		return array( $this, 'dieHandler' );
	}

	/**
	 * Handler for wp_die()
	 * Don't die, just continue on.
	 *
	 * @param string $message
	 */
	public function dieHandler( $message ) {
	}

	/**
	 * Get the stream Id used for testing, it must exist in the scope of the account used for tests.
	 *
	 * @return int The stream Id.
	 */
	private function get_stream_id() {

		return 116;

	}

	/**
	 * How many bitrates are expected for the specified stream Id.
	 *
	 * @return int The number of bitrates.
	 */
	private function get_bitrates_count() {

		return 2;

	}

	/**
	 * @runInSeparateProcess
	 */
	public function test_ajax_load_smil() {

		if ( ! function_exists( 'xdebug_get_headers' ) ) {
			$this->markTestSkipped( 'xdebug is required for this test' );
		}

		$server_url = getenv( 'HEWA_SERVER_URL' );
		$app_key    = getenv( 'HEWA_APPLICATION_KEY' );
		$app_secret = getenv( 'HEWA_APPLICATION_SECRET' );

		// If we don't have these information, we skip this test.
		if ( false === $server_url || false === $app_key || false === $app_secret ) {
			$this->markTestSkipped( 'HelixWare settings not provided.' );
		}

		// Set the clip Id.
		$_GET['id'] = $this->get_stream_id();

		$this->assertTrue( $this->is_response_valid( $this->load_smil( 'default' ) ) );
		$this->assertTrue( $this->is_response_valid( $this->load_smil( 'rtmpt' ) ) );
		$this->assertTrue( $this->is_response_valid( $this->load_smil( 'rtmps' ) ) );
	}

	/**
	 * Load a SMIL to the remote stream using the specified protocol.
	 *
	 * @param string $protocol A protocol configuration (default, rtmpt or rtmps).
	 *
	 * @return string The remote XML response.
	 */
	private function load_smil( $protocol ) {

		hewa_set_option( HEWA_SETTINGS_STREAMING_PROTOCOL, $protocol );

		// Call the server and check the response.
		ob_start();
		hewa_ajax_load_smil();

		$headers = xdebug_get_headers();
		$this->assertTrue( in_array('Content-Type: application/smil', $headers) );

		$response = ob_get_clean();

		// Get the base.
		$matches = array();
		$this->assertEquals( 1, preg_match( '<meta base="([^"]+)"/>', $response, $matches ) );

		$base = $matches[1];

		$this->assertEquals( 0, strpos( $base, $protocol . '://' ) );

		return $response;

	}

	/**
	 * Validate the remote XML response. Currently the validation checks that the number of video tags matches the
	 * number of expected bitrates.
	 *
	 * @uses $this->get_bitrates_count to get the number of expected bitrates.
	 *
	 * @param string $response The XML response body.
	 *
	 * @return bool True if valid, otherwise false.
	 */
	private function is_response_valid( $response ) {

		$matches         = array();
		$expected_videos = $this->get_bitrates_count();
		$this->assertEquals(
			$expected_videos,
			preg_match_all(
				'<video src="([^"]+)" system-bitrate="(\d+)" width="(\d+)" height="(\d+)" />',
				$response,
				$matches,
				PREG_SET_ORDER
			)
		);

		return true;

	}

}