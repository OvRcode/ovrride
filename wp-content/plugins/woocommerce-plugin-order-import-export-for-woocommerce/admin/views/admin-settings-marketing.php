<?php
if ( ! defined( 'WPINC' ) ) {
    die;
}
?>
<div class="wt-iew-tab-content" data-id="<?php echo $target_id;?>">
    <div class="wt-ier-wrapper">
    <h2 class="wt-ier-page-title"><?php _e('Make a complete import and export for your store\'s valuable data, all in one place.');?></h2>
    <p class="wt-ier-subp"><?php _e('Get access to advanced features and premium support. Upgrade to the premium version.');?></p>
    <div class="wt-ier-row">
      <div class="wt-ier-col-12 wt-ier-col-lg-4 wt-ier-lg-4 wt-ier-mb-lg-0">
        <div class="wt-ier-p-5 wt-ier-box-wrapper wt-ier-box-highlight">
          <img src="<?php echo WT_O_IEW_PLUGIN_URL; ?>assets/images/upgrade/product-ie.svg" class="wt-ier-thumbnails">
          <h3 class="wt-ier-plugin-name"><?php _e('Product import export plugin for wooCommerce');?></h3>
          <p class="wt-ier-p"><?php _e('Easily export and import all types of products and product information.');?></p>
          <div class="wt-ier-d-sm-flex wt-ier-btn-group">
            <a href="https://www.webtoffee.com/product/product-import-export-woocommerce/?utm_source=free_plugin_revamp&utm_medium=basic_revamp&utm_campaign=Product_Import_Export&utm_content=<?php echo WT_O_IEW_VERSION; ?>" target="_blank" class="wt-ier-primary-btn wt-ier-btn"><?php _e('Get Premium');?></a>
            <a href="" class="wt-ier-secondary-btn wt-ier-btn" data-toggle="modal" data-target="#wt-ier-comparison-modal-product"><?php _e('Compare with basic');?></a>
          </div>
        </div>
      </div>
      <div class="wt-ier-col-12 wt-ier-col-lg-4 wt-ier-lg-4 wt-ier-mb-lg-0">
        <div class="wt-ier-p-5 wt-ier-box-wrapper wt-ier-box-highlight">
          <img src="<?php echo WT_O_IEW_PLUGIN_URL; ?>assets/images/upgrade/customer-ie.svg" class="wt-ier-thumbnails">
          <h3 class="wt-ier-plugin-name"><?php _e('Customer Import Export for WooCommerce');?></h3>
          <p class="wt-ier-p"><?php _e('Easily import or export WordPress User and WooCommerce Customer data from/to a CSV file!');?></p>
          <div class="wt-ier-d-sm-flex wt-ier-btn-group">
            <a href="https://www.webtoffee.com/product/wordpress-users-woocommerce-customers-import-export/?utm_source=free_plugin_revamp&utm_medium=basic_revamp&utm_campaign=User_Import_Export&utm_content=<?php echo WT_O_IEW_VERSION; ?>" class="wt-ier-primary-btn wt-ier-btn" target="_blank"><?php _e('Get Premium');?></a>
            <a href="" class="wt-ier-secondary-btn wt-ier-btn" data-toggle="modal" data-target="#wt-ier-comparison-modal-customer"><?php _e('Compare with basic');?></a>
          </div>
        </div>
      </div>
      <div class="wt-ier-col-12 wt-ier-col-lg-4 wt-ier-lg-4 wt-ier-mb-lg-0">
        <div class="wt-ier-p-5 wt-ier-box-wrapper wt-ier-box-highlight">
          <img src="<?php echo WT_O_IEW_PLUGIN_URL; ?>assets/images/upgrade/order-ie.svg" class="wt-ier-thumbnails">
          <h3 class="wt-ier-plugin-name"><?php _e('Order, Coupon, Subscription Export Import for WooCommerce');?></h3>
          <p class="wt-ier-p"><?php _e('Export or Import WooCommerce orders, Coupons and Subscriptions.');?></p>
          <div class="wt-ier-d-sm-flex wt-ier-btn-group">
            <a  href="https://www.webtoffee.com/product/order-import-export-plugin-for-woocommerce/?utm_source=free_plugin_revamp&utm_medium=basic_revamp&utm_campaign=Order_Import_Export&utm_content=<?php echo WT_O_IEW_VERSION; ?>" class="wt-ier-primary-btn wt-ier-btn" target="_blank"><?php _e('Get Premium');?></a>
            <a href="" class="wt-ier-secondary-btn wt-ier-btn" data-toggle="modal" data-target="#wt-ier-comparison-modal-order"><?php _e('Compare with basic');?></a>
          </div>
        </div>
      </div>
    </div>
    <!--------product imp-exp comparison table --------->
    <div id="wt-ier-comparison-modal-product" class="wt-ier-modal">
      <div class="wt-ier-modal-content">
        <div class="wt-ier-resposive-table">
          <table class="wt-ier-table">

            <thead>
              <tr class="wt-ier-top-tr">
                <td></td>
                <td colspan="3"><span class="wt-ier-close">&times;</span></td>
              </tr>
              <tr>
                <th><?php _e('Features');?></th>
                <th><?php _e('Free');?></th>
                <th><img src="<?php echo WT_O_IEW_PLUGIN_URL; ?>assets/images/upgrade/pre-icon.svg" class="wt-ier-pre-icon"><?php _e('Premium');?></th>
                <th><?php _e('Import Export Suite');?></th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td><?php _e('Import and export products');?></td>
                <td><img src="<?php echo WT_O_IEW_PLUGIN_URL; ?>assets/images/upgrade/yes.svg"></td>
                  <td><img src="<?php echo WT_O_IEW_PLUGIN_URL; ?>assets/images/upgrade/yes.svg"></td>
                  <td>
                    <ul>
                      <li><?php _e('Products');?></li>
                      <li><?php _e('Reviews');?></li>
                      <li><?php _e('Orders');?></li>
                      <li><?php _e('Coupons');?></li>
                      <li><?php _e('Subscriptions');?></li>
                      <li><?php _e('Users');?></li>
                    </ul>
                  </td>
                </tr>
                <tr>
                  <td><?php _e('Export and import with product images');?></td>
                  <td><img src="<?php echo WT_O_IEW_PLUGIN_URL; ?>assets/images/upgrade/yes.svg"></td>
                  <td><img src="<?php echo WT_O_IEW_PLUGIN_URL; ?>assets/images/upgrade/yes.svg"></td>
                  <td><img src="<?php echo WT_O_IEW_PLUGIN_URL; ?>assets/images/upgrade/yes.svg"></td>
                </tr>
                <tr>
                  <td><?php _e('Batch import/export');?></td>
                  <td><img src="<?php echo WT_O_IEW_PLUGIN_URL; ?>assets/images/upgrade/yes.svg"></td>
                  <td><img src="<?php echo WT_O_IEW_PLUGIN_URL; ?>assets/images/upgrade/yes.svg"></td>
                  <td><img src="<?php echo WT_O_IEW_PLUGIN_URL; ?>assets/images/upgrade/yes.svg"></td>
                </tr>
                <tr>
                  <td><?php _e('Quick import/export');?></td>
                  <td><img src="<?php echo WT_O_IEW_PLUGIN_URL; ?>assets/images/upgrade/yes.svg"></td>
                  <td><img src="<?php echo WT_O_IEW_PLUGIN_URL; ?>assets/images/upgrade/yes.svg"></td>
                  <td><img src="<?php echo WT_O_IEW_PLUGIN_URL; ?>assets/images/upgrade/yes.svg"></td>
                </tr>
                <tr>
                  <td><?php _e('Bulk product update');?></td>
                  <td><img src="<?php echo WT_O_IEW_PLUGIN_URL; ?>assets/images/upgrade/yes.svg"></td>
                  <td><img src="<?php echo WT_O_IEW_PLUGIN_URL; ?>assets/images/upgrade/yes.svg"></td>
                  <td><img src="<?php echo WT_O_IEW_PLUGIN_URL; ?>assets/images/upgrade/yes.svg"></td>
                </tr>
                <tr>
                  <td><?php _e('Advanced import/export filters');?></td>
                  <td><img src="<?php echo WT_O_IEW_PLUGIN_URL; ?>assets/images/upgrade/yes.svg"><p><?php _e('Limited');?></p></td>
                  <td><img src="<?php echo WT_O_IEW_PLUGIN_URL; ?>assets/images/upgrade/yes.svg"></td>
                  <td><img src="<?php echo WT_O_IEW_PLUGIN_URL; ?>assets/images/upgrade/yes.svg"></td>
                </tr>
                <tr>
                  <td><?php _e('Option to save a template for future import/exports');?></td>
                  <td><img src="<?php echo WT_O_IEW_PLUGIN_URL; ?>assets/images/upgrade/yes.svg"></td>
                  <td><img src="<?php echo WT_O_IEW_PLUGIN_URL; ?>assets/images/upgrade/yes.svg"></td>
                  <td><img src="<?php echo WT_O_IEW_PLUGIN_URL; ?>assets/images/upgrade/yes.svg"></td>
                </tr>
                <tr>
                  <td><?php _e('Export/Import mapping');?></td>
                  <td><img src="<?php echo WT_O_IEW_PLUGIN_URL; ?>assets/images/upgrade/yes.svg"></td>
                  <td><img src="<?php echo WT_O_IEW_PLUGIN_URL; ?>assets/images/upgrade/yes.svg"></td>
                  <td><img src="<?php echo WT_O_IEW_PLUGIN_URL; ?>assets/images/upgrade/yes.svg"></td>
                </tr>
                <tr>
                  <td><?php _e('Import by uploading CSV');?></td>
                  <td><img src="<?php echo WT_O_IEW_PLUGIN_URL; ?>assets/images/upgrade/yes.svg"></td>
                  <td><img src="<?php echo WT_O_IEW_PLUGIN_URL; ?>assets/images/upgrade/yes.svg"></td>
                  <td><img src="<?php echo WT_O_IEW_PLUGIN_URL; ?>assets/images/upgrade/yes.svg"></td>
                </tr>
                <tr>
                  <td><?php _e('Import/Export history');?></td>
                  <td><img src="<?php echo WT_O_IEW_PLUGIN_URL; ?>assets/images/upgrade/yes.svg"></td>
                  <td><img src="<?php echo WT_O_IEW_PLUGIN_URL; ?>assets/images/upgrade/yes.svg"></td>
                  <td><img src="<?php echo WT_O_IEW_PLUGIN_URL; ?>assets/images/upgrade/yes.svg"></td>
                </tr>
                <tr>
                  <td><?php _e('Debug logs');?></td>
                  <td><img src="<?php echo WT_O_IEW_PLUGIN_URL; ?>assets/images/upgrade/yes.svg"></td>
                  <td><img src="<?php echo WT_O_IEW_PLUGIN_URL; ?>assets/images/upgrade/yes.svg"></td>
                  <td><img src="<?php echo WT_O_IEW_PLUGIN_URL; ?>assets/images/upgrade/yes.svg"></td>
                </tr>
                <tr>
                  <td><?php _e('Supports multiple file formats');?></td>
                  <td><img src="<?php echo WT_O_IEW_PLUGIN_URL; ?>assets/images/upgrade/no.svg"><p><?php _e('Only supports CSV');?></p></td>
                  <td><img src="<?php echo WT_O_IEW_PLUGIN_URL; ?>assets/images/upgrade/yes.svg"></td>
                  <td><img src="<?php echo WT_O_IEW_PLUGIN_URL; ?>assets/images/upgrade/yes.svg"></td>
                </tr>
                <tr>
                  <td><?php _e('Import/Export file via FTP/SFTP');?></td>
                  <td><img src="<?php echo WT_O_IEW_PLUGIN_URL; ?>assets/images/upgrade/no.svg"></td>
                  <td><img src="<?php echo WT_O_IEW_PLUGIN_URL; ?>assets/images/upgrade/yes.svg"></td>
                  <td><img src="<?php echo WT_O_IEW_PLUGIN_URL; ?>assets/images/upgrade/yes.svg"></td>
                </tr>
                <tr>
                  <td><?php _e('Import from URL');?></td>
                  <td><img src="<?php echo WT_O_IEW_PLUGIN_URL; ?>assets/images/upgrade/no.svg"></td>
                  <td><img src="<?php echo WT_O_IEW_PLUGIN_URL; ?>assets/images/upgrade/yes.svg"></td>
                  <td><img src="<?php echo WT_O_IEW_PLUGIN_URL; ?>assets/images/upgrade/yes.svg"></td>
                </tr>
                <tr>
                  <td><?php _e('Export product images as a zip');?></td>
                  <td><img src="<?php echo WT_O_IEW_PLUGIN_URL; ?>assets/images/upgrade/no.svg"></td>
                  <td><img src="<?php echo WT_O_IEW_PLUGIN_URL; ?>assets/images/upgrade/yes.svg"></td>
                  <td><img src="<?php echo WT_O_IEW_PLUGIN_URL; ?>assets/images/upgrade/yes.svg"></td>
                </tr>
                <tr>
                  <td><?php _e('Choose delimiter');?></td>
                  <td><img src="<?php echo WT_O_IEW_PLUGIN_URL; ?>assets/images/upgrade/yes.svg"></td>
                  <td><img src="<?php echo WT_O_IEW_PLUGIN_URL; ?>assets/images/upgrade/yes.svg"></td>
                  <td><img src="<?php echo WT_O_IEW_PLUGIN_URL; ?>assets/images/upgrade/yes.svg"></td>
                </tr>
                <tr>
                  <td><?php _e('Support for multiple product types');?></td>
                  <td><img src="<?php echo WT_O_IEW_PLUGIN_URL; ?>assets/images/upgrade/no.svg"></td>
                  <td><img src="<?php echo WT_O_IEW_PLUGIN_URL; ?>assets/images/upgrade/yes.svg"></td>
                  <td><img src="<?php echo WT_O_IEW_PLUGIN_URL; ?>assets/images/upgrade/yes.svg"></td>
                </tr>
                <tr>
                  <td><?php _e('Automatic scheduled import/export');?></td>
                  <td><img src="<?php echo WT_O_IEW_PLUGIN_URL; ?>assets/images/upgrade/no.svg"></td>
                  <td><img src="<?php echo WT_O_IEW_PLUGIN_URL; ?>assets/images/upgrade/yes.svg"></td>
                  <td><img src="<?php echo WT_O_IEW_PLUGIN_URL; ?>assets/images/upgrade/yes.svg"></td>
                </tr>
                <tr>
                  <td><?php _e('Import/Export hidden meta');?></td>
                  <td><img src="<?php echo WT_O_IEW_PLUGIN_URL; ?>assets/images/upgrade/no.svg"></td>
                  <td><img src="<?php echo WT_O_IEW_PLUGIN_URL; ?>assets/images/upgrade/yes.svg"></td>
                  <td><img src="<?php echo WT_O_IEW_PLUGIN_URL; ?>assets/images/upgrade/yes.svg"></td>
                </tr>
                <tr>
                  <td><?php _e('Third-party plugin filter support');?></td>
                  <td><img src="<?php echo WT_O_IEW_PLUGIN_URL; ?>assets/images/upgrade/no.svg"></td>
                  <td><img src="<?php echo WT_O_IEW_PLUGIN_URL; ?>assets/images/upgrade/yes.svg"></td>
                  <td><img src="<?php echo WT_O_IEW_PLUGIN_URL; ?>assets/images/upgrade/yes.svg"></td>
                </tr>
                <tr>
                  <td><?php _e('Import/Export Product ratings');?></td>
                  <td><img src="<?php echo WT_O_IEW_PLUGIN_URL; ?>assets/images/upgrade/no.svg"></td>
                  <td><img src="<?php echo WT_O_IEW_PLUGIN_URL; ?>assets/images/upgrade/yes.svg"></td>
                  <td><img src="<?php echo WT_O_IEW_PLUGIN_URL; ?>assets/images/upgrade/yes.svg"></td>
                </tr>
                <tr>
                  <td><?php _e('Import/export products reviews');?></td>
                  <td><img src="<?php echo WT_O_IEW_PLUGIN_URL; ?>assets/images/upgrade/no.svg"></td>
                  <td><img src="<?php echo WT_O_IEW_PLUGIN_URL; ?>assets/images/upgrade/yes.svg"></td>
                  <td><img src="<?php echo WT_O_IEW_PLUGIN_URL; ?>assets/images/upgrade/yes.svg"></td>
                </tr>

              </tbody>
            </table>
          </div>
        </div>
      </div>
      <!--------customer imp-exp comparison table --------->
      <div id="wt-ier-comparison-modal-customer" class="wt-ier-modal">
        <div class="wt-ier-modal-content">
          <div class="wt-ier-resposive-table">
            <table class="wt-ier-table">

              <thead>
                <tr class="wt-ier-top-tr">
                  <td></td>
                  <td colspan="3"><span class="wt-ier-close">&times;</span></td>
                </tr>
                <tr>
                  <th><?php _e('Features');?></th>
                  <th><?php _e('Free');?></th>
                  <th><img src="<?php echo WT_O_IEW_PLUGIN_URL; ?>assets/images/upgrade/pre-icon.svg" class="wt-ier-pre-icon"><?php _e('Premium');?></th>
                  <th><?php _e('Import Export Suite');?></th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td><?php _e('Import/Export Users');?></td>
                  <td><img src="<?php echo WT_O_IEW_PLUGIN_URL; ?>assets/images/upgrade/yes.svg"></td>
                  <td><img src="<?php echo WT_O_IEW_PLUGIN_URL; ?>assets/images/upgrade/yes.svg"></td>
                  <td>
                    <ul>
                      <li><?php _e('Products');?></li>
                      <li><?php _e('Reviews');?></li>
                      <li><?php _e('Orders');?></li>
                      <li><?php _e('Coupons');?></li>
                      <li><?php _e('Subscriptions');?></li>
                      <li><?php _e('Users');?></li>
                    </ul>
                  </td>
                </tr>
                <tr>
                  <td><?php _e('Import/Export customers');?></td>
                  <td><img src="<?php echo WT_O_IEW_PLUGIN_URL; ?>assets/images/upgrade/yes.svg"></td>
                  <td><img src="<?php echo WT_O_IEW_PLUGIN_URL; ?>assets/images/upgrade/yes.svg"></td>
                  <td><img src="<?php echo WT_O_IEW_PLUGIN_URL; ?>assets/images/upgrade/yes.svg"></td>
                </tr>
                <tr>
                  <td><?php _e('Retain user password on Import/Export');?></td>
                  <td><img src="<?php echo WT_O_IEW_PLUGIN_URL; ?>assets/images/upgrade/yes.svg"></td>
                  <td><img src="<?php echo WT_O_IEW_PLUGIN_URL; ?>assets/images/upgrade/yes.svg"></td>
                  <td><img src="<?php echo WT_O_IEW_PLUGIN_URL; ?>assets/images/upgrade/yes.svg"></td>
                </tr>
                <tr>
                  <td><?php _e('Bulk update data');?></td>
                  <td><img src="<?php echo WT_O_IEW_PLUGIN_URL; ?>assets/images/upgrade/no.svg"></td>
                  <td><img src="<?php echo WT_O_IEW_PLUGIN_URL; ?>assets/images/upgrade/yes.svg"></td>
                  <td><img src="<?php echo WT_O_IEW_PLUGIN_URL; ?>assets/images/upgrade/yes.svg"></td>
                </tr>
                <tr>
                  <td><?php _e('Evaluation fields');?></td>
                  <td><img src="<?php echo WT_O_IEW_PLUGIN_URL; ?>assets/images/upgrade/yes.svg"></td>
                  <td><img src="<?php echo WT_O_IEW_PLUGIN_URL; ?>assets/images/upgrade/yes.svg"></td>
                  <td><img src="<?php echo WT_O_IEW_PLUGIN_URL; ?>assets/images/upgrade/yes.svg"></td>
                </tr>
                <tr>
                  <td><?php _e('Advanced filters');?></td>
                  <td><img src="<?php echo WT_O_IEW_PLUGIN_URL; ?>assets/images/upgrade/yes.svg"><p><?php _e('Limited options');?></p></td>
                  <td><img src="<?php echo WT_O_IEW_PLUGIN_URL; ?>assets/images/upgrade/yes.svg"></td>
                  <td><img src="<?php echo WT_O_IEW_PLUGIN_URL; ?>assets/images/upgrade/yes.svg"></td>
                </tr>
                <tr>
                  <td><?php _e('Quick Import/Export');?></td>
                  <td><img src="<?php echo WT_O_IEW_PLUGIN_URL; ?>assets/images/upgrade/yes.svg"></td>
                  <td><img src="<?php echo WT_O_IEW_PLUGIN_URL; ?>assets/images/upgrade/yes.svg"></td>
                  <td><img src="<?php echo WT_O_IEW_PLUGIN_URL; ?>assets/images/upgrade/yes.svg"></td>
                </tr>
                <tr>
                  <td><?php _e('Import/Export with pre-saved template');?></td>
                  <td><img src="<?php echo WT_O_IEW_PLUGIN_URL; ?>assets/images/upgrade/yes.svg"></td>
                  <td><img src="<?php echo WT_O_IEW_PLUGIN_URL; ?>assets/images/upgrade/yes.svg"></td>
                  <td><img src="<?php echo WT_O_IEW_PLUGIN_URL; ?>assets/images/upgrade/yes.svg"></td>
                </tr>
                <tr>
                  <td><?php _e('View Import/Export history');?></td>
                  <td><img src="<?php echo WT_O_IEW_PLUGIN_URL; ?>assets/images/upgrade/yes.svg"></td>
                  <td><img src="<?php echo WT_O_IEW_PLUGIN_URL; ?>assets/images/upgrade/yes.svg"></td>
                  <td><img src="<?php echo WT_O_IEW_PLUGIN_URL; ?>assets/images/upgrade/yes.svg"></td>
                </tr>
                <tr>
                  <td><?php _e('View debug log');?></td>
                  <td><img src="<?php echo WT_O_IEW_PLUGIN_URL; ?>assets/images/upgrade/yes.svg"></td>
                  <td><img src="<?php echo WT_O_IEW_PLUGIN_URL; ?>assets/images/upgrade/yes.svg"></td>
                  <td><img src="<?php echo WT_O_IEW_PLUGIN_URL; ?>assets/images/upgrade/yes.svg"></td>
                </tr>
                <tr>
                  <td><?php _e('Batch Import/Export');?></td>
                  <td><img src="<?php echo WT_O_IEW_PLUGIN_URL; ?>assets/images/upgrade/yes.svg"></td>
                  <td><img src="<?php echo WT_O_IEW_PLUGIN_URL; ?>assets/images/upgrade/yes.svg"></td>
                  <td><img src="<?php echo WT_O_IEW_PLUGIN_URL; ?>assets/images/upgrade/yes.svg"></td>
                </tr> 
                <tr>
                  <td><?php _e('Customer Notification via email');?></td>
                  <td><img src="<?php echo WT_O_IEW_PLUGIN_URL; ?>assets/images/upgrade/no.svg"></td>
                  <td><img src="<?php echo WT_O_IEW_PLUGIN_URL; ?>assets/images/upgrade/yes.svg"></td>
                  <td><img src="<?php echo WT_O_IEW_PLUGIN_URL; ?>assets/images/upgrade/yes.svg"></td>
                </tr> 
                <tr>
                  <td><?php _e('Supports multiple file formats');?></td>
                  <td><img src="<?php echo WT_O_IEW_PLUGIN_URL; ?>assets/images/upgrade/no.svg"><p><?php _e('CSV only');?></p></td>
                  <td><img src="<?php echo WT_O_IEW_PLUGIN_URL; ?>assets/images/upgrade/yes.svg"></td>
                  <td><img src="<?php echo WT_O_IEW_PLUGIN_URL; ?>assets/images/upgrade/yes.svg"></td>
                </tr>
                <tr>
                  <td><?php _e('Choose delimiter');?></td>
                  <td><img src="<?php echo WT_O_IEW_PLUGIN_URL; ?>assets/images/upgrade/yes.svg"></td>
                  <td><img src="<?php echo WT_O_IEW_PLUGIN_URL; ?>assets/images/upgrade/yes.svg"></td>
                  <td><img src="<?php echo WT_O_IEW_PLUGIN_URL; ?>assets/images/upgrade/yes.svg"></td>
                </tr>
                <tr>
                  <td><?php _e('Import from URL');?></td>
                  <td><img src="<?php echo WT_O_IEW_PLUGIN_URL; ?>assets/images/upgrade/no.svg"></td>
                  <td><img src="<?php echo WT_O_IEW_PLUGIN_URL; ?>assets/images/upgrade/yes.svg"></td>
                  <td><img src="<?php echo WT_O_IEW_PLUGIN_URL; ?>assets/images/upgrade/yes.svg"></td>
                </tr>
                <tr>
                  <td><?php _e('Import/export via FTP');?></td>
                  <td><img src="<?php echo WT_O_IEW_PLUGIN_URL; ?>assets/images/upgrade/no.svg"></td>
                  <td><img src="<?php echo WT_O_IEW_PLUGIN_URL; ?>assets/images/upgrade/yes.svg"></td>
                  <td><img src="<?php echo WT_O_IEW_PLUGIN_URL; ?>assets/images/upgrade/yes.svg"></td>
                </tr>
                <tr>
                  <td><?php _e('Scheduled Import/Export using Cron');?></td>
                  <td><img src="<?php echo WT_O_IEW_PLUGIN_URL; ?>assets/images/upgrade/no.svg"></td>
                  <td><img src="<?php echo WT_O_IEW_PLUGIN_URL; ?>assets/images/upgrade/yes.svg"></td>
                  <td><img src="<?php echo WT_O_IEW_PLUGIN_URL; ?>assets/images/upgrade/yes.svg"></td>
                </tr>
                <tr>
                  <td><?php _e('Third-party plugin filter support');?></td>
                  <td><img src="<?php echo WT_O_IEW_PLUGIN_URL; ?>assets/images/upgrade/no.svg"></td>
                  <td><img src="<?php echo WT_O_IEW_PLUGIN_URL; ?>assets/images/upgrade/yes.svg"></td>
                  <td><img src="<?php echo WT_O_IEW_PLUGIN_URL; ?>assets/images/upgrade/yes.svg"></td>
                </tr>
                
              </tbody>
            </table>
          </div>
        </div>
      </div>
      <!--------order imp-exp comparison table --------->
      <div id="wt-ier-comparison-modal-order" class="wt-ier-modal">
        <div class="wt-ier-modal-content">
          <div class="wt-ier-resposive-table">
            <table class="wt-ier-table">

              <thead>
                <tr class="wt-ier-top-tr">
                  <td></td>
                  <td colspan="3"><span class="wt-ier-close">&times;</span></td>
                </tr>
                <tr>
                  <th><?php _e('Features');?></th>
                  <th><?php _e('Free');?></th>
                  <th><img src="<?php echo WT_O_IEW_PLUGIN_URL; ?>assets/images/upgrade/pre-icon.svg" class="wt-ier-pre-icon"><?php _e('Premium');?></th>
                  <th><?php _e('Import Export Suite');?></th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td><?php _e('Export and Import orders');?> </td>
                  <td><img src="<?php echo WT_O_IEW_PLUGIN_URL; ?>assets/images/upgrade/yes.svg"></td>
                  <td><img src="<?php echo WT_O_IEW_PLUGIN_URL; ?>assets/images/upgrade/yes.svg"></td>
                  <td>
                    <ul>
                      <li><?php _e('Products');?></li>
                      <li><?php _e('Reviews');?></li>
                      <li><?php _e('Orders');?></li>
                      <li><?php _e('Coupons');?></li>
                      <li><?php _e('Subscriptions');?></li>
                      <li><?php _e('Users');?></li>
                    </ul>
                  </td>
                </tr>
                <tr>
                  <td><?php _e('Export and Import Coupons');?> </td>
                  <td><img src="<?php echo WT_O_IEW_PLUGIN_URL; ?>assets/images/upgrade/yes.svg"></td>
                  <td><img src="<?php echo WT_O_IEW_PLUGIN_URL; ?>assets/images/upgrade/yes.svg"></td>
                  <td><img src="<?php echo WT_O_IEW_PLUGIN_URL; ?>assets/images/upgrade/yes.svg"></td>
                </tr>
                <tr>
                  <td><?php _e('Batch Import/Export');?></td>
                  <td><img src="<?php echo WT_O_IEW_PLUGIN_URL; ?>assets/images/upgrade/yes.svg"></td>
                  <td><img src="<?php echo WT_O_IEW_PLUGIN_URL; ?>assets/images/upgrade/yes.svg"></td>
                  <td><img src="<?php echo WT_O_IEW_PLUGIN_URL; ?>assets/images/upgrade/yes.svg"></td>
                </tr>
                <tr>
                  <td><?php _e('Quick Import/Export');?></td>
                  <td><img src="<?php echo WT_O_IEW_PLUGIN_URL; ?>assets/images/upgrade/yes.svg"></td>
                  <td><img src="<?php echo WT_O_IEW_PLUGIN_URL; ?>assets/images/upgrade/yes.svg"></td>
                  <td><img src="<?php echo WT_O_IEW_PLUGIN_URL; ?>assets/images/upgrade/yes.svg"></td>
                </tr>
                <tr>
                  <td><?php _e('Easy Import/export with pre-saved template');?></td>
                  <td><img src="<?php echo WT_O_IEW_PLUGIN_URL; ?>assets/images/upgrade/yes.svg"></td>
                  <td><img src="<?php echo WT_O_IEW_PLUGIN_URL; ?>assets/images/upgrade/yes.svg"></td>
                  <td><img src="<?php echo WT_O_IEW_PLUGIN_URL; ?>assets/images/upgrade/yes.svg"></td>
                </tr>
                <tr>
                  <td><?php _e('Export/Import column mapping');?></td>
                  <td><img src="<?php echo WT_O_IEW_PLUGIN_URL; ?>assets/images/upgrade/yes.svg"></td>
                  <td><img src="<?php echo WT_O_IEW_PLUGIN_URL; ?>assets/images/upgrade/yes.svg"></td>
                  <td><img src="<?php echo WT_O_IEW_PLUGIN_URL; ?>assets/images/upgrade/yes.svg"></td>
                </tr>
                <tr>
                  <td><?php _e('Bulk update of data');?></td>
                  <td><img src="<?php echo WT_O_IEW_PLUGIN_URL; ?>assets/images/upgrade/yes.svg"></td>
                  <td><img src="<?php echo WT_O_IEW_PLUGIN_URL; ?>assets/images/upgrade/yes.svg"></td>
                  <td><img src="<?php echo WT_O_IEW_PLUGIN_URL; ?>assets/images/upgrade/yes.svg"></td>
                </tr>
                <tr>
                  <td><?php _e('Support evaluation fields');?></td>
                  <td><img src="<?php echo WT_O_IEW_PLUGIN_URL; ?>assets/images/upgrade/yes.svg"></td>
                  <td><img src="<?php echo WT_O_IEW_PLUGIN_URL; ?>assets/images/upgrade/yes.svg"></td>
                  <td><img src="<?php echo WT_O_IEW_PLUGIN_URL; ?>assets/images/upgrade/yes.svg"></td>
                </tr>
                <tr>
                  <td><?php _e('View debug logs');?></td>
                  <td><img src="<?php echo WT_O_IEW_PLUGIN_URL; ?>assets/images/upgrade/yes.svg"></td>
                  <td><img src="<?php echo WT_O_IEW_PLUGIN_URL; ?>assets/images/upgrade/yes.svg"></td>
                  <td><img src="<?php echo WT_O_IEW_PLUGIN_URL; ?>assets/images/upgrade/yes.svg"></td>
                </tr>
                <tr>
                  <td><?php _e('Import/Export history');?></td>
                  <td><img src="<?php echo WT_O_IEW_PLUGIN_URL; ?>assets/images/upgrade/yes.svg"></td>
                  <td><img src="<?php echo WT_O_IEW_PLUGIN_URL; ?>assets/images/upgrade/yes.svg"></td>
                  <td><img src="<?php echo WT_O_IEW_PLUGIN_URL; ?>assets/images/upgrade/yes.svg"></td>
                </tr>
                <tr>
                  <td><?php _e('Advanced filters');?></td>
                  <td><img src="<?php echo WT_O_IEW_PLUGIN_URL; ?>assets/images/upgrade/yes.svg"><p><?php _e('Limited');?></p></td>
                  <td><img src="<?php echo WT_O_IEW_PLUGIN_URL; ?>assets/images/upgrade/yes.svg"></td>
                  <td><img src="<?php echo WT_O_IEW_PLUGIN_URL; ?>assets/images/upgrade/yes.svg"></td>
                </tr>
                <tr>
                  <td><?php _e('Supports multiple file formats');?></td>
                  <td><img src="<?php echo WT_O_IEW_PLUGIN_URL; ?>assets/images/upgrade/no.svg"><p><?php _e('CSV only');?></p></td>
                  <td><img src="<?php echo WT_O_IEW_PLUGIN_URL; ?>assets/images/upgrade/yes.svg"></td>
                  <td><img src="<?php echo WT_O_IEW_PLUGIN_URL; ?>assets/images/upgrade/yes.svg"></td>
                </tr>
                <tr>
                  <td><?php _e('Export/Import subscription orders');?> </td>
                  <td><img src="<?php echo WT_O_IEW_PLUGIN_URL; ?>assets/images/upgrade/no.svg"></td>
                  <td><img src="<?php echo WT_O_IEW_PLUGIN_URL; ?>assets/images/upgrade/yes.svg"></td>
                  <td><img src="<?php echo WT_O_IEW_PLUGIN_URL; ?>assets/images/upgrade/yes.svg"></td>
                </tr>
                <tr>
                  <td><?php _e('Import and Export via FTP/SFTP');?></td>
                  <td><img src="<?php echo WT_O_IEW_PLUGIN_URL; ?>assets/images/upgrade/no.svg"></td>
                  <td><img src="<?php echo WT_O_IEW_PLUGIN_URL; ?>assets/images/upgrade/yes.svg"></td>
                  <td><img src="<?php echo WT_O_IEW_PLUGIN_URL; ?>assets/images/upgrade/yes.svg"></td>
                </tr>
                <tr>
                  <td><?php _e('Scheduled automatic Import/Export using Cron job');?></td>
                  <td><img src="<?php echo WT_O_IEW_PLUGIN_URL; ?>assets/images/upgrade/no.svg"></td>
                  <td><img src="<?php echo WT_O_IEW_PLUGIN_URL; ?>assets/images/upgrade/yes.svg"></td>
                  <td><img src="<?php echo WT_O_IEW_PLUGIN_URL; ?>assets/images/upgrade/yes.svg"></td>
                </tr>
                <tr>
                  <td><?php _e('Platform independent XML Import/Export');?> </td>
                  <td><img src="<?php echo WT_O_IEW_PLUGIN_URL; ?>assets/images/upgrade/no.svg"></td>
                  <td><img src="<?php echo WT_O_IEW_PLUGIN_URL; ?>assets/images/upgrade/yes.svg"></td>
                  <td><img src="<?php echo WT_O_IEW_PLUGIN_URL; ?>assets/images/upgrade/yes.svg"></td>
                </tr>
                <tr>
                  <td><?php _e('Multiple Import methods');?></td>
                  <td><img src="<?php echo WT_O_IEW_PLUGIN_URL; ?>assets/images/upgrade/no.svg"><p><?php _e('Upload CSV only');?></p></td>
                  <td><img src="<?php echo WT_O_IEW_PLUGIN_URL; ?>assets/images/upgrade/yes.svg"></td>
                  <td><img src="<?php echo WT_O_IEW_PLUGIN_URL; ?>assets/images/upgrade/yes.svg"></td>
                </tr>
                <tr>
                  <td><?php _e('Choose delimiter');?></td>
                  <td><img src="<?php echo WT_O_IEW_PLUGIN_URL; ?>assets/images/upgrade/yes.svg"></td>
                  <td><img src="<?php echo WT_O_IEW_PLUGIN_URL; ?>assets/images/upgrade/yes.svg"></td>
                  <td><img src="<?php echo WT_O_IEW_PLUGIN_URL; ?>assets/images/upgrade/yes.svg"></td>
                </tr>
                <tr>
                  <td><?php _e('Third-party plugin filter support');?></td>
                  <td><img src="<?php echo WT_O_IEW_PLUGIN_URL; ?>assets/images/upgrade/no.svg"></td>
                  <td><img src="<?php echo WT_O_IEW_PLUGIN_URL; ?>assets/images/upgrade/yes.svg"></td>
                  <td><img src="<?php echo WT_O_IEW_PLUGIN_URL; ?>assets/images/upgrade/yes.svg"></td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
      <!--------comparison table ends here--------->
      <div class=" wt-ier-box-wrapper wt-ier-mt-5 wt-ier-suite">
        <div class="wt-ier-row wt-ier-p-5">
          <div class="wt-ier-col-12 wt-ier-col-lg-6">
            <img src="<?php echo WT_O_IEW_PLUGIN_URL; ?>assets/images/upgrade/suite.svg" class="wt-ier-thumbnails">
            <h2 class="wt-ier-page-title"><?php _e('Import Export Suite for WooCommerce');?></h2>
            <p class="wt-ier-p"><?php _e('WooCommerce Import Export Suite is an all-in-one bundle of plugins that will enable you to import and export WooCommerce products, product reviews, orders, customers, coupons, and subscriptions.');?></p>
            <a href="https://www.webtoffee.com/product/woocommerce-import-export-suite/?utm_source=free_plugin_revamp&utm_medium=basic_revamp&utm_campaign=Import_Export_Suite&utm_content=<?php echo WT_O_IEW_VERSION; ?>" class="wt-ier-primary-btn" target="_blank"><?php _e('Get Premium');?></a>
          </div>
        </div>
      </div>
    </div>
    <script>
    jQuery("a[data-toggle=modal]").click(function(e){
      e.preventDefault();
      var target=jQuery(this).attr('data-target');
      jQuery(target).css('display','block');
    });
    jQuery(document).click(function (e) {
      if (jQuery(e.target).is('.wt-ier-modal')) {
        jQuery('.wt-ier-modal').css('display','none');
      }

    });
    jQuery(".wt-ier-close").click(function (e) {
      jQuery(this).closest('.wt-ier-modal').css('display','none');
    });
  </script>
</div>