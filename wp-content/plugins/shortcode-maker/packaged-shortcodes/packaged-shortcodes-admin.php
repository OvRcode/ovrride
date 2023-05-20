<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class SM_Packaged_Shortcodes_Admin {

    public static function init() {
        add_action( 'edit_form_after_title', array( 'SM_Packaged_Shortcodes_Admin', 'shortcode_editor_panel' ) );
        add_action( 'save_post' , array( __CLASS__, 'save_post_meta' ) );
    }

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
                            foreach ( $shortcode_packages as $name => $package_label ) :
                                $classname = sm_get_package_classname( $name );
                                $settigs = sm_get_package_settings( $classname );
                                $s_items = isset( $settigs['items'] ) ? $settigs['items'] : array();
                                do_action( 'smps_shortcode_settings', $s_items );
                            endforeach; ?>
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
                    $sections = array();
                    foreach ( $shortcode_packages as $name => $package_label ) : ?>
                        <a class="btn btn-primary br3" @click="hide_packaged_shortcode_panel = 0" v-if="hide_packaged_shortcode_panel == 1">
                            <span aria-controls="<?php echo $name; ?>">
                                <?php
                                $classname = sm_get_package_classname( $name );
                                $settigs = sm_get_package_settings( $classname );
                                echo $settigs['name'];
                                ob_start();
                                ?>
                                <div id="<?php echo $name; ?>" class="sm_shortcode_buttons">
                                    <?php
                                    $s_items = isset( $settigs['items'] ) ? $settigs['items'] : array();

                                    foreach ( $s_items as $item_name => $item_array ) {
                                        $sections[$item_array['section']] = $item_array['section']; ?>
                                        <?php
                                        if( isset( $item_array['pro'] ) ) {
                                            ?>
                                            <a href="javascript:"
                                               class="btn btn-default mb5 pro-btn"
                                               :class="{ active : edit_target_item == '<?php echo $item_name; ?>' }"
                                               data-section="<?php echo $item_array['section']; ?>"
                                               data-item_name="<?php echo $item_name; ?>"
                                               v-if="!visible_button_section || visible_button_section == '<?php echo $item_array['section']; ?>'"
                                            ><?php echo $item_array['label']; ?></a>
                                            <?php
                                        } else {
                                            ?>
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

                                    <?php } ?>
                                </div>
                                <?php
                                $tab_contents .= ob_get_contents();
                                ob_end_clean();
                                ?>
                            </span>
                        </a>
                        <?php $i++; ?>
                    <?php endforeach; ?>
                </div>
                <?php
                if( !empty( $sections ) ) {
                    ?>
                    <!-- Tab panes -->
                    <div class="mt5" v-show="hide_packaged_shortcode_panel == 0">
                        <div class="panel panel-default">
                            <div class="panel-heading oh">
                                <h5 class="pull-left"><?php _e( 'Packaged shortcodes', 'sm'); ?></h5>
                                <a href="javascript:" @click="hide_packaged_shortcode_panel = 1" class="btn pull-right btn-default btn-xs br0"><i class="glyphicon glyphicon-remove"></i></a>
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
                    <?php
                }
                ?>

            </div>
        </div>
        <?php
    }


    /**
     * Hide/Show shortcode editor panel
     * Save post meta
     * @param $post_id
     */
    public static function save_post_meta( $post_id ) {
        if( isset( $_POST['sm_hide_shortcode_panel'] ) ) {
            update_post_meta( $post_id, 'sm_hide_shortcode_panel', $_POST['sm_hide_shortcode_panel'] );
        }
    }
}

SM_Packaged_Shortcodes_Admin::init();