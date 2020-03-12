<?php
/**
 * Opt-in.
 *
 * @package ConstantContact
 * @subpackage Optin
 * @author Constant Contact
 * @since 1.2.0
 *
 * phpcs:disable WebDevStudios.All.RequireAuthor -- Don't require author tags in docblocks.
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Optin for usage tracking.
 *
 * @since 1.2.0
 */
class ConstantContact_Optin {

	/**
	 * Parent plugin class.
	 *
	 * @since 1.2.0
	 * @var object
	 */
	protected $plugin;

	/**
	 * Get things going.
	 *
	 * @since 1.2.0
	 *
	 * @param object $plugin Parent plugin class.
	 */
	public function __construct( $plugin ) {
		$this->plugin = $plugin;
		add_action( 'init', [ $this, 'hooks' ] );
	}

	/**
	 * Execute our hooks, if requirements met.
	 *
	 * @since 1.2.0
	 */
	public function hooks() {
		if ( $this->can_track() && constant_contact()->is_constant_contact() ) {
			add_action( 'admin_footer', [ $this, 'anonymous_tracking' ] );
		}
		if ( ! $this->privacy_policy_status() ) {
			add_action( 'admin_footer', [ $this, 'privacy_notice_markup' ] );
		}
	}

	/**
	 * Outputs our Analytics tracking code to the admin footer.
	 *
	 * @since 1.2.0
	 */
	public function anonymous_tracking() {
		?>
		<!-- Google Analytics -->
		<script>
			(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)})(window,document,'script','https://www.google-analytics.com/analytics.js','ga');
			ga('create', 'UA-89027837-1', 'auto');
			ga('send', 'pageview');
		</script>
		<!-- End Google Analytics -->
		<?php
	}

	/**
	 * Determines whether or not we have been granted permission to do some tracking.
	 *
	 * @since 1.2.0
	 *
	 * @return bool
	 */
	public function can_track() {
		$can_track = false;

		$options = get_option( constant_contact()->settings->key );
		$optin   = isset( $options['_ctct_data_tracking'] ) ? $options['_ctct_data_tracking'] : '';
		$privacy = get_option( 'ctct_privacy_policy_status', '' );

		if ( 'on' === $optin && 'true' === $privacy ) {
			$can_track = true;
		}
		return $can_track;
	}

	/**
	 * Returns the status of our privacy policy acceptance.
	 *
	 * @since 1.2.0
	 *
	 * @return bool
	 */
	public function privacy_policy_status() {
		$status = get_option( 'ctct_privacy_policy_status', '' );
		return ! ( '' === $status || 'false' === $status );
	}

	/**
	 * Outputs the markup for the privacy policy modal popup.
	 *
	 * @since 1.2.0
	 *
	 * @return void
	 */
	public function privacy_notice_markup() {
		if ( ! constant_contact_maybe_display_optin_notification() ) {
			return;
		}

		if ( $this->privacy_policy_status() || ! constant_contact()->is_constant_contact() ) {
			return;
		}
	?>
		<div id="ctct-privacy-modal" class="ctct-modal">
			<div class="ctct-modal-dialog" role="document">
				<div class="ctct-modal-content">
					<div class="ctct-modal-header">
						<a href="#" class="ctct-modal-close" aria-hidden="true">&times;</a>
						<h2 class="ctct-logo"><img src="<?php echo esc_url( constant_contact()->url ) . '/assets/images/constant-contact-logo.png'; ?>" alt="<?php echo esc_attr_x( 'Constant Contact logo', 'img alt text', 'constant-contact-forms' ); ?>" /></h2>
					</div>
					<div class="ctct-modal-body ctct-privacy-modal-body">
					<?php
						echo wp_kses_post( constant_contact_privacy_policy_content() );
					?>
					</div><!-- modal body -->
					<div id="ctct-modal-footer-privacy" class="ctct-modal-footer ctct-modal-footer-privacy">
						<a class="button button-blue ctct-connect" data-agree="true"><?php esc_html_e( 'Agree', 'constant-contact-forms' ); ?></a>
						<a class="button no-bg" data-agree="false"><?php esc_html_e( 'Disagree', 'constant-contact-forms' ); ?></a>
					</div>
				</div><!-- .modal-content -->
			</div><!-- .modal-dialog -->
		</div>
	<?php
	}
}
