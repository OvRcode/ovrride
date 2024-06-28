<?php
$addon_url = 'http://sharethingz.com/downloads/woocommerce-simply-order-export-add-on/';
?>

<div style="padding: 10px;" class="notice notice-info woocommerce-message woooe-addon is-dismissible">
    <div>
    <p style="font-weight: 600; font-size: 13px; line-height: 19px;">
        <?php _e('Thank you for using WooCommerce Simply Order Export Plugin.&nbsp;', 'woooe'); ?>
        <?php printf( __('We recommend you to try our <a target="_blank" href="%s">Add-on plugin</a>, it adds all fields to export, scheduled export and many more.', 'woooe'), $addon_url ); ?>
    </p>
    </div>
    <div>
        <a class="button-primary btn" target="_blank" href="<?php echo $addon_url ?>"><?php _e('Know More...', 'woooe'); ?></a>
    </div>
</div>