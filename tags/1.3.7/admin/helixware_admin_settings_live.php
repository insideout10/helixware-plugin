<?php
/**
 */

function hewa_admin_settings_live_section() {

    register_setting( HEWA_OPTIONS_SETTINGS_LIVE, HEWA_OPTIONS_SETTINGS_LIVE );

    // Add the general section.
    add_settings_section(
        HEWA_OPTIONS_SETTINGS_LIVE,
        'Live Settings',
        'hewa_admin_settings_live_section_callback',
        HEWA_OPTIONS_SETTINGS_LIVE
    );

}


function hewa_admin_settings_live_section_callback() {

    // Add the scripts.
    wp_enqueue_script( 'angular-js', plugin_dir_url( __FILE__ ) . 'js/angular.min.js', array(), '1.2.21' );
    wp_enqueue_script( 'hewa-admin-settings-live-js', plugin_dir_url( __FILE__ ) . 'js/helixware.admin.settings.live.js' );
    wp_localize_script( 'hewa-admin-settings-live-js', 'hewa_admin_options', array(
        'server_url' => admin_url( 'admin-ajax.php' ),
        'end_points' => array(
            'live_assets' => '?action=hewa_live_assets'
        )
    ) );

    // The client IP.
    $client_ip      = ( isset( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : $_SERVER['REMOTE_ADDR'] );
    echo '<p>Get here the encoder key and set the allowed IP sources (' . esc_html__( 'your IP address is : ', HEWA_LANGUAGE_DOMAIN ) . $client_ip . ')</p>';

    // Set the labels.
    $label_username_h  = esc_html__( 'Username', HEWA_LANGUAGE_DOMAIN );
    $label_path_h      = esc_html__( 'Path', HEWA_LANGUAGE_DOMAIN );
    $label_source_h    = esc_html__( 'Source', HEWA_LANGUAGE_DOMAIN );
    $label_shortcode_h = esc_html__( 'Shortcode', HEWA_LANGUAGE_DOMAIN );
    $label_token_h     = esc_html__( 'Token', HEWA_LANGUAGE_DOMAIN );

    $label_create_h    = esc_html__( 'Create', HEWA_LANGUAGE_DOMAIN );
    $label_delete_h    = esc_html__( 'Delete', HEWA_LANGUAGE_DOMAIN );
    $label_save_h      = esc_html__( 'Save', HEWA_LANGUAGE_DOMAIN );

    ?>

    <div ng-app="hewa" ng-controller="LiveAssetController" >

        <?php hewa_admin_table_nav( 'top' ); ?>

        <table class="wp-list-table widefat fixed posts">
            <thead>
            <th scope="col" class="manage-column"><?php echo $label_shortcode_h; ?></th>
            <th scope="col" class="manage-column"><?php echo $label_username_h; ?></th>
            <th scope="col" class="manage-column"><?php echo $label_path_h; ?></th>
            <th scope="col" class="manage-column"><?php echo $label_source_h; ?></th>
            <th scope="col" class="manage-column"><?php echo $label_token_h; ?></th>
<!--            <th></th>-->
            </thead>

            <tfoot>
            <th scope="col" class="manage-column"><?php echo $label_shortcode_h; ?></th>
            <th scope="col" class="manage-column"><?php echo $label_username_h; ?></th>
            <th scope="col" class="manage-column"><?php echo $label_path_h; ?></th>
            <th scope="col" class="manage-column"><?php echo $label_source_h; ?></th>
            <th scope="col" class="manage-column"><?php echo $label_token_h; ?></th>
<!--            <th></th>-->
            </tfoot>

            <tbody id="the-list">
<!--            <tr class="alternate">-->
<!--                <td></td>-->
<!--                <td></td>-->
<!--                <td></td>-->
<!--                <td></td>-->
<!--                <td></td>-->
<!--                <td>-->
<!--                    <button style="display:none;" type="button" ng-click="create({source:'127.0.0.1/32'});" class="button-primary save alignright">--><?php //echo $label_create_h; ?><!--</button>-->
<!--                </td>-->
<!--            </tr>-->
            <tr ng-class="$odd ? 'alternate' : ''" ng-repeat="asset in data.content">
                <td>[hewa_player live_id="<span ng-bind="asset.id"></span>"]</td>
                <td ng-bind="asset.username"></td>
                <td ng-bind="asset.relativePath"></td>
                <td ng-bind="asset.source"></td>
                <td>
                    <div ng-show="asset._show_token" ng-bind="asset.token"></div>
                    <button ng-hide="asset._show_token" type="button" ng-click="asset._show_token = true" class="button">Show Token</button>
                    <button ng-show="asset._show_token" type="button" ng-click="asset._show_token = false" class="button">Hide Token</button>
                </td>
<!--                <td>-->
<!--                    <button style="display:none;" type="button" ng-click="kill(asset);" class="button-primary delete alignright">--><?php //echo $label_delete_h; ?><!--</button>-->
<!--                    <button style="display:none;" type="button" ng-click="update(asset);" class="button-primary save alignright">--><?php //echo $label_save_h; ?><!--</button>-->
<!--                </td>-->
            </tr>
            </tbody>
        </table>

        <?php hewa_admin_table_nav( 'bottom' ); ?>

    </div>

<?php

}