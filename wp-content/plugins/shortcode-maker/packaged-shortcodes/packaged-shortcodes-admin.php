<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class SM_Packaged_Shortcodes_Admin {

    static function shortcode_editor_panel() {
        $shortcode_packages = sm_get_shortcode_packages();
        /*hide shortcode panel*/
        ?>

        <div class="bs-container smps_app mt20" v-cloak>
            <input type="hidden" name="sm_hide_shortcode_panel" v-model="hide_packaged_shortcode_panel">
            <!-- Modal -->
            <div class="modal smps_shortcode_modal" id="shortcode_settings_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
            v-show="show_shortcode_settings_panel"
            >
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title" id="myModalLabel">{{ settings_modal_label }}</h4>
                        </div>
                        <div class="modal-body">
                            <?php
                            foreach ( $shortcode_packages as $name => $package ) :
                                $classname = sm_get_package_classname( $name );
                                $settigs = sm_get_package_settings( $classname );
                                $s_items = isset( $settigs['items'] ) ? $settigs['items'] : array();
                                do_action( 'smps_shortcode_settings', $s_items );
                            endforeach;
                            ?>
                            <?php ?>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" @click="dismiss_settings_panel()">Close</button>
                        </div>
                    </div>
                </div>
            </div>
            <div>
                <!-- Nav tabs -->
                <div>
                    <?php
                    $tab_contents = '';
                    $i = 0;
                    foreach ( $shortcode_packages as $name => $package ) : ?>
                        <a class="btn btn-primary br3" @click="hide_packaged_shortcode_panel = 0" v-if="hide_packaged_shortcode_panel == 1">
                            <span aria-controls="<?php echo $name; ?>">
                                <?php
                                $classname = sm_get_package_classname( $name );
                                $settigs = sm_get_package_settings( $classname );
                                echo $settigs['name'];

                                $sections = array();

                                ob_start();
                                ?>
                                <div id="<?php echo $name; ?>">
                                    <?php
                                    $s_items = isset( $settigs['items'] ) ? $settigs['items'] : array();

                                    foreach ( $s_items as $item_name => $item_array ) {

                                    $sections[$item_array['section']] = $item_array['section'];
                                    ?><!--br0-->
                                        <a href="javascript:" @click="get_settings_html( '<?php echo $classname.'_Admin'; ?>', '<?php echo $item_name; ?>','<?php echo $item_array['label']; ?>');"
                                           class="btn btn-default mb5"
                                           :class="{ active : edit_target_item == '<?php echo $item_name; ?>' }"
                                           data-section="<?php echo $item_array['section']; ?>"
                                           data-item_name="<?php echo $item_name; ?>"
                                           v-if="!visible_button_section || visible_button_section == '<?php echo $item_array['section']; ?>'"
                                        ><?php echo $item_array['label']; ?></a>
                                    <?php
                                    }
                                    ?>
                                </div>
                                <!--data-toggle="modal"
                                           data-target="#shortcode_settings_modal"-->
                                <?php
                                $tab_contents .= ob_get_contents();
                                ob_end_clean();
                                ?>
                            </span>
                        </a>
                        <?php $i++; ?>
                    <?php endforeach; ?>
                </div>
                <!-- Tab panes -->
                <div class="mt5" v-show="hide_packaged_shortcode_panel == 0">
                    <div class="panel panel-default">
                        <div class="panel-heading oh">
                            <h5 class="pull-left"><?php _e( 'Packaged shortcodes', 'sm'); ?></h5>
                            <a href="javascript:" @click="hide_packaged_shortcode_panel = 1" class="btn pull-right btn-default btn-xs br0"><i class="fa fa-remove"></i></a>
                        </div>
                        <div class="panel-body">
                            <div class="mb20" style="width: 100%;background: #eee;padding: 10px;">
                                <label for=""><?php _e( 'Types :', 'smps' ); ?></label>
                                <div class="btn-group" role="group" aria-label="..."
                                >
                                    <button type="button" class="btn btn-default br0"
                                            :class="{ active : visible_button_section == '' }"
                                            @click="make_section_visible('')"><?php _e('All','smps'); ?></button>
                                    <?php
                                    foreach ( $sections as $k => $section ) {
                                        ?>
                                        <button type="button" class="btn btn-default br0" :class="{ active : visible_button_section == '<?php echo $section; ?>' }" @click="make_section_visible('<?php echo $section; ?>')"><?php echo $section;?></button>
                                        <?php
                                    }
                                    ?>
                                </div>
                            </div>
                            <?php echo $tab_contents;?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }
}