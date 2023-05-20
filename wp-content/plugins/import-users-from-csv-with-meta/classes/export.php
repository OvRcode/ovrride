<?php
if ( ! defined( 'ABSPATH' ) ) exit; 

class ACUI_Exporter{
	private $path_csv;

	function __construct(){
		$upload_dir = wp_upload_dir();
		$this->path_csv = $upload_dir['basedir'] . "/export-users.csv";

        add_action( 'init', array( $this, 'download_export_file' ) );
        add_action( 'admin_init', array( $this, 'download_export_file' ) );
		add_action( 'wp_ajax_acui_export_users_csv', array( $this, 'export_users_csv' ) );
	}

    static function enqueue(){
        wp_enqueue_script( 'acui_export_js', plugins_url( 'assets/export.js', dirname( __FILE__ ) ), false, ACUI_VERSION, true );
        wp_localize_script( 'acui_export_js', 'acui_export_js_object', array(
            'ajaxurl' => admin_url( 'admin-ajax.php' ),
            'starting_process' => __( 'Starting process', 'import-users-from-csv-with-meta' ),
            'step' => __( 'Step', 'import-users-from-csv-with-meta' ),
            'of_approximately' => __( 'of approximately', 'import-users-from-csv-with-meta' ),
            'steps' => __( 'steps', 'import-users-from-csv-with-meta' ),
            'error_thrown' => __( 'Error thrown in the server, we cannot continue. Please check console to see full details about the error.', 'import-users-from-csv-with-meta' ),
        ) );
    }

    static function styles(){
        ?>
        <style>
#acui_export_results{
	display: none;
	background-color: #dcdcde;
	padding: 20px;
}

#acui_exporter .user-exporter-progress-wrapper{
    padding: 5px;
    background-color: white;
    width: 80%;
    margin: 0 auto;
    text-align: center;
}

#acui_exporter .user-exporter-progress{
    width: 100%;
    height: 42px;
	border: 0;
	border-radius: 9px;
}
.user-exporter-progress::-webkit-progress-bar {
	background-color: #f3f3f3;
	border-radius: 9px;
}

.user-exporter-progress::-webkit-progress-value {
	background: #2271b1;
	border-radius: 9px;
}

.user-exporter-progress::-moz-progress-bar {
	background: #2271b1;
	border-radius: 9px;
}

.user-exporter-progress .progress-value {
	padding: 0px 5px;
	line-height: 20px;
	margin-left: 5px;
	font-size: .8em;
	color: #555;
	height: 18px;
	float: right;
}

#acui_exporter.user-exporter__exporting table,
#acui_exporter .user-exporter-progress-wrapper{
    display: none;
}

#acui_exporter.user-exporter__exporting .user-exporter-progress-wrapper{
    display: block;
}
        </style>
        <?php
    }

	static function admin_gui(){
	?>
	<div id="acui_export_results"></div>

	<h3 id="acui_export_users_header"><?php _e( 'Export users', 'import-users-from-csv-with-meta' ); ?></h3>
	<form id="acui_exporter">
		<table class="form-table">
			<tbody>
				<tr id="acui_role_wrapper" valign="top">
					<th scope="row"><?php _e( 'Role', 'import-users-from-csv-with-meta' ); ?></th>
					<td>
                        <?php ACUIHTML()->select( array(
                            'options' => ACUI_Helper::get_editable_roles(),
                            'name' => 'role',
                            'show_option_all' => false,
                            'show_option_none' => __( 'All roles', 'import-users-from-csv-with-meta' ),
                        )); ?>
					</td>
				</tr>
				<tr id="acui_columns" valign="top">
					<th scope="row"><?php _e( 'Columns', 'import-users-from-csv-with-meta' ); ?></th>
					<td>
						<?php ACUIHTML()->textarea( array( 'name' => 'columns' ) ); ?>
						<span class="description"><?php _e( 'You can use this field to set which columns must be exported and in which order.  If you leave it empty, all columns will be exported. Use a list of fields separated by commas, for example', 'import-users-from-csv-with-meta' ); ?>: user_email,first_name,last_name</span>
					</td>
				</tr>
				<tr id="acui_user_created_wrapper" valign="top">
					<th scope="row"><?php _e( 'User created', 'import-users-from-csv-with-meta' ); ?></th>
					<td>
						<label for="from">from <?php ACUIHTML()->text( array( 'type' => 'date', 'name' => 'from', 'class' => '' ) ); ?></label>
						<label for="to">to <?php ACUIHTML()->text( array( 'type' => 'date', 'name' => 'to', 'class' => '' ) ); ?></label>
					</td>
				</tr>
				<tr id="acui_delimiter_wrapper" valign="top">
					<th scope="row"><?php _e( 'Delimiter', 'import-users-from-csv-with-meta' ); ?></th>
					<td>
                        <?php ACUIHTML()->select( array(
                            'options' => ACUI_Helper::get_csv_delimiters_titles(),
                            'name' => 'delimiter',
                            'show_option_all' => false,
                            'show_option_none' => false,
                        )); ?>
					</td>
				</tr>
				<tr id="acui_timestamp_wrapper" valign="top">
					<th scope="row"><?php _e( 'Convert timestamp data to date format', 'import-users-from-csv-with-meta' ); ?></th>
					<td>
                        <?php ACUIHTML()->checkbox( array( 'name' => 'convert_timestamp', 'current' => 0 ) ); ?>
						<?php ACUIHTML()->text( array( 'name' => 'datetime_format', 'value' => 'Y-m-d H:i:s', 'class' => '' ) ); ?>
                        <span class="description"><a href="https://www.php.net/manual/en/datetime.formats.php"><?php _e( 'accepted formats', 'import-users-from-csv-with-meta' ); ?></a> <?php _e( 'If you have problems and you get some value exported as a date that should not be converted to date, please deactivate this option. If this option is not activated, datetime format will be ignored.', 'import-users-from-csv-with-meta' ); ?></span>
					</td>
				</tr>
				<tr id="acui_order_fields_alphabetically_wrapper" valign="top">
					<th scope="row"><?php _e( 'Order fields alphabetically', 'import-users-from-csv-with-meta' ); ?></th>
					<td>
                        <?php ACUIHTML()->checkbox( array( 'name' => 'order_fields_alphabetically', 'current' => 0 ) ); ?>
						<span class="description"><?php _e( "Order all columns alphabetically to check easier your data. First two columns won't be affected", 'import-users-from-csv-with-meta' ); ?></span>
					</td>
				</tr>
                <tr id="acui_order_fields_double_encapsulate_serialized_values" valign="top">
					<th scope="row"><?php _e( 'Double encapsulate serialized values', 'import-users-from-csv-with-meta' ); ?></th>
					<td>
                        <?php ACUIHTML()->checkbox( array( 'name' => 'double_encapsulate_serialized_values', 'current' => 0 ) ); ?>                    
						<span class="description"><?php _e( "Serialized values sometimes can have problems being displayed in Microsoft Excel or LibreOffice, we can double encapsulate this kind of data but you would not be able to import this data beucase instead of serialized data it would be managed as strings", 'import-users-from-csv-with-meta' ); ?></span>
					</td>
				</tr>
				<tr id="acui_download_csv_wrapper" valign="top">
					<th scope="row"><?php _e( 'Download CSV file with users', 'import-users-from-csv-with-meta' ); ?></th>
					<td>
						<input class="button-primary" type="submit" value="<?php _e( 'Download', 'import-users-from-csv-with-meta'); ?>"/>
					</td>
				</tr>
			</tbody>
		</table>
		<input type="hidden" name="action" value="acui_export_users_csv"/>
		<?php wp_nonce_field( 'codection-security', 'security' ); ?>

        <div class="user-exporter-progress-wrapper">
            <progress class="user-exporter-progress" value="0" max="100"></progress>
            <span class="user-exporter-progress-value">0%</span>
        </div>
	</form>

	<script type="text/javascript">
	jQuery( document ).ready( function( $ ){
		$( "input[name='from']" ).change( function() {
			$( "input[name='to']" ).attr( 'min', $( this ).val() );
		})

		$( '#convert_timestamp' ).on( 'click', function() {
			check_convert_timestamp_checked();
		});

		function check_convert_timestamp_checked(){
			if( $('#convert_timestamp').is(':checked') ){
				$( '#datetime_format' ).prop( 'disabled', false );
			} else {
				$( '#datetime_format' ).prop( 'disabled', true );
			}
		}
	} )
	</script>
	<?php
	}

    function download_export_file() {
		if( current_user_can( apply_filters( 'acui_capability', 'create_users' ) ) && isset( $_GET['action'], $_GET['nonce'] ) && wp_verify_nonce( wp_unslash( $_GET['nonce'] ), 'codection-security' ) && 'download_user_csv' === wp_unslash( $_GET['action'] ) ) {
            $exporter = new ACUI_Batch_Exporter();

			if ( !empty( $_GET['filename'] ) ){
				$exporter->set_filename( wp_unslash( $_GET['filename'] ) );
			}

			$exporter->export();
		}
	}

	function get_results(){
		$bad_character_formulas_values_cleaned = get_transient( 'acui_export_bad_character_formulas_values_cleaned' );
		delete_transient( 'acui_export_bad_character_formulas_values_cleaned' );
		
		if( empty( $bad_character_formulas_values_cleaned ) ){
			return '';
		}
		
		$results = array();

		foreach( $bad_character_formulas_values_cleaned as $info ){
			$results[] = sprintf( __( 'User with id: %s has the cell of the column: %s edited because has content that may auto-run formulas in certain spreadsheet apps, new value is: %s', 'import-users-from-csv-with-meta' ), $info['user_id'], $info['key'], $info['value'] );
		}

		$ret = '<h3>' . __( 'Export results','import-users-from-csv-with-meta' ) . '</h3>';
		$ret .= '<h4>' . __( 'Some values has been altered','import-users-from-csv-with-meta' ) . '</h4>';
		$ret .= '<ul>';
		foreach( $results as $result ){
			$ret .= '<li>' . $result . '</li>';
		}

		$ret .= '</ul>';

		return $ret;
	}

    function export_users_csv(){
        check_ajax_referer( 'codection-security', 'security' );

		if( !current_user_can( apply_filters( 'acui_capability', 'create_users' ) ) )
			wp_die( __( 'Only users who are able to create users can export them.', 'import-users-from-csv-with-meta' ) );

    
        $step = isset( $_POST['step'] ) ? absint( $_POST['step'] ) : 1;
                
        $exporter = new ACUI_Batch_Exporter();

		if( $step == 1 )
			delete_transient( 'acui_export_bad_character_formulas_values_cleaned' );
               
        $exporter->set_page( $step );
        $exporter->set_delimiter( sanitize_text_field( $_POST['delimiter'] ) );
        $exporter->set_role( sanitize_text_field( $_POST['role'] ) );
        $exporter->set_from( sanitize_text_field( $_POST['from'] ) );
        $exporter->set_to( sanitize_text_field( $_POST['to'] ) );
        $exporter->set_convert_timestamp( $_POST['convert_timestamp'] );
        $exporter->set_datetime_format( sanitize_text_field( $_POST['datetime_format'] ) );
        $exporter->set_order_fields_alphabetically( $_POST['order_fields_alphabetically'] );
        $exporter->set_double_encapsulate_serialized_values( $_POST['double_encapsulate_serialized_values'] );
        $exporter->set_filtered_columns( ( isset( $_POST['columns'] ) && !empty( $_POST['columns'] ) ) ? $_POST['columns'] : array() );
        $exporter->set_orderby( ( isset( $_POST['orderby'] ) && !empty( $_POST['orderby'] ) ) ? sanitize_text_field( $_POST['orderby'] ) : '' );
        $exporter->set_order( ( isset( $_POST['order'] ) && !empty( $_POST['order'] ) ) ? sanitize_text_field( $_POST['order'] ) : 'ASC' );
        $exporter->load_columns();
        
        $exporter->generate_file();

        if ( 100 <= $exporter->get_percent_complete() ) {
            $query_args = array(
                'nonce'    => wp_create_nonce( 'codection-security' ),
                'action'   => 'download_user_csv',
                'filename' => $exporter->get_filename()
            );

            wp_send_json_success(
                array(
                    'step'       => 'done',
                    'percentage' => 100,
                    'url'        => add_query_arg( $query_args, admin_url( 'tools.php?page=acui&tab=export' ) ),
					'results'	 => $this->get_results()
                )
            );
        } else {
            wp_send_json_success(
                array(
                    'step'       => ++$step,
                    'total_steps' => $exporter->get_total_steps(),
                    'percentage' => $exporter->get_percent_complete(),
                )
            );
        }
    }
}

$acui_exporter = new ACUI_Exporter();