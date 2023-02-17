<?php
if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="wt-import-export-upsell-wrapper market-box table-box-main">
    <div class="ier-premium-upgrade wt-ierpro-sidebar">

        <div class="wt-ierpro-header">
            <div class="wt-ierpro-name">
                <img src="<?php echo WT_O_IEW_PLUGIN_URL; ?>assets/images/gopro/order-ie.svg" alt="featured img" width="36" height="36">
                <h4 class="wt-ier-product-name"><?php _e('Order, Coupon, Subscription Export Import for WooCommerce'); ?></h4>
            </div>
            <div class="wt-ierpro-mainfeatures">
                <ul>
                    <li class="money-back"><?php _e('30 Day Money Back Guarantee'); ?></li>
                    <li class="support"><?php _e('Fast and Superior Support'); ?></li>
                </ul>
                <div class="wt-ierpro-btn-wrapper">
                    <a href="<?php echo admin_url('admin.php?page=wt_import_export_for_woo_basic#wt-pro-upgrade'); ?>" class="wt-ierpro-blue-btn" target="_blank"><?php _e('UPGRADE TO PREMIUM'); ?></a>
                </div>                
            </div>
        </div>
        <?php do_action('wt_order_addon_basic_gopro_content'); ?>
        <?php do_action('wt_product_addon_basic_gopro_content'); ?>
        <?php do_action('wt_user_addon_basic_gopro_content'); ?>        
    </div>
</div>