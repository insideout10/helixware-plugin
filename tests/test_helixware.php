<?php

/**
 * Sample test file.
 */


class HelixWareTest extends WP_UnitTestCase
{

    /**
     * Set up the test.
     */
    function setUp()
    {
        parent::setUp();
    }


    public function tearDown()
    {
        parent::tearDown();
    }

    /**
     * Test the output from the print source tag method.
     */
    function test_hewa_player_print_source_tag() {

        $source = 'http://example.org/file.mp4';
        $type   = 'video/mp4';

        ob_start();
        hewa_player_print_source_tag( $source, $type );
        $output = ob_get_clean();

        $this->assertEquals( "<source src='$source' type='$type'>", $output );

    }

    /**
     * @expectedException WPDieException
     * @expectedExceptionMessage The file parameter is not set.
     */
    function test_hewa_ajax_load_m3u8_file_not_set() {

        hewa_ajax_load_m3u8();

    }

}

