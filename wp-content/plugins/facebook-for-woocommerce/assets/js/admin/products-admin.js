/**
 * Copyright (c) Facebook, Inc. and its affiliates. All Rights Reserved
 *
 * This source code is licensed under the license found in the
 * LICENSE file in the root directory of this source tree.
 *
 * @package FacebookCommerce
 */

jQuery( document ).ready( function( $ ) {

	const pagenow = window.pagenow.length ? window.pagenow : '',
	      typenow = window.typenow.length ? window.typenow : '';


	// products list edit screen
	if ( 'edit-product' === pagenow ) {

		// handle bulk actions
		let submitProductBulkAction = false;

		$( 'input#doaction, input#doaction2' ).on( 'click', function( e ) {

			if ( ! submitProductBulkAction ) {
				e.preventDefault();
			} else {
				return true;
			}

			let $submitButton    = $( this ),
				chosenBulkAction = $submitButton.prev( 'select' ).val();

			if ( 'facebook_include' === chosenBulkAction ) {

				let products = [];

				$.each( $( 'input[name="post[]"]:checked' ), function() {
					products.push( parseInt( $( this ).val(), 10 ) );
				} );

				$.post( facebook_for_woocommerce_products_admin.ajax_url, {
					action:   'facebook_for_woocommerce_set_product_sync_bulk_action_prompt',
					security: facebook_for_woocommerce_products_admin.set_product_sync_bulk_action_prompt_nonce,
					toggle:   chosenBulkAction,
					products: products
				}, function( response ) {

					if ( response && ! response.success ) {

						closeExistingModal();

						// open new modal, populate template with AJAX response data
						new $.WCBackboneModal.View( {
							target: 'facebook-for-woocommerce-modal',
							string: response.data
						} );

					} else {

						// no modal displayed: submit form as normal
						submitProductBulkAction = true;
						$submitButton.trigger( 'click' );
					}
				} );

			} else {

				// no modal displayed: submit form as normal
				submitProductBulkAction = true;
				$submitButton.trigger( 'click' );
			}
		} );
	}


	// individual product edit screen
	if ( 'product' === pagenow ) {

		/**
		 * Toggles (enables/disables) Facebook setting fields.
		 *
		 * @since 1.10.0
		 *
		 * @param {boolean} enabled whether the settings fields should be enabled or not
		 * @param {jQuery} $container a common ancestor of all the elements that can be enabled/disabled
		 */
		function toggleFacebookSettings( enabled, $container ) {

			$container.find( '.enable-if-sync-enabled' ).prop( 'disabled', ! enabled );
		}


		/**
		 * Toggles (shows/hides) Sync and show option and changes the select value if needed.
		 *
		 * @since 2.0.0
		 *
		 * @param {boolean} show whether the Sync and show option should be displayed or not
		 * @param {jQuery} $select the sync mode select
		 */
		function toggleSyncAndShowOption( show, $select ) {

			if ( ! show ) {

				// hide Sync and show option
				$select.find( 'option[value=\'sync_and_show\']' ).hide();

				if ( 'sync_and_show' === $select.val() ) {
					// change selected option to Sync and hide
					$select.val( 'sync_and_hide' );
				}

			} else {

				// show Sync and Show option
				$select.find( 'option[value=\'sync_and_show\']' ).show();

				// restore originally selected option
				if ( $select.prop( 'original' ) ) {
					$select.val( $select.prop( 'original' ) );
				}
			}
		}


		/**
		 * Toggles (shows/hides) the visibility of Facebook Commerce setting fields.
		 *
		 * @since 2.1.0
		 *
		 * @param {boolean} enabled whether the settings fields should be enabled or not
		 * @param {jQuery} $container a common ancestor of all the elements that can be shown/hidden
		 */
		function toggleFacebookCommerceSettings( enabled, $container ) {

			let $group = $container.find( '.wc-facebook-commerce-options-group' );

			if ( enabled ) {
				$group.show();
			} else {
				$group.hide();
			}
		}


		/**
		 * Disables and changes the checked status of the Sell on Instagram setting field.
		 *
		 * Additionally, shows/hides messages explaining that the product is not ready for Commerce.
		 *
		 * @since 2.1.0
		 *
		 * @param {boolean} enabled whether the setting field should be enabled or not
		 * @param {jQuery} $container a common ancestor of all the elements that need to modified
		 */
		function toggleFacebookSellOnInstagramSetting( enabled, $container ) {

			let $field = $container.find( '#wc_facebook_commerce_enabled' );
			let checked = $field.prop( 'original' );

			$field.prop( 'checked', enabled ? checked : false ).prop( 'disabled', ! enabled );

			// trigger change to hide fields based on the new state
			$field.trigger( 'change' );

			// restore previously stored value so that we can later restore the field to the status it had before we disabled it here
			$field.prop( 'original', checked );

			$container.find( '#product-not-ready-notice, #variable-product-not-ready-notice' ).hide();

			if ( isVariableProduct() && ! isSyncEnabledForVariableProduct() ) {
				$container.find( '#variable-product-not-ready-notice' ).show();
			} else if ( ! enabled ) {
				$container.find( '#product-not-ready-notice' ).show();
			}
		}


		/**
		 * Determines whether product properties are configured using appropriate values for Commerce.
		 *
		 * @since 2.1.0
		 *
		 * @return {boolean}
		 */
		function isProductReadyForCommerce() {

			if ( ! isSyncEnabledForProduct() ) {
				return false;
			}

			if ( ! isPriceDefinedForProduct() ) {
				return false;
			}

			if ( ! isStockManagementEnabledForProduct() ) {
				return false;
			}

			return true;
		}


		/**
		 * Determines whether the product or one of its variations has Facebook Sync enabled.
		 *
		 * @since 2.1.0
		 *
		 * @return {boolean}
		 */
		function isSyncEnabledForProduct() {

			if ( isVariableProduct() ) {
				return isSyncEnabledForVariableProduct();
			}

			return isSyncEnabledForSimpleProduct();
		}


		/**
		 * Determines whether the current product has synced variations.
		 *
		 * @since 2.1.0
		 *
		 * @returns {boolean}
		 */
		function isSyncEnabledForVariableProduct() {

			let $fields = $( '.js-variable-fb-sync-toggle' );

			// fallback to the value at page load if the variation fields haven't been loaded
			if ( 0 === $fields.length ) {
				return !! facebook_for_woocommerce_products_admin.is_sync_enabled_for_product;
			}

			// returns true if any of the Facebook Sync settings is set to a value other than 'sync_disabled'
			return !! $fields.map( ( i, element ) => $( element ).val() !== 'sync_disabled' ? element : null ).length;
		}


		/**
		 * Determines whether the product has Facebook Sync enabled.
		 *
		 * @since 2.1.0
		 *
		 * @return {boolean}
		 */
		function isSyncEnabledForSimpleProduct() {

			return simpleProductSyncModeSelect.val() !== 'sync_disabled';
		}


		/**
		 * Determines whether the product has a Regular Price or Facebook Price defined.
		 *
		 * @since 2.1.0
		 *
		 * @return {boolean}
		 */
		function isPriceDefinedForProduct() {

			if ( isVariableProduct() ) {
				// TODO: determine whether variations enabled for sync have a Regular Price or Facebook Price defined {WV 2020-09-19}
				return true;
			}

			return isPriceDefinedForSimpleProduct();
		}


		/**
		 * Determines whether the product is a Variable product.
		 *
		 * @since 2.1.2
		 *
		 * @return {boolean}
		 */
		function isVariableProduct() {

			var productType = $( 'select#product-type' ).val();

			return !! ( productType && productType.match( /variable/ ) );
		}


		/**
		 * Determines whether a simple product has a Regular Price or Facebook Price defined.
		 *
		 * @since 2.1.0
		 *
		 * @return {boolean}
		 */
		function isPriceDefinedForSimpleProduct() {

			return !! ( $( '#_regular_price' ).val() || $( '#fb_product_price' ).val() );
		}


		/**
		 * Determines whether the product has Manage Stock enabled and Stock quantity defined.
		 *
		 * @since 2.1.0
		 *
		 * @return {boolean}
		 */
		function isStockManagementEnabledForProduct() {

			// TODO: determine whether variations enabled for sync have stock management enabled {WV 2020-09-19}

			return isStockManagementEnabledForSimpleProduct();
		}


		/**
		 * Determines whether a simple product has Manage Stock enabled and Stock quantity defined.
		 *
		 * @since 2.1.0
		 *
		 * @return {boolean}
		 */
		function isStockManagementEnabledForSimpleProduct() {

			return !! ( $( '#_manage_stock' ).prop( 'checked' ) && $( '#_stock' ).val() );
		}


		/**
		 * Determines whether we should ask the user to select a Google Product Category.
		 *
		 * @since 2.1.0
		 *
		 * @return {boolean}
		 */
		function shouldShowMissingGoogleProductCategoryAlert() {

			if ( ! $( '#wc_facebook_commerce_enabled' ).prop( 'checked' ) ) {
				return false;
			}

			if ( ! isProductReadyForCommerce() ) {
				return false;
			}

			let selectedCategories = $( '.wc_facebook_commerce_fields .wc-facebook-google-product-category-select' ).map( ( i, element ) => {
				return $( element ).val() ? $( element ).val() : null;
			} );

			return selectedCategories.length < 2;
		}


		/**
		 * Shows an alert asking the user to select a Google product category and sub-category.
		 *
		 * @since 2.1.0
		 *
		 * @param {jQuery.Event} event a jQuery Event object for the submit event
		 * @returns {boolean}
		 */
		function showMissingGoogleProductCategoryAlert( event ) {

			event.preventDefault();

			alert( facebook_for_woocommerce_products_admin.i18n.missing_google_product_category_message );

			return false;
		}


		/**
		 * Store the original value of the given element for later use.
		 *
		 * @since 2.3.0
		 *
		 * @param {jQuery} $syncModeSelect a jQuery element object
		 */
		function storeSyncModeOriginalValue( $syncModeSelect ) {

			$syncModeSelect.attr( 'data-original-value', $syncModeSelect.val() );
		}


		/**
		 * Reverts the value of the given sync mode element to its original value.
		 *
		 * @since 2.3.0
		 *
		 * @param {jQuery} $syncModeSelect a jQuery element object
		 */
		function revertSyncModeToOriginalValue( $syncModeSelect ) {

			$syncModeSelect.val( $syncModeSelect.attr( 'data-original-value') );
		}


		/**
		 * Determines whether we should show the product removed from sync confirm modal.
		 *
		 * @since 2.3.0
		 *
		 * @param {jQuery} $syncModeSelect a jQuery object with one or more sync mode select elements
		 * @return {boolean}
		 */
		function shouldShowProductRemovedFromSyncConfirmModal( $syncModeSelect ) {

			let syncValuesStatus = $syncModeSelect.map( function ( index, selectElement ) {

				let $syncMode     	   = $( selectElement );
				let syncModeValue 	   = $syncMode.val();
				let isProductPublished = !! facebook_for_woocommerce_products_admin.is_product_published;

				return isProductPublished && 'sync_disabled' === syncModeValue && syncModeValue !== $syncMode.attr( 'data-original-value' );
			} ).toArray();

			return syncValuesStatus.indexOf( true ) > -1;
		}


		/**
		 * Gets the target product ID based on the given sync select element.
		 *
		 * @since 2.3.0
		 *
		 * @param {jQuery} $syncModeSelect a jQuery element object
		 */
		function getSyncTargetProductID( $syncModeSelect ) {

			if ( simpleProductSyncModeSelect === $syncModeSelect ) {
				// simple product
				return $( 'input#post_ID' ).val();
			}

			// variable product
			return $syncModeSelect.closest( '.woocommerce_variation' ).find( 'input[name^=variable_post_id]' ).val();
		}


		/**
		 * Shows the product removed from sync confirm modal.
		 *
		 * @since 2.3.0
		 *
		 * @param {jQuery} $syncModeSelect a jQuery element object
		 */
		function showProductRemovedFromSyncConfirmModal( $syncModeSelect ) {

			closeExistingModal();

			$maybeRemoveFromSyncModeSelect = $syncModeSelect;
			maybeRemoveFromSyncProductID   = getSyncTargetProductID( $syncModeSelect )

			new $.WCBackboneModal.View( {
				target: 'facebook-for-woocommerce-modal',
				string: {
					message: facebook_for_woocommerce_products_admin.product_removed_from_sync_confirm_modal_message,
					buttons: facebook_for_woocommerce_products_admin.product_removed_from_sync_confirm_modal_buttons
				}
			} );
		}


		/**
		 * Fills in product IDs to remove from Sync.
		 *
		 * @since 2.3.0
		 */
		function populateRemoveFromSyncProductIDsField() {

			$( facebook_for_woocommerce_products_admin.product_removed_from_sync_field_id ).val( removeFromSyncProductIDs.join( ',' ) );
		}


		/**
		 * Removes the given product ID from the list of product to delete from Sync.
		 *
		 * @since 2.3.0
		 *
		 * @param {String} productID Product ID to remove
		 */
		function removeProductIDFromUnSyncList( productID ) {

			removeFromSyncProductIDs = removeFromSyncProductIDs.filter( function ( value ) {
				return value !== productID;
			} );

			populateRemoveFromSyncProductIDsField();
		}

		let $maybeRemoveFromSyncModeSelect = null;
		let maybeRemoveFromSyncProductID = null;
		let removeFromSyncProductIDs = [];

		$( document.body ).on( 'click', 'button.button-product-removed-from-sync-delete', function () {

			if ( maybeRemoveFromSyncProductID ) {

				closeExistingModal();

				removeFromSyncProductIDs.push( maybeRemoveFromSyncProductID );

				populateRemoveFromSyncProductIDsField();
			}
		} )
		.on( 'click', 'button.button-product-removed-from-sync-cancel', function () {

			closeExistingModal();

			if ( $maybeRemoveFromSyncModeSelect ) {
				revertSyncModeToOriginalValue( $maybeRemoveFromSyncModeSelect );
				$maybeRemoveFromSyncModeSelect = null;
			}

			populateRemoveFromSyncProductIDsField();
		} );

		// handle change events for the Sell on Instagram checkbox field
		$( '#facebook_options #wc_facebook_commerce_enabled' ).on( 'change', function() {

			let checked = $( this ).prop( 'checked' );

			// toggle visibility of all commerce fields
			if ( checked ) {
				$( '.wc_facebook_commerce_fields' ).show();
			} else {
				$( '.wc_facebook_commerce_fields').hide();
			}

			// toggle visibility of attribute fields
			if ( $( '.product_attributes' ).find( '.woocommerce_attribute' ).length ) {
				$( '.show_if_has_attributes' ).show();
			} else {
				$( '.show_if_has_attributes' ).hide();
			}

			$( this ).prop( 'original', checked );
		} ).trigger( 'change' );

		// toggle Facebook settings fields for simple products
		const simpleProductSyncModeSelect = $( '#wc_facebook_sync_mode' );
		const facebookSettingsPanel       = simpleProductSyncModeSelect.closest( '.woocommerce_options_panel' );

		// store sync mode original value for later use
		storeSyncModeOriginalValue( simpleProductSyncModeSelect );

		simpleProductSyncModeSelect.on( 'change', function() {

			let syncEnabled = simpleProductSyncModeSelect.val() !== 'sync_disabled';

			toggleFacebookSettings( syncEnabled, facebookSettingsPanel );
			toggleFacebookCommerceSettings( syncEnabled, facebookSettingsPanel );

			if ( syncEnabled ) {
				removeProductIDFromUnSyncList( getSyncTargetProductID( simpleProductSyncModeSelect ) );
			}

			simpleProductSyncModeSelect.prop( 'original', simpleProductSyncModeSelect.val() );

			if ( shouldShowProductRemovedFromSyncConfirmModal( simpleProductSyncModeSelect ) ) {
				showProductRemovedFromSyncConfirmModal( simpleProductSyncModeSelect );
			}

		} ).trigger( 'change' );

		$( '#_virtual' ).on( 'change', function () {
			toggleSyncAndShowOption( ! $( this ).prop( 'checked' ), simpleProductSyncModeSelect );
		} ).trigger( 'change' );

		// Update the sync when catalog visibility changes.
		$( 'input[name=_visibility]' ).on ( 'change', function(){
			if ( $( this ).val() !== 'hidden' && $( this ).val() !== 'search' ) {

				if ( simpleProductSyncModeSelect.val() === 'sync_disabled' ) {
					simpleProductSyncModeSelect.val( 'sync_and_show' ).trigger( 'change' );;
				}
			}
		})

		const $productData = $( '#woocommerce-product-data' );

		// check whether the product meets the requirements for Commerce
		$productData.on(
			'change',
			'#_regular_price, #_manage_stock, #_stock, #wc_facebook_sync_mode, #fb_product_price',
			function( event ) {

				// allow validation handlers that run on change to run before we check any field values
				setTimeout( function() {
					toggleFacebookSellOnInstagramSetting( isProductReadyForCommerce(), $( '#facebook_options' ) );
				}, 1 );
			}
		);

		// toggle Facebook settings fields for variations
		$( '.woocommerce_variations' ).on( 'change', '.js-variable-fb-sync-toggle', function () {

			let $syncModeSelect = $( this );
			let syncEnabled     = $syncModeSelect.val() !== 'sync_disabled';

			toggleFacebookSettings( syncEnabled, $syncModeSelect.closest( '.wc-metabox-content' ) );
			toggleFacebookSellOnInstagramSetting( isProductReadyForCommerce(), $( '#facebook_options' ) );

			if ( syncEnabled ) {
				removeProductIDFromUnSyncList( getSyncTargetProductID( $syncModeSelect ) );
			}

			$syncModeSelect.prop( 'original', $syncModeSelect.val() );

			if ( shouldShowProductRemovedFromSyncConfirmModal( $syncModeSelect ) ) {
				showProductRemovedFromSyncConfirmModal( $syncModeSelect );
			}
		} );

		$productData.on( 'woocommerce_variations_loaded', function () {

			$productData.find( '.js-variable-fb-sync-toggle' ).each( function ( index, element ) {
				let $syncModeSelect = $( element );
				toggleFacebookSettings( $syncModeSelect.val() !== 'sync_disabled', $syncModeSelect.closest( '.wc-metabox-content' ) );
				$syncModeSelect.prop( 'original', $syncModeSelect.val() );
				storeSyncModeOriginalValue( $syncModeSelect );
			} );

			$( '.variable_is_virtual' ).on( 'change', function () {
				const jsSyncModeToggle = $( this ).closest( '.wc-metabox-content' ).find( '.js-variable-fb-sync-toggle' );
				toggleSyncAndShowOption( ! $( this ).prop( 'checked' ), jsSyncModeToggle );
			} );

			toggleFacebookSellOnInstagramSetting( isProductReadyForCommerce(), $( '#facebook_options' ) );
		} );

		// show/hide Custom Image URL setting
		$productData.on( 'change', '.js-fb-product-image-source', function() {

			let $container  = $( this ).closest( '.woocommerce_options_panel, .wc-metabox-content' );
			let imageSource = $( this ).val();

			$container.find( '.product-image-source-field' ).closest( '.form-field' ).hide();
			$container.find( `.show-if-product-image-source-${imageSource}` ).closest( '.form-field' ).show();
		} );

		$( '.js-fb-product-image-source:checked:visible' ).trigger( 'change' );

		// trigger settings fields modifiers when variations are loaded
		$productData.on( 'woocommerce_variations_loaded', function() {
			$( '.js-variable-fb-sync-toggle:visible' ).trigger( 'change' );
			$( '.js-fb-product-image-source:checked:visible' ).trigger( 'change' );
			$( '.variable_is_virtual:visible' ).trigger( 'change' );
		} );

		// open modal explaining sell on Instagram requirements
		$( '#facebook_options' ).on( 'click', '#product-not-ready-notice-open-modal', function( event ) {

			event.preventDefault();

			closeExistingModal();

			new $.WCBackboneModal.View( {
				target: 'facebook-for-woocommerce-modal',
				string: {
					message: facebook_for_woocommerce_products_admin.product_not_ready_modal_message,
					buttons: facebook_for_woocommerce_products_admin.product_not_ready_modal_buttons
				}
			} );
		} );

		// toggle Sell on Instagram checkbox on page load
		toggleFacebookSellOnInstagramSetting( isProductReadyForCommerce(), facebookSettingsPanel );

		let submitProductSave = false;

		$( 'form#post input[type="submit"]' ).on( 'click', function( e ) {

			if ( shouldShowMissingGoogleProductCategoryAlert() ) {
				return showMissingGoogleProductCategoryAlert( e );
			}

			if ( ! submitProductSave ) {
				e.preventDefault();
			} else {
				return true;
			}

			let $submitButton    = $( this ),
				productID        = parseInt( $( 'input#post_ID' ).val(), 10 ),
				productCat       = [],
				// this query will get tags when not using checkboxes
				productTag       = $( 'textarea[name="tax_input[product_tag]"]' ).length ? $( 'textarea[name="tax_input[product_tag]"]' ).val().split( ',' ) : [],
				syncEnabled      = simpleProductSyncModeSelect.val() !== 'sync_disabled',
				varSyncEnabled   = isSyncEnabledForVariableProduct();

			$( '#taxonomy-product_cat input[name="tax_input[product_cat][]"]:checked' ).each( function() {
				productCat.push( parseInt( $( this ).val(), 10 ) );
			} );

			// this query will get tags when using checkboxes
			$( '#taxonomy-product_tag input[name="tax_input[product_tag][]"]:checked' ).each( function() {
				productTag.push( parseInt( $( this ).val(), 10 ) );
			} );

			if ( productID > 0 ) {

				$.post( facebook_for_woocommerce_products_admin.ajax_url, {
					action:           'facebook_for_woocommerce_set_product_sync_prompt',
					security:         facebook_for_woocommerce_products_admin.set_product_sync_prompt_nonce,
					sync_enabled:     syncEnabled    ? 'enabled' : 'disabled',
					var_sync_enabled: varSyncEnabled ? 'enabled' : 'disabled',
					product:          productID,
					categories:       productCat,
					tags:             productTag
				}, function( response ) {

					// open modal if visibility checkbox is checked or if there are conflicting terms set for sync exclusion
					if ( response && ! response.success && syncEnabled ) {

						closeExistingModal();

						// open new modal, populate template with AJAX response data
						new $.WCBackboneModal.View( {
							target: 'facebook-for-woocommerce-modal',
							string: response.data
						} );

					} else {

						// no modal displayed: submit form as normal
						submitProductSave = true;
						$submitButton.trigger( 'click' );
					}
				} );

			} else {

				// no modal displayed: submit form as normal
				submitProductSave = true;
				$submitButton.trigger( 'click' );
			}

		} );
	}


} );
