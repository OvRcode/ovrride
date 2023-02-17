<?php
if ( !defined( 'ABSPATH' ) ) {
	exit;
}
if ( !class_exists( 'wt_inform_server_secure' ) ) {

	class wt_inform_server_secure {

		/**
		 * config options 
		 */
		public $plugin					 = "";
		public $banner_message			 = "";
		public $sholud_show_server_info	 = '';

		public function __construct( $plugin ) {
			$this->plugin					 = $plugin;
			$this->sholud_show_server_info	 = 'wt_' . $this->plugin . '_show_server_info';

			if ( !$this->wt_get_display_server_info() ) {
				if ( Wt_Import_Export_For_Woo_Basic_Common_Helper::wt_is_screen_allowed() ) {
					$this->banner_css_class = 'wt_' . $this->plugin . '_show_server_info';
					add_action( 'admin_notices', array( $this, 'show_banner' ) );
					add_action( 'admin_print_footer_scripts', array( $this, 'add_banner_scripts' ) ); /* add banner scripts */
				}
			}
			$this->ajax_action_name = $this->plugin . '_process_show_server_info_action';
			add_action( 'wp_ajax_' . $this->ajax_action_name, array( $this, 'process_server_info__action' ) ); /* process banner user action */
		}

		/**
		 * 	Prints the banner 
		 */
		public function show_banner() {
			?>
			<div class="<?php echo $this->banner_css_class; ?> notice-warning notice is-dismissible">

				<p>
					<?php echo $this->banner_message; ?>				
				</p>
				<p>
					<?php if ( (strpos( $_SERVER[ 'SERVER_SOFTWARE' ], 'nginx' ) !== false ) ): ?>
					<h4><?php _e( 'Incase of Nginx server, copy the below code into your server config file to restrict public access to the wp-content folder or contact the server team to assist accordingly.' ); ?></h4>
					<code>
						#Deny access to wp-content folders<br/>
						location ~* ^/(wp-content)/(.*?)\.(zip|gz|tar|csv|bzip2|7z)\$ { deny all; }<br/>
						location ~ ^/wp-content/webtoffee_import { deny all; }
					</code>
				<?php endif; ?>
			</p>
			</div>
			<?php
		}

		/**
		 * 	Ajax hook to process user action on the banner
		 */
		public function process_server_info__action() {
			check_ajax_referer( $this->plugin );
			if ( isset( $_POST[ 'wt_action_type' ] ) && 'dismiss' == $_POST[ 'wt_action_type' ] ) {
				$this->wt_set_display_server_info( 1 );
			}
			exit();
		}

		/**
		 * 	Add banner JS to admin footer
		 */
		public function add_banner_scripts() {
			$ajax_url	 = admin_url( 'admin-ajax.php' );
			$nonce		 = wp_create_nonce( $this->plugin );
			?>
			<script type="text/javascript">
			( function ( $ ) {
			"use strict";

			/* prepare data object */
			var data_obj = {
			_wpnonce: '<?php echo $nonce; ?>',
			action: '<?php echo $this->ajax_action_name; ?>',
			wt_action_type: 'dismiss',
			};

			$( document ).on( 'click', '.<?php echo $this->banner_css_class; ?> .notice-dismiss', function ( e )
			{
			e.preventDefault();
			$.ajax( {
				url: '<?php echo $ajax_url; ?>',
				data: data_obj,
				type: 'POST',
			} );

			} );

			} )( jQuery )
			</script>
			<?php
		}

		public function wt_get_display_server_info() {

			if ( (strpos( $_SERVER[ 'SERVER_SOFTWARE' ], 'Apache' ) !== false) || (strpos( $_SERVER[ 'SERVER_SOFTWARE' ], 'LiteSpeed' ) !== false) ) {
				return true;
			} else {
				return (bool) get_option( $this->sholud_show_server_info );
			}
		}

		public function wt_set_display_server_info( $display = false ) {
			update_option( $this->sholud_show_server_info, $display ? 1 : 0  );
		}

	}

}
