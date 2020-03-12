<?php
/**
 * Field for allowing the import of historical data.
 *
 * @since 2019-06-05
 * @author Zach Owen <zach@webdevstudios>
 * @package cc-woo-view-admin-field
 */

namespace WebDevStudios\CCForWoo\View\Admin\Field;

/**
 * ImportHistoricalData clss
 *
 * @since 2019-06-05
 * @author Zach Owen <zach@webdevstudios>
 * @package cc-woo-view-admin-field
 */
class ImportHistoricalData {
	/**
	 * Historical customer data import field.
	 *
	 * @since 2019-03-12
	 */
	const OPTION_FIELD_NAME = 'cc_woo_customer_data_allow_import';

	/**
	 * Returns the form field configuration.
	 *
	 * @since 2019-06-05
	 * @author Zach Owen <zach@webdevstudios>
	 * @return array.
	 */
	public function get_form_field() : array {
		return [
			'title'             => esc_html__( 'Import your contacts', 'cc-woo' ),
			'desc'              => $this->get_description(),
			'type'              => 'select',
			'id'                => self::OPTION_FIELD_NAME,
			'default'           => '',
			'custom_attributes' => $this->get_custom_attributes(),
			'options'           => [
				''      => '----',
				'false' => esc_html__( 'No', 'cc-woo' ),
				'true'  => esc_html__( 'Yes', 'cc-woo' ),
			],
		];
	}

	/**
	 * Get the field's custom attribute configuration.
	 *
	 * @TODO "$configuration" should probably be an object.
	 * @since 2019-06-05
	 * @author Zach Owen <zach@webdevstudios>
	 *
	 * @return array
	 */
	private function get_custom_attributes() : array {
		$attributes = [];

		if ( $this->is_required() ) {
			$attributes['required'] = true;
		}

		if ( $this->is_readonly() ) {
			$attributes['readonly'] = 'readonly';
			$attributes['disabled'] = 'disabled';
		}

		return $attributes;
	}

	/**
	 * Return whether the field is required.
	 *
	 * @since 2019-06-05
	 * @author Zach Owen <zach@webdevstudios>
	 * @return bool
	 */
	protected function is_required() : bool {
		return true;
	}

	/**
	 * Returns the value of the field option.
	 *
	 * @since 2019-06-05
	 * @author Zach Owen <zach@webdevstudios>
	 * @return bool
	 */
	protected function is_readonly() : bool {
		return 'true' === get_option( self::OPTION_FIELD_NAME, 'false' );
	}

	/**
	 * Get the field description based on the option's readonly state.
	 *
	 * @since 2019-06-05
	 * @author Zach Owen <zach@webdevstudios>
	 * @return string
	 */
	private function get_description() : string {
		return $this->is_readonly() ? '' : esc_html__( 'Selecting Yes here will enable the ability to import your historical customer information to Constant Contact.', 'cc-woo' );
	}
}
