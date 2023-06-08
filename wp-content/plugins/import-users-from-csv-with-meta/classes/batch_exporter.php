<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class ACUI_Batch_Exporter{
	protected $path = '';
	protected $filename = 'user-export.csv';
	protected $limit = 50;
	protected $exported_row_count = 0;
	protected $row_data = array();
	protected $total_rows = 0;
	protected $columns_to_export = array();
	
    protected $page = 1;
    protected $delimiter = ',';
    protected $role = '';
    protected $from = '';
    protected $to = '';
    protected $convert_timestamp = false;
    protected $datetime_format;
    protected $order_fields_alphabetically = false;
    protected $double_encapsulate_serialized_values = false;
    protected $filtered_columns = array();
    protected $orderby = '';
    protected $order = '';

    protected $user_data;
    protected $accepted_order_by;
    protected $woocommerce_default_user_meta_keys;
    protected $other_non_date_keys;
    
    function __construct() {
        add_filter( 'acui_export_columns', array( $this, 'maybe_order_columns_alphabetacally' ), 10, 2 );
        add_filter( 'acui_export_columns', array( $this, 'maybe_order_columns_filtered_columns_parameter' ), 11, 2 );
		add_filter( 'acui_export_data', array( $this, 'maybe_double_encapsulate_serialized_values' ), 8, 3 );
        add_filter( 'acui_export_data', array( $this, 'maybe_order_row_alphabetically' ), 10, 3 );
        add_filter( 'acui_export_data', array( $this, 'maybe_order_row_filtered_columns_parameter' ), 11, 5 );

		$this->user_data = array( "user_login", "user_email", "source_user_id", "user_pass", "user_nicename", "user_url", "user_registered", "display_name" );
        $this->accepted_order_by = array( 'ID', 'display_name', 'name', 'user_name', 'login', 'user_login', 'nicename', 'user_nicename', 'email', 'user_email', 'url', 'user_url', 'registered', 'user_registered', 'post_count' );
		$this->woocommerce_default_user_meta_keys = array( 'billing_first_name', 'billing_last_name', 'billing_email', 'billing_phone', 'billing_country', 'billing_address_1', 'billing_city', 'billing_state', 'billing_postcode', 'shipping_first_name', 'shipping_last_name', 'shipping_country', 'shipping_address_1', 'shipping_address_2', 'shipping_city', 'shipping_state', 'shipping_postcode' );
		$this->other_non_date_keys = array( 'shipping_phone', '_vat_number', '_billing_vat_number' );
        $this->total_rows = $this->get_total_rows();
	}

    function get_non_date_keys(){
		return apply_filters( 'acui_export_non_date_keys', array_merge( $this->user_data, $this->woocommerce_default_user_meta_keys, $this->other_non_date_keys ) );
	}

    function maybe_order_columns_alphabetacally( $row, $args ){
        if( !$args['order_fields_alphabetically'] )
			return $row;
		
		$first_two_columns = array_slice( $row, 0, 2 );
		$to_order_columns = array_unique( array_slice( $row, 2 ) );
		sort( $to_order_columns, SORT_LOCALE_STRING );

        return array_merge( $first_two_columns, $to_order_columns );
	}

    function maybe_order_columns_filtered_columns_parameter( $row, $args ){
        return ( !is_array( $args['filtered_columns'] ) || count( $args['filtered_columns'] ) == 0 ) ? $row : $args['filtered_columns'];
    }

	function maybe_order_row_alphabetically( $row, $user,$args ){
        if( !$args['order_fields_alphabetically'] )
			return $row;

        $row_sorted = array();
        foreach( $args['columns'] as $field ){
            $row_sorted[ $field ] = $row[ $field ];
        }

        return $row_sorted;
	}

    function maybe_order_row_filtered_columns_parameter( $row, $user, $args ){
        if( !is_array( $args['filtered_columns'] ) || count( $args['filtered_columns'] ) == 0 )
			return $row;

        $row_sorted = array();
        foreach( $args['filtered_columns'] as $field ){
            $row_sorted[ $field ] = $row[ $field ];
        }

        return $row_sorted;
	}

    function maybe_double_encapsulate_serialized_values( $row, $user, $args ){
        if( !$args['double_encapsulate_serialized_values'] )
			return $row;

        foreach( $args['columns'] as $field ){
            if( is_serialized( $row[ $field ] ) )
                $row[ $field ] = '"' . $row[ $field ] . '"';
        }

        return $row;
	}

    function load_columns(){
        $row = array();
		
		foreach ( $this->get_user_data() as $key ) {
            $row[ $key ] = $key;
		}

        if( count( $this->get_filtered_columns() ) == 0 || in_array( 'role', $this->get_filtered_columns() ) )
		    $row[ "role" ] = "role";

		foreach ( $this->get_user_meta_keys() as $key ) {
			$row[ $key ] = $key;
		}

		$this->set_columns_to_export( apply_filters( 'acui_export_columns', $row, array( 'order_fields_alphabetically' => $this->get_order_fields_alphabetically(), 'double_encapsulate_serialized_values' => $this->get_double_encapsulate_serialized_values(), 'filtered_columns' => $this->get_filtered_columns() ) ) );
    }

	function set_path( $path ){
		$this->path = $path;
	}

	function get_path(){
		return $this->path;
	}

    function set_columns_to_export( $columns ) {
		$this->columns_to_export = $columns;
	}

	function get_columns_to_export() {
		return $this->columns_to_export;
	}

    function set_delimiter( $delimiter ){
        switch ( $delimiter ) {
			case 'COMMA':
				$this->delimiter = ",";
				break;
			
			case 'COLON':
				$this->delimiter = ":";
				break;

			case 'SEMICOLON':
				$this->delimiter = ";";
				break;

			case 'TAB':
				$this->delimiter = "\t";
				break;

            default:
                $this->delimiter = ",";
                break;
		}
    }

    function get_delimiter() {
		return $this->delimiter;
	}

    function set_role( $role ){
        $this->role = $role;
    }

    function get_role(){
        return $this->role;
    }

    function set_from( $from ){
        $this->from = $from;
    }

    function get_from(){
        return $this->from;
    }

    function set_to( $to ){
        $this->to = $to;
    }

    function get_to(){
        return $this->to;
    }

    function set_convert_timestamp( $convert_timestamp ){
        $this->convert_timestamp = filter_var( $convert_timestamp, FILTER_VALIDATE_BOOLEAN );
    }

    function get_convert_timestamp(){
        return $this->convert_timestamp;
    }

    function set_datetime_format( $datetime_format ){
        $this->datetime_format = $datetime_format;
    }

    function get_datetime_format(){
        return $this->datetime_format;
    }

    function set_order_fields_alphabetically( $order_fields_alphabetically ){
        $this->order_fields_alphabetically = filter_var( $order_fields_alphabetically, FILTER_VALIDATE_BOOLEAN );
    }

    function get_order_fields_alphabetically(){
        return $this->order_fields_alphabetically;
    }

    function set_double_encapsulate_serialized_values( $double_encapsulate_serialized_values ){
        $this->double_encapsulate_serialized_values = filter_var( $double_encapsulate_serialized_values, FILTER_VALIDATE_BOOLEAN );
    }

    function get_double_encapsulate_serialized_values(){
        return $this->double_encapsulate_serialized_values;
    }

    function set_filtered_columns( $filtered_columns ){
        $filtered_columns = ( is_array( $filtered_columns ) ) ? array_map( 'sanitize_text_field', $filtered_columns ) : explode( ',', sanitize_text_field( $filtered_columns ) );

        if( empty( $filtered_columns[0] ) )
            $filtered_columns = array();

        $this->filtered_columns = array_map( 'trim', $filtered_columns );
    }

    function get_filtered_columns(){
        return $this->filtered_columns;
    }

    function set_orderby( $orderby ){
        $this->orderby = $orderby;
    }

    function get_orderby(){
        return $this->orderby;
    }

    function set_order( $order ){
        $this->order = $order;
    }

    function get_order(){
        return $this->order;
    }

    function get_total_rows(){
        $total_rows = get_transient( 'acui_export_total_rows' );

        if( empty( $total_rows ) ){
            $this->total_rows = $this->calculate_total();
        }
        else{
            $this->total_rows = $total_rows;
        }

        return $this->total_rows;
    }

    function get_total_steps(){
        return floor( $this->get_total_rows() / $this->get_limit() ) + 1;
    }

    function set_time_limit( $limit = 0 ) {
        if ( function_exists( 'set_time_limit' ) && false === strpos( ini_get( 'disable_functions' ), 'set_time_limit' ) && ! ini_get( 'safe_mode' ) ) { // phpcs:ignore PHPCompatibility.IniDirectives.RemovedIniDirectives.safe_modeDeprecatedRemoved
            @set_time_limit( $limit );
        }
    }

    function maybe_define_constant( $name, $value ) {
        if ( ! defined( $name ) ) {
            define( $name, $value );
        }
    }

    function set_nocache_constants() {
		$this->maybe_define_constant( 'DONOTCACHEPAGE', true );
		$this->maybe_define_constant( 'DONOTCACHEOBJECT', true );
		$this->maybe_define_constant( 'DONOTCACHEDB', true );
	}

    function send_nocache_headers() {
        $this->set_nocache_constants();
        nocache_headers();
    }

	function send_headers() {
		if ( function_exists( 'gc_enable' ) ) {
			gc_enable();
		}
		if ( function_exists( 'apache_setenv' ) ) {
			@apache_setenv( 'no-gzip', 1 );
		}
		@ini_set( 'zlib.output_compression', 'Off' );
		@ini_set( 'output_buffering', 'Off' );
		@ini_set( 'output_handler', '' );
		ignore_user_abort( true );
		$this->set_time_limit( 0 );
		$this->send_nocache_headers();
		header( 'Content-Type: text/csv; charset=utf-8' );
		header( 'Content-Disposition: attachment; filename=' . $this->get_filename() );
		header( 'Pragma: no-cache' );
		header( 'Expires: 0' );
	}

	function set_filename( $filename ) {
		$this->filename = sanitize_file_name( str_replace( '.csv', '', $filename ) . '.csv' );
	}

	function get_filename() {
		return sanitize_file_name( $this->filename );
	}

	function send_content( $csv_data ) {
		echo $csv_data;
	}

	protected function get_csv_data() {
		return $this->export_rows();
	}

	protected function export_column_headers() {
		$columns    = $this->get_columns_to_export();
		$export_row = array();
		$buffer     = fopen( 'php://output', 'w' );
		ob_start();

		foreach ( $columns as $column_name ) {
			$export_row[] = $this->format_data( $column_name );
		}

		$this->fputcsv( $buffer, $export_row );

		return ob_get_clean();
	}

	protected function get_data_to_export() {
		return $this->row_data;
	}

	protected function export_rows() {
		$data   = $this->get_data_to_export();
		$buffer = fopen( 'php://output', 'w' );
		ob_start();

		array_walk( $data, array( $this, 'export_row' ), $buffer );

		return ob_get_clean();
	}

	protected function export_row( $row_data, $key, $buffer ) {
		$this->fputcsv( $buffer, $row_data );
		++ $this->exported_row_count;
	}

	function get_limit() {
		return $this->limit;
	}

	function set_limit( $limit ) {
		$this->limit = ( $limit == -1 ) ? $limit : absint( $limit );
	}

	function escape_data( $data ) {
		$active_content_triggers = array( '=', '+', '-', '@' );

		if ( in_array( mb_substr( $data, 0, 1 ), $active_content_triggers, true ) ) {
			$data = "'" . $data;
		}

		return $data;
	}

	function format_data( $data ) {
		if ( is_bool( $data ) ) {
			$data = $data ? 1 : 0;
		}

		$use_mb = function_exists( 'mb_convert_encoding' );
		if ( $use_mb ) {
			$encoding = mb_detect_encoding( $data, 'UTF-8, ISO-8859-1', true );
			$data     = 'UTF-8' === $encoding ? $data : utf8_encode( $data );
		}

		return $this->escape_data( $data );
	}
    
	protected function implode_values( $values ) {
		$values_to_implode = array();

		foreach ( $values as $value ) {
			$value               = (string) is_scalar( $value ) ? $value : '';
			$values_to_implode[] = str_replace( ',', '\\,', $value );
		}

		return implode( ', ', $values_to_implode );
	}

	protected function fputcsv( $buffer, $export_row ) {
		if ( version_compare( PHP_VERSION, '5.5.4', '<' ) ) {
			ob_start();
			$temp = fopen( 'php://output', 'w' );
    		fputcsv( $temp, $export_row, $this->get_delimiter(), '"' );
			fclose( $temp );
			$row = ob_get_clean();
			$row = str_replace( '\\"', '\\""', $row );
			fwrite( $buffer, $row );
		} else {
			fputcsv( $buffer, $export_row, $this->get_delimiter(), '"', "\0" );
		}
	}

    protected function get_file_path() {
		if( !empty( $this->get_path() ) )
			return $this->get_path();
		
		$upload_dir = wp_upload_dir();
		return trailingslashit( $upload_dir['basedir'] ) . $this->get_filename();
	}

    protected function get_headers_row_file_path() {
		return $this->get_file_path() . '.headers';
	}

	function get_headers_row_file() {
		$file = chr( 239 ) . chr( 187 ) . chr( 191 ) . $this->export_column_headers();

		if ( @file_exists( $this->get_headers_row_file_path() ) ){
			$file = @file_get_contents( $this->get_headers_row_file_path() );
		}

		return $file;
	}

	function get_file() {
		$file = '';
		if ( @file_exists( $this->get_file_path() ) ){
			$file = @file_get_contents( $this->get_file_path() );
		} else {
			@file_put_contents( $this->get_file_path(), '' );
			@chmod( $this->get_file_path(), 0664 );
		}
		return $file;
	}

	function export() {
		$this->send_headers();
		$this->send_content( $this->get_headers_row_file() . $this->get_file() );

		do_action( 'acui_export_before_delete_file', $this->get_file_path() );

		@unlink( $this->get_file_path() );
		@unlink( $this->get_headers_row_file_path() );
		die();
	}
    
    function generate_file() {
        if( 1 === $this->get_page() ){
			@unlink( $this->get_file_path() );

			$this->get_file();
		}
		$this->prepare_data_to_export();
		$this->write_csv_data( $this->get_csv_data() );
	}

    function calculate_total(){
        $total_rows = count( $this->get_user_id_list( true ) );
        set_transient( 'acui_export_total_rows', $total_rows, HOUR_IN_SECONDS );

        return $total_rows;
    }

	protected function write_csv_data( $data ) {
		if ( ! file_exists( $this->get_file_path() ) || ! is_writeable( $this->get_file_path() ) ) {
			return false;
		}

		$fp = fopen( $this->get_file_path(), 'a+' );

		if ( $fp ) {
			fwrite( $fp, $data );
			fclose( $fp );
		}

		if ( 100 <= $this->get_percent_complete() ) {
			$header = chr( 239 ) . chr( 187 ) . chr( 191 ) . $this->export_column_headers();

			@file_put_contents( $this->get_headers_row_file_path(), $header );
		}

	}

	function get_page() {
		return $this->page;
	}

    function set_page( $page ) {
		$this->page = absint( $page );
	}

	function get_total_exported() {
		return ( ( $this->get_page() - 1 ) * $this->get_limit() ) + $this->exported_row_count;
	}

	function get_percent_complete() {
        return $this->get_total_rows() ? floor( ( $this->get_total_exported() / $this->get_total_rows() ) * 100 ) : 100;
	}
    
    function get_user_data(){
        if( count( $this->get_filtered_columns() ) == 0 )
            return $this->user_data;
        
        $result = array();
        foreach( $this->user_data as $column ){
            if( in_array( $column, $this->get_filtered_columns() ) )
                $result[] = $column;
        }
        
        return $result;
    }

    function get_user_id_list( $calculate_total = false ){
        $args = array( 'fields' => array( 'ID' ), 'order' => $this->get_order() );

        if( !$calculate_total && $this->get_limit() != -1 ){
            $args['number'] = $this->get_limit();
            $args['offset'] = ($this->get_page() - 1) * $this->get_limit();
        }

		if( !empty( $this->get_role() ) )
			$args['role'] = $this->get_role();

		$date_query = array();

		if( !empty( $this->get_from() ) )
			$date_query[] = array( 'after' => $this->get_from(), 'inclusive' => true );
		
		if( !empty( $this->get_to() ) )
			$date_query[] = array( 'before' => $this->get_to(), 'inclusive' => true );

		if( !empty( $date_query ) ){
			$date_query['inclusive'] = true;
			$args['date_query'] = $date_query;
		}

        if( !empty( $this->get_orderby() ) ){
			if( in_array( $this->get_orderby(), $this->accepted_order_by ) )
			    $args['orderby'] = $this->get_orderby();
            else{
                $args['orderby'] = "meta_value";
                $args['meta_key'] = $this->get_orderby();
            }
		}

		$users = get_users( $args );

        if( $calculate_total )
            return $users;

		$list = array();

	    foreach ( $users as $user ) {
	    	$list[] = $user->ID;
	    }

	    return $list;
	}
    
	function prepare_data_to_export() {
        $acui_helper = new ACUI_Helper();

		$users = $this->get_user_id_list();
        $this->row_data = array();
        
		foreach ( $users as $user ) {
            $row = array();
			$userdata = get_userdata( $user );

            foreach ( $this->get_user_data( $this->get_filtered_columns() ) as $key ) {
				if( $key == 'source_user_id' )
					$row[ $key ] = $this->prepare( $key, $userdata->ID, $this->get_datetime_format(), $user );
				else
					$row[ $key ] = $this->prepare( $key, $userdata->data->{$key}, $this->get_datetime_format(), $user );
			}

            if( count( $this->get_filtered_columns() ) == 0 || in_array( 'role', $this->get_filtered_columns() ) )
			    $row['role'] = implode( ',', $acui_helper->get_roles_by_user_id( $user ) );

			foreach ( $this->get_user_meta_keys() as $key ) {
				$row[ $key ] = $this->prepare( $key, get_user_meta( $user, $key, true ), $this->get_datetime_format(), $user );
			}

            if( count( $this->get_filtered_columns() ) == 0 || in_array( 'user_email', $this->get_filtered_columns() ) || in_array( 'user_login', $this->get_filtered_columns() ) )
			    $row = $this->maybe_fill_empty_data( $row, $user, $this->get_filtered_columns() );

			$row = apply_filters( 'acui_export_data', $row, $user, array( 'columns' => $this->get_columns_to_export(), 'datetime_format' => $this->get_datetime_format(), 'order_fields_alphabetically' => $this->get_order_fields_alphabetically(), 'double_encapsulate_serialized_values' => $this->get_double_encapsulate_serialized_values(), 'filtered_columns' => $this->get_filtered_columns() ));

            $this->row_data[] = array_values( $row );
		}
	}

    function prepare( $key, $value, $datetime_format = '', $user = 0 ){
        $acui_helper = new ACUI_Helper();

		$timestamp_keys = apply_filters( 'acui_export_timestamp_keys', array( 'wc_last_active' ) );
		$original_value = $value;
		$value = $this->clean_bad_characters_formulas( $value, $key, $user );

        if( has_filter( 'acui_export_prepare' ) ){
            return apply_filters( 'acui_export_prepare', $value, $original_value );
        }

		if( $key == 'role' ){
			return implode( ',', $acui_helper->get_roles_by_user_id( $user ) );
		}

		if( is_array( $value ) || is_object( $value ) ){
			return serialize( $value );
		}

		if( in_array( $key, $this->get_non_date_keys() ) || empty( $datetime_format ) ){
			return $value;
		}

		if( $this->get_convert_timestamp() && is_int( $value ) && ( ( $this->is_valid_timestamp( $value ) && strlen( $value ) > 4 ) || in_array( $key, $timestamp_keys) ) ){ // dates in timestamp format
			return date( $datetime_format, $value );
		}

        return $value;
	}

    function clean_bad_characters_formulas( $value, $key, $user ){
        if( is_array( $value ) )
            return $value;

		if( strlen( $value ) == 0 )
			return $value;

		$bad_characters = array( "\t", "\r", "+", "-", "=", "@" );
		$first_character = substr( $value, 0, 1 );
		if( in_array( $first_character, $bad_characters ) ){
			$value = "'" . $first_character . substr( $value, 1 );
			$this->add_bad_character_formulas_values_cleaned( $key, $value, $user );
		}

		return $value;
	}

	function add_bad_character_formulas_values_cleaned( $key, $value, $user ){
		$current = get_transient( 'acui_export_bad_character_formulas_values_cleaned' );
		if( !is_array( $current ) )
			$current = array();

		$current[] = array( 'key' => $key, 'value' => $value, 'user_id' => $user );
		set_transient( 'acui_export_bad_character_formulas_values_cleaned', $current );
	}

    function is_valid_timestamp( $timestamp ){
		return ( (string) (int) $timestamp === $timestamp ) && ( $timestamp <= PHP_INT_MAX ) && ( $timestamp >= ~PHP_INT_MAX );
	}

    function maybe_fill_empty_data( $row, $user_id, $filtered_columns ){
		if( empty( $row['user_login'] ) || empty( $row['user_email'] ) ){
			$user = new WP_User( $user_id );

			if( $user->ID == 0 )
				return $row;

            if( count( $filtered_columns ) == 0 || in_array( 'user_login', $filtered_columns ) )
			    $row['user_login'] = $user->user_login;

            if( count( $filtered_columns ) == 0 || in_array( 'user_email', $filtered_columns ) )
			    $row['user_email'] = $user->user_email;
		}
		
		return $row;
	}

    function get_user_meta_keys() {
	    global $wpdb;
	    $meta_keys = array();

        $usermeta = get_transient( 'acui_export_user_meta_keys' );
		$usermeta = '';

        if( empty( $usermeta ) ){
            $usermeta = $wpdb->get_results( "SELECT distinct $wpdb->usermeta.meta_key FROM $wpdb->usermeta", ARRAY_A );
            set_transient( 'acui_export_user_meta_keys', $usermeta, HOUR_IN_SECONDS );
        }
	    
	  	foreach( $usermeta as $key => $value) {
			if( $value["meta_key"] == 'role' || $value["meta_key"] == 'source_user_id' )
				continue;

            if( count( $this->get_filtered_columns() ) == 0 || in_array( $value["meta_key"], $this->get_filtered_columns() ) )
			    $meta_keys[] = $value["meta_key"];
		}

	    return apply_filters( 'acui_export_get_user_meta_keys', $meta_keys );
	}
}
