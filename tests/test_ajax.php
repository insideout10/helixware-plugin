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
        if (!defined('DOING_AJAX'))
            define('DOING_AJAX', true);

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

        remove_filter('wp_die_ajax_handler', array($this, 'getDieHandler'), 1, 1);
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
        return array($this, 'dieHandler');
    }

    /**
     * Handler for wp_die()
     * Don't die, just continue on.
     * @param string $message
     */
    public function dieHandler($message)
    {
    }

    /**
     * @runInSeparateProcess
     */
    public function test_ajax()
    {

//        if (!function_exists('xdebug_get_headers')) {
//            $this->markTestSkipped('xdebug is required for this test');
//        }
//
//
//        ob_start();
//        // TODO: call ajax method
//        $headers = xdebug_get_headers();
//        $contents = ob_get_clean();
//
//        $this->assertTrue(in_array('Content-Type: application/json', $headers));
    }

}