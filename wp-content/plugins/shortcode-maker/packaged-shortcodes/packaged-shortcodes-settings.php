<?php

if( !class_exists( 'Packaged_Shortcodes_Settings' ) ) {
    class Packaged_Shortcodes_Settings {

        public function __construct() {
            add_action( 'admin_menu', array( $this, 'add_menu_page' ) );
            add_action( 'admin_head', array( $this, 'save_added_packages' ) );
        }

        /**
         * add menu item to
         * admin menu
         */
        public function add_menu_page() {
            add_submenu_page( 'edit.php?post_type=sm_shortcode', 'Shortcode Packages', 'Shortcode Packages', 'manage_options', 'smps_shortcode_packages', array( $this, 'build_shortocde_package_list_page' ) );
        }


        /**
         * Shortcode package settings page
         */
        public function build_shortocde_package_list_page() {
            $sm_get_shortcode_packages = sm_get_shortcode_packages();
            ?>
            <form method="post">
                <input type="hidden" name="_wpnonce" value="<?php echo wp_create_nonce( 'smps-package-list' ); ?>">
                <?php
                if ($handle = opendir(SHORTCODE_MAKER_ROOT.'/packaged-shortcodes/packages')) {

                    /* This is the correct way to loop over the directory. */
                    ?>
                    <div class="bs-container">
                        <div class="container-fluid">
                            <div class="row">
                                <?php
                                $package_dirs = sm_get_all_shortcode_packages();

                                foreach ( $package_dirs as  $package_slug => $package_label ) {
                                    include_once SHORTCODE_MAKER_ROOT.'/packaged-shortcodes/packages/'.$package_slug.'/'.$package_slug.'.php';;
                                    ?>
                                    <div class="col-sm-3">
                                        <label></label>
                                        <?php $package_settings = sm_get_package_settings( '', $package_slug );?>
                                        <div class="panel panel-default">
                                            <div class="panel-heading">
                                                <h6 class="panel-title">
                                                    <label>
                                                        <input type="checkbox" name="sm_shortcode_packages[<?php echo $package_slug; ?>]" value="<?php echo $package_slug; ?>" <?php echo isset( $sm_get_shortcode_packages[$package_slug])? 'checked' : ''; ?>>
                                                        <?php echo $package_settings['name']; ?>
                                                    </label>
                                                </h6>
                                            </div>
                                            <div class="panel panel-body">
                                                <?php foreach ( $package_settings['items'] as $k => $item ) {
                                                    echo '<div>'.$item['label'].'</div>';
                                                } ?>
                                            </div>
                                        </div>
                                    </div>
                                    <?php
                                }
                                ?>
                            </div>
                            <div class="row">
                                <div class="col-sm-4">
                                    <input type="submit" name="save_added_packages" class="btn btn-primary smps_save_added_packages" value="<?php _e( 'Add Selected Packages', 'sm' ); ?>">
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php
                    closedir($handle);
                }
                ?>
            </form>
            <?php
        }


        /**
         * Save packages
         */
        public function save_added_packages() {

            if( isset( $_POST['save_added_packages'] ) ) {
                $nonce = $_REQUEST['_wpnonce'];

                if ( ! wp_verify_nonce( $nonce, 'smps-package-list' ) ) return;

                if( !isset( $_POST['sm_shortcode_packages'] ) || !is_array( $_POST['sm_shortcode_packages'] ) ) {
                    $_POST['sm_shortcode_packages'] = array();
                }
                sm_save_shortcode_packages($_POST['sm_shortcode_packages']);
            }
        }
    }
}

new Packaged_Shortcodes_Settings();