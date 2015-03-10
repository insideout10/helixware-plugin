<?php
/*  Copyright 2014  InsideOut10  (email : info@insideout.io)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

/**
 * Plugin Name: HelixWare
 * Plugin URI: http://helixware.tv
 * Description: HelixWare turns WordPress in a Video web site
 * Version: 1.0.0
 * Author: InsideOut10
 * Author URI: http://helixware.tv
 * License: GPL2
 */

// Log functions.
require_once( 'helixware_log.php' );

// Define constants.
require_once( 'helixware_constants.php' );

// Provides general functions.
require_once( 'helixware_functions.php' );

// Register the Video custom post type.
//require_once( 'helixware_post_type_clip.php' );

// Provides HelixServer API calls functions.
require_once( 'helixware_server.php' );

// Load AJAX methods.
require_once( 'ajax/helixware_ajax_m3u8.php' );
require_once( 'ajax/helixware_ajax_smil.php' );
require_once( 'ajax/helixware_ajax_rss.php' );
require_once( 'ajax/helixware_ajax_rss_live.php' );

// Provides the [hw-player ...] shortcode.
require_once( 'shortcodes/helixware_shortcode_player.php' );

// Files related to the admin screen.

// Load general functions: scripts.
require_once( 'admin/helixware_admin.php' );

// Provide the still image AJAX call.
require_once( 'ajax/helixware_ajax_still_image.php' );

// Provide the admin quota AJAX call.
require_once( 'ajax/helixware_ajax_quota.php' );

// Provide the create post AJAX call.
require_once( 'ajax/helixware_ajax_create_post.php' );

// Provide the edit post AJAX call.
require_once( 'ajax/helixware_ajax_edit_post.php' );

// Provide the *set post thumbnail* ajax action.
require_once( 'ajax/helixware_ajax_set_post_thumbnail.php' );

// Provide the admin settings screen.
require_once( 'admin/helixware_admin_settings.php' );

// Run admin notices.
require_once( 'admin/helixware_admin_notices.php' );

// Add custom meta-boxes to the admin screens.
require_once( 'admin/helixware_admin_metaboxes.php' );

require_once( 'modules/seo/seo.php' );