<?php
if (!defined('ABSPATH')) {
    exit;
}
$wf_admin_view_path=WT_O_IEW_PLUGIN_PATH.'admin/views/';
$wf_img_path=WT_O_IEW_PLUGIN_URL.'images/';
?>
<div class="wrap" id="<?php echo WT_IEW_PLUGIN_ID_BASIC;?>">
    <h2 class="wp-heading-inline">
    <?php _e('Import Export for WooCommerce');?>
    </h2>
    <div class="nav-tab-wrapper wp-clearfix wt-iew-tab-head">
        <?php
        $tab_head_arr=array(
            'wt-advanced'=>__('General'),
            'wt-help'=>__('Help Guide'),
            'wt-pro-upgrade'=>__('Pro Upgrade')
        );
        if(isset($_GET['debug']))
        {
            $tab_head_arr['wt-debug']='Debug';
        }
        Wt_Import_Export_For_Woo_Basic::generate_settings_tabhead($tab_head_arr);
        ?>
    </div>
    <div class="wt-iew-tab-container">
        <?php
        //inside the settings form
        $setting_views_a=array(
            'wt-advanced'=>'admin-settings-advanced.php',        
        );

        //outside the settings form
        $setting_views_b=array(          
            'wt-help'=>'admin-settings-help.php',           
        );
        $setting_views_b['wt-pro-upgrade']='admin-settings-marketing.php';
        if(isset($_GET['debug']))
        {
            $setting_views_b['wt-debug']='admin-settings-debug.php';
        }
        ?>
        <form method="post" class="wt_iew_settings_form_basic">
            <?php
            // Set nonce:
            if (function_exists('wp_nonce_field'))
            {
                wp_nonce_field(WT_IEW_PLUGIN_ID_BASIC);
            }
            foreach ($setting_views_a as $target_id=>$value) 
            {
                $settings_view=$wf_admin_view_path.$value;
                if(file_exists($settings_view))
                {
                    include $settings_view;
                }
            }
            ?>
            <?php 
            //settings form fields for module
            do_action('wt_iew_plugin_settings_form');?>           
        </form>
        <?php
        foreach ($setting_views_b as $target_id=>$value) 
        {
            $settings_view=$wf_admin_view_path.$value;
            if(file_exists($settings_view))
            {
                include $settings_view;
            }
        }
        ?>
        <?php do_action('wt_iew_plugin_out_settings_form');?> 
    </div>
</div>