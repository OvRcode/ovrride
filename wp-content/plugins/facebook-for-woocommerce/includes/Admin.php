<?php
// phpcs:ignoreFile
/**
 * Copyright (c) Facebook, Inc. and its affiliates. All Rights Reserved
 *
 * This source code is licensed under the license found in the
 * LICENSE file in the root directory of this source tree.
 *
 * @package FacebookCommerce
 */

namespace WooCommerce\Facebook;

use WooCommerce\Facebook\Admin\Enhanced_Catalog_Attribute_Fields;
use WooCommerce\Facebook\Framework\Helper;
use Automattic\WooCommerce\Utilities\OrderUtil;

defined( 'ABSPATH' ) or exit;

/**
 * Admin handler.
 *
 * @since 1.10.0
 */
class Admin {

	/** @var string the "sync and show" sync mode slug */
	const SYNC_MODE_SYNC_AND_SHOW = 'sync_and_show';

	/** @var string the "sync and show" sync mode slug */
	const SYNC_MODE_SYNC_AND_HIDE = 'sync_and_hide';

	/** @var string the "sync disabled" sync mode slug */
	const SYNC_MODE_SYNC_DISABLED = 'sync_disabled';

	/** @var Product_Categories the product category admin handler */
	protected $product_categories;

	/** @var array screens ids where to include scripts */
	protected $screen_ids = [];


	/**
	 * Admin constructor.
	 *
	 * @since 1.10.0
	 */
	public function __construct() {

		$order_screen_id = class_exists( OrderUtil::class ) ? OrderUtil::get_order_admin_screen() : 'shop_order';

		$this->screen_ids = [
			'product',
			'edit-product',
			'woocommerce_page_wc-facebook',
			'marketing_page_wc-facebook',
			'edit-product_cat',
			$order_screen_id,
		];

		// enqueue admin scripts
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

		$plugin = facebook_for_woocommerce();
		// only alter the admin UI if the plugin is connected to Facebook and ready to sync products
		if ( ! $plugin->get_connection_handler()->is_connected() || ! $plugin->get_integration()->get_product_catalog_id() ) {
			return;
		}

		$this->product_categories = new Admin\Product_Categories();
		$this->product_sets       = new Admin\Product_Sets();
		// add a modal in admin product pages
		add_action( 'admin_footer', array( $this, 'render_modal_template' ) );
		// may trigger the modal to open to warn the merchant about a conflict with the current product terms
		add_action( 'admin_footer', array( $this, 'validate_product_excluded_terms' ) );

		// add admin notice to inform that disabled products may need to be deleted manually
		add_action( 'admin_notices', array( $this, 'maybe_show_product_disabled_sync_notice' ) );

		// add admin notice if the user is enabling sync for virtual products using the bulk action
		add_action( 'admin_notices', array( $this, 'maybe_add_enabling_virtual_products_sync_notice' ) );
		add_filter( 'request', array( $this, 'filter_virtual_products_affected_enabling_sync' ) );

		// add admin notice to inform sync mode has been automatically set to Sync and hide for virtual products and variations
		add_action( 'admin_notices', array( $this, 'add_handled_virtual_products_variations_notice' ) );

		// add columns for displaying Facebook sync enabled/disabled and catalog visibility status
		add_filter( 'manage_product_posts_columns', array( $this, 'add_product_list_table_columns' ) );
		add_action( 'manage_product_posts_custom_column', array( $this, 'add_product_list_table_columns_content' ) );

		// add input to filter products by Facebook sync enabled
		add_action( 'restrict_manage_posts', array( $this, 'add_products_by_sync_enabled_input_filter' ), 40 );
		add_filter( 'request', array( $this, 'filter_products_by_sync_enabled' ) );

		// add bulk actions to manage products sync
		add_filter( 'bulk_actions-edit-product', array( $this, 'add_products_sync_bulk_actions' ), 40 );
		add_action( 'handle_bulk_actions-edit-product', array( $this, 'handle_products_sync_bulk_actions' ) );

		// add Product data tab
		add_filter( 'woocommerce_product_data_tabs', array( $this, 'add_product_settings_tab' ) );
		add_action( 'woocommerce_product_data_panels', array( $this, 'add_product_settings_tab_content' ) );

		// add Variation edit fields
		add_action( 'woocommerce_product_after_variable_attributes', array( $this, 'add_product_variation_edit_fields' ), 10, 3 );
		add_action( 'woocommerce_save_product_variation', array( $this, 'save_product_variation_edit_fields' ), 10, 2 );

		// add custom taxonomy for Product Sets
		add_filter( 'gettext', array( $this, 'change_custom_taxonomy_tip' ), 20, 2 );
	}

	/**
	 * Change custom taxonomy tip text
	 *
	 * @since 2.3.0
	 *
	 * @param string $translation Text translation.
	 * @param string $text Original text.
	 *
	 * @return string
	 */
	public function change_custom_taxonomy_tip( $translation, $text ) {
		global $current_screen;
		if ( isset( $current_screen->id ) && 'edit-fb_product_set' === $current_screen->id && 'The name is how it appears on your site.' === $text ) {
			$translation = esc_html__( 'The name is how it appears on Facebook Catalog.', 'facebook-for-woocommerce' );
		}
		return $translation;
	}

	/**
	 * Enqueues admin scripts.
	 *
	 * @internal
	 *
	 * @since 1.10.0
	 */
	public function enqueue_scripts() {
		global $current_screen;

		if ( isset( $current_screen->id ) ) {

			if ( in_array( $current_screen->id, $this->screen_ids, true ) || facebook_for_woocommerce()->is_plugin_settings() ) {

				// enqueue modal functions
				wp_enqueue_script(
					'facebook-for-woocommerce-modal',
					facebook_for_woocommerce()->get_asset_build_dir_url() . '/admin/modal.js',
					array( 'jquery', 'wc-backbone-modal', 'jquery-blockui' ),
					\WC_Facebookcommerce::PLUGIN_VERSION
				);

				// enqueue google product category select
				wp_enqueue_script(
					'wc-facebook-google-product-category-fields',
					facebook_for_woocommerce()->get_asset_build_dir_url() . '/admin/google-product-category-fields.js',
					array( 'jquery' ),
					\WC_Facebookcommerce::PLUGIN_VERSION
				);

				wp_localize_script(
					'wc-facebook-google-product-category-fields',
					'facebook_for_woocommerce_google_product_category',
					array(
						'i18n' => array(
							'top_level_dropdown_placeholder' => __( 'Search main categories...', 'facebook-for-woocommerce' ),
							'second_level_empty_dropdown_placeholder' => __( 'Choose a main category', 'facebook-for-woocommerce' ),
							'general_dropdown_placeholder'   => __( 'Choose a category', 'facebook-for-woocommerce' ),
						),
					)
				);
			}

			if ( 'edit-fb_product_set' === $current_screen->id ) {
				// enqueue WooCommerce Admin Styles because of Select2
				wp_enqueue_style(
					'woocommerce_admin_styles',
					WC()->plugin_url() . '/assets/css/admin.css',
					[],
					\WC_Facebookcommerce::PLUGIN_VERSION
				);
				wp_enqueue_style(
					'facebook-for-woocommerce-product-sets-admin',
					facebook_for_woocommerce()->get_plugin_url() . '/assets/css/admin/facebook-for-woocommerce-product-sets-admin.css',
					[],
					\WC_Facebookcommerce::PLUGIN_VERSION
				);

				wp_enqueue_script(
					'facebook-for-woocommerce-product-sets',
					facebook_for_woocommerce()->get_asset_build_dir_url() . '/admin/product-sets-admin.js',
					[ 'jquery', 'select2' ],
					\WC_Facebookcommerce::PLUGIN_VERSION,
					true
				);

				wp_localize_script(
					'facebook-for-woocommerce-product-sets',
					'facebook_for_woocommerce_product_sets',
					array(

						'excluded_category_ids'             => facebook_for_woocommerce()->get_integration()->get_excluded_product_category_ids(),
						'excluded_category_warning_message' => __( 'You have selected one or more categories currently excluded from the Facebook sync. Products belonging to the excluded categories will not be added to your Facebook Product Set.', 'facebook-for-woocommerce' ),
					)
				);
			}

			if ( 'product' === $current_screen->id || 'edit-product' === $current_screen->id ) {
				wp_enqueue_style(
					'facebook-for-woocommerce-products-admin',
					facebook_for_woocommerce()->get_plugin_url() . '/assets/css/admin/facebook-for-woocommerce-products-admin.css',
					[],
					\WC_Facebookcommerce::PLUGIN_VERSION
				);
				wp_enqueue_script(
					'facebook-for-woocommerce-products-admin',
					facebook_for_woocommerce()->get_asset_build_dir_url() . '/admin/products-admin.js',
					[ 'jquery', 'wc-backbone-modal', 'jquery-blockui', 'facebook-for-woocommerce-modal' ],
					\WC_Facebookcommerce::PLUGIN_VERSION
				);
				wp_localize_script(
					'facebook-for-woocommerce-products-admin',
					'facebook_for_woocommerce_products_admin',
					[
						'ajax_url'                                        => admin_url( 'admin-ajax.php' ),
						'enhanced_attribute_optional_selector'            => Enhanced_Catalog_Attribute_Fields::FIELD_ENHANCED_CATALOG_ATTRIBUTE_PREFIX . Enhanced_Catalog_Attribute_Fields::OPTIONAL_SELECTOR_KEY,
						'enhanced_attribute_page_type_edit_category'      => Enhanced_Catalog_Attribute_Fields::PAGE_TYPE_EDIT_CATEGORY,
						'enhanced_attribute_page_type_add_category'       => Enhanced_Catalog_Attribute_Fields::PAGE_TYPE_ADD_CATEGORY,
						'enhanced_attribute_page_type_edit_product'       => Enhanced_Catalog_Attribute_Fields::PAGE_TYPE_EDIT_PRODUCT,
						'is_product_published'                            => $this->is_current_product_published(),
						'is_sync_enabled_for_product'                     => $this->is_sync_enabled_for_current_product(),
						'set_product_visibility_nonce'                    => wp_create_nonce( 'set-products-visibility' ),
						'set_product_sync_prompt_nonce'                   => wp_create_nonce( 'set-product-sync-prompt' ),
						'set_product_sync_bulk_action_prompt_nonce'       => wp_create_nonce( 'set-product-sync-bulk-action-prompt' ),
						'product_not_ready_modal_message'                 => $this->get_product_not_ready_modal_message(),
						'product_not_ready_modal_buttons'                 => $this->get_product_not_ready_modal_buttons(),
						'product_removed_from_sync_confirm_modal_message' => $this->get_product_removed_from_sync_confirm_modal_message(),
						'product_removed_from_sync_confirm_modal_buttons' => $this->get_product_removed_from_sync_confirm_modal_buttons(),
						'product_removed_from_sync_field_id'              => '#' . \WC_Facebook_Product::FB_REMOVE_FROM_SYNC,
						'i18n'                                            => [
							'missing_google_product_category_message' => __( 'Please enter a Google product category and at least one sub-category to sell this product on Instagram.', 'facebook-for-woocommerce' ),
						],
					]
				);
			}//end if

			if ( facebook_for_woocommerce()->is_plugin_settings() ) {
				wp_enqueue_style( 'woocommerce_admin_styles' );
				wp_enqueue_script( 'wc-enhanced-select' );
			}
		}//end if

	}

	/**
	 * Determines whether sync is enabled for the current product.
	 *
	 * @since 2.0.5
	 *
	 * @return bool
	 */
	private function is_sync_enabled_for_current_product() {
		global $post;
		$product = wc_get_product( $post );
		if ( ! $product instanceof \WC_Product ) {
			return false;
		}
		return Products::is_sync_enabled_for_product( $product );
	}

	/**
	 * Determines whether the current product is published.
	 *
	 * @since 2.6.15
	 *
	 * @return bool
	 */
	private function is_current_product_published() {
		global $post;
		$product = wc_get_product( $post );
		if ( ! $product instanceof \WC_Product ) {
			return false;
		}
		return 'publish' === $product->get_status();
	}

	/**
	 * Gets the markup for the message used in the product not ready modal.
	 *
	 * @since 2.1.0
	 *
	 * @return string
	 */
	private function get_product_not_ready_modal_message() {
		ob_start();
		?>
		<p><?php esc_html_e( 'To sell this product on Instagram, please ensure it meets the following requirements:', 'facebook-for-woocommerce' ); ?></p>
		<ul class="ul-disc">
			<li><?php esc_html_e( 'Has a price defined', 'facebook-for-woocommerce' ); ?></li>
			<li>
			<?php
			echo esc_html(
				sprintf(
				/* translators: Placeholders: %1$s - <strong> opening HTML tag, %2$s - </strong> closing HTML tag */
					__( 'Has %1$sManage Stock%2$s enabled on the %1$sInventory%2$s tab', 'facebook-for-woocommerce' ),
					'<strong>',
					'</strong>'
				)
			);
			?>
			</li>
			<li>
			<?php
			echo esc_html(
				sprintf(
				/* translators: Placeholders: %1$s - <strong> opening HTML tag, %2$s - </strong> closing HTML tag */
					__( 'Has the %1$sFacebook Sync%2$s setting set to "Sync and show" or "Sync and hide"', 'facebook-for-woocommerce' ),
					'<strong>',
					'</strong>'
				)
			);
			?>
			</li>
		</ul>
		<?php
		return ob_get_clean();
	}

	/**
	 * Gets the markup for the buttons used in the product not ready modal.
	 *
	 * @since 2.1.0
	 *
	 * @return string
	 */
	private function get_product_not_ready_modal_buttons() {
		ob_start();
		?>
		<button
			id="btn-ok"
			class="button button-large button-primary"
		><?php esc_html_e( 'Close', 'facebook-for-woocomerce' ); ?></button>
		<?php
		return ob_get_clean();
	}

	/**
	 * Gets the markup for the message used in the product removed from sync confirm modal.
	 *
	 * @internal
	 *
	 * @since 2.3.0
	 *
	 * @return string
	 */
	private function get_product_removed_from_sync_confirm_modal_message() {
		ob_start();
		?>
		<p>
		<?php
		printf(
			/* translators: Placeholders: %1$s - opening <a> link tag, %2$s - closing </a> link tag */
			esc_html__( 'You\'re removing a product from the Facebook sync that is currently listed in your %1$sFacebook catalog%2$s. Would you like to delete the product from the Facebook catalog as well?', 'facebook-for-woocommerce' ),
			'<a href="https://www.facebook.com/products" target="_blank">',
			'</a>'
		);
		?>
			</p>
		<?php
		return ob_get_clean();
	}

	/**
	 * Gets the markup for the buttons used in the product removed from sync confirm modal.
	 *
	 * @internal
	 *
	 * @since 2.3.0
	 *
	 * @return string
	 */
	private function get_product_removed_from_sync_confirm_modal_buttons() {
		ob_start();
		?>
		<button
			id="btn-ok"
			class="button button-large button-primary"
		><?php esc_html_e( 'Remove from sync only', 'facebook-for-woocomerce' ); ?></button>

		<button
			class="button button-large button-delete button-product-removed-from-sync-delete"
		><?php esc_html_e( 'Remove from sync and delete', 'facebook-for-woocomerce' ); ?></button>

		<button
			class="button button-large button-product-removed-from-sync-cancel"
		><?php esc_html_e( 'Cancel', 'facebook-for-woocomerce' ); ?></button>
		<?php
		return ob_get_clean();
	}

	/**
	 * Gets the product category admin handler instance.
	 *
	 * @since 2.1.0
	 *
	 * @return Product_Categories
	 */
	public function get_product_categories_handler() {
		return $this->product_categories;
	}

	/**
	 * Adds Facebook-related columns in the products edit screen.
	 *
	 * @internal
	 *
	 * @since 1.10.0
	 *
	 * @param array $columns array of keys and labels
	 * @return array
	 */
	public function add_product_list_table_columns( $columns ) {
		$columns['facebook_sync'] = __( 'Facebook sync', 'facebook-for-woocommerce' );
		return $columns;
	}

	/**
	 * Outputs sync information for products in the edit screen.
	 *
	 * @internal
	 *
	 * @since 1.10.0
	 *
	 * @param string $column the current column in the posts table
	 */
	public function add_product_list_table_columns_content( $column ) {
		global $post;

		if ( 'facebook_sync' !== $column ) {
			return;
		}

		$product = wc_get_product( $post );
		if ( $product && Products::product_should_be_synced( $product ) ) {
			if ( Products::is_product_visible( $product ) ) {
				esc_html_e( 'Sync and show', 'facebook-for-woocommerce' );
			} else {
				esc_html_e( 'Sync and hide', 'facebook-for-woocommerce' );
			}
		} else {
			esc_html_e( 'Do not sync', 'facebook-for-woocommerce' );
		}
	}

	/**
	 * Adds a dropdown input to let shop managers filter products by sync setting.
	 *
	 * @internal
	 *
	 * @since 1.10.0
	 */
	public function add_products_by_sync_enabled_input_filter() {
		global $typenow;

		if ( 'product' !== $typenow ) {
			return;
		}

		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$choice = isset( $_GET['fb_sync_enabled'] ) ? (string) sanitize_text_field( wp_unslash( $_GET['fb_sync_enabled'] ) ) : '';
		?>
		<select name="fb_sync_enabled">
			<option value="" <?php selected( $choice, '' ); ?>><?php esc_html_e( 'Filter by Facebook sync setting', 'facebook-for-woocommerce' ); ?></option>
			<option value="<?php echo esc_attr( self::SYNC_MODE_SYNC_AND_SHOW ); ?>" <?php selected( $choice, self::SYNC_MODE_SYNC_AND_SHOW ); ?>><?php esc_html_e( 'Sync and show', 'facebook-for-woocommerce' ); ?></option>
			<option value="<?php echo esc_attr( self::SYNC_MODE_SYNC_AND_HIDE ); ?>" <?php selected( $choice, self::SYNC_MODE_SYNC_AND_HIDE ); ?>><?php esc_html_e( 'Sync and hide', 'facebook-for-woocommerce' ); ?></option>
			<option value="<?php echo esc_attr( self::SYNC_MODE_SYNC_DISABLED ); ?>" <?php selected( $choice, self::SYNC_MODE_SYNC_DISABLED ); ?>><?php esc_html_e( 'Do not sync', 'facebook-for-woocommerce' ); ?></option>
		</select>
		<?php
	}

	/**
	 * Filters products by Facebook sync setting.
	 *
	 * @internal
	 *
	 * @since 1.10.0
	 *
	 * @param array $query_vars product query vars for the edit screen
	 * @return array
	 */
	public function filter_products_by_sync_enabled( $query_vars ) {
		$valid_values = array(
			self::SYNC_MODE_SYNC_AND_SHOW,
			self::SYNC_MODE_SYNC_AND_HIDE,
			self::SYNC_MODE_SYNC_DISABLED,
		);

		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		if ( isset( $_REQUEST['fb_sync_enabled'] ) && in_array( $_REQUEST['fb_sync_enabled'], $valid_values, true ) ) {
			// store original meta query
			$original_meta_query = ! empty( $query_vars['meta_query'] ) ? $query_vars['meta_query'] : [];
			// phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$filter_value = wc_clean( wp_unslash( $_REQUEST['fb_sync_enabled'] ) );
			// by default use an "AND" clause if multiple conditions exist for a meta query
			if ( ! empty( $query_vars['meta_query'] ) ) {
				$query_vars['meta_query']['relation'] = 'AND';
			} else {
				$query_vars['meta_query'] = [];
			}

			if ( self::SYNC_MODE_SYNC_AND_SHOW === $filter_value ) {
				// when checking for products with sync enabled we need to check both "yes" and meta not set, this requires adding an "OR" clause
				$query_vars = $this->add_query_vars_to_find_products_with_sync_enabled( $query_vars );
				// only get visible products (both "yes" and meta not set)
				$query_vars = $this->add_query_vars_to_find_visible_products( $query_vars );
				// since we record enabled status and visibility on child variations, we need to query variable products found for their children to exclude them from query results
				$exclude_products = [];
				$found_ids        = get_posts( array_merge( $query_vars, array( 'fields' => 'ids' ) ) );
				$found_products   = empty( $found_ids ) ? [] : wc_get_products(
					array(
						'limit'   => -1,
						'type'    => 'variable',
						'include' => $found_ids,
					)
				);
				/** @var \WC_Product[] $found_products */
				foreach ( $found_products as $product ) {
					if ( ! Products::is_sync_enabled_for_product( $product )
						 || ! Products::is_product_visible( $product ) ) {
						$exclude_products[] = $product->get_id();
					}
				}

				if ( ! empty( $exclude_products ) ) {
					if ( ! empty( $query_vars['post__not_in'] ) ) {
						$query_vars['post__not_in'] = array_merge( $query_vars['post__not_in'], $exclude_products );
					} else {
						$query_vars['post__not_in'] = $exclude_products;
					}
				}
			} elseif ( self::SYNC_MODE_SYNC_AND_HIDE === $filter_value ) {
				// when checking for products with sync enabled we need to check both "yes" and meta not set, this requires adding an "OR" clause
				$query_vars = $this->add_query_vars_to_find_products_with_sync_enabled( $query_vars );
				// only get hidden products
				$query_vars = $this->add_query_vars_to_find_hidden_products( $query_vars );
				// since we record enabled status and visibility on child variations, we need to query variable products found for their children to exclude them from query results
				$exclude_products = [];
				$found_ids        = get_posts( array_merge( $query_vars, array( 'fields' => 'ids' ) ) );
				$found_products   = empty( $found_ids ) ? [] : wc_get_products(
					array(
						'limit'   => -1,
						'type'    => 'variable',
						'include' => $found_ids,
					)
				);
				/** @var \WC_Product[] $found_products */
				foreach ( $found_products as $product ) {
					if ( ! Products::is_sync_enabled_for_product( $product )
						 || Products::is_product_visible( $product ) ) {
						$exclude_products[] = $product->get_id();
					}
				}

				if ( ! empty( $exclude_products ) ) {
					if ( ! empty( $query_vars['post__not_in'] ) ) {
						$query_vars['post__not_in'] = array_merge( $query_vars['post__not_in'], $exclude_products );
					} else {
						$query_vars['post__not_in'] = $exclude_products;
					}
				}

				// for the same reason, we also need to include variable products with hidden children
				$include_products  = [];
				$hidden_variations = get_posts(
					array(
						'limit'      => -1,
						'post_type'  => 'product_variation',
						'meta_query' => array(
							'key'   => Products::VISIBILITY_META_KEY,
							'value' => 'no',
						),
					)
				);

				/** @var \WP_Post[] $hidden_variations */
				foreach ( $hidden_variations as $variation_post ) {
					$variable_product = wc_get_product( $variation_post->post_parent );
					// we need this check because we only want products with ALL variations hidden
					if ( $variable_product instanceof \WC_Product && Products::is_sync_enabled_for_product( $variable_product )
						 && ! Products::is_product_visible( $variable_product ) ) {
						$include_products[] = $variable_product->get_id();
					}
				}
			} else {
				// self::SYNC_MODE_SYNC_DISABLED
				// products to be included in the QUERY, not in the sync
				$include_products = [];
				$found_ids        = [];
				$integration             = facebook_for_woocommerce()->get_integration();
				$excluded_categories_ids = $integration ? $integration->get_excluded_product_category_ids() : [];
				$excluded_tags_ids       = $integration ? $integration->get_excluded_product_tag_ids() : [];
				// get the product IDs from all products in excluded taxonomies
				if ( $excluded_categories_ids || $excluded_tags_ids ) {
					$tax_query_vars   = $this->maybe_add_tax_query_for_excluded_taxonomies( $query_vars, true );
					$include_products = array_merge( $include_products, get_posts( array_merge( $tax_query_vars, array( 'fields' => 'ids' ) ) ) );
				}
				$excluded_products = get_posts(
					array(
						'fields'     => 'ids',
						'limit'      => -1,
						'post_type'  => 'product',
						'meta_query' => array(
							array(
								'key'   => Products::SYNC_ENABLED_META_KEY,
								'value' => 'no',
							),
						),
					)
				);
				$include_products = array_unique( array_merge( $include_products, $excluded_products ) );
				// since we record enabled status and visibility on child variations,
				// we need to include variable products with excluded children
				$excluded_variations = get_posts(
					array(
						'limit'      => -1,
						'post_type'  => 'product_variation',
						'meta_query' => array(
							array(
								'key'   => Products::SYNC_ENABLED_META_KEY,
								'value' => 'no',
							),
						),
					)
				);
				/** @var \WP_Post[] $excluded_variations */
				foreach ( $excluded_variations as $variation_post ) {
					$variable_product = wc_get_product( $variation_post->post_parent );
					// we need this check because we only want products with ALL variations excluded
					if ( ! Products::is_sync_enabled_for_product( $variable_product ) ) {
						$include_products[] = $variable_product->get_id();
					}
				}
			}//end if

			if ( ! empty( $include_products ) ) {
				// we are going to query by ID, so we want to include all the IDs from before
				$include_products = array_unique( array_merge( $found_ids, $include_products ) );
				if ( ! empty( $query_vars['post__in'] ) ) {
					$query_vars['post__in'] = array_merge( $query_vars['post__in'], $include_products );
				} else {
					$query_vars['post__in'] = $include_products;
				}

				// remove sync enabled and visibility meta queries
				if ( ! empty( $original_meta_query ) ) {
					$query_vars['meta_query'] = $original_meta_query;
				} else {
					unset( $query_vars['meta_query'] );
				}
			}
		}//end if

		if ( isset( $query_vars['meta_query'] ) && empty( $query_vars['meta_query'] ) ) {
			unset( $query_vars['meta_query'] );
		}

		return $query_vars;
	}


	/**
	 * Adds query vars to limit the results to products that have sync enabled.
	 *
	 * @since 1.10.0
	 *
	 * @param array $query_vars
	 * @return array
	 */
	private function add_query_vars_to_find_products_with_sync_enabled( array $query_vars ) {
		$meta_query = array(
			'relation' => 'OR',
			array(
				'key'   => Products::SYNC_ENABLED_META_KEY,
				'value' => 'yes',
			),
			array(
				'key'     => Products::SYNC_ENABLED_META_KEY,
				'compare' => 'NOT EXISTS',
			),
		);

		if ( empty( $query_vars['meta_query'] ) ) {
			$query_vars['meta_query'] = $meta_query;
		} elseif ( is_array( $query_vars['meta_query'] ) ) {
			$original_meta_query      = $query_vars['meta_query'];
			$query_vars['meta_query'] = array(
				'relation' => 'AND',
				$original_meta_query,
				$meta_query,
			);
		}

		// check whether the product belongs to an excluded product category or tag
		$query_vars = $this->maybe_add_tax_query_for_excluded_taxonomies( $query_vars );
		return $query_vars;
	}


	/**
	 * Adds a tax query to filter in/out products in excluded product categories and product tags.
	 *
	 * @since 1.10.0
	 *
	 * @param array $query_vars product query vars for the edit screen
	 * @param bool  $in whether we want to return products in excluded categories and tags or not
	 * @return array
	 */
	private function maybe_add_tax_query_for_excluded_taxonomies( $query_vars, $in = false ) {
		$integration = facebook_for_woocommerce()->get_integration();
		if ( $integration ) {
			$tax_query               = [];
			$excluded_categories_ids = $integration->get_excluded_product_category_ids();
			if ( $excluded_categories_ids ) {
				$tax_query[] = array(
					'taxonomy' => 'product_cat',
					'terms'    => $excluded_categories_ids,
					'field'    => 'term_id',
					'operator' => $in ? 'IN' : 'NOT IN',
				);
			}
			$excluded_tags_ids = $integration->get_excluded_product_tag_ids();
			if ( $excluded_tags_ids ) {
				$tax_query[] = array(
					'taxonomy' => 'product_tag',
					'terms'    => $excluded_tags_ids,
					'field'    => 'term_id',
					'operator' => $in ? 'IN' : 'NOT IN',
				);
			}

			if ( count( $tax_query ) > 1 ) {
				$tax_query['relation'] = $in ? 'OR' : 'AND';
			}

			if ( $tax_query && empty( $query_vars['tax_query'] ) ) {
				$query_vars['tax_query'] = $tax_query;
			} elseif ( $tax_query && is_array( $query_vars ) ) {
				$query_vars['tax_query'][] = $tax_query;
			}
		}//end if

		return $query_vars;
	}


	/**
	 * Adds query vars to limit the results to visible products.
	 *
	 * @since 2.0.0
	 *
	 * @param array $query_vars
	 * @return array
	 */
	private function add_query_vars_to_find_visible_products( array $query_vars ) {
		$visibility_meta_query = array(
			'relation' => 'OR',
			array(
				'key'   => Products::VISIBILITY_META_KEY,
				'value' => 'yes',
			),
			array(
				'key'     => Products::VISIBILITY_META_KEY,
				'compare' => 'NOT EXISTS',
			),
		);

		if ( empty( $query_vars['meta_query'] ) ) {
			$query_vars['meta_query'] = $visibility_meta_query;
		} elseif ( is_array( $query_vars['meta_query'] ) ) {
			$enabled_meta_query       = $query_vars['meta_query'];
			$query_vars['meta_query'] = array(
				'relation' => 'AND',
				$enabled_meta_query,
				$visibility_meta_query,
			);
		}

		return $query_vars;
	}


	/**
	 * Adds query vars to limit the results to hidden products.
	 *
	 * @since 2.0.0
	 *
	 * @param array $query_vars
	 * @return array
	 */
	private function add_query_vars_to_find_hidden_products( array $query_vars ) {
		$visibility_meta_query = array(
			'key'   => Products::VISIBILITY_META_KEY,
			'value' => 'no',
		);

		if ( empty( $query_vars['meta_query'] ) ) {
			$query_vars['meta_query'] = $visibility_meta_query;
		} elseif ( is_array( $query_vars['meta_query'] ) ) {
			$enabled_meta_query       = $query_vars['meta_query'];
			$query_vars['meta_query'] = array(
				'relation' => 'AND',
				$enabled_meta_query,
				$visibility_meta_query,
			);
		}

		return $query_vars;
	}


	/**
	 * Adds bulk actions in the products edit screen.
	 *
	 * @internal
	 *
	 * @since 1.10.0
	 *
	 * @param array $bulk_actions array of bulk action keys and labels
	 * @return array
	 */
	public function add_products_sync_bulk_actions( $bulk_actions ) {
		$bulk_actions['facebook_include'] = __( 'Include in Facebook sync', 'facebook-for-woocommerce' );
		$bulk_actions['facebook_exclude'] = __( 'Exclude from Facebook sync', 'facebook-for-woocommerce' );
		return $bulk_actions;
	}


	/**
	 * Handles a Facebook product sync bulk action.
	 *
	 * @internal
	 *
	 * @since 1.10.0
	 *
	 * @param string $redirect admin URL used by WordPress to redirect after performing the bulk action
	 * @return string
	 */
	public function handle_products_sync_bulk_actions( $redirect ) {

		// primary dropdown at the top of the list table
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$action = isset( $_REQUEST['action'] ) && -1 !== (int) $_REQUEST['action'] ? sanitize_text_field( wp_unslash( $_REQUEST['action'] ) ) : null;

		// secondary dropdown at the bottom of the list table
		if ( ! $action ) {
			// phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$action = isset( $_REQUEST['action2'] ) && -1 !== (int) $_REQUEST['action2'] ? sanitize_text_field( wp_unslash( $_REQUEST['action2'] ) ) : null;
		}

		if ( $action && in_array( $action, array( 'facebook_include', 'facebook_exclude' ), true ) ) {
			$products = [];
			// phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$product_ids = isset( $_REQUEST['post'] ) && is_array( $_REQUEST['post'] ) ? array_map( 'absint', $_REQUEST['post'] ) : [];
			if ( ! empty( $product_ids ) ) {
				/** @var \WC_Product[] $enabling_sync_virtual_products virtual products that are being included */
				$enabling_sync_virtual_products = [];
				/** @var \WC_Product_Variation[] $enabling_sync_virtual_variations virtual variations that are being included */
				$enabling_sync_virtual_variations = [];
				foreach ( $product_ids as $product_id ) {
					if ( $product = wc_get_product( $product_id ) ) {
						$products[] = $product;
						if ( 'facebook_include' === $action ) {
							if ( $product->is_virtual() && ! Products::is_sync_enabled_for_product( $product ) ) {
								$enabling_sync_virtual_products[ $product->get_id() ] = $product;
							} else {
								if ( $product->is_type( 'variable' ) ) {
									// collect the virtual variations
									foreach ( $product->get_children() as $variation_id ) {
										$variation = wc_get_product( $variation_id );
										if ( $variation && $variation->is_virtual() && ! Products::is_sync_enabled_for_product( $variation ) ) {
											$enabling_sync_virtual_products[ $product->get_id() ]     = $product;
											$enabling_sync_virtual_variations[ $variation->get_id() ] = $variation;
										}
									}
								}
							}//end if
						}//end if
					}//end if
				}//end foreach

				if ( ! empty( $enabling_sync_virtual_products ) || ! empty( $enabling_sync_virtual_variations ) ) {
					// display notice if enabling sync for virtual products or variations
					set_transient( 'wc_' . facebook_for_woocommerce()->get_id() . '_enabling_virtual_products_sync_show_notice_' . get_current_user_id(), true, 15 * MINUTE_IN_SECONDS );
					set_transient( 'wc_' . facebook_for_woocommerce()->get_id() . '_enabling_virtual_products_sync_affected_products_' . get_current_user_id(), array_keys( $enabling_sync_virtual_products ), 15 * MINUTE_IN_SECONDS );

					// set visibility for virtual products
					foreach ( $enabling_sync_virtual_products as $product ) {

						// do not set visibility for variable products
						if ( ! $product->is_type( 'variable' ) ) {
							Products::set_product_visibility( $product, false );
						}
					}

					// set visibility for virtual variations
					foreach ( $enabling_sync_virtual_variations as $variation ) {

						Products::set_product_visibility( $variation, false );
					}
				}//end if

				if ( 'facebook_include' === $action ) {

					Products::enable_sync_for_products( $products );

					$this->resync_products( $products );

				} elseif ( 'facebook_exclude' === $action ) {

					Products::disable_sync_for_products( $products );

					self::add_product_disabled_sync_notice( count( $products ) );
				}
			}//end if
		}//end if

		return $redirect;
	}


	/**
	 * Re-syncs the given products.
	 *
	 * @since 2.0.0
	 *
	 * @param \WC_Product $products
	 */
	private function resync_products( array $products ) {

		$integration = facebook_for_woocommerce()->get_integration();

		// re-sync each product
		foreach ( $products as $product ) {

			if ( $product->is_type( 'variable' ) ) {

				// create product group and schedule product variations to be synced in the background
				$integration->on_product_publish( $product->get_id() );

			} elseif ( $integration->product_should_be_synced( $product ) ) {

				// schedule simple products to be updated or deleted from the catalog in the background
				if ( Products::product_should_be_deleted( $product ) ) {
					facebook_for_woocommerce()->get_products_sync_handler()->delete_products( array( $product->get_id() ) );
				} else {
					facebook_for_woocommerce()->get_products_sync_handler()->create_or_update_products( array( $product->get_id() ) );
				}
			}
		}
	}


	/**
	 * Adds a transient so an informational notice is displayed on the next page load.
	 *
	 * @since 2.0.0
	 *
	 * @param int $count number of products
	 */
	public static function add_product_disabled_sync_notice( $count = 1 ) {

		if ( ! facebook_for_woocommerce()->get_admin_notice_handler()->is_notice_dismissed( 'wc-' . facebook_for_woocommerce()->get_id_dasherized() . '-product-disabled-sync' ) ) {
			set_transient( 'wc_' . facebook_for_woocommerce()->get_id() . '_show_product_disabled_sync_notice_' . get_current_user_id(), $count, MINUTE_IN_SECONDS );
		}
	}


	/**
	 * Adds a message for after a product or set of products get excluded from sync.
	 *
	 * @since 2.0.0
	 */
	public function maybe_show_product_disabled_sync_notice() {

		$transient_name = 'wc_' . facebook_for_woocommerce()->get_id() . '_show_product_disabled_sync_notice_' . get_current_user_id();
		$message_id     = 'wc-' . facebook_for_woocommerce()->get_id_dasherized() . '-product-disabled-sync';

		if ( ( $count = get_transient( $transient_name ) ) && ( Helper::is_current_screen( 'edit-product' ) || Helper::is_current_screen( 'product' ) ) ) {

			$message = sprintf(
				/* translators: Placeholders: %1$s - <strong> tag, %2$s - </strong> tag, %3$s - <a> tag, %4$s - <a> tag */
				_n( '%1$sHeads up!%2$s If this product was previously visible in Facebook, you may need to delete it from the %3$sFacebook catalog%4$s to completely hide it from customer view.', '%1$sHeads up!%2$s If these products were previously visible in Facebook, you may need to delete them from the %3$sFacebook catalog%4$s to completely hide them from customer view.', $count, 'facebook-for-woocommerce' ),
				'<strong>',
				'</strong>',
				'<a href="https://facebook.com/products" target="_blank">',
				'</a>'
			);

			$message .= '<a class="button js-wc-plugin-framework-notice-dismiss">' . esc_html__( "Don't show this notice again", 'facebook-for-woocommerce' ) . '</a>';

			facebook_for_woocommerce()->get_admin_notice_handler()->add_admin_notice(
				$message,
				$message_id,
				array(
					'dismissible' => false,
					// we add our own dismiss button
														'notice_class' => 'notice-info',
				)
			);

			delete_transient( $transient_name );
		}//end if
	}


	/**
	 * Prints a notice on products page to inform users that the virtual products selected for the Include bulk action will have sync enabled, but will be hidden.
	 *
	 * @internal
	 *
	 * @since 1.11.3-dev.2
	 */
	public function maybe_add_enabling_virtual_products_sync_notice() {

		$show_notice_transient_name       = 'wc_' . facebook_for_woocommerce()->get_id() . '_enabling_virtual_products_sync_show_notice_' . get_current_user_id();
		$affected_products_transient_name = 'wc_' . facebook_for_woocommerce()->get_id() . '_enabling_virtual_products_sync_affected_products_' . get_current_user_id();

		if ( Helper::is_current_screen( 'edit-product' ) && get_transient( $show_notice_transient_name ) && ( $affected_products = get_transient( $affected_products_transient_name ) ) ) {

			$message = sprintf(
				esc_html(
				/* translators: Placeholders: %1$s - number of affected products, %2$s opening HTML <a> tag, %3$s - closing HTML </a> tag, %4$s - opening HTML <a> tag, %5$s - closing HTML </a> tag */
					_n(
						'%2$s%1$s product%3$s or some of its variations could not be updated to show in the Facebook catalog — %4$sFacebook Commerce Policies%5$s prohibit selling some product types (like virtual products). You may still advertise Virtual products on Facebook.',
						'%2$s%1$s products%3$s or some of their variations could not be updated to show in the Facebook catalog — %4$sFacebook Commerce Policies%5$s prohibit selling some product types (like virtual products). You may still advertise Virtual products on Facebook.',
						count( $affected_products ),
						'facebook-for-woocommerce'
					)
				),
				count( $affected_products ),
				'<a href="' . esc_url( add_query_arg( array( 'facebook_show_affected_products' => 1 ) ) ) . '">',
				'</a>',
				'<a href="https://www.facebook.com/policies/commerce/prohibited_content/subscriptions_and_digital_products" target="_blank">',
				'</a>'
			);

			facebook_for_woocommerce()->get_admin_notice_handler()->add_admin_notice(
				$message,
				'wc-' . facebook_for_woocommerce()->get_id_dasherized() . '-enabling-virtual-products-sync',
				array(
					'dismissible'  => false,
					'notice_class' => 'notice-info',
				)
			);

			delete_transient( $show_notice_transient_name );
		}//end if
	}


	/**
	 * Tweaks the query to show a filtered view with the affected products.
	 *
	 * @internal
	 *
	 * @since 2.0.0
	 *
	 * @param array $query_vars product query vars for the edit screen
	 * @return array
	 */
	public function filter_virtual_products_affected_enabling_sync( $query_vars ) {

		$transient_name = 'wc_' . facebook_for_woocommerce()->get_id() . '_enabling_virtual_products_sync_affected_products_' . get_current_user_id();

		if ( isset( $_GET['facebook_show_affected_products'] ) && Helper::is_current_screen( 'edit-product' ) && $affected_products = get_transient( $transient_name ) ) {

			$query_vars['post__in'] = $affected_products;
		}

		return $query_vars;
	}


	/**
	 * Prints a notice to inform sync mode has been automatically set to Sync and hide for virtual products and variations.
	 *
	 * @internal
	 *
	 * @since 2.0.0
	 */
	public function add_handled_virtual_products_variations_notice() {

		if ( 'yes' === get_option( 'wc_facebook_background_handle_virtual_products_variations_complete', 'no' ) &&
			 'yes' !== get_option( 'wc_facebook_background_handle_virtual_products_variations_skipped', 'no' ) ) {

			facebook_for_woocommerce()->get_admin_notice_handler()->add_admin_notice(
				sprintf(
					/* translators: Placeholders: %1$s - opening HTML <strong> tag, %2$s - closing HTML </strong> tag, %3$s - opening HTML <a> tag, %4$s - closing HTML </a> tag */
					esc_html__( '%1$sHeads up!%2$s Facebook\'s %3$sCommerce Policies%4$s do not support selling virtual products, so we have hidden your synced Virtual products in your Facebook catalog. You may still advertise Virtual products on Facebook.', 'facebook-for-woocommerce' ),
					'<strong>',
					'</strong>',
					'<a href="https://www.facebook.com/policies/commerce/prohibited_content/subscriptions_and_digital_products" target="_blank">',
					'</a>'
				),
				'wc-' . facebook_for_woocommerce()->get_id_dasherized() . '-updated-virtual-products-sync',
				array(
					'notice_class'            => 'notice-info',
					'always_show_on_settings' => false,
				)
			);
		}
	}


	/**
	 * Adds a new tab to the Product edit page.
	 *
	 * @internal
	 *
	 * @since 1.10.0
	 *
	 * @param array $tabs product tabs
	 * @return array
	 */
	public function add_product_settings_tab( $tabs ) {

		$tabs['fb_commerce_tab'] = array(
			'label'  => __( 'Facebook', 'facebook-for-woocommerce' ),
			'target' => 'facebook_options',
			'class'  => array( 'show_if_simple', 'show_if_variable', 'show_if_external' ),
		);

		return $tabs;
	}


	/**
	 * Adds content to the new Facebook tab on the Product edit page.
	 *
	 * @internal
	 *
	 * @since 1.10.0
	 */
	public function add_product_settings_tab_content() {
		global $post;


		// all products have sync enabled unless explicitly disabled
		$sync_enabled = 'no' !== get_post_meta( $post->ID, Products::SYNC_ENABLED_META_KEY, true );
		$is_visible   = ( $visibility = get_post_meta( $post->ID, Products::VISIBILITY_META_KEY, true ) ) ? wc_string_to_bool( $visibility ) : true;
		$product 	  = wc_get_product( $post );

		$description  = get_post_meta( $post->ID, \WC_Facebookcommerce_Integration::FB_PRODUCT_DESCRIPTION, true );
		$price        = get_post_meta( $post->ID, \WC_Facebook_Product::FB_PRODUCT_PRICE, true );
		$image_source = get_post_meta( $post->ID, Products::PRODUCT_IMAGE_SOURCE_META_KEY, true );
		$image        = get_post_meta( $post->ID, \WC_Facebook_Product::FB_PRODUCT_IMAGE, true );

		if ( $sync_enabled ) {
			$sync_mode = $is_visible ? self::SYNC_MODE_SYNC_AND_SHOW : self::SYNC_MODE_SYNC_AND_HIDE;
		} else {
			$sync_mode = self::SYNC_MODE_SYNC_DISABLED;
		}

		// 'id' attribute needs to match the 'target' parameter set above
		?>
		<div id='facebook_options' class='panel woocommerce_options_panel'>
			<div class='options_group hide_if_variable'>
				<?php

				woocommerce_wp_select(
					array(
						'id'      => 'wc_facebook_sync_mode',
						'label'   => __( 'Facebook sync', 'facebook-for-woocommerce' ),
						'options' => array(
							self::SYNC_MODE_SYNC_AND_SHOW => __( 'Sync and show in catalog', 'facebook-for-woocommerce' ),
							self::SYNC_MODE_SYNC_AND_HIDE => __( 'Sync and hide in catalog', 'facebook-for-woocommerce' ),
							self::SYNC_MODE_SYNC_DISABLED => __( 'Do not sync', 'facebook-for-woocommerce' ),
						),
						'value'   => $sync_mode,
					)
				);

				woocommerce_wp_textarea_input(
					array(
						'id'          => \WC_Facebookcommerce_Integration::FB_PRODUCT_DESCRIPTION,
						'label'       => __( 'Facebook Description', 'facebook-for-woocommerce' ),
						'desc_tip'    => true,
						'description' => __( 'Custom (plain-text only) description for product on Facebook. If blank, product description will be used. If product description is blank, shortname will be used.', 'facebook-for-woocommerce' ),
						'cols'        => 40,
						'rows'        => 20,
						'value'       => $description,
						'class'       => 'short enable-if-sync-enabled',
					)
				);

				woocommerce_wp_radio(
					array(
						'id'            => 'fb_product_image_source',
						'label'         => __( 'Facebook Product Image', 'facebook-for-woocommerce' ),
						'desc_tip'      => true,
						'description'   => __( 'Choose the product image that should be synced to the Facebook catalog for this product. If using a custom image, please enter an absolute URL (e.g. https://domain.com/image.jpg).', 'facebook-for-woocommerce' ),
						'options'       => array(
							Products::PRODUCT_IMAGE_SOURCE_PRODUCT => __( 'Use WooCommerce image', 'facebook-for-woocommerce' ),
							Products::PRODUCT_IMAGE_SOURCE_CUSTOM  => __( 'Use custom image', 'facebook-for-woocommerce' ),
						),
						'value'         => $image_source ?: Products::PRODUCT_IMAGE_SOURCE_PRODUCT,
						'class'         => 'short enable-if-sync-enabled js-fb-product-image-source',
						'wrapper_class' => 'fb-product-image-source-field',
					)
				);

				woocommerce_wp_text_input(
					array(
						'id'    => \WC_Facebook_Product::FB_PRODUCT_IMAGE,
						'label' => __( 'Custom Image URL', 'facebook-for-woocommerce' ),
						'value' => $image,
						'class' => sprintf( 'enable-if-sync-enabled product-image-source-field show-if-product-image-source-%s', Products::PRODUCT_IMAGE_SOURCE_CUSTOM ),
					)
				);

				woocommerce_wp_text_input(
					array(
						'id'          => \WC_Facebook_Product::FB_PRODUCT_PRICE,
						'label'       => sprintf(
						 /* translators: Placeholders %1$s - WC currency symbol */
							__( 'Facebook Price (%1$s)', 'facebook-for-woocommerce' ),
							get_woocommerce_currency_symbol()
						),
						'desc_tip'    => true,
						'description' => __( 'Custom price for product on Facebook. Please enter in monetary decimal (.) format without thousand separators and currency symbols. If blank, product price will be used.', 'facebook-for-woocommerce' ),
						'cols'        => 40,
						'rows'        => 60,
						'value'       => $price,
						'class'       => 'enable-if-sync-enabled',
					)
				);

				woocommerce_wp_hidden_input(
					array(
						'id'    => \WC_Facebook_Product::FB_REMOVE_FROM_SYNC,
						'value' => '',
					)
				);
				?>
			</div>
			<?php
			$commerce_handler = facebook_for_woocommerce()->get_commerce_handler();
			?>
			<?php if ( $commerce_handler->is_connected() && $commerce_handler->is_available() ) : ?>
				<div class='wc-facebook-commerce-options-group options_group'>
					<?php
					if ( $product instanceof \WC_Product ) {
						\WooCommerce\Facebook\Admin\Products::render_commerce_fields( $product );
					}
					?>
			</div>
			<?php endif; ?>
			<div class='wc-facebook-commerce-options-group options_group'>
				<?php \WooCommerce\Facebook\Admin\Products::render_google_product_category_fields_and_enhanced_attributes( $product ); ?>
			</div>
		</div>
		<?php
	}


	/**
	 * Outputs the Facebook settings fields for a single variation.
	 *
	 * @internal
	 *
	 * @since 1.10.0
	 *
	 * @param int      $index the index of the current variation
	 * @param array    $variation_data unused
	 * @param \WC_Post $post the post type for the current variation
	 */
	public function add_product_variation_edit_fields( $index, $variation_data, $post ) {

		$variation = wc_get_product( $post );

		if ( ! $variation instanceof \WC_Product_Variation ) {
			return;
		}

		$parent = wc_get_product( $variation->get_parent_id() );

		if ( ! $parent instanceof \WC_Product ) {
			return;
		}

		$sync_enabled = 'no' !== $this->get_product_variation_meta( $variation, Products::SYNC_ENABLED_META_KEY, $parent );
		$is_visible   = ( $visibility = $this->get_product_variation_meta( $variation, Products::VISIBILITY_META_KEY, $parent ) ) ? wc_string_to_bool( $visibility ) : true;

		$description  = $this->get_product_variation_meta( $variation, \WC_Facebookcommerce_Integration::FB_PRODUCT_DESCRIPTION, $parent );
		$price        = $this->get_product_variation_meta( $variation, \WC_Facebook_Product::FB_PRODUCT_PRICE, $parent );
		$image_url    = $this->get_product_variation_meta( $variation, \WC_Facebook_Product::FB_PRODUCT_IMAGE, $parent );
		$image_source = $variation->get_meta( Products::PRODUCT_IMAGE_SOURCE_META_KEY );

		if ( $sync_enabled ) {
			$sync_mode = $is_visible ? self::SYNC_MODE_SYNC_AND_SHOW : self::SYNC_MODE_SYNC_AND_HIDE;
		} else {
			$sync_mode = self::SYNC_MODE_SYNC_DISABLED;
		}

		woocommerce_wp_select(
			array(
				'id'            => "variable_facebook_sync_mode$index",
				'name'          => "variable_facebook_sync_mode[$index]",
				'label'         => __( 'Facebook sync', 'facebook-for-woocommerce' ),
				'options'       => array(
					self::SYNC_MODE_SYNC_AND_SHOW => __( 'Sync and show in catalog', 'facebook-for-woocommerce' ),
					self::SYNC_MODE_SYNC_AND_HIDE => __( 'Sync and hide in catalog', 'facebook-for-woocommerce' ),
					self::SYNC_MODE_SYNC_DISABLED => __( 'Do not sync', 'facebook-for-woocommerce' ),
				),
				'value'         => $sync_mode,
				'class'         => 'js-variable-fb-sync-toggle',
				'wrapper_class' => 'form-row form-row-full',
			)
		);

		woocommerce_wp_textarea_input(
			array(
				'id'            => sprintf( 'variable_%s%s', \WC_Facebookcommerce_Integration::FB_PRODUCT_DESCRIPTION, $index ),
				'name'          => sprintf( "variable_%s[$index]", \WC_Facebookcommerce_Integration::FB_PRODUCT_DESCRIPTION ),
				'label'         => __( 'Facebook Description', 'facebook-for-woocommerce' ),
				'desc_tip'      => true,
				'description'   => __( 'Custom (plain-text only) description for product on Facebook. If blank, product description will be used. If product description is blank, shortname will be used.', 'facebook-for-woocommerce' ),
				'cols'          => 40,
				'rows'          => 5,
				'value'         => $description,
				'class'         => 'enable-if-sync-enabled',
				'wrapper_class' => 'form-row form-row-full',
			)
		);

		woocommerce_wp_radio(
			array(
				'id'            => "variable_fb_product_image_source$index",
				'name'          => "variable_fb_product_image_source[$index]",
				'label'         => __( 'Facebook Product Image', 'facebook-for-woocommerce' ),
				'desc_tip'      => true,
				'description'   => __( 'Choose the product image that should be synced to the Facebook catalog for this product. If using a custom image, please enter an absolute URL (e.g. https://domain.com/image.jpg).', 'facebook-for-woocommerce' ),
				'options'       => array(
					Products::PRODUCT_IMAGE_SOURCE_PRODUCT        => __( 'Use variation image', 'facebook-for-woocommerce' ),
					Products::PRODUCT_IMAGE_SOURCE_PARENT_PRODUCT => __( 'Use parent image', 'facebook-for-woocommerce' ),
					Products::PRODUCT_IMAGE_SOURCE_CUSTOM         => __( 'Use custom image', 'facebook-for-woocommerce' ),
				),
				'value'         => $image_source ?: Products::PRODUCT_IMAGE_SOURCE_PRODUCT,
				'class'         => 'enable-if-sync-enabled js-fb-product-image-source',
				'wrapper_class' => 'fb-product-image-source-field',
			)
		);

		woocommerce_wp_text_input(
			array(
				'id'            => sprintf( 'variable_%s%s', \WC_Facebook_Product::FB_PRODUCT_IMAGE, $index ),
				'name'          => sprintf( "variable_%s[$index]", \WC_Facebook_Product::FB_PRODUCT_IMAGE ),
				'label'         => __( 'Custom Image URL', 'facebook-for-woocommerce' ),
				'value'         => $image_url,
				'class'         => sprintf( 'enable-if-sync-enabled product-image-source-field show-if-product-image-source-%s', Products::PRODUCT_IMAGE_SOURCE_CUSTOM ),
				'wrapper_class' => 'form-row form-row-full',
			)
		);

		woocommerce_wp_text_input(
			array(
				'id'            => sprintf( 'variable_%s%s', \WC_Facebook_Product::FB_PRODUCT_PRICE, $index ),
				'name'          => sprintf( "variable_%s[$index]", \WC_Facebook_Product::FB_PRODUCT_PRICE ),
				'label'         => sprintf(
				 /* translators: Placeholders %1$s - WC currency symbol */
					__( 'Facebook Price (%1$s)', 'facebook-for-woocommerce' ),
					get_woocommerce_currency_symbol()
				),
				'desc_tip'      => true,
				'description'   => __( 'Custom price for product on Facebook. Please enter in monetary decimal (.) format without thousand separators and currency symbols. If blank, product price will be used.', 'facebook-for-woocommerce' ),
				'value'         => wc_format_decimal( $price ),
				'class'         => 'enable-if-sync-enabled',
				'wrapper_class' => 'form-row form-full',
			)
		);
	}


	/**
	 * Gets the stored value for the given meta of a product variation.
	 *
	 * If no value is found, we try to use the value stored in the parent product.
	 *
	 * @since 1.10.0
	 *
	 * @param \WC_Product_Variation $variation the product variation
	 * @param string                $key the name of the meta to retrieve
	 * @param \WC_Product           $parent the parent product
	 * @return mixed
	 */
	private function get_product_variation_meta( $variation, $key, $parent ) {
		$value = $variation->get_meta( $key );
		if ( '' === $value && $parent instanceof \WC_Product ) {
			$value = $parent->get_meta( $key );
		}
		return $value;
	}


	/**
	 * Saves the submitted Facebook settings for each variation.
	 *
	 * @internal
	 *
	 * @since 1.10.0
	 *
	 * @param int $variation_id the ID of the product variation being edited
	 * @param int $index the index of the current variation
	 */
	public function save_product_variation_edit_fields( $variation_id, $index ) {
		$variation = wc_get_product( $variation_id );
		if ( ! $variation instanceof \WC_Product_Variation ) {
			return;
		}
		$sync_mode    = isset( $_POST['variable_facebook_sync_mode'][ $index ] ) ? wc_clean( wp_unslash( $_POST['variable_facebook_sync_mode'][ $index ] ) ) : self::SYNC_MODE_SYNC_DISABLED;
		$sync_enabled = self::SYNC_MODE_SYNC_DISABLED !== $sync_mode;
		if ( self::SYNC_MODE_SYNC_AND_SHOW === $sync_mode && $variation->is_virtual() ) {
			// force to Sync and hide
			$sync_mode = self::SYNC_MODE_SYNC_AND_HIDE;
		}
		// phpcs:disable WordPress.Security.NonceVerification.Missing
		if ( $sync_enabled ) {
			Products::enable_sync_for_products( array( $variation ) );
			Products::set_product_visibility( $variation, self::SYNC_MODE_SYNC_AND_HIDE !== $sync_mode );
			$posted_param = 'variable_' . \WC_Facebookcommerce_Integration::FB_PRODUCT_DESCRIPTION;
			$description  = isset( $_POST[ $posted_param ][ $index ] ) ? sanitize_text_field( wp_unslash( $_POST[ $posted_param ][ $index ] ) ) : null;
			$posted_param = 'variable_fb_product_image_source';
			$image_source = isset( $_POST[ $posted_param ][ $index ] ) ? sanitize_key( wp_unslash( $_POST[ $posted_param ][ $index ] ) ) : '';
			$posted_param = 'variable_' . \WC_Facebook_Product::FB_PRODUCT_IMAGE;
			$image_url    = isset( $_POST[ $posted_param ][ $index ] ) ? esc_url_raw( wp_unslash( $_POST[ $posted_param ][ $index ] ) ) : null;
			$posted_param = 'variable_' . \WC_Facebook_Product::FB_PRODUCT_PRICE;
			$price        = isset( $_POST[ $posted_param ][ $index ] ) ? wc_format_decimal( wc_clean( wp_unslash( $_POST[ $posted_param ][ $index ] ) ) ) : '';
			$variation->update_meta_data( \WC_Facebookcommerce_Integration::FB_PRODUCT_DESCRIPTION, $description );
			$variation->update_meta_data( Products::PRODUCT_IMAGE_SOURCE_META_KEY, $image_source );
			$variation->update_meta_data( \WC_Facebook_Product::FB_PRODUCT_IMAGE, $image_url );
			$variation->update_meta_data( \WC_Facebook_Product::FB_PRODUCT_PRICE, $price );
			$variation->save_meta_data();
		} else {
			Products::disable_sync_for_products( array( $variation ) );
		}//end if
		// phpcs:enable WordPress.Security.NonceVerification.Missing
	}


	/**
	 * Outputs a modal template in admin product pages.
	 *
	 * @internal
	 *
	 * @since 1.10.0
	 */
	public function render_modal_template() {
		global $current_screen;

		// bail if not on the products, product edit, or settings screen
		if ( ! $current_screen || ! in_array( $current_screen->id, $this->screen_ids, true ) ) {
			return;
		}
		?>
		<script type="text/template" id="tmpl-facebook-for-woocommerce-modal">
			<div class="wc-backbone-modal facebook-for-woocommerce-modal">
				<div class="wc-backbone-modal-content">
					<section class="wc-backbone-modal-main" role="main">
						<header class="wc-backbone-modal-header">
							<h1><?php esc_html_e( 'Facebook for WooCommerce', 'facebook-for-woocommerce' ); ?></h1>
							<button class="modal-close modal-close-link dashicons dashicons-no-alt">
								<span class="screen-reader-text"><?php esc_html_e( 'Close modal panel', 'facebook-for-woocommerce' ); ?></span>
							</button>
						</header>
						<article>{{{data.message}}}</article>
						<footer>
							<div class="inner">{{{data.buttons}}}</div>
						</footer>
					</section>
				</div>
			</div>
			<div class="wc-backbone-modal-backdrop modal-close"></div>
		</script>
		<?php
	}


	/**
	 * Maybe triggers the modal to open on the product edit screen on page load.
	 *
	 * If the product is set to be synced in Facebook, but belongs to a term that is set to be excluded, the modal prompts the merchant for action.
	 *
	 * @internal
	 *
	 * @since 1.10.0
	 */
	public function validate_product_excluded_terms() {
		global $current_screen, $post;
		if ( $post && $current_screen && $current_screen->id === 'product' ) :
			$product = wc_get_product( $post );
			if ( $product instanceof \WC_Product
				 && Products::is_sync_enabled_for_product( $product )
				 && Products::is_sync_excluded_for_product_terms( $product )
			) :
				?>
				<script type="text/javascript">
					jQuery( document ).ready( function( $ ) {

						var productID   = parseInt( $( 'input#post_ID' ).val(), 10 ),
							productTag  = $( 'textarea[name=\"tax_input[product_tag]\"]' ).val().split( ',' ),
							productCat  = [];

						$( '#taxonomy-product_cat input[name=\"tax_input[product_cat][]\"]:checked' ).each( function() {
							productCat.push( parseInt( $( this ).val(), 10 ) );
						} );

						$.post( facebook_for_woocommerce_products_admin.ajax_url, {
							action:      'facebook_for_woocommerce_set_product_sync_prompt',
							security:     facebook_for_woocommerce_products_admin.set_product_sync_prompt_nonce,
							sync_enabled: 'enabled',
							product:      productID,
							categories:   productCat,
							tags:         productTag
						}, function( response ) {

							if ( response && ! response.success ) {

								$( '#wc-backbone-modal-dialog .modal-close' ).trigger( 'click' );

								new $.WCBackboneModal.View( {
									target: 'facebook-for-woocommerce-modal',
									string: response.data
								} );
							}
						} );
					} );
				</script>
				<?php
			endif;
		endif;
	}
}
