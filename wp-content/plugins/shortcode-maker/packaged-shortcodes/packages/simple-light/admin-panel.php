<?php

class Smps_Simple_Light_Admin {

    public static function init() {
        add_action( 'smps_shortcode_settings', function ()  {
            ?>
            <smps_simple_light_tabs_settings v-if="target_class == 'Smps_Simple_Light_Admin' && target_item == 'tabs'"></smps_simple_light_tabs_settings>
            <smps_simple_light_accordion_settings v-if="target_class == 'Smps_Simple_Light_Admin' && target_item == 'accordion'"></smps_simple_light_accordion_settings>
            <smps_simple_light_table_settings v-if="target_class == 'Smps_Simple_Light_Admin' && target_item == 'table'"></smps_simple_light_table_settings>
            <smps_simple_light_panel_settings v-if="target_class == 'Smps_Simple_Light_Admin' && target_item == 'panel'"></smps_simple_light_panel_settings>
            <smps_simple_light_alert_settings v-if="target_class == 'Smps_Simple_Light_Admin' && target_item == 'alert'"></smps_simple_light_alert_settings>
            <smps_simple_light_heading_settings v-if="target_class == 'Smps_Simple_Light_Admin' && target_item == 'heading'"></smps_simple_light_heading_settings>
            <smps_simple_light_quote_settings v-if="target_class == 'Smps_Simple_Light_Admin' && target_item == 'quote'"></smps_simple_light_quote_settings>
            <smps_simple_light_button_settings v-if="target_class == 'Smps_Simple_Light_Admin' && target_item == 'button'"></smps_simple_light_button_settings>
            <?php
        });
    }
}

Smps_Simple_Light_Admin::init();