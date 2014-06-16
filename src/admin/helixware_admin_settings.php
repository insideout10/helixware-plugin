<?php
/**
 * This file contains functions related to the settings screen.
 *
 * For reference see http://kovshenin.com/2012/the-wordpress-settings-api/
 */

/**
 * Create a menu entry in WordPress *Settings* menu.
 *
 * @uses helixware_admin_options_page to display the options page.
 */
function hewa_admin_menu() {

    add_options_page( 'HelixWare', 'HelixWare', 'manage_options', 'helixware', 'helixware_admin_options_page' );

}
add_action( 'admin_menu', 'hewa_admin_menu' );

/**
 * Display the HelixWare options page.
 */
function helixware_admin_options_page() {
    ?>
    <div class="wrap">
        <h2>HelixWare Options</h2>
        <form action="options.php" method="POST">
            <?php settings_fields( 'helixware' ); ?>
            <?php do_settings_sections( 'helixware' ); ?>
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
    register_setting( 'helixware', HEWA_SETTINGS );

    // Add the general section.
    add_settings_section(
        'hewa_settings_section',
        'General settings',
        'hewa_admin_settings_section_callback',
        'helixware'
    );


    // Add the field for Server URL.
    add_settings_field(
        'hewa_server_url',
        'Server URL',
        'hewa_admin_settings_server_url_callback',
        'helixware',
        'hewa_settings_section'
    );

    // Add the field for Application Key.
    add_settings_field(
        'hewa_app_key',
        'Application Key',
        'hewa_admin_settings_app_key_callback',
        'helixware',
        'hewa_settings_section'
    );

    // Add the field for Application Secret.
    add_settings_field(
        'hewa_app_secret',
        'Application Secret',
        'hewa_admin_settings_app_secret_callback',
        'helixware',
        'hewa_settings_section'
    );

}
add_action( 'admin_init', 'hewa_admin_settings' );

function hewa_admin_settings_section_callback() {
    echo '<p>General settings</p>';
}

/**
 * Print an input box with the specified name. The value is loaded from the stored settings. If not found, the default
 * value is used.
 *
 * @uses hewa_get_option to get the option value.
 *
 * @param string $name The option name.
 * @param string $default The default value (default: empty string).
 */
function hewa_admin_settings_input_text( $name, $default = '' ) {

    $value_e = esc_attr( hewa_get_option( $name, $default ) );
    $name_e  = esc_attr( $name );

    echo "<input name='" . HEWA_SETTINGS . "[$name_e]' type='text' value='$value_e' size='40' />";
}

/**
 * Print the HelixWare Server URL input box.
 *
 * @uses hewa_admin_settings_input_text to print the input box.
 */
function hewa_admin_settings_server_url_callback() {

    hewa_admin_settings_input_text( 'hewa_server_url', '' );

}

/**
 * Print the HelixWare Application Key input box.
 *
 * @uses hewa_admin_settings_input_text to print the input box.
 */
function hewa_admin_settings_app_key_callback() {

    hewa_admin_settings_input_text( 'hewa_app_key', '' );

}

/**
 * Print the HelixWare Application Secret input box.
 *
 * @uses hewa_admin_settings_input_text to print the input box.
 */
function hewa_admin_settings_app_secret_callback() {

    hewa_admin_settings_input_text( 'hewa_app_secret', '' );

}
