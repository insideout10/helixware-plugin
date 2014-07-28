<?php

/**
 * Sample test file.
 */

require_once 'functions.php';

class HelixWareTest extends WP_UnitTestCase
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

//    /**
//     * Test the output from the print source tag method.
//     *
//     * @uses hewa_player_print_source_tag to print source tags.
//     */
//    function test_hewa_player_print_source_tag() {
//
//        $source = 'http://example.org/file.mp4';
//        $type   = 'video/mp4';
//        $width  = 960;
//
////        ob_start();
//        $output = hewa_player_print_source_tag( $source, $type, 960 );
////        $output = ob_get_clean();
//
//        $this->assertEquals( "<source src='$source' type='$type' data-res='$width'>", $output );
//
//    }

//    /**
//     * Test the *hewa_ajax_load_m3u8* for exceptions when the $_GET['file'] parameter is not set.
//     *
//     * @uses hewa_ajax_load_m3u8 to load a remote m3u8 file.
//     *
//     * @expectedException WPDieException
//     * @expectedExceptionMessage The file parameter is not set.
//     */
//    function test_hewa_ajax_load_m3u8_file_not_set() {
//
//        hewa_ajax_load_m3u8();
//
//    }

    /**
     * Test the *hewa_get_option* method.
     *
     * @uses hewa_get_option to get the plugin settings.
     */
    function test_hewa_get_option() {

        // Pop the current options and delete them, we'll restore them at the end.
        $options   = get_option( HEWA_SETTINGS );
        // Ensure no options are set for these tests.
        delete_option( HEWA_SETTINGS );

        $default_1 = uniqid();
        $this->assertEquals( $default_1, hewa_get_option( HEWA_SETTINGS_SERVER_URL, $default_1 ) );

        $default_2 = uniqid();
        $this->assertEquals( $default_2, hewa_get_option( HEWA_SETTINGS_APPLICATION_KEY, $default_2 ) );

        $default_3 = uniqid();
        $this->assertEquals( $default_3, hewa_get_option( HEWA_SETTINGS_APPLICATION_SECRET, $default_3 ) );

        $value_1   = uniqid();
        $value_2   = uniqid();
        $value_3   = uniqid();

        $this->assertTrue( add_option( HEWA_SETTINGS, array(
            HEWA_SETTINGS_SERVER_URL         => $value_1,
            HEWA_SETTINGS_APPLICATION_KEY    => $value_2,
            HEWA_SETTINGS_APPLICATION_SECRET => $value_3
        ) ) );

        $this->assertEquals( $value_1, hewa_get_option( HEWA_SETTINGS_SERVER_URL, $default_1 ) );
        $this->assertEquals( $value_2, hewa_get_option( HEWA_SETTINGS_APPLICATION_KEY, $default_2 ) );
        $this->assertEquals( $value_3, hewa_get_option( HEWA_SETTINGS_APPLICATION_SECRET, $default_3 ) );

        if ( false !== $options ) {
            update_option( HEWA_SETTINGS, $options );
        } else {
            delete_option( HEWA_SETTINGS );
        }

    }

}

