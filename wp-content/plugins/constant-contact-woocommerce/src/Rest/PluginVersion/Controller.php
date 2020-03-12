<?php
/**
 * Controller for wc/cc-woo/plugin-version endpoint.
 *
 * @package WebDevStudios\CCForWoo\Rest\PluginVersion
 * @since   1.2.0
 */

namespace WebDevStudios\CCForWoo\Rest\PluginVersion;

use WP_REST_Server;
use WP_REST_Request;
use WP_REST_Controller;
use WP_REST_Response;
use WP_Error;

use WebDevStudios\CCForWoo\Plugin;
use WebDevStudios\CCForWoo\Rest\Registrar;

/**
 * Class PluginVersion\Controller
 *
 * @package WebDevStudios\CCForWoo\Rest\PluginVersion
 * @since   1.2.0
 */
class Controller extends WP_REST_Controller {

	/**
	 * This endpoint's rest base.
	 *
	 * @since 1.2.0
	 *
	 * @var string
	 */
	protected $rest_base;

	/**
	 * Constructor.
	 *
	 * @author George Gecewicz <george.gecewicz@webdevstudios.com>
	 * @since  1.2.0
	 */
	public function __construct() {
		$this->rest_base = 'plugin-version';
	}

	/**
	 * Register the Plugin Version route.
	 *
	 * @author George Gecewicz <george.gecewicz@webdevstudios.com>
	 * @since  1.2.0
	 */
	public function register_routes() {
		register_rest_route(
			Registrar::$namespace,
			'/' . $this->rest_base,
			[
				[
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => [ $this, 'get_item' ],
					'permission_callback' => [ $this, 'get_item_permissions_check' ],
				],
				'schema' => [ '\WebDevStudios\CCForWoo\Rest\PluginVersion\Schema', 'get_public_item_schema' ],
			]
		);
	}

	/**
	 * Check whether a given request has permission to show plugin version.
	 *
	 * @author George Gecewicz <george.gecewicz@webdevstudios.com>
	 * @since  1.2.0
	 *
	 * @param  WP_REST_Request $request Full details about the request.
	 * @return WP_Error|boolean
	 */
	public function get_item_permissions_check( $request ) {
		if ( ! wc_rest_check_manager_permissions( 'settings', 'read' ) ) {
			return new WP_Error( 'cc-woo-rest-not-allowed', esc_html__( 'Sorry, you cannot list that resource.', 'cc-woo' ), [ 'status' => rest_authorization_required_code() ] );
		}

		return true;
	}

	/**
	 * Register the Plugin Version endpoint.
	 *
	 * @author George Gecewicz <george.gecewicz@webdevstudios.com>
	 * @since  1.2.0
	 *
	 * @param  WP_REST_Request $request The REST request.
	 * @return array
	 */
	public function get_item( $request ) {
		$response = [
			'current_version' => Plugin::PLUGIN_VERSION,
		];

		return new WP_REST_Response( $response, 200 );
	}

}

