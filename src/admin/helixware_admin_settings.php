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
    $active_tab = isset( $_GET[ 'tab' ] ) ? $_GET[ 'tab' ] : key( $sections );

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
    register_setting( HEWA_OPTIONS_SETTINGS_SERVER, HEWA_OPTIONS_SETTINGS_SERVER );
//    register_setting( HEWA_OPTIONS_SETTINGS_PLAYER, HEWA_OPTIONS_SETTINGS_PLAYER );
//    register_setting( HEWA_OPTIONS_SETTINGS_LIVE, HEWA_OPTIONS_SETTINGS_LIVE );

    // Add the general section.
    add_settings_section(
        HEWA_OPTIONS_SETTINGS_SERVER,
        'Server Settings',
        'hewa_admin_settings_server_section_callback',
        HEWA_OPTIONS_SETTINGS_SERVER
    );

    // Add the general section.
    add_settings_section(
        HEWA_OPTIONS_PAGE,
        'Player Settings',
        'hewa_admin_settings_player_section_callback',
        HEWA_OPTIONS_SETTINGS_PLAYER
    );

    hewa_admin_settings_add_field(
        HEWA_SETTINGS_SERVER_URL, __( 'Server URL', HEWA_LANGUAGE_DOMAIN ), 'hewa_admin_settings_input_text'
    );

    hewa_admin_settings_add_field(
        HEWA_SETTINGS_APPLICATION_KEY, __( 'Application Key', HEWA_LANGUAGE_DOMAIN ), 'hewa_admin_settings_input_text'
    );

    hewa_admin_settings_add_field(
        HEWA_SETTINGS_APPLICATION_SECRET, __( 'Application Secret', HEWA_LANGUAGE_DOMAIN ), 'hewa_admin_settings_input_text'
    );

    
//    // Add the field for the post template Id.
//    add_settings_field(
//        hewa_get_option_name( HEWA_SETTINGS_TEMPLATE_ID ),
//        __( 'Template Id', HEWA_LANGUAGE_DOMAIN ),
//        'hewa_admin_settings_select_page',
//        HEWA_OPTIONS_SETTINGS_PLAYER,
//        HEWA_OPTIONS_PAGE,
//        array(
//            'name'    => HEWA_SETTINGS_TEMPLATE_ID,
//            'default' => hewa_get_option( HEWA_SETTINGS_TEMPLATE_ID )
//        )
//    );
//
//
//    // Add the field for the post template Id.
//    add_settings_field(
//        hewa_get_option_name( HEWA_SETTINGS_JWPLAYER_ID ),
//        __( 'JWPlayer Key', HEWA_LANGUAGE_DOMAIN ),
//        'hewa_admin_settings_select_page',
//        HEWA_OPTIONS_SETTINGS_PLAYER,
//        HEWA_OPTIONS_PAGE,
//        array(
//            'name'    => HEWA_SETTINGS_JWPLAYER_ID,
//            'default' => hewa_get_option( HEWA_SETTINGS_JWPLAYER_ID )
//        )
//    );
//
//
//    // Add the field for the post template Id.
//    add_settings_field(
//        hewa_get_option_name( HEWA_SETTINGS_JWPLAYER_DEFAULT_SKIN ),
//        __( 'JWPlayer Default Skin', HEWA_LANGUAGE_DOMAIN ),
//        'hewa_admin_settings_select_page',
//        HEWA_OPTIONS_SETTINGS_PLAYER,
//        HEWA_OPTIONS_PAGE,
//        array(
//            'name'    => HEWA_SETTINGS_JWPLAYER_DEFAULT_SKIN,
//            'default' => ''
//        )
//    );
//
//
//    // Add the field for the post template Id.
//    add_settings_field(
//        hewa_get_option_name( HEWA_SETTINGS_JWPLAYER_LOGO_URL ),
//        __( 'JWPlayer Logo', HEWA_LANGUAGE_DOMAIN ),
//        'hewa_admin_settings_select_page',
//        HEWA_OPTIONS_SETTINGS_PLAYER,
//        HEWA_OPTIONS_PAGE,
//        array(
//            'name'    => HEWA_SETTINGS_JWPLAYER_LOGO_URL,
//            'default' => ''
//        )
//    );
//
//
//    // Add the field for the post template Id.
//    add_settings_field(
//        hewa_get_option_name( HEWA_SETTINGS_JWPLAYER_LOGO_LINK ),
//        __( 'JWPlayer Logo Link', HEWA_LANGUAGE_DOMAIN ),
//        'hewa_admin_settings_select_page',
//        HEWA_OPTIONS_SETTINGS_PLAYER,
//        HEWA_OPTIONS_PAGE,
//        array(
//            'name'    => HEWA_SETTINGS_JWPLAYER_LOGO_LINK,
//            'default' => ''
//        )
//    );
//
//
//    // Add the field for Server URL.
//    add_settings_field(
//        hewa_get_option_name( HEWA_SETTINGS_SERVER_URL ),
//        __( 'Server URL', HEWA_LANGUAGE_DOMAIN ),
//        'hewa_admin_settings_input_text',
//        HEWA_OPTIONS_SETTINGS_SERVER,
//        HEWA_OPTIONS_SETTINGS_SERVER,
//        array(
//            'name'    => HEWA_SETTINGS_SERVER_URL,
//            'default' => ''
//        )
//    );

}
add_action( 'admin_init', 'hewa_admin_settings' );


function hewa_admin_settings_add_field( $option, $label, $input_callback ) {

    $configuration = hewa_get_option_group_and_name( $option );
    $section       = $configuration[0];
    $key           = $configuration[1];

    hewa_write_log( 'Adding field [ option :: {option} ][ section :: {section} ][ key :: {key} ]', array( 'option' => $option, 'section' => $section, 'key' => $key ) );

    add_settings_field( $key, $label, $input_callback, $section, $section, array( 'option' => $option, 'section' => $section, 'name' => $key, 'default' => '' ) );

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

    $value_e = esc_attr( hewa_get_option( $args['option'], $args['default'] ) );
    $name_e  = esc_attr( $args['name'] );
    $section_e = esc_attr( $args['section'] );

    echo "<input name='${section_e}[$name_e]' type='text' value='$value_e' size='40' />";
}