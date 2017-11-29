<?php

class Smps_Simple_Light_Admin {

    public static function init() {
        add_action( 'smps_shortcode_settings', function ( $items )  {
            foreach ( $items as $item_name => $item_array ) {
                if( $item_array['section'] == 'Custom') {
                    ?>
                    <sm_custom_shortcode :id="<?php echo $item_name; ?>" :tag="'<?php echo $item_array['label']; ?>'" v-if="target_item == '<?php echo $item_name; ?>'"></sm_custom_shortcode>
                    <?php
                } else {
                    ?>
                    <smps_simple_light_<?php echo $item_name; ?>_settings v-if="target_class == 'Smps_Simple_Light_Admin' && target_item == '<?php echo $item_name; ?>'"
                    ></smps_simple_light_<?php echo $item_name; ?>_settings>
                    <?php
                }
            }
            ?>
            <!--<smps_simple_light_tabs_settings v-if="target_class == 'Smps_Simple_Light_Admin' && target_item == 'tabs'"></smps_simple_light_tabs_settings>
            <smps_simple_light_accordion_settings v-if="target_class == 'Smps_Simple_Light_Admin' && target_item == 'accordion'"></smps_simple_light_accordion_settings>
            <smps_simple_light_table_settings v-if="target_class == 'Smps_Simple_Light_Admin' && target_item == 'table'"></smps_simple_light_table_settings>
            <smps_simple_light_panel_settings v-if="target_class == 'Smps_Simple_Light_Admin' && target_item == 'panel'"></smps_simple_light_panel_settings>
            <smps_simple_light_alert_settings v-if="target_class == 'Smps_Simple_Light_Admin' && target_item == 'alert'"></smps_simple_light_alert_settings>
            <smps_simple_light_heading_settings v-if="target_class == 'Smps_Simple_Light_Admin' && target_item == 'heading'"></smps_simple_light_heading_settings>
            <smps_simple_light_quote_settings v-if="target_class == 'Smps_Simple_Light_Admin' && target_item == 'quote'"></smps_simple_light_quote_settings>
            <smps_simple_light_button_settings v-if="target_class == 'Smps_Simple_Light_Admin' && target_item == 'button'"></smps_simple_light_button_settings>-->
            <?php
        });
    }
}

Smps_Simple_Light_Admin::init();