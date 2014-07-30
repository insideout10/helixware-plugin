<?php
/**
 */

/**
 * Print the general section header.
 */
function hewa_admin_settings_player_section_callback() {

    echo '<p>' .
        esc_html__( 'Configure the player settings.' ) .
        '</p>';

}

function hewa_admin_settings_player_section() {

    register_setting( HEWA_OPTIONS_SETTINGS_PLAYER, HEWA_OPTIONS_SETTINGS_PLAYER );

    // Add the general section.
    add_settings_section(
        HEWA_OPTIONS_SETTINGS_PLAYER,
        'Player Settings',
        'hewa_admin_settings_player_section_callback',
        HEWA_OPTIONS_SETTINGS_PLAYER
    );


    hewa_admin_settings_add_field(
        HEWA_SETTINGS_TEMPLATE_ID, __( 'Template Id', HEWA_LANGUAGE_DOMAIN ), 'hewa_admin_settings_select_page'
    );

    hewa_admin_settings_add_field(
        HEWA_SETTINGS_JWPLAYER_ID, __( 'JWPlayer Key', HEWA_LANGUAGE_DOMAIN ), 'hewa_admin_settings_input_text'
    );

    hewa_admin_settings_add_field(
        HEWA_SETTINGS_JWPLAYER_DEFAULT_SKIN, __( 'JWPlayer Default Skin', HEWA_LANGUAGE_DOMAIN ), 'hewa_admin_settings_input_text'
    );

    hewa_admin_settings_add_field(
        HEWA_SETTINGS_JWPLAYER_LOGO_URL, __( 'JWPlayer Logo', HEWA_LANGUAGE_DOMAIN ), 'hewa_admin_settings_input_text'
    );

    hewa_admin_settings_add_field(
        HEWA_SETTINGS_JWPLAYER_LOGO_LINK, __( 'JWPlayer Logo Link', HEWA_LANGUAGE_DOMAIN ), 'hewa_admin_settings_input_text'
    );

}
