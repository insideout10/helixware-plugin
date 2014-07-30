<?php

/**
 * This file tests the HelixServer related methods.
 */

require_once 'functions.php';

class ServerTest extends WP_UnitTestCase
{

    /**
     * Set up the test.
     */
    function setUp()
    {
        parent::setUp();
        
        hewa_configure_wordpress_test();
    }


    public function tearDown()
    {
        parent::tearDown();
    }

    /**
     * Test the *hewa_server_call* for exceptions when the plugin is not configured.
     *
     * @uses hewa_server_call to call the remote HelixServer.
     *
     * @expectedException WPDieException
     * @expectedExceptionMessage The plugin is not configured.
     */
    public function test_server_call_without_settings() {

        // Save the current options in order to restore them later.
        $options = get_option( HEWA_SETTINGS );
        delete_option( HEWA_SETTINGS );

        hewa_server_call( '/doesntmatter' );

        // Restore the options if they were present.
        if ( false !== $options ) {
            add_option( HEWA_SETTINGS, $options );
        }
    }

    /**
     *
     */
    public function test_server_call() {

        $server_url = getenv( 'HEWA_SERVER_URL' );
        $app_key    = getenv( 'HEWA_APPLICATION_KEY' );
        $app_secret = getenv( 'HEWA_APPLICATION_SECRET' );

        // If we don't have these information, we skip this test.
        if ( false === $server_url || false === $app_key || false === $app_secret ) {
            $this->markTestSkipped( 'HelixWare settings not provided.' );
        }

        // Call the server and check the response.
        $response = hewa_server_call( '/me' );
        $object   = json_decode( $response );
        
        $this->assertTrue( isset( $object->userName ) );
        $this->assertTrue( isset( $object->account ) );
        $this->assertTrue( isset( $object->email ) );
        $this->assertTrue( isset( $object->enabled ) );
        $this->assertTrue( $object->enabled );
        $this->assertTrue( isset( $object->authorities ) );
        $this->assertTrue( isset( $object->account->name ) );
        $this->assertTrue( isset( $object->account->maxQuota ) );
        $this->assertTrue( isset( $object->account->currentQuota ) );
        $this->assertTrue( is_numeric( $object->account->maxQuota ) );
        $this->assertTrue( is_numeric( $object->account->currentQuota ) );  
    }

}

