<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class SM_Packaged_Shortcodes_Admin {

    static function shortcode_editor_panel() {
        $shortcode_packages = sm_get_shortcode_packages();
        ?>
        <div class="bs-container smps_app">
            <!-- Modal -->
            <div class="modal fade smps_shortcode_modal" id="shortcode_settings_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title" id="myModalLabel">{{ settings_modal_label }}</h4>
                        </div>
                        <div class="modal-body">
                            <?php do_action( 'smps_shortcode_settings' ); ?>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>
            <div>
                <!-- Nav tabs -->
                <ul class="nav nav-tabs" role="tablist">
                    <?php
                    $tab_contents = '';
                    foreach ( $shortcode_packages as $name => $package ) : ?>
                        <li role="presentation" class="active"><a href="#<?php echo $name; ?>" aria-controls="<?php echo $name; ?>" role="tab" data-toggle="tab">
                                <?php
                                $classname = sm_get_package_classname( $name );
                                $settigs = sm_get_package_settings( $classname );
                                echo $settigs['name'];
                                ob_start();
                                ?>
                                <div role="tabpanel" class="tab-pane active" id="<?php echo $name; ?>">
                                    <?php
                                    $s_items = isset( $settigs['items'] ) ? $settigs['items'] : array();
                                    foreach ( $s_items as $item_name => $item_label ) {
                                        ?>
                                        <a href="#" @click="get_settings_html( '<?php echo $classname.'_Admin'; ?>', '<?php echo $item_name; ?>','<?php echo $item_label; ?>')" class="btn btn-default br0" data-toggle="modal" data-target="#shortcode_settings_modal"><?php echo $item_label; ?></a>
                                        <?php
                                    }
                                    ?>
                                </div>
                                <?php
                                $tab_contents .= ob_get_contents();
                                ob_end_clean();
                                ?>
                            </a></li>
                    <?php endforeach; ?>
                </ul>
                <!-- Tab panes -->
                <div class="tab-content mt10">
                    <?php echo $tab_contents;?>
                </div>
            </div>
        </div>
        <?php
    }
}