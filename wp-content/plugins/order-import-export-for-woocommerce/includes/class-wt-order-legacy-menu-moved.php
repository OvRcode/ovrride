<?php
if (!defined('ABSPATH')) {
    exit;
}
if (!class_exists('wt_order_legacy_menu_moved')) {

    class wt_order_legacy_menu_moved {

        /**
         * config options 
         */
        public $plugin = "";
        public $prefix = "";
        public $menu_title = "";
        public $plugin_title = "";
        public $banner_message = "";
        public $old_menu = '';
        public $sholud_show_legacy_menu = '';
        public $old_menu_params = array();

        public function __construct($plugin) {
            $this->plugin = $plugin;
            $this->sholud_show_legacy_menu = 'wt_' . $this->plugin . '_show_legacy_menu';
            add_action('upgrader_process_complete', array($this, 'wt_upgrade_completed'), 10, 2);
            if ($this->wt_should_display_legacy_menu()) {
                add_action('admin_menu', array($this, 'wt_maybe_add_legacy_menu_redirect'));
                if (Wt_Import_Export_For_Woo_Basic_Common_Helper::wt_is_screen_allowed()) {
                    $this->banner_css_class = 'wt_' . $this->plugin . '_show_legacy_menu';
                    add_action('admin_notices', array($this, 'show_banner'));
                    add_action('admin_print_footer_scripts', array($this, 'add_banner_scripts')); /* add banner scripts */
                }
            }
            $this->ajax_action_name = $this->plugin . '_process_show_legacy_menu_action';
            add_action('wp_ajax_' . $this->ajax_action_name, array($this, 'process_user_action')); /* process banner user action */
            add_action('admin_init',array($this,'wt_import_export_menu_old_moved'));
        }

        public function wt_upgrade_completed($upgrader_object, $options) {
            // The path to our plugin's main file
            $our_plugin = plugin_basename(__FILE__);
            // If an update has taken place and the updated type is plugins and the plugins element exists
            if ($options['action'] == 'update' && $options['type'] == 'plugin' && isset($options['plugins'])) {
                // Iterate through the plugins being updated and check if ours is there
                foreach ($options['plugins'] as $plugin) {
                    if ($plugin == $our_plugin) {
                        $this->display_legacy_menu(1);
                    }
                }
            }
        }

        /**
         * 	Prints the banner 
         */
        public function show_banner() {
            ?>
            <div class="<?php echo $this->banner_css_class; ?> notice-info notice is-dismissible">

                <p>
                    <?php echo $this->banner_message; ?>				
                </p>
                <p>

                    <a class="button button-primary" data-type="remove_legacy_menu_banner"><?php _e('Remove order legacy menu');?></a>
                </p>
            </div>
            <?php
        }

        /**
         * 	Ajax hook to process user action on the banner
         */
        public function process_user_action() {
            check_ajax_referer($this->plugin);
            if (isset($_POST['wt_action_type']) && 'dismiss' == $_POST['wt_action_type']) {
                $this->display_legacy_menu(0);
            }
            exit();
        }

        /**
         * 	Add banner JS to admin footer
         */
        public function add_banner_scripts() {
            $ajax_url = admin_url('admin-ajax.php');
            $nonce = wp_create_nonce($this->plugin);
            ?>
            <script type="text/javascript">
                (function ($) {
                    "use strict";

                    /* prepare data object */
                    var data_obj = {
                        _wpnonce: '<?php echo $nonce; ?>',
                        action: '<?php echo $this->ajax_action_name; ?>',
                        wt_action_type: 'dismiss',
                    };

                    $(document).on('click', '.<?php echo $this->banner_css_class; ?> a.button', function (e)
                    {
                        e.preventDefault();
                        var elm = $(this);
                        elm.parents('.<?php echo $this->banner_css_class; ?>').hide();

                        $.ajax({
                            url: '<?php echo $ajax_url; ?>',
                            data: data_obj,
                            type: 'POST'
                        });

                    }).on('click', '.<?php echo $this->banner_css_class; ?> .notice-dismiss', function (e)
                    {
                        e.preventDefault();
                        $.ajax({
                            url: '<?php echo $ajax_url; ?>',
                            data: data_obj,
                            type: 'POST',
                        });

                    });

                })(jQuery)
            </script>
            <?php
        }

        /**
         * Maybe add menu item back in original spot to help people transition
         */
        public function wt_maybe_add_legacy_menu_redirect() {
            if(isset($this->old_menu_params) && !empty($this->old_menu_params) && is_array($this->old_menu_params)){
                foreach ($this->old_menu_params as $menu) {
                    add_submenu_page($menu['parent_slug'],__($menu['menu_title']), __($menu['menu_title']), $menu['capability'], 'import-export-menu-old', array($this, 'wt_import_export_menu_old_moved_old'));
                }                
            }
        }
        
        public function wt_import_export_menu_old_moved_old(){
            // this mooved wt_import_export_menu_old_moved
        }

        /**
         * Call back for transition menu item
         */
        public function wt_import_export_menu_old_moved() {
            if(isset($_GET['page']) && $_GET['page']=='import-export-menu-old'){
                wp_safe_redirect(admin_url('/admin.php?page=wt_import_export_for_woo_basic_export'), 301);
                exit();
            }
        }

        public function wt_should_display_legacy_menu() {
            return (bool) get_option($this->sholud_show_legacy_menu);
        }

        public function display_legacy_menu($display = false) {
            update_option($this->sholud_show_legacy_menu, $display ? 1 : 0 );
        }

    }

}
