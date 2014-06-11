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
 * Plugin URI: http://insideout.io
 * Description: HelixWare turns WordPress in a Video web site
 * Version: 1.0.0-SNAPSHOT
 * Author: ziodave, pieroit
 * Author URI: http://twitter.com/ziodave
 * License: GPL2
 */

// Log functions.
require_once( 'helixware_log.php' );
// Define constants.
require_once( 'helixware_constants.php' );
// Provides general functions.
require_once( 'helixware_functions.php' );
// Load AJAX methods.
require_once( 'ajax/helixware_ajax_m3u8.php' );
// Provides the [hw-player ...] shortcode.
require_once( 'shortcodes/helixware_shortcode_player.php' );
