<?php
if ( ! defined( 'ABSPATH' ) ) exit; 

if( !is_plugin_active( 'paid-member-subscriptions/index.php' ) ){
	return;
}

class ACUI_PaidMemberSubscriptions{
	function __construct(){
		add_filter( 'acui_restricted_fields', array( $this, 'restricted_fields' ), 10, 1 );
		add_action( 'acui_documentation_after_plugins_activated', array( $this, 'documentation' ) );
		add_action( 'post_acui_import_single_user', array( $this, 'assign' ), 10, 4 );
    }
    
    function get_fields(){
        return array( 'subscription_plan_id', 'start_date', 'expiration_date', 'status' );
    }

	function restricted_fields( $acui_restricted_fields ){
		return array_merge( $acui_restricted_fields, $this->get_fields() );
	}

	function documentation(){
		?>
		<tr valign="top">
			<th scope="row"><?php _e( "Paid Member Subscriptions is activated", 'import-users-from-csv-with-meta' ); ?></th>
			<td><?php _e( "Plugin can create member subscriptions while this is importing. You will need to use those columns:", 'import-users-from-csv-with-meta' ); ?>
				<ul style="list-style:disc outside none; margin-left:2em;">
					<li><?php _e( "<strong>subscription_plan_id</strong>: you can find it in Paid Member Subscriptions, Subscriptions Plans ", 'import-users-from-csv-with-meta' ); ?></li>
					<li><?php _e( "<strong>start_date <em>(optional)</em></strong>: if you leave empty, current moment will be used, format is Y-m-d H:i:s", 'import-users-from-csv-with-meta' ); ?></li>
                    <li><?php _e( "<strong>expiration_date (optional)</strong>: if you leave it empty, no expired date will be defined", 'import-users-from-csv-with-meta' ); ?></li>
                    <li><?php _e( "<strong>status <em>(optional)</em></strong>: if you do not fill it, active will be used", 'import-users-from-csv-with-meta' ); ?></li>
				</ul>
			</td>
		</tr>
		<?php
	}

	function assign( $headers, $row, $user_id, $role ){
        if( !class_exists( 'PMS_Member_Subscription' ) )
            return;

		$keys = $this->get_fields();
        $columns = array();
        
        $status = 'active';
        $start_date = date('Y-m-d H:i:s');

        foreach ( $keys as $key ) {
            $pos = array_search( $key, $headers );

            if( $pos !== FALSE ){
                $columns[ $key ] = $pos;
                $$key = $row[ $columns[ $key ] ];
            }
        }

        if( !isset( $subscription_plan_id ) || empty( $subscription_plan_id ) )
            return;

        $subscription_data = array(
            'user_id'              => $user_id,
            'subscription_plan_id' => $subscription_plan_id,
            'start_date'           => $start_date,
            'status'               => $status,
        );

        if( isset( $expiration_date ) && !empty( $expiration_date ) )
            $subscription_data['expiration_date'] =$expiration_date;

        $subscription = new PMS_Member_Subscription();
        $subscription->insert( $subscription_data );
	}
}
new ACUI_PaidMemberSubscriptions();