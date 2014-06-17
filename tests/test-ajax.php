<?php

/**
 * Testing ajax response class
 */
class AjaxTest extends WP_UnitTestCase
{

    /**
     * Saved error reporting level
     * @var int
     */
    protected $_error_level = 0;

    /**
     * Set up the test fixture.
     * Override wp_die(), pretend to be ajax, and suppres E_WARNINGs
     */
    public function setUp()
    {

        parent::setUp();

        // Suppress warnings from "Cannot modify header information - headers already sent by"
        $this->_error_level = error_reporting();
        error_reporting( $this->_error_level & ~E_WARNING );

        add_filter('wp_die_ajax_handler', array($this, 'getDieHandler'), 1, 1);
        if ( !defined('DOING_AJAX') )
            define( 'DOING_AJAX', true );

        // Disable the *wl_write_log* as it can create issues with AJAX tests.
        add_filter( 'hewa_write_log_handler', array( $this, 'get_write_log_handler' ), 1, 1 );
    }

    /**
     * Tear down the test fixture.
     * Remove the wp_die() override, restore error reporting
     */
    public function tearDown()
    {
        parent::tearDown();

        remove_filter( 'wp_die_ajax_handler', array($this, 'getDieHandler'), 1, 1 );
        remove_filter( 'hewa_write_log_handler', array( $this, 'get_write_log_handler'), 1, 1 );
        error_reporting($this->_error_level);
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
    public function getDieHandler()
    {
        return array( $this, 'dieHandler' );
    }

    /**
     * Handler for wp_die()
     * Don't die, just continue on.
     * @param string $message
     */
    public function dieHandler( $message ) {
    }

    /**
     * @runInSeparateProcess
     */
    public function test_ajax() {

        if (!function_exists('xdebug_get_headers')) {
            $this->markTestSkipped('xdebug is required for this test');
        }

        $server_url = getenv( 'HEWA_SERVER_URL' );
        $app_key    = getenv( 'HEWA_APPLICATION_KEY' );
        $app_secret = getenv( 'HEWA_APPLICATION_SECRET' );

        // If we don't have these information, we skip this test.
        if ( false === $server_url || false === $app_key || false === $app_secret ) {
            $this->markTestSkipped( 'HelixWare settings not provided.' );
        }

        // Save the current options in order to restore them later.
        $options = get_option( HEWA_SETTINGS );
        delete_option( HEWA_SETTINGS );
        add_option( HEWA_SETTINGS, array(
            HEWA_SETTINGS_SERVER_URL         => $server_url,
            HEWA_SETTINGS_APPLICATION_KEY    => $app_key,
            HEWA_SETTINGS_APPLICATION_SECRET => $app_secret
        ) );

        ob_start();

        // Call the server and check the response.
        hewa_ajax_quota();

        $headers  = xdebug_get_headers();

        // TODO: re-enable the following line.
//        $this->assertTrue( in_array('Content-Type: application/json; charset=UTF-8', $headers) );

        $response = ob_get_clean();
        $object   = json_decode( $response );
        $this->assertTrue( isset( $object->userName ) );
        $this->assertTrue( isset( $object->account ) );
        $this->assertTrue( isset( $object->firstName ) );
        $this->assertTrue( isset( $object->lastName ) );
        $this->assertTrue( isset( $object->email ) );
        $this->assertTrue( isset( $object->enabled ) );
        $this->assertTrue( $object->enabled );
        $this->assertTrue( isset( $object->authorities ) );
        $this->assertTrue( isset( $object->account->name ) );
        $this->assertTrue( isset( $object->account->maxQuota ) );
        $this->assertTrue( isset( $object->account->currentQuota ) );
        $this->assertTrue( is_numeric( $object->account->maxQuota ) );
        $this->assertTrue( is_numeric( $object->account->currentQuota ) );

        // Restore the options if they were present.
        if ( false !== $options ) {
            add_option( HEWA_SETTINGS, $options );
        }

    }

}