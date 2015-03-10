<?php
/**
 * This file create the *clip* custom post type.
 */

/**
 * Registers the *clip* post type.
 */
function hewa_post_type_clip_create() {

    register_post_type( HEWA_POST_TYPE_CLIP,
        array(
            'labels' => array(
                'name'          => __( 'Videos', HEWA_LANGUAGE_DOMAIN ),
                'singular_name' => __( 'Video', HEWA_LANGUAGE_DOMAIN )
            ),
            'public'      => true,
            'has_archive' => true,
            'rewrite'     => array('slug' => 'clips'),
        )
    );

}
add_action( 'init', 'hewa_post_type_clip_create' );
