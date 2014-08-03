<?php
/**
 * Test the *admin* functions.
 */

require_once 'functions.php';

class AdminTest extends WP_UnitTestCase
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

    public function test_create_post_content_template_id_not_set() {

        $asset_id = rand(0, 1000);
        $content  = hewa_admin_filters_create_post_content( $asset_id, '', '', '' );
        $expect   = '[' . HEWA_SHORTCODE_PREFIX . 'player asset_id="' . $asset_id . '"]';

        $this->assertEquals( $expect, $content );

    }

    public function test_create_post_content_empty_template_id() {

        hewa_set_option( HEWA_SETTINGS_TEMPLATE_ID, '' );

        $asset_id = rand(0, 1000);
        $content  = hewa_admin_filters_create_post_content( $asset_id, '', '', '' );
        $expect   = '[' . HEWA_SHORTCODE_PREFIX . 'player asset_id="' . $asset_id . '"]';

        $this->assertEquals( $expect, $content );

    }

    public function test_create_post_content_non_existent_template_id() {

        hewa_set_option( HEWA_SETTINGS_TEMPLATE_ID, 999999999999 );

        $asset_id = rand(0, 1000);
        $content  = hewa_admin_filters_create_post_content( $asset_id, '', '', '' );
        $expect   = '[' . HEWA_SHORTCODE_PREFIX . 'player asset_id="' . $asset_id . '"]';

        $this->assertEquals( $expect, $content );

    }

    public function test_create_post_content_with_draft_template() {

        // Create a template post.
        $post_id = wp_insert_post( array(
            'post_content' => '{post_title} {post_type} {post_tags} [' . HEWA_SHORTCODE_PREFIX . 'player asset_id="{asset_id}" width="100%"]',
            'post_status'  => 'draft',
            'post_type'    => 'page'
        ) );

        $this->assertNotNull( $post_id );

        // Set the post template.
        hewa_set_option( HEWA_SETTINGS_TEMPLATE_ID, $post_id );

        $asset_id = rand(0, 1000);
        $content  = hewa_admin_filters_create_post_content( $asset_id, 'post', 'Post Title', 'tag 1, tag 2, tag 3' );
        $expect   = 'Post Title post tag 1, tag 2, tag 3 [' . HEWA_SHORTCODE_PREFIX . 'player asset_id="' . $asset_id . '" width="100%"]';

        $this->assertEquals( $expect, $content );

    }

    public function test_create_post_content_with_private_template() {

        // Create a template post.
        $post_id = wp_insert_post( array(
            'post_content' => '{post_title} {post_type} {post_tags} [' . HEWA_SHORTCODE_PREFIX . 'player asset_id="{asset_id}" width="100%"]',
            'post_status'  => 'private',
            'post_type'    => 'page'
        ) );

        $this->assertNotNull( $post_id );

        // Set the post template.
        hewa_set_option( HEWA_SETTINGS_TEMPLATE_ID, $post_id );

        $asset_id = rand(0, 1000);
        $content  = hewa_admin_filters_create_post_content( $asset_id, 'post', 'Post Title', 'tag 1, tag 2, tag 3' );
        $expect   = 'Post Title post tag 1, tag 2, tag 3 [' . HEWA_SHORTCODE_PREFIX . 'player asset_id="' . $asset_id . '" width="100%"]';

        $this->assertEquals( $expect, $content );

    }

}