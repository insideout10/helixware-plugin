<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              http://insideout.io
 * @since             1.0.0
 * @package           Helixware_Mico
 *
 * @wordpress-plugin
 * Plugin Name:       HelixWare MICO Extensions
 * Plugin URI:        http://helixware.tv
 * Description:       This is a short description of what the plugin does. It's displayed in the WordPress admin area.
 * Version:           1.2.0
 * Author:            David Riccitelli
 * Author URI:        http://insideout.io
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       helixware-mico
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

// Define constants.
defined( 'HELIXWARE_MICO_GW_URL' ) || define( 'HELIXWARE_MICO_GW_URL', 'http://mico-gateway.insideout.io' );
defined( 'HELIXWARE_MICO_GW_USERNAME' ) || define( 'HELIXWARE_MICO_GW_USERNAME', '' );
defined( 'HELIXWARE_MICO_GW_PASSWORD' ) || define( 'HELIXWARE_MICO_GW_PASSWORD', '' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-helixware-mico-activator.php
 */
function activate_helixware_mico() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-helixware-mico-activator.php';
	Helixware_Mico_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-helixware-mico-deactivator.php
 */
function deactivate_helixware_mico() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-helixware-mico-deactivator.php';
	Helixware_Mico_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_helixware_mico' );
register_deactivation_hook( __FILE__, 'deactivate_helixware_mico' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-helixware-mico.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_helixware_mico() {

	$plugin = new Helixware_Mico();
	$plugin->run();

}

run_helixware_mico();
