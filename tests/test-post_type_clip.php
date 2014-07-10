<?php
/**
 * This file tests the video custom post type.
 */


class PostTypeVideoTest extends WP_UnitTestCase
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

    public function test_post_type_video_exists() {

        $this->markTestSkipped('The hewa_clip type has been removed.');

        $types = get_post_types( array(
            'public' => true
        ) );

        $this->assertContains( HEWA_POST_TYPE_CLIP, $types );
    }
}