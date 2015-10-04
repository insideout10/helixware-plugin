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
 * @link              http://helixware.tv
 * @since             1.0.0
 * @package           HelixWare
 *
 * @wordpress-plugin
 * Plugin Name:       HelixWare
 * Plugin URI:        http://helixware.tv
 * Description:       HelixWare turns WordPress in a Video web site
 * Version:           1.2.0-dev
 * Author:            InsideOut10
 * Author URI:        http://helixware.tv
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       helixware
 * Domain Path:       /languages
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


// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

// Get the HelixWare server URL.
defined( 'HELIXWARE_SERVER_URL' ) || define( 'HELIXWARE_SERVER_URL', 'https://cloud.helixware.tv' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-helixware-activator.php
 */
function activate_helixware() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-helixware-activator.php';
	HelixWare_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-helixware-deactivator.php
 */
function deactivate_helixware() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-helixware-deactivator.php';
	HelixWare_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_helixware' );
register_deactivation_hook( __FILE__, 'deactivate_helixware' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-helixware.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_helixware() {

	$plugin = new HelixWare();
	$plugin->run();

}

run_helixware();