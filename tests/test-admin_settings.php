<?php
/**
 * Test the *admin/settings* functions.
 */

require_once 'functions.php';

class AdminSettingsTest extends WP_UnitTestCase
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

    public function test() {

        // TODO: write tests.

    }

}