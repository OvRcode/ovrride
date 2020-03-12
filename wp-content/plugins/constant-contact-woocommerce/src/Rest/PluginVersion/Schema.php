<?php
/**
 * Schema for wc/cc-woo/plugin-version endpoint.
 *
 * @package WebDevStudios\CCForWoo\Rest\PluginVersion
 * @since   1.2.0
 */

namespace WebDevStudios\CCForWoo\Rest\PluginVersion;

/**
 * Class PluginVersion\Schema
 *
 * @package WebDevStudios\CCForWoo\Rest\PluginVersion
 * @since   1.2.0
 */
class Schema {

	/**
	 * Get the Plugin Version's schema for public consumption.
	 *
	 * @author George Gecewicz <george.gecewicz@webdevstudios.com>
	 * @since  1.2.0
	 *
	 * @return array
	 */
	public static function get_public_item_schema() {
		return [
			'$schema'    => 'http://json-schema.org/draft-04/schema#',
			'title'      => 'cc_woo_plugin_version',
			'type'       => 'object',
			'properties' => [
				'current_version' => [
					'description' => esc_html__( 'The current version of the plugin.', 'cc-woo' ),
					'type'        => 'string',
					'context'     => [ 'view' ],
					'readonly'    => true,
				],
			],
		];
	}

}
