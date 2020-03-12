<?php
/**
 * Constant Contact WooCommerce Settings Tab
 *
 * @since   2019-03-07
 * @author  Zach Owen <zach@webdevstudios>, Jeremy Ward <jeremy.ward@webdevstudios.com>
 * @package cc-woo
 */

namespace WebDevStudios\CCForWoo\View\Admin;

use WebDevStudios\CCForWoo\Meta\ConnectionStatus;
use WebDevStudios\CCForWoo\Settings\SettingsModel;
use WebDevStudios\CCForWoo\Settings\SettingsValidator;
use WebDevStudios\CCForWoo\Utility\NonceVerification;
use WebDevStudios\CCForWoo\View\Checkout\NewsletterPreferenceCheckbox;
use WebDevStudios\OopsWP\Utility\Hookable;
use WC_Settings_Page;

/**
 * Class WooTab
 *
 * @author  Jeremy Ward <jeremy.ward@webdevstudios.com>
 * @package WebDevStudios\CCForWoo\View\Admin
 * @since   2019-03-08
 */
class WooTab extends WC_Settings_Page implements Hookable {
	use NonceVerification;

	/**
	 * Store owner first name field.
	 *
	 * @since 2019-03-12
	 */
	const FIRST_NAME_FIELD = 'cc_woo_store_information_first_name';

	/**
	 * Store owner last name field.
	 *
	 * @since 2019-03-12
	 */
	const LAST_NAME_FIELD = 'cc_woo_store_information_last_name';

	/**
	 * Store phone number field.
	 *
	 * @since 2019-03-12
	 */
	const PHONE_NUMBER_FIELD = 'cc_woo_store_information_phone_number';

	/**
	 * Store name field.
	 *
	 * @since 2019-03-12
	 */
	const STORE_NAME_FIELD = 'cc_woo_store_information_store_name';

	/**
	 * Store currency field.
	 *
	 * @since 2019-03-12
	 */
	const CURRENCY_FIELD = 'cc_woo_store_information_currency';

	/**
	 * Store country code field.
	 *
	 * @since 2019-03-12
	 */
	const COUNTRY_CODE_FIELD = 'cc_woo_store_information_country_code';

	/**
	 * Store contact e-mail field.
	 *
	 * @since 2019-03-12
	 */
	const EMAIL_FIELD = 'cc_woo_store_information_contact_email';

	/**
	 * Settings section ID.
	 *
	 * @var string
	 * @since 2019-03-08
	 */
	protected $id = 'cc_woo';

	/**
	 * Settings Section label.
	 *
	 * @var string
	 * @since 2019-03-08
	 */
	protected $label = '';

	/**
	 * Array of form errors to display with their fields.
	 *
	 * @since 2019-03-08
	 * @var array
	 */
	private $errors = [];

	/**
	 * Instance of the ConnectionStatus object.
	 *
	 * @var ConnectionStatus
	 * @since 2019-03-21
	 */
	private $connection;

	/**
	 * Is the current request a REST API request?
	 *
	 * @since 2019-04-16
	 * @var bool
	 */
	private $is_rest = false;

	/**
	 * The identifier for the Importing Existing Customers section.
	 *
	 * @since 2019-04-16
	 * @var string
	 */
	private $import_existing_customer_section = 'customer_data_import';

	/**
	 * WooTab constructor.
	 *
	 * @since  2019-03-08
	 * @author Zach Owen <zach@webdevstudios>
	 */
	public function __construct() {
		$this->label        = esc_html__( 'Constant Contact', 'cc-woo' );
		$this->nonce_name   = '_cc_woo_nonce';
		$this->nonce_action = 'cc-woo-connect-action';
		$this->connection   = new ConnectionStatus();
		$this->is_rest      = defined( 'REST_REQUEST' ) && REST_REQUEST;
	}

	/**
	 * Register hooks into WooCommerce
	 *
	 * @since  2019-03-08
	 * @author Zach Owen <zach@webdevstudios>
	 */
	public function register_hooks() {
		add_filter( 'woocommerce_settings_tabs_array', [ $this, 'add_settings_page' ], 99 );
		add_action( "woocommerce_settings_{$this->id}", [ $this, 'output' ] );
		add_action( "woocommerce_settings_{$this->id}", [ $this, 'enqueue_scripts' ] );

		// Output settings sections.
		add_action( "woocommerce_sections_{$this->id}", [ $this, 'output_sections' ] );

		// CC API interactions.
		add_action( "woocommerce_sections_{$this->id}", [ $this, 'maybe_redirect_to_cc' ] );
		add_action( "woocommerce_sections_{$this->id}", [ $this, 'maybe_update_connection_status' ], 1 );

		// REST API.
		add_filter( 'woocommerce_settings_groups', [ $this, 'add_rest_group' ] );
		add_filter( "woocommerce_settings-{$this->id}", [ $this, 'add_rest_fields' ] );

		// Form.
		add_filter( 'pre_option_' . self::CURRENCY_FIELD, 'get_woocommerce_currency' );
		add_filter( 'pre_option_' . self::COUNTRY_CODE_FIELD, [ $this, 'get_woo_country' ] );
		add_filter( 'woocommerce_admin_settings_sanitize_option_' . self::PHONE_NUMBER_FIELD, [ $this, 'sanitize_phone_number' ] );
		add_filter( "woocommerce_get_settings_{$this->id}", [ $this, 'maybe_add_connection_button' ] );
		add_action( 'woocommerce_admin_field_cc_connection_button', [ $this, 'add_cc_connection_button' ] );
		add_action( 'woocommerce_admin_field_cc_cta_button', [ $this, 'render_cta_button' ] );

		// Save actions.
		add_filter( 'woocommerce_settings_start', [ $this, 'validate_option_values' ], 10, 3 );
		add_action( "woocommerce_settings_save_{$this->id}", [ $this, 'save' ] );
		add_action( "woocommerce_settings_save_{$this->id}", [ $this, 'update_setup_option' ] );

		// Custom field for labels.
		add_action( 'woocommerce_admin_field_cc_woo_anti_spam_notice', [ $this, 'display_anti_spam_notice' ] );
	}

	/**
	 * Add the settings sections.
	 *
	 * @since  2019-03-08
	 * @author Zach Owen <zach@webdevstudios>
	 * @return array
	 */
	public function get_sections() {
		$sections = [
			''                                      => esc_html__( 'Store Information', 'cc-woo' ),
			$this->import_existing_customer_section => esc_html__( 'Import your contacts', 'cc-woo' ),
		];

		return apply_filters( 'woocommerce_get_sections_' . $this->id, $sections );
	}

	/**
	 * Get the settings for the settings tab.
	 *
	 * @since  2019-03-08
	 * @author Zach Owen <zach@webdevstudios>
	 * @return array
	 */
	public function get_settings() {
		// @TODO this should be able to be removed.
		if ( $this->is_rest ) {
			$settings = $this->get_rest_settings_options();

			if ( ! $this->connection->is_connected() ) {
				$settings = array_merge( $settings, $this->get_connection_attempted_options() );
			}

			return $this->get_filtered_settings( $settings );
		}

		if ( ! $this->connection->connection_was_attempted() ) {
			return $this->get_filtered_settings( $this->get_default_settings_options() );
		}

		if ( ! $this->connection->is_connected() ) {
			return $this->get_filtered_settings(
				array_merge( $this->get_connection_attempted_options(), $this->get_default_settings_options() )
			);
		}

		return $this->get_filtered_settings( array_merge(
			$this->get_connection_established_options(),
			$this->get_default_settings_options()
		) );
	}

	/**
	 * Run the settings for the current connection status through the WooCommerce settings filter.
	 *
	 * @param array $settings Settings options.
	 *
	 * @author Jeremy Ward <jeremy.ward@webdevstudios.com>
	 * @since  2019-03-21
	 * @return array
	 */
	private function get_filtered_settings( array $settings ) {
		return apply_filters( 'woocommerce_get_settings_' . $this->id, $settings, $GLOBALS['current_section'] );
	}

	/**
	 * Get the default view for our settings page.
	 *
	 * @author Jeremy Ward <jeremy.ward@webdevstudios.com>
	 * @since  2019-03-21
	 * @return array
	 */
	private function get_default_settings_options() {
		$settings = [];

		switch ( $GLOBALS['current_section'] ) {
			case '':
			default:
				$settings = $this->get_store_information_settings();
				break;

			case $this->import_existing_customer_section:
				$settings = $this->get_customer_data_settings();
				break;
		}

		$settings = $this->process_errors( $settings );
		$settings = $this->adjust_styles( $settings );

		return $settings;
	}

	/**
	 * Add our settings group to the REST API.
	 *
	 * @since  2019-03-08
	 * @author Zach Owen <zach@webdevstudios>
	 *
	 * @param array $groups The array of groups being sent to the API.
	 *
	 * @return array
	 */
	public function add_rest_group( $groups ) {
		$groups[] = [
			'id'          => 'cc_woo',
			'label'       => esc_html__( 'Constant Contact WooCommerce', 'cc-woo' ),
			'description' => esc_html__( 'This endpoint provides information for the Constant Contact for WooCommerce plugin.', 'cc-woo' ),
		];

		return $groups;
	}

	/**
	 * Add fields to the REST API for our settings.
	 *
	 * @since  2019-03-08
	 * @author Zach Owen <zach@webdevstudios>
	 *
	 * @param array $settings The array of settings going to the API.
	 *
	 * @return array
	 */
	public function add_rest_fields( $settings ) {
		$fields       = [];
		$section_keys = array_keys( $this->get_sections() );

		foreach ( $section_keys as $section_id ) {
			$fields = array_merge( $fields, $this->get_settings( $section_id ) );
		}

		foreach ( $fields as $field ) {
			$field['option_key'] = $field['option_key'] ?? $field['id'];
			$settings[]          = $field;
		}

		return $settings;
	}

	/**
	 * Get the section options for an attempted connection that failed.
	 *
	 * @author Jeremy Ward <jeremy.ward@webdevstudios.com>
	 * @since  2019-03-21
	 * @return array
	 */
	private function get_connection_attempted_options() {
		return [
			[
				'title' => '',
				'desc'  => '<h2 style="color:red;margin-top:0;">' . esc_html__( 'There was a problem connecting your store to Constant Contact. Please try again.', 'cc-woo' ) . '</h2>',
				'type'  => 'title',
				'id'    => 'cc_woo_connection_attempted_heading',
			],
		];
	}

	/**
	 * Get the settings for the main section if already connected to Constant Contact.
	 *
	 * @author Jeremy Ward <jeremy.ward@webdevstudios.com>
	 * @since  2019-03-21
	 * @return array
	 */
	private function get_connection_established_options() {
		return [
			[
				'title' => esc_html__( 'Congratulations! Your store is connected to Constant Contact.', 'cc-woo' ),
				'type'  => 'title',
				'id'    => 'cc_woo_connection_established_heading',
			],
			[
				'type' => 'cc_cta_button',
			],
			[
				'type' => 'sectionend',
				'id'   => 'cc_woo_store_information_settings',
			],
		];
	}

	/**
	 * Render the call-to-action button in the admin.
	 *
	 * @author Jeremy Ward <jeremy.ward@webdevstudios.com>
	 * @since  2019-03-21
	 */
	public function render_cta_button() {
		$url = 'https://login.constantcontact.com/login/?goto=https%3A%2F%2Fapp.constantcontact.com%2Fpages%2Fecomm%2Fdashboard%23woocommerce';
		?>
		<a
			class="button button-primary"
			href="<?php echo esc_url( $url ); ?>"
		>
			<?php esc_html_e( 'Constant Contact Dashboard', 'cc-woo' ); ?>
		</a>
		<?php
	}

	/**
	 * Gets the settings for the Store Information section.
	 *
	 * @since  2019-03-08
	 * @author Zach Owen <zach@webdevstudios>
	 * @return array
	 */
	private function get_store_information_settings() {
		$readonly_from_general_settings = esc_html__( 'This field is read from your General settings.', 'cc-woo' );

		return [
			[
				'title' => esc_html__( 'Store Information', 'cc-woo' ),
				'type'  => 'title',
				'desc'  => esc_html__( 'All fields are required.', 'cc-woo' ),
				'id'    => 'cc_woo_store_information_settings',
			],
			[
				'title'             => esc_html__( 'First Name', 'cc-woo' ),
				'desc'              => '',
				'id'                => self::FIRST_NAME_FIELD,
				'type'              => 'text',
				'custom_attributes' => [
					'required'  => 'required',
					'maxlength' => 255,
				],
			],
			[
				'title'             => esc_html__( 'Last Name', 'cc-woo' ),
				'desc'              => '',
				'id'                => self::LAST_NAME_FIELD,
				'type'              => 'text',
				'custom_attributes' => [
					'required'  => 'required',
					'maxlength' => 255,
				],
			],
			[
				'title'             => esc_html__( 'Phone Number', 'cc-woo' ),
				'id'                => self::PHONE_NUMBER_FIELD,
				'desc'              => '',
				'type'              => 'text',
				'custom_attributes' => [
					'required'  => 'required',
					'maxlength' => 255,
				],
			],
			[
				'title'             => esc_html__( 'Store Name', 'cc-woo' ),
				'id'                => self::STORE_NAME_FIELD,
				'desc'              => '',
				'type'              => 'text',
				'custom_attributes' => [
					'required'  => 'required',
					'maxlength' => 255,
				],
			],
			[
				'title'             => esc_html__( 'Contact E-mail Address', 'cc-woo' ),
				'id'                => self::EMAIL_FIELD,
				'desc'              => '',
				'type'              => 'email',
				'custom_attributes' => [
					'required'  => 'required',
					'maxlength' => 255,
				],
			],
			[
				'title'             => esc_html__( 'Currency', 'cc-woo' ),
				'id'                => self::CURRENCY_FIELD,
				'desc'              => $readonly_from_general_settings,
				'type'              => 'text',
				'custom_attributes' => [
					'readonly' => 'readonly',
					'size'     => 4,
				],
			],
			[
				'title'             => esc_html__( 'Country Code', 'cc-woo' ),
				'id'                => self::COUNTRY_CODE_FIELD,
				'desc'              => $readonly_from_general_settings,
				'type'              => 'text',
				'custom_attributes' => [
					'readonly' => 'readonly',
					'size'     => 4,
				],
			],
			[
				'title'   => esc_html__( 'Marketing Opt-in', 'cc-woo' ),
				'desc'    => esc_html__( 'At checkout, new customers must check a box if they want to receive marketing emails from you. Do you want this box checked by default?', 'cc-woo' ),
				'type'    => 'select',
				'id'      => NewsletterPreferenceCheckbox::STORE_NEWSLETTER_DEFAULT_OPTION,
				'default' => 'false',
				'options' => [
					'false' => esc_html__( 'No - do not check this box by default', 'cc-woo' ),
					'true'  => esc_html__( 'Yes - check this box by default', 'cc-woo' ),
				],
			],
			[
				'type' => 'sectionend',
				'id'   => 'cc_woo_store_information_settings',
			],
		];
	}

	/**
	 * Get the customer marketing settings.
	 *
	 * @since  2019-03-08
	 * @author Zach Owen <zach@webdevstudios>
	 * @return array
	 */
	private function get_customer_data_settings() {
		$settings = [
			[
				'title' => esc_html__( 'Import your contacts', 'cc-woo' ),
				'id'    => 'cc_woo_customer_data_settings',
				'type'  => 'title',
			],
			[
				'title' => '',
				'type'  => 'title',
				'desc'  => esc_html__( "Start marketing to your customers right away by importing all your contacts now.\n\nDo you want to import your current contacts? By selecting yes below, you agree you have permission to market to your current contacts.", 'cc-woo' ),
			],
		];

		$historical_import_field = new \WebDevStudios\CCForWoo\View\Admin\Field\ImportHistoricalData();

		$settings[] = array_merge(
			$settings,
			$historical_import_field->get_form_field(),
			[
				[
					'title' => '',
					'type'  => 'cc_woo_anti_spam_notice',
					'id'    => 'anti-spam-notice',
				],
				[
					'type' => 'sectionend',
					'id'   => 'cc_woo_customer_data_settings',
				],
			]
		);

		return $settings;
	}

	/**
	 * Displays the Constant Contact connection button when the form is validated and a connection is not already established.
	 *
	 * @since  2019-03-08
	 * @author Zach Owen <zach@webdevstudios>
	 *
	 * @param array $settings The current settings array.
	 *
	 * @return array
	 */
	public function maybe_add_connection_button( $settings ) {
		if ( ! $this->meets_connect_requirements() || $this->connection->is_connected() ) {
			return $settings;
		}

		return array_merge( [ $this->get_connection_button() ], $settings );
	}

	/**
	 * Add the Constant Contact connection button when displaying the form.
	 *
	 * Will display as a "Disconnect" button if the connection has already been established.
	 *
	 * @since  2019-03-08
	 * @author Zach Owen <zach@webdevstudios>
	 */
	public function add_cc_connection_button() {
		$connected = get_option( ConnectionStatus::CC_CONNECTION_ESTABLISHED_KEY );
		$value     = $connected ? 'disconnect' : 'connect';
		$message   = $connected
			? esc_html__( 'Disconnect from Constant Contact', 'cc-woo' )
			: esc_html__( 'Connect with Constant Contact', 'cc-woo' );

		wp_nonce_field( $this->nonce_action, $this->nonce_name );
		?>
		<div style="padding: 1rem 0;">
		<button class="button button-primary" type="submit" name="cc_woo_action" value="<?php echo esc_attr( $value ); ?>">
			<?php echo esc_html( $message ); ?>
		</button>
			<span style="line-height:28px; margin-left:25px;">
				<?php
				printf(
					/* translators: the placeholders hold opening and closing `<a>` tags. */
					esc_html__( 'If you have any issues connecting please call %1$sConstant Contact Support%2$s', 'cc-woo' ),
					'<a href="https://community.constantcontact.com/contact-support">',
					'</a>'
				);
				?>
			</span>
		</div>
		<?php
	}

	/**
	 * Maybe redirects to Constant Contact to connect accounts.
	 *
	 * @author Jeremy Ward <jeremy.ward@webdevstudios.com>
	 * @since  2019-03-19
	 * @return void
	 */
	public function maybe_redirect_to_cc() {
		if ( ! $this->requested_connect_to_cc() ) {
			return;
		}

		add_filter( 'allowed_redirect_hosts', [ $this, 'allow_redirect_to_cc' ] );

		wp_safe_redirect( 'https://shoppingcart.constantcontact.com/auth/woocommerce/WhoDis?storeDomain="' . get_home_url() . '"' );
		exit;
	}

	/**
	 * Check whether a connection request to CC has been triggered.
	 *
	 * @author Jeremy Ward <jeremy.ward@webdevstudios.com>
	 * @since  2019-03-19
	 * @return bool
	 */
	private function requested_connect_to_cc() {
		if ( ! $this->has_valid_nonce() ) {
			return false;
		}

		// phpcs:disable -- Ignoring $_POST warnings.
		return (
			isset( $_POST['cc_woo_action'] )
			&& 'connect' === filter_var( $_POST['cc_woo_action'], FILTER_SANITIZE_STRING )
		);
		// phpcs:enable
	}

	/**
	 * Add the Constant Contact host to the list of allowed hosts.
	 *
	 * @author Jeremy Ward <jeremy.ward@webdevstudios.com>
	 * @since  2019-03-19
	 * @return array
	 */
	public function allow_redirect_to_cc() {
		$hosts[] = 'shoppingcart.constantcontact.com';

		return $hosts;
	}

	/**
	 * Check to see if the settings meet the requirements to connect to CC.
	 *
	 * @since  2019-03-08
	 * @author Zach Owen <zach@webdevstudios>
	 * @return bool
	 */
	private function meets_connect_requirements() {
		$model = new SettingsModel(
			get_option( self::FIRST_NAME_FIELD, '' ),
			get_option( self::LAST_NAME_FIELD, '' ),
			get_option( self::PHONE_NUMBER_FIELD, '' ),
			get_option( self::STORE_NAME_FIELD, '' ),
			get_option( self::CURRENCY_FIELD, '' ),
			get_option( self::COUNTRY_CODE_FIELD ),
			get_option( self::EMAIL_FIELD )
		);

		$validator = new SettingsValidator( $model );

		return $validator->is_valid();
	}

	/**
	 * Listen for GET request that establishes connection.
	 *
	 * @author Jeremy Ward <jeremy.ward@webdevstudios.com>
	 * @since  2019-03-21
	 * @return void
	 */
	public function maybe_update_connection_status() {
		$success = filter_input( INPUT_GET, 'success', FILTER_SANITIZE_NUMBER_INT );
		$user_id = filter_input( INPUT_GET, 'user_id', FILTER_SANITIZE_NUMBER_INT );

		if ( is_null( $success ) || is_null( $user_id ) ) {
			return;
		}

		$this->connection->set_connection( $success, $user_id );
	}

	/**
	 * Verify that all option values meet the minimum requirements.
	 *
	 * @since  2019-03-08
	 * @author Zach Owen <zach@webdevstudios>
	 * @return void
	 */
	public function validate_option_values() {
		if ( ! get_option( 'constant_contact_for_woo_has_setup' ) ) {
			return;
		}

		$settings = $this->get_store_information_settings();

		foreach ( $settings as $field ) {
			$this->validate_value( $field );
		}
	}

	/**
	 * Validate a field's value is set, otherwise log an error.
	 *
	 * @since  2019-03-08
	 * @author Zach Owen <zach@webdevstudios>
	 *
	 * @param array $field The field to validate.
	 *
	 * @return void
	 */
	private function validate_value( $field ) {
		if ( in_array( $field['type'], [ 'title', 'sectionend' ], true ) ) {
			return;
		}

		if ( ! empty( get_option( $field['id'] ) ) ) {
			return;
		}

		$this->errors[ $field['id'] ] = sprintf(
			/* Translators: Placeholder is the field's title. */
			esc_html__( 'The "%s" field is required to connect to Constant Contact.', 'cc-woo' ),
			$field['title']
		);
	}

	/**
	 * Sanitize the phone number to only include digits, -, and (, )
	 *
	 * @since  2019-03-08
	 * @author Zach Owen <zach@webdevstudios>
	 *
	 * @param mixed $value The incoming phone number value.
	 *
	 * @return string
	 */
	public function sanitize_phone_number( $value ) {
		return is_scalar( $value ) ? preg_replace( '/[^\d\-()+]+/', '', $value ) : '';
	}

	/**
	 * Process errors logged for form fields.
	 *
	 * @since  2019-03-08
	 * @author Zach Owen <zach@webdevstudios>
	 *
	 * @param array $settings An array of settings fields.
	 *
	 * @return array
	 */
	private function process_errors( $settings ) {
		if ( empty( $this->errors ) ) {
			return $settings;
		}

		foreach ( $settings as $key => &$field ) {
			if ( empty( $field['id'] ) ) {
				continue;
			}

			if ( ! isset( $this->errors[ $field['id'] ] ) ) {
				continue;
			}

			$field['desc'] = ( ! empty( $field['desc'] ) ? $field['desc'] . '<br/>' : '' ) . $this->errors[ $field['id'] ];
		}

		return $settings;
	}

	/**
	 * Update the setup option.
	 *
	 * This is used to prevent errors from appearing before the user has submitted the form,
	 * i.e. after a fresh installation.
	 *
	 * @since  2019-03-08
	 * @author Zach Owen <zach@webdevstudios>
	 */
	public function update_setup_option() {
		update_option( 'constant_contact_for_woo_has_setup', true );
	}

	/**
	 * Make all form elements for our settings `display:block`.
	 *
	 * @since  2019-03-08
	 * @author Zach Owen <zach@webdevstudios>
	 *
	 * @param array $settings Array of settings to adjust.
	 *
	 * @return array
	 */
	private function adjust_styles( $settings ) {
		foreach ( $settings as $key => $field ) {
			if ( ! empty( $field['css'] ) ) {
				continue;
			}

			if ( in_array( $field['type'], [ 'title', 'sectionend' ], true ) ) {
				continue;
			}

			$settings[ $key ]['css'] = 'display: block';
		}

		return $settings;
	}

	/**
	 * Get the Country code from the WooCommerce settings.
	 *
	 * @since 2019-03-15
	 * @author Zach Owen <zach@webdevstudios>
	 * @return string
	 */
	public function get_woo_country() : string {
		return wc_get_base_location()['country'] ?? '';
	}

	/**
	 * Display a link to the anti-spam policy.
	 *
	 * @since 2019-05-17
	 * @author Zach Owen <zach@webdevstudios>
	 */
	public function display_anti_spam_notice() {
?>
<tr>
	<td colspan="2">
		<?php
		echo sprintf(
			// phpcs:ignore -- output is escaped properly with esc_url.
			__( 'See more on Constant Contact\'s <a href="%s" target="_blank">anti-spam policy</a>.', 'cc-woo' ),
			esc_url( 'https://www.constantcontact.com/legal/anti-spam' )
		);
		?>
	</td>
</tr>
<?php
	}

	/**
	 * Save settings.
	 *
	 * @author Zach Owen <zach@webdevstudios>
	 * @since 2019-04-16
	 * @return void
	 */
	public function save() {
		parent::save();

		if ( $this->connection->is_connected() || $this->has_active_settings_section() ) {
			return;
		}

		wp_safe_redirect( add_query_arg( 'section', $this->import_existing_customer_section ) );
		exit;
	}

	/**
	 * Return the options for REST requests.
	 *
	 * @since 2019-05-06
	 * @author Zach Owen <zach@webdevstudios>
	 * @return array
	 */
	private function get_rest_settings_options() : array {
		return array_merge(
			$this->get_store_information_settings(),
			$this->get_customer_data_settings()
		);
	}

	/**
	 * Gets the Connect Button for the settings fields.
	 *
	 * @since 2019-05-06
	 * @author Zach Owen <zach@webdevstudios>
	 * @return array
	 */
	private function get_connection_button() : array {
		return [
			'type' => 'cc_connection_button',
		];
	}

	/**
	 * Check whether there is an active section on the Woo settings page.
	 *
	 * When a user clicks a subsection (in this case the Historical data tab),
	 * Woo sets a global `$current_section` variable to know which tab to select.
	 *
	 * @since 2019-05-06
	 * @author Zach Owen <zach@webdevstudios>
	 * @return bool
	 */
	private function has_active_settings_section() : bool {
		return ! empty( $GLOBALS['current_section'] );
	}
}
