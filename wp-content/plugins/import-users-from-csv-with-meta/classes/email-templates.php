<?php

if ( ! defined( 'ABSPATH' ) ) exit; 

class ACUI_Email_Template{
	function __construct(){
		add_action( 'wp_loaded', array( $this, 'cpt_email_template' ) );
		add_action( 'edit_form_after_editor', array( $this, 'email_templates_edit_form_after_editor' ), 10, 1 );
		add_action( 'wp_ajax_acui_refresh_enable_email_templates', array( $this, 'refresh_enable_email_templates' ) );
		add_action( 'wp_ajax_acui_email_template_selected', array( $this, 'email_template_selected' ) );
		add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ) );
		add_action( 'save_post', array( $this, 'save_post' ) );
		add_action( 'acui_email_options_after_editor', array( $this, 'email_templates_edit_form_after_editor' ) );
	}

	function cpt_email_template() {
		if( !get_option( 'acui_enable_email_templates' ) )
			return;

		$labels = array(
			'name'                  => _x( 'Email templates (Import and Export Users and Customers)', 'Post Type General Name', 'import-users-from-csv-with-meta' ),
			'singular_name'         => _x( 'Email template (Import and Export Users and Customers)', 'Post Type Singular Name', 'import-users-from-csv-with-meta' ),
			'menu_name'             => __( 'Email templates', 'import-users-from-csv-with-meta' ),
			'name_admin_bar'        => __( 'Email templates (Import and Export Users and Customers)', 'import-users-from-csv-with-meta' ),
			'archives'              => __( 'Item Archives', 'import-users-from-csv-with-meta' ),
			'attributes'            => __( 'Item Attributes', 'import-users-from-csv-with-meta' ),
			'parent_item_colon'     => __( 'Parent Item:', 'import-users-from-csv-with-meta' ),
			'all_items'             => __( 'All email template', 'import-users-from-csv-with-meta' ),
			'add_new_item'          => __( 'Add new email template', 'import-users-from-csv-with-meta' ),
			'add_new'               => __( 'Add new email template', 'import-users-from-csv-with-meta' ),
			'new_item'              => __( 'New email template', 'import-users-from-csv-with-meta' ),
			'edit_item'             => __( 'Edit email template', 'import-users-from-csv-with-meta' ),
			'update_item'           => __( 'Update email template', 'import-users-from-csv-with-meta' ),
			'view_item'             => __( 'View email template', 'import-users-from-csv-with-meta' ),
			'view_items'            => __( 'View email templates', 'import-users-from-csv-with-meta' ),
			'search_items'          => __( 'Search email template', 'import-users-from-csv-with-meta' ),
			'not_found'             => __( 'Not found', 'import-users-from-csv-with-meta' ),
			'not_found_in_trash'    => __( 'Not found in Trash', 'import-users-from-csv-with-meta' ),
			'featured_image'        => __( 'Featured Image', 'import-users-from-csv-with-meta' ),
			'set_featured_image'    => __( 'Set featured image', 'import-users-from-csv-with-meta' ),
			'remove_featured_image' => __( 'Remove featured image', 'import-users-from-csv-with-meta' ),
			'use_featured_image'    => __( 'Use as featured image', 'import-users-from-csv-with-meta' ),
			'insert_into_item'      => __( 'Insert into email template', 'import-users-from-csv-with-meta' ),
			'uploaded_to_this_item' => __( 'Uploaded to this email template', 'import-users-from-csv-with-meta' ),
			'items_list'            => __( 'Items list', 'import-users-from-csv-with-meta' ),
			'items_list_navigation' => __( 'Email template list navigation', 'import-users-from-csv-with-meta' ),
			'filter_items_list'     => __( 'Filter email template list', 'import-users-from-csv-with-meta' ),
		);
		$args = array(
			'label'                 => __( 'Mail template (Import Users From CSV With Meta)', 'import-users-from-csv-with-meta' ),
			'description'           => __( 'Mail templates for Import Users From CSV With Meta', 'import-users-from-csv-with-meta' ),
			'labels'                => $labels,
			'supports'              => array( 'title', 'editor' ),
			'hierarchical'          => true,
			'public'                => false,
			'show_ui'               => true,
			'show_in_menu'          => true,
			'menu_position'         => 100,
			'menu_icon'             => 'dashicons-email',
			'show_in_admin_bar'     => true,
			'show_in_nav_menus'     => false,
			'can_export'            => true,
			'has_archive'           => false,
			'exclude_from_search'   => true,
			'publicly_queryable'    => false,
			'rewrite'               => false,
			'capability_type'       => 'page',
		);
		register_post_type( 'acui_email_template', $args );
	}
	
	public function email_templates_edit_form_after_editor( $post = "" ){
		if( !empty( $post ) && $post->post_type != 'acui_email_template' )
			return;
		?>
	<p><?php _e( 'You can use', 'import-users-from-csv-with-meta' ); ?></p>
	<ul style="list-style-type:disc; margin-left:2em;">
		<li>**username** = <?php _e( 'username to login', 'import-users-from-csv-with-meta' ); ?></li>
		<li>**password** = <?php _e( 'user password', 'import-users-from-csv-with-meta' ); ?></li>
		<li>**email** = <?php _e( 'user email', 'import-users-from-csv-with-meta' ); ?></li>
		<li>**loginurl** = <?php _e( 'current site login url', 'import-users-from-csv-with-meta' ); ?></li>
		<li>**lostpasswordurl** = <?php _e( 'lost password url', 'import-users-from-csv-with-meta' ); ?></li>
		<li>**passwordreseturl** = <?php _e( 'password reset url', 'import-users-from-csv-with-meta' ); ?></li>
		<li>**passwordreseturllink** = <?php _e( 'password reset url with HTML link', 'import-users-from-csv-with-meta' ); ?></li>
		<li><?php _e( "You can also use any WordPress user standard field or an own metadata, if you have used it in your CSV. For example, if you have a first_name column, you could use **first_name** or any other meta_data like **my_custom_meta**", 'import-users-from-csv-with-meta' ) ;?></li>
		<?php do_action( 'acui_email_wildcards_list_elements' ); ?>
	</ul>
		<?php
	}
	
	function refresh_enable_email_templates(){
        check_ajax_referer( 'codection-security', 'security' );
        update_option( 'acui_enable_email_templates', ( $_POST[ 'enable' ] == "true" ) );
		wp_die();
	}
	
	function email_template_selected(){
		check_ajax_referer( 'codection-security', 'security' );
		$email_template = get_post( intval( $_POST['email_template_selected'] ) );
		$attachment_id = get_post_meta( $email_template->ID, 'email_template_attachment_id', true );

		echo json_encode( array( 
			'id' => $email_template->ID, 
			'title' => $email_template->post_title, 
			'content' => wpautop( $email_template->post_content ),
			'attachment_id' => $attachment_id,
			'attachment_url' => wp_get_attachment_url( $attachment_id ),
		) );

		wp_die();
	}

	function add_meta_boxes(){
		add_meta_box( 'email_template_attachments', 
						__( 'Attachment', 'import-users-from-csv-with-meta' ), 
						array( $this, 'email_template_attachments' ), 
						'acui_email_template', 
						'side',
        				'core' );
	}

	public function email_template_attachments( $post ){
		$email_template_attachment_id = get_post_meta( $post->ID, 'email_template_attachment_id', true );
		?>
			<fieldset>
				<div>
					<label for="email_template_attachment_file"><?php _e( 'Attachment', 'import-users-from-csv-with-meta' )?></label><br>
					<input type="url" class="large-text" name="email_template_attachment_file" id="email_template_attachment_file" value="<?php echo wp_get_attachment_url( $email_template_attachment_id ); ?>" readonly/><br>
					<input type="hidden" name="email_template_attachment_id" id="email_template_attachment_id" value="<?php echo $email_template_attachment_id ?>"/>
					<button type="button" class="button" id="acui_email_template_upload_button"><?php _e( 'Upload file', 'import-users-from-csv-with-meta' )?></button>
					<button type="button" class="button" id="acui_email_template_remove_upload_button"><?php _e( 'Remove file', 'import-users-from-csv-with-meta' )?></button>
				</div>
			</fieldset>
		<?php
		wp_nonce_field( 'acui_email_template_attachment', 'acui_email_template_attachment' );
	}

	function save_post( $post_id ){
		if( !isset( $_POST['acui_email_template_attachment'] ) )
			return $post_id;

		if( !wp_verify_nonce( $_POST['acui_email_template_attachment'], 'acui_email_template_attachment' ) ) {
			return $post_id;
		}
		
		if( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return $post_id;
		}
		
		if( 'acui_email_template' != $_POST['post_type'] ) {
			return $post_id;
		}

		update_post_meta( $post_id, 'email_template_attachment_id', intval( $_POST['email_template_attachment_id'] ) );
	}
}

new ACUI_Email_Template();