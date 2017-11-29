<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class SM_Packaged_Shortcodes_Admin {

    static function shortcode_editor_panel() {
        $shortcode_packages = sm_get_shortcode_packages();
        ?>
        <div class="bs-container smps_app mt20" v-cloak>
            <!-- Modal -->
            <div class="modal fade smps_shortcode_modal" id="shortcode_settings_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
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
                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
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
                        <a class="btn btn-primary br3" @click="show_packaged_shortcode_panel = true">
                            <span aria-controls="<?php echo $name; ?>">
                                <?php
                                $classname = sm_get_package_classname( $name );
                                $settigs = sm_get_package_settings( $classname );
                                echo $settigs['name'];
                                ob_start();
                                ?>
                                <div id="<?php echo $name; ?>">
                                    <?php
                                    $s_items = isset( $settigs['items'] ) ? $settigs['items'] : array();
                                    foreach ( $s_items as $item_name => $item_label ) {
                                        ?><!--br0-->
                                        <a href="#" @click="get_settings_html( '<?php echo $classname.'_Admin'; ?>', '<?php echo $item_name; ?>','<?php echo $item_label; ?>')" class="btn btn-default mb5" data-toggle="modal" data-target="#shortcode_settings_modal"><?php echo $item_label; ?></a>
                                        <?php
                                    }
                                    ?>
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
                <!-- Tab panes -->
                <div class="mt5" v-if="show_packaged_shortcode_panel == true">
                    <div class="panel panel-default">
                        <div class="panel-heading oh">
                            <h5 class="pull-left"><?php _e( 'Packaged shortcodes', 'sm'); ?></h5>
                            <a href="javascript:" @click="show_packaged_shortcode_panel = false" class="btn pull-right btn-default btn-xs br0"><i class="fa fa-remove"></i></a>
                        </div>
                        <div class="panel-body">
                            <?php echo $tab_contents;?>
                        </div>
                    </div>


                </div>

            </div>
        </div>
        <?php
    }
}