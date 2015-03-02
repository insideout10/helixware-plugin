<?php
/**
 * This file contains functions related to the settings screen.
 *
 * For reference see http://kovshenin.com/2012/the-wordpress-settings-api/
 */

require_once( 'helixware_admin_settings_live.php' );
require_once( 'helixware_admin_settings_player.php' );
require_once( 'helixware_admin_settings_server.php' );

/**
 * Create a menu entry in WordPress *Settings* menu.
 *
 * @uses helixware_admin_options_page to display the options page.
 */
function hewa_admin_menu() {

	add_options_page( 'HelixWare', 'HelixWare', 'manage_options', HEWA_OPTIONS_PAGE, 'helixware_admin_options_page' );

}

add_action( 'admin_menu', 'hewa_admin_menu' );

/**
 * Display the HelixWare options page.
 */
function helixware_admin_options_page() {

	// The list of sections.
	$sections = array(
		HEWA_OPTIONS_SETTINGS_SERVER => __( 'Server', HEWA_LANGUAGE_DOMAIN ),
		HEWA_OPTIONS_SETTINGS_PLAYER => __( 'Player', HEWA_LANGUAGE_DOMAIN ),
		HEWA_OPTIONS_SETTINGS_LIVE   => __( 'Live', HEWA_LANGUAGE_DOMAIN )
	);

	// Set th active section.
	$active_tab = isset( $_GET['tab'] ) ? $_GET['tab'] : key( $sections );

	?>

	<div class="wrap">
		<h2><?php esc_html_e( 'HelixWare Options', HEWA_LANGUAGE_DOMAIN ) ?></h2>

		<h2 class="nav-tab-wrapper">
			<?php

			// Print the tabs titles.
			foreach ( $sections as $key => $value ) {

				echo '<a href="?page=' . HEWA_OPTIONS_PAGE . '&tab=' . $key . '" class="nav-tab ' .
				     ( $active_tab === $key ? 'nav-tab-active' : '' ) . '">' . esc_html( $value ) . '</a>';
			}
			?>
		</h2>

		<form action="options.php" method="POST">
			<?php settings_fields( $active_tab ); ?>
			<?php do_settings_sections( $active_tab ); ?>
			<?php submit_button(); ?>
		</form>

	</div>
<?php

}

/**
 * Register HelixWare settings and related configuration screen.
 */
function hewa_admin_settings() {

	// Register the settings.
	hewa_admin_settings_server_section();

	hewa_admin_settings_player_section();

	hewa_admin_settings_live_section();

}

add_action( 'admin_init', 'hewa_admin_settings' );


/**
 * Add a configuration field.
 *
 * @param string $option The name of the configuration option.
 * @param string $label The label name.
 * @param string $input_callback The name of the function to call to display the input.
 * @param array $select A key/value array of options used when displaying the options as a select.
 * @param string $default The default value.
 */
function hewa_admin_settings_add_field( $option, $label, $input_callback, $select = null, $default = '' ) {

	$configuration = hewa_get_option_group_and_name( $option );
	$section       = $configuration[0];
	$key           = $configuration[1];

	hewa_write_log( 'Adding field [ option :: {option} ][ section :: {section} ][ key :: {key} ]', array(
		'option'  => $option,
		'section' => $section,
		'key'     => $key
	) );

	add_settings_field( $key, $label, $input_callback, $section, $section, array(
		'option'  => $option,
		'section' => $section,
		'name'    => $key,
		'default' => $default,
		'select'  => $select
	) );

}


/**
 * Print an input box with the specified name. The value is loaded from the stored settings. If not found, the default
 * value is used.
 *
 * @uses hewa_get_option to get the option value.
 *
 * @param array $args An array with a *name* field containing the option name and a *default* field with its default
 *                    value.
 */
function hewa_admin_settings_input_text( $args ) {

	$value_e   = esc_attr( hewa_get_option( $args['option'], $args['default'] ) );
	$name_e    = esc_attr( $args['name'] );
	$section_e = esc_attr( $args['section'] );

	echo "<input name='${section_e}[$name_e]' type='text' value='$value_e' size='40' />";
}

/**
 * Print a select box with the specified name. The value is loaded from the stored settings. If not found, the default
 * value is used.
 *
 * @uses hewa_get_option to get the option value.
 *
 * @param array $args An array with a *name* field containing the option name and a *default* field with its default
 *                    value.
 */
function hewa_admin_settings_select( $args ) {

	$option_value = hewa_get_option( $args['option'], $args['default'] );
	$name_e       = esc_attr( $args['name'] );
	$section_e    = esc_attr( $args['section'] );

	echo "<select name='${section_e}[$name_e]'>";
	foreach ( $args['select'] as $value => $label ) {
		$value_e = esc_attr( $value );
		$label_e = esc_html__( $label, HEWA_LANGUAGE_DOMAIN );
		echo "<option value='$value_e'";
		if ( $option_value === $value ) {
			echo " selected";
		}
		echo ">$label_e</option>";
	}
	echo '</select>';

}


/**
 * Print an input box with the specified name. The value is loaded from the stored settings. If not found, the default
 * value is used.
 *
 * @uses hewa_get_option to get the option value.
 *
 * @param array $args An array with a *name* field containing the option name and a *default* field with its default
 *                    value.
 */
function hewa_admin_settings_select_page( $args ) {

	// TODO: change this in a page select.

	$value_e   = esc_attr( hewa_get_option( $args['option'], $args['default'] ) );
	$name_e    = esc_attr( $args['name'] );
	$section_e = esc_attr( $args['section'] );

	echo "<input name='${section_e}[$name_e]' type='text' value='$value_e' size='40' />";
}


/**
 * Prints the WP table using the specified stylesheet class.
 *
 * @param string $class The stylesheet class.
 */
function hewa_admin_table_nav( $class ) {

	?>
	<div class="tablenav <?php echo $class; ?>">
		<div class="tablenav-pages">
			<span class="displaying-num"><span ng-bind="data.totalElements"></span> items</span>
            <span class="pagination-links">
                <a class="first-page" ng-click="goToPage(0)" ng-class="data.first ? 'disabled' : ''"
                   title="Go to the first page">«</a>
                <a class="prev-page" ng-click="goToPage(data.number-1)" ng-class="data.first ? 'disabled' : ''"
                   title="Go to the previous page">‹</a>
                <span class="paging-input"><input class="current-page" title="Current page" type="text" name="paged"
                                                  ng-model="currentPage" ng-pattern="/\d/"
                                                  ng-change="goToPage(currentPage - 1)" size="2"> of <span
		                class="total-pages" ng-bind="data.totalPages"></span></span>
                <a class="next-page" ng-click="goToPage(data.number+1)" ng-class="data.last ? 'disabled' : ''"
                   title="Go to the next page">›</a>
                <a class="last-page" ng-click="goToPage(data.totalPages-1)" ng-class="data.last ? 'disabled' : ''"
                   title="Go to the last page">»</a>
            </span>
		</div>
		<br class="clear">
	</div>

<?php

}