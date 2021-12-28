<?php
/**
 * Fired during plugin activation
 *
 * @link       https://www.webtoffee.com/
 * @since      1.0.0
 *
 * @package    Wt_Import_Export_For_Woo
 * @subpackage Wt_Import_Export_For_Woo/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Wt_Import_Export_For_Woo
 * @subpackage Wt_Import_Export_For_Woo/includes
 * @author     Webtoffee <info@webtoffee.com>
 */
if(!class_exists('Wt_Import_Export_For_Woo_Basic_Activator_Order')){
class Wt_Import_Export_For_Woo_Basic_Activator_Order {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() 
	{
		global $wpdb;
		delete_option('wt_o_iew_is_active'); /* remove if exists */

	    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );       
        if(is_multisite()) 
        {
            // Get all blogs in the network and activate plugin on each one
            $blog_ids = $wpdb->get_col( "SELECT blog_id FROM $wpdb->blogs" );
            foreach($blog_ids as $blog_id ) 
            {
                switch_to_blog( $blog_id );
                self::install_tables();
                restore_current_blog();
            }
        }
        else 
        {
            self::install_tables();
        }

        add_option('wt_o_iew_is_active', 1);
	}

	public static function install_tables()
	{
		global $wpdb;
		$charset_collate = $wpdb->get_charset_collate();
		//install necessary tables
		
		//creating table for saving template data================
        $search_query = "SHOW TABLES LIKE %s";
        $tb='wt_iew_mapping_template';
        $like = '%' . $wpdb->prefix.$tb.'%';
        $table_name = $wpdb->prefix.$tb;
        if(!$wpdb->get_results($wpdb->prepare($search_query, $like), ARRAY_N)) 
        {
            $sql_settings = "CREATE TABLE IF NOT EXISTS `$table_name` (
				`id` INT NOT NULL AUTO_INCREMENT, 
				`template_type` VARCHAR(255) NOT NULL, 
				`item_type` VARCHAR(255) NOT NULL, 
				`name` VARCHAR(255) NOT NULL, 
				`data` LONGTEXT NOT NULL, 
				PRIMARY KEY (`id`)
			) DEFAULT CHARSET=utf8;";
            dbDelta($sql_settings);
        }
        //creating table for saving template data================

        //creating table for saving export/import history================
        $search_query = "SHOW TABLES LIKE %s";
        $tb='wt_iew_action_history';
        $like = '%'.$wpdb->prefix.$tb.'%';
        $table_name = $wpdb->prefix.$tb;
        if(!$wpdb->get_results($wpdb->prepare($search_query, $like), ARRAY_N)) 
        {
            $sql_settings = "CREATE TABLE IF NOT EXISTS `$table_name` (
				`id` INT NOT NULL AUTO_INCREMENT, 
				`template_type` VARCHAR(255) NOT NULL, 
				`item_type` VARCHAR(255) NOT NULL,
				`file_name` VARCHAR(255) NOT NULL, 
				`created_at` INT NOT NULL DEFAULT '0', 
				`status` INT NOT NULL DEFAULT '0', 
				`status_text` VARCHAR(255) NOT NULL,
				`offset` INT NOT NULL DEFAULT '0', 
				`total` INT NOT NULL DEFAULT '0', 
				`data` LONGTEXT NOT NULL, 
				PRIMARY KEY (`id`)
			) DEFAULT CHARSET=utf8;";
            dbDelta($sql_settings);
        }
        //creating table for saving export/import history================

        //creating table for saving ftp details================
        $search_query = "SHOW TABLES LIKE %s";
        $tb='wt_iew_ftp';
        $like = '%'.$wpdb->prefix.$tb.'%';
        $table_name = $wpdb->prefix.$tb;
        if(!$wpdb->get_results($wpdb->prepare($search_query, $like), ARRAY_N)) 
        {
            $sql_settings = "CREATE TABLE IF NOT EXISTS `$table_name` (
				`id` INT NOT NULL AUTO_INCREMENT, 
				`name` VARCHAR(255) NOT NULL, 
				`server` VARCHAR(255) NOT NULL, 
				`user_name` VARCHAR(255) NOT NULL, 
				`password` VARCHAR(255) NOT NULL, 
				`port` INT NOT NULL DEFAULT '21', 
				`ftps` INT NOT NULL DEFAULT '0', 
				`is_sftp` INT NOT NULL DEFAULT '0', 
				`passive_mode` INT NOT NULL DEFAULT '0',
				`export_path` VARCHAR(255) NOT NULL, 
				`import_path` VARCHAR(255) NOT NULL,
				PRIMARY KEY (`id`)
			) DEFAULT CHARSET=utf8;";
            dbDelta($sql_settings);
        }
        //creating table for saving ftp details================


        //creating table for saving cron data================
        $search_query = "SHOW TABLES LIKE %s";
        $tb='wt_iew_cron';
        $like = '%'.$wpdb->prefix.$tb.'%';
        $table_name = $wpdb->prefix.$tb;
        if(!$wpdb->get_results($wpdb->prepare($search_query, $like), ARRAY_N)) 
        {
            $sql_settings = "CREATE TABLE IF NOT EXISTS `$table_name` (
				`id` INT NOT NULL AUTO_INCREMENT, 
				`status` INT NOT NULL DEFAULT '0',
				`old_status` INT NOT NULL DEFAULT '0',
				`action_type` VARCHAR(255) NOT NULL, 
				`schedule_type` VARCHAR(50) NOT NULL, 
				`item_type` VARCHAR(255) NOT NULL, 
				`data` LONGTEXT NOT NULL, 
				`start_time` INT NOT NULL, 
				`cron_data` TEXT NOT NULL,
				`last_run` INT NOT NULL,
				`next_offset` INT NOT NULL DEFAULT '0',
				`history_id_list` TEXT NOT NULL, 
				`history_id` INT NOT NULL, 
				PRIMARY KEY (`id`)
			) DEFAULT CHARSET=utf8;";
            dbDelta($sql_settings);
        }
        //creating table for saving cron data================
	}
}
}
