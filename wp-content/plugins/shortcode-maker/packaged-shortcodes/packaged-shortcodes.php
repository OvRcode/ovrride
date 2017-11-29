<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class SM_Packaged_Shortcodes {

    /**
     * @var Singleton The reference the *Singleton* instance of this class
     */
    private static $instance;

    /**
     * Returns the *Singleton* instance of this class.
     *
     * @return Singleton The *Singleton* instance.
     */
    public static function get_instance() {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function __construct() {
        add_action( 'admin_menu', array( $this, 'add_packages_list_menu' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts_styles' ) );
        add_action( 'admin_head', array( $this, 'save_added_packages' ) );

        //add panel in admin editor page to add the shortcodes buttons
        add_action( 'edit_form_after_title', array( 'SM_Packaged_Shortcodes_Admin', 'shortcode_editor_panel' ) );
        add_action( 'shortcode_maker_activation_task', array( $this, 'set_data_on_activation' ) );
        $this->includes();
    }

    public function includes() {
        $sm_get_shortcode_packages = sm_get_shortcode_packages();
        foreach ( $sm_get_shortcode_packages as $package_name => $package_dir ) {
            include_once $package_dir.'/'.$package_name.'.php';
        }

        include_once 'packaged-shortcodes-admin.php';
    }

    /**
     * add menu item to
     * admin menu the shortcode packages
     */
    public function add_packages_list_menu() {
        add_submenu_page( 'edit.php?post_type=sm_shortcode', 'Shortcode Packages', 'Shortcode Packages', 'manage_options', 'smps_shortcode_packages', array( $this, 'build_shortocde_package_list_page' ) );
    }

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
                            $package_dirs = apply_filters('smps_package_dir', array(
                                'simple-light' => SHORTCODE_MAKER_ROOT.'/packaged-shortcodes/packages/simple-light'
                            ));

                            foreach ( $package_dirs as  $entry => $package_dir ) {
                                include_once $package_dir.'/'.$entry.'.php';
                                ?>
                                <div class="col-sm-3">
                                    <label></label>
                                    <?php $package_settings = sm_get_package_settings( '', $entry );?>
                                    <div class="panel panel-default">
                                        <div class="panel-heading">
                                            <h6 class="panel-title">
                                                <?php
                                                ?>
                                                <label>
                                                    <input type="checkbox" name="sm_shortcode_packages[<?php echo $entry; ?>]" value="<?php echo $package_dir; ?>" <?php echo in_array( addslashes($package_dir), $sm_get_shortcode_packages )? 'checked' : ''; ?>>
                                                    <?php echo $package_settings['name']; ?>
                                                </label>
                                            </h6>
                                        </div>
                                        <div class="panel panel-body">
                                            <?php
                                            foreach ( $package_settings['items'] as $item ) {
                                                echo '<div>'.$item.'</div>';
                                            }
                                            ?>
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



    /**
     * enqueue admin scripts
     * and styles
     * @param $hook
     */
    public function admin_enqueue_scripts_styles( $hook ) {

        if( in_array( $hook, array(
            'sm_shortcode_page_smps_shortcode_packages',
            'post-new.php',
            'post.php'
        ) ) ) {
            wp_enqueue_style( 'smps-swal-css', SHORTCODE_MAKER_ASSET_PATH.'/css/sweetalert.css' );
            wp_enqueue_style( 'sm-post-css', SHORTCODE_MAKER_ASSET_PATH.'/css/sm-post.css' );

            wp_enqueue_script( 'smps-swal-js', SHORTCODE_MAKER_ASSET_PATH.'/js/sweetalert.min.js', array( 'jquery' ) );
            wp_enqueue_script( 'sm-post-js', SHORTCODE_MAKER_ASSET_PATH.'/js/sm-post.js', array( 'jquery','sm-vue' ) );
        }

    }

    /**
     * save data on plugin activation
     */
    function set_data_on_activation() {
        $sm_shortcode_packages = sm_get_shortcode_packages();
        if( empty( $sm_shortcode_packages ) ) {
            sm_save_shortcode_packages( array(
                'simple-light' => 'packages/simple-light'
            ) );
        }
    }
}

SM_Packaged_Shortcodes::get_instance();