<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */


        if ( class_exists( 'WP_Importer' ) ) {
            
            class WC_CSV_Coupon_Import extends WP_Importer {
                var $id; // CSV attachment ID
                var $file_url; // CSV attachmente url
                var $import_page;

                // information to import from CSV file
                var $posts = array();

                // mappings from old information to new
                var $processed_terms = array();
                var $processed_posts = array();
                var $post_orphans = array();

                var $fetch_attachments = false;
                var $url_remap = array();

                // Counts
                var $log;
                var $merged;
                var $skipped = 0;
                var $imported = 0;
                
                
                public function __construct() { 
                        $this->import_page = 'woocommerce_coupon_csv';
                        ob_start();
                }
                
                /**
                * Registered callback function for the WordPress Importer
                *
                * Manages the three separate stages of the CSV import process
                */
                function dispatch() {
                        global $woocommerce_smart_coupon;     
                        $step = empty( $_GET['step'] ) ? 0 : (int) $_GET['step'];
                        
                        switch ( $step ) {
                                case 0:
                                        $this->greet();
                                        break;
                                case 1:
                                        check_admin_referer( 'import-upload' );
                                        if ( $this->handle_upload() )
                                                $this->import_options();	
                                        break;
                                case 2:
                                        check_admin_referer( 'import-woocommerce-coupon' );

                                        if(!isset($_POST['sc_export_and_import']) && !isset($_POST['generate_and_import']) ){
                                            
                                            $this->id = (int) $_POST['import_id'];
                                            $this->file_url = esc_attr( $_POST['import_url'] );
                                            
                                            if ( $this->id ) {
                                                $file = get_attached_file( $this->id );
                                            } else  {
                                               $file = ABSPATH . $this->file_url; 
                                            }
                                                
                                            
                                        } else {
                                            
                                            $file = $woocommerce_smart_coupon->export_coupon($_POST,'','');
                                        }
                                        
                                        if ( isset( $_POST['woo_sc_is_email_imported_coupons'] ) ) {
                                            update_option( 'woo_sc_is_email_imported_coupons', 'yes' );
                                        }

                                        add_filter( 'http_request_timeout', array( &$this, 'bump_request_timeout' ) );

                                        @set_time_limit(0);

                                        $this->import_start( $file );
                                        $this->import();
                                        
                                        $params = array('show_import_message' => true,
                                                        'imported' => $this->imported,
                                                        'skipped' => $this->skipped,
                                            
                                            );
                                        $url = add_query_arg( $params, admin_url('edit.php?post_type=shop_coupon') );
                                        
                                        if($this->imported > 0){
                                         ob_clean();
                                         wp_safe_redirect($url);
                                         exit;
                                    }
                        }
                }
                                
                /**
                * Display pre-import options
                */
                function import( ){
                        global $woocommerce, $wpdb;

                        wp_suspend_cache_invalidation( true );
                        echo '<div class="progress">';

                        foreach ( $this->parsed_data as $key => &$item ) {

                            $coupon = $this->parser->parse_coupon( $item );


                            if ( $coupon ) {
                                $this->process_coupon( $coupon );
                            } else {
                                $this->skipped++;
                            }

                            unset( $item, $coupon ); 
                        }
                        update_option( 'woo_sc_is_email_imported_coupons', 'no' );
                        $this->import_end();
                    
                }
                
                /**
                * Create new posts based on import information
                */
                function process_coupon( $post ){
                        global $woocommerce_smart_coupon;

                        // Get parent
                        $post_parent = (int) $post['post_parent'];
                        
                        if ( $post_parent ) {
                                // if we already know the parent, map it to the new local ID
                                if ( isset( $this->processed_posts[$post_parent] ) ) {
                                        $post_parent = $this->processed_posts[$post_parent];
                                // otherwise record the parent for later
                                } else {
                                        $this->post_orphans[intval($post['post_id'])] = $post_parent;
                                        $post_parent = 0;
                                }
                        }

                        $postdata = array(
                                'import_id' => $post['post_id'], 
                                'post_author' => get_current_user_id(), 
                                'post_date' => ( $post['post_date'] ) ? date( 'Y-m-d H:i:s', strtotime( $post['post_date'] )) : '',
                                'post_date_gmt' => ( $post['post_date_gmt'] ) ? date( 'Y-m-d H:i:s', strtotime( $post['post_date_gmt'] )) : '',
                                'post_content' => $post['post_content'],
                                'post_excerpt' => $post['post_excerpt'], 
                                'post_title' => $post['post_title'],
                                'post_name' => ( $post['post_name'] ) ? $post['post_name'] : sanitize_title( $post['post_title'] ),
                                'post_status' => $post['post_status'], 
                                'post_parent' => $post_parent, 
                                'menu_order' => $post['menu_order'],
                                'post_type' => 'shop_coupon', 
                                'post_password' => $post['post_password'],
                                'comment_status' => $post['comment_status'],
                        );

                        $post_id = wp_insert_post( $postdata, true );

                        if ( is_wp_error( $post_id ) ) {

                            $this->skipped++;
                            unset( $post );
                            return;

                        } 
                        
                        unset( $postdata );

                        // map pre-import ID to local ID
                        if (!isset($post['post_id'])) $post['post_id'] = (int) $post_id;
                        $this->processed_posts[intval($post['post_id'])] = (int) $post_id;

                        $coupon_code = $post['post_title'];

                        // add/update post meta
                        if ( ! empty( $post['postmeta'] ) && is_array( $post['postmeta'] ) ) {
                                foreach ( $post['postmeta'] as $meta ) {
                                        $key = apply_filters( 'import_post_meta_key', $meta['key'] );

                                        switch( $meta['key'] ) {

                                            case 'customer_email':
                                                $customer_emails = maybe_unserialize( $meta['value'] );
                                                break;

                                            case 'coupon_amount':
                                                $coupon_amount = maybe_unserialize( $meta['value'] );
                                                break;

                                            case 'discount_type':
                                                $discount_type = maybe_unserialize( $meta['value'] );
                                                break;
                                        }

                                        if ( $key ) {
                                                update_post_meta( $post_id, $key, maybe_unserialize( $meta['value'] ) );
                                        }
                                }

                                unset( $post['postmeta'] );
                        }

                        $is_email_imported_coupons = get_option( 'woo_sc_is_email_imported_coupons' );

                        if ( $is_email_imported_coupons == 'yes' && !empty( $customer_emails ) && !empty( $coupon_amount ) && !empty( $coupon_code ) && !empty( $discount_type ) ) {
                            $coupon = array(
                                            'amount'    => $coupon_amount,
                                            'code'      => $coupon_code
                                        );
                            $coupon_title = array();
                            foreach ( $customer_emails as $customer_email ) {
                                $coupon_title[ $customer_email ] = $coupon;
                            }
                            $woocommerce_smart_coupon->sa_email_coupon( $coupon_title, $discount_type );
                        }
                        
                        $this->imported++;
                        
                        unset( $post );

                }
                
                /**
                * Parses the CSV file and prepares us for the task of processing parsed data
                *
                * @param string $file Path to the CSV file for importing
                */
                function import_start( $file ) {
                    
                        if ( ! is_file($file) ) {
                            echo '<p><strong>' . __( 'Sorry, there has been an error.', 'wc_smart_coupons' ) . '</strong><br />';
                            echo __( 'The file does not exist, please try again.', 'wc_smart_coupons' ) . '</p>';
                            die();
                        }

                        $this->parser = new WC_Coupon_Parser( 'shop_coupon' );
                        $import_data = $this->parser->parse_data( $file );

                        $this->parsed_data = $import_data[0];
                        $this->raw_headers = $import_data[1];

                        unset( $import_data );

                        wp_defer_term_counting( true );
                        wp_defer_comment_counting( true );
                    
                }
                
                /**
                * Added to http_request_timeout filter to force timeout at 60 seconds during import
                * @return int 60
                */
                function bump_request_timeout() {
                        return 60;
                }
                
                /**
                * Performs post-import cleanup of files and the cache
                */
                function import_end() {
                    
                        wp_cache_flush();

                        wp_defer_term_counting( false );
                        wp_defer_comment_counting( false );

                        do_action( 'import_end' );
                        
                }
                
                /**
                * Handles the CSV upload and initial parsing of the file to prepare for
                * displaying author import options
                *
                * @return bool False if error uploading or invalid file, true otherwise
                */
                function handle_upload(){
                    
                        if ( empty( $_POST['file_url'] ) ) {
                            $file = wp_import_handle_upload();

                            if ( isset( $file['error'] ) ) {
                                    echo '<p><strong>' . __( 'Sorry, there has been an error.', 'wc_smart_coupons' ) . '</strong><br />';
                                    echo esc_html( $file['error'] ) . '</p>';
                                    return false;
                            }

                            $this->id = (int) $file['id'];

                        } else {

                            if ( file_exists( ABSPATH . $_POST['file_url'] ) ) {

                                    $this->file_url = esc_attr( $_POST['file_url'] );

                            } else {

                                    echo '<p><strong>' . __( 'Sorry, there has been an error.', 'wc_smart_coupons' ) . '</strong></p>';
                                    return false;

                            }
                        }

                        return true;
                }
                
                /**
                * Display pre-import options
                */
                function import_options(){
                        $j = 0;

                        if ( $this->id ) 
                                $file = get_attached_file( $this->id );
                        else 
                                $file = ABSPATH . $this->file_url;

                        // Set locale
                        $enc = mb_detect_encoding( $file, 'UTF-8, ISO-8859-1', true );
                        if ( $enc ) setlocale( LC_ALL, 'en_US.' . $enc );
                        @ini_set( 'auto_detect_line_endings', true );

                        if ( ( $handle = fopen( $file, "r" ) ) !== FALSE ) {

                            $row = $raw_headers = array();

                            $header = fgetcsv( $handle, 0 ); //gets header of the file

                            while ( ( $postmeta = fgetcsv( $handle, 0 ) ) !== FALSE ) {
                                foreach ( $header as $key => $heading ) { 
                                    if ( ! $heading ) continue;

                                    $s_heading = strtolower( $heading );
                                    $row[$s_heading] = ( isset( $postmeta[$key] ) ) ? $this->format_data_from_csv( $postmeta[$key], $enc ) : '';
                                    $raw_headers[ $s_heading ] = $heading;
                                } 
                                break;
                            }

                            fclose( $handle );
                        }
                        ?>
                        <form action="<?php echo admin_url( 'admin.php?import=' . $this->import_page . '&step=2'); ?>" method="post">
                                <?php wp_nonce_field( 'import-woocommerce-coupon' ); ?>
                                <input type="hidden" name="import_id" value="<?php echo $this->id; ?>" />
                                <input type="hidden" name="import_url" value="<?php echo $this->file_url; ?>" />

                                <h3><?php _e( 'Map Fields', 'wc_smart_coupons' ); ?></h3>
                                <p><?php _e( 'Here you can map your imported columns to coupon data fields.', 'wc_smart_coupons' ); ?></p>

                                <table class="widefat widefat_importer">
                                    <thead>
                                        <tr>
                                                <th><?php _e( 'Map to', 'wc_smart_coupons' ); ?></th>
                                                <th><?php _e( 'Column Header', 'wc_smart_coupons' ); ?></th>
                                                <th><?php _e( 'Column Value', 'wc_smart_coupons' ); ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                        <?php foreach ( $row as $key => $value ) { ?>
                                        <tr>
                                            <td width="25%">
                                                <?php
                                                    if ( strstr( $key, 'meta:' ) ) {

                                                        $column = trim( str_replace( 'meta:', '', $key ) );
                                                        printf(__('Custom Field: <strong>%s</strong>', 'wc_smart_coupons'), $column);

                                                    } else {
                                                        ?>
                                                                <select name="map_to[<?php echo $key; ?>]">
                                                                    <optgroup label="<?php _e( 'Post data', 'wc_smart_coupons' ); ?>">
                                                                            <option <?php selected( $key, 'post_id' ); selected( $key, 'id' ); ?>>post_id</option>
                                                                            <option <?php selected( $key, 'post_type' ); ?>>post_type</option>
                                                                            <option <?php selected( $key, 'menu_order' ); ?>>menu_order</option>
                                                                            <option <?php selected( $key, 'post_status' ); ?>>post_status</option>
                                                                            <option <?php selected( $key, 'post_title' ); ?>>post_title</option>
                                                                            <option <?php selected( $key, 'post_name' ); ?>>post_name</option>
                                                                            <option <?php selected( $key, 'post_date' ); ?>>post_date</option>
                                                                            <option <?php selected( $key, 'post_date_gmt' ); ?>>post_date_gmt</option>
                                                                            <option <?php selected( $key, 'post_content' ); ?>>post_content</option>
                                                                            <option <?php selected( $key, 'post_excerpt' ); ?>>post_excerpt</option>
                                                                            <option <?php selected( $key, 'post_parent' ); ?>>post_parent</option>
                                                                            <option <?php selected( $key, 'post_password' ); ?>>post_password</option>
                                                                            <option <?php selected( $key, 'comment_status' ); ?>>comment_status</option>
                                                                    </optgroup>
                                                                    <optgroup label="<?php _e( 'Coupon data', 'wc_smart_coupons' ); ?>">

                                                                            <option <?php selected( $key, 'discount_type' ); ?>>discount_type</option>
                                                                            <option <?php selected( $key, 'coupon_amount' ); ?>>coupon_amount</option>
                                                                            <option <?php selected( $key, 'individual_use' ); ?>>individual_use</option>
                                                                            <option <?php selected( $key, 'product_ids' ); ?>>product_ids</option>
                                                                            <option <?php selected( $key, 'exclude_product_ids' ); ?>>exclude_product_ids</option>
                                                                            <option <?php selected( $key, 'usage_limit' ); ?>>usage_limit</option>
                                                                            <option <?php selected( $key, 'expiry_date' ); ?>>expiry_date</option>
                                                                            <option <?php selected( $key, 'apply_before_tax' ); ?>>apply_before_tax</option>
                                                                            <option <?php selected( $key, 'free_shipping' ); ?>>free_shipping</option>
                                                                            <option <?php selected( $key, 'product_categories' ); ?>>product_categories</option>
                                                                            <option <?php selected( $key, 'exclude_product_categories' ); ?>>exclude_product_categories</option>
                                                                            <option <?php selected( $key, 'minimum_amount' ); ?>>minimum_amount</option>
                                                                            <option <?php selected( $key, 'customer_email' ); ?>>customer_email</option>

                                                                    </optgroup>
                                                                </select>
                                                        <?php
                                                    }
                                                ?>
                                            </td>
                                            <td width="25%"><?php echo $raw_headers[$key]; ?></td>
                                            <td><code><?php if ( $value != '' ) echo $value; else echo '-'; ?></code></td>

                                        </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                                <p>
                                    <label for="woo_sc_is_email_imported_coupons"><input type="checkbox" name="woo_sc_is_email_imported_coupons" id="woo_sc_is_email_imported_coupons"  /> 
                                                                    <?php _e( 'E-mail imported coupon codes to respective customers/users.', 'wc_smart_coupons' ); ?></label>
                                </p>
                                <p class="submit">
                                    <input type="submit" class="button" value="<?php esc_attr_e( 'Submit', 'wc_smart_coupons' ); ?>" />
                                </p>

                        </form>
                        <?php
                }
                
                //
                function format_data_from_csv( $data, $enc ) {
                        return ( $enc == 'UTF-8' ) ? $data : utf8_encode( $data );
                }
                
                /**
                * Display introductory text and file upload form
                */
                function greet() {
                    
                        echo '<div class="narrow">';
                        echo '<p>'.__( 'Choose a CSV (.csv) file to upload, then click Upload file and import.', 'wc_smart_coupons' ).'</p>';
                        //wp_import_upload_form( 'admin.php?import=woocommerce_csv&amp;step=1&amp;merge=' . ( ! empty( $_GET['merge'] ) ? 1 : 0 ) );

                        $action = 'admin.php?import=woocommerce_coupon_csv&amp;step=1';

                        $bytes = apply_filters( 'import_upload_size_limit', wp_max_upload_size() );
                        $size = wp_convert_bytes_to_hr( $bytes );
                        $upload_dir = wp_upload_dir();
                        if ( ! empty( $upload_dir['error'] ) ) :
                                ?><div class="error"><p><?php _e('Before you can upload your import file, you will need to fix the following error:', 'wc_smart_coupons'); ?></p>
                                <p><strong><?php echo $upload_dir['error']; ?></strong></p></div><?php
                        else :
                                ?>
                                <form enctype="multipart/form-data" id="import-upload-form" method="post" action="<?php echo esc_attr(wp_nonce_url($action, 'import-upload')); ?>">
                                        <table class="form-table">
                                                <tbody>
                                                        <tr>
                                                                <th>
                                                                        <label for="upload"><?php _e( 'Choose a file from your computer:', 'wc_smart_coupons' ); ?></label>
                                                                </th>
                                                                <td>
                                                                        <input type="file" id="upload" name="import" size="25" />
                                                                        <input type="hidden" name="action" value="save" />
                                                                        <input type="hidden" name="max_file_size" value="<?php echo $bytes; ?>" />
                                                                        <small><?php printf( __('Maximum size: %s', 'wc_smart_coupons' ), $size ); ?></small>
                                                                </td>
                                                        </tr>
                                                        <tr>
                                                                <th>
                                                                        <label for="file_url"><?php _e( 'OR enter path to file:', 'wc_smart_coupons' ); ?></label>
                                                                </th>
                                                                <td>
                                                                        <?php echo ' ' . ABSPATH . ' '; ?><input type="text" id="file_url" name="file_url" size="25" />
                                                                </td>
                                                        </tr>
                                                </tbody>
                                        </table>
                                        <p class="submit">
                                                <input type="submit" class="button" value="<?php esc_attr_e( 'Upload file and import', 'wc_smart_coupons' ); ?>" />
                                        </p>
                                </form>
                                <?php
                        endif;

                        echo '</div>';  
                
                }
                
                
            }
            
            $GLOBALS['wc_csv_coupon_import'] = new WC_CSV_Coupon_Import();
        }


?>
