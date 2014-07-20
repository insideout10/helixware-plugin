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
        <h2><?php esc_html_e( 'HelixWare Options', HEWA_LANGUAGE_DOMAIN ) ?></h2>
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
        HEWA_SETTINGS_SERVER_URL,
        __( 'Server URL', HEWA_LANGUAGE_DOMAIN ),
        'hewa_admin_settings_input_text',
        'helixware',
        'hewa_settings_section',
        array(
            'name'    => HEWA_SETTINGS_SERVER_URL,
            'default' => ''
        )
    );

    // Add the field for Application Key.
    add_settings_field(
        HEWA_SETTINGS_APPLICATION_KEY,
        __( 'Application Key', HEWA_LANGUAGE_DOMAIN ),
        'hewa_admin_settings_input_text',
        'helixware',
        'hewa_settings_section',
        array(
            'name'    => HEWA_SETTINGS_APPLICATION_KEY,
            'default' => ''
        )
    );

    // Add the field for Application Secret.
    add_settings_field(
        HEWA_SETTINGS_APPLICATION_SECRET,
        __( 'Application Secret', HEWA_LANGUAGE_DOMAIN ),
        'hewa_admin_settings_input_text',
        'helixware',
        'hewa_settings_section',
        array(
            'name'    => HEWA_SETTINGS_APPLICATION_SECRET,
            'default' => ''
        )
    );

    // Add the field for the post template Id.
    add_settings_field(
        HEWA_SETTINGS_TEMPLATE_ID,
        __( 'Template Id', HEWA_LANGUAGE_DOMAIN ),
        'hewa_admin_settings_select_page',
        'helixware',
        'hewa_settings_section',
        array(
            'name'    => HEWA_SETTINGS_TEMPLATE_ID,
            'default' => ''
        )
    );


    // Add the field for the post template Id.
    add_settings_field(
        HEWA_SETTINGS_JWPLAYER_ID,
        __( 'JWPlayer Key', HEWA_LANGUAGE_DOMAIN ),
        'hewa_admin_settings_select_page',
        'helixware',
        'hewa_settings_section',
        array(
            'name'    => HEWA_SETTINGS_JWPLAYER_ID,
            'default' => ''
        )
    );


    // Add the field for the post template Id.
    add_settings_field(
        HEWA_SETTINGS_JWPLAYER_DEFAULT_SKIN,
        __( 'JWPlayer Default Skin', HEWA_LANGUAGE_DOMAIN ),
        'hewa_admin_settings_select_page',
        'helixware',
        'hewa_settings_section',
        array(
            'name'    => HEWA_SETTINGS_JWPLAYER_DEFAULT_SKIN,
            'default' => ''
        )
    );


    // Add the field for the post template Id.
    add_settings_field(
        HEWA_SETTINGS_JWPLAYER_LOGO_URL,
        __( 'JWPlayer Logo', HEWA_LANGUAGE_DOMAIN ),
        'hewa_admin_settings_select_page',
        'helixware',
        'hewa_settings_section',
        array(
            'name'    => HEWA_SETTINGS_JWPLAYER_LOGO_URL,
            'default' => ''
        )
    );


    // Add the field for the post template Id.
    add_settings_field(
        HEWA_SETTINGS_JWPLAYER_LOGO_LINK,
        __( 'JWPlayer Logo Link', HEWA_LANGUAGE_DOMAIN ),
        'hewa_admin_settings_select_page',
        'helixware',
        'hewa_settings_section',
        array(
            'name'    => HEWA_SETTINGS_JWPLAYER_LOGO_LINK,
            'default' => ''
        )
    );

}
add_action( 'admin_init', 'hewa_admin_settings' );

/**
 * Print the general section header.
 */
function hewa_admin_settings_section_callback() {

    echo '<p>' .
        esc_html__( 'Set here the basic settings for HelixWare including the URL for HelixWare Server and the application data.' ) .
        '</p>';

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

    $value_e = esc_attr( hewa_get_option( $args['name'], $args['default'] ) );
    $name_e  = esc_attr( $args['name'] );

    echo "<input name='" . HEWA_SETTINGS . "[$name_e]' type='text' value='$value_e' size='40' />";
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

    $value_e = esc_attr( hewa_get_option( $args['name'], $args['default'] ) );
    $name_e  = esc_attr( $args['name'] );

    echo "<input name='" . HEWA_SETTINGS . "[$name_e]' type='text' value='$value_e' size='40' />";
}