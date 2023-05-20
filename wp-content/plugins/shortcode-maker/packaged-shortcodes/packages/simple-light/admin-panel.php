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
        });
    }
}

Smps_Simple_Light_Admin::init();