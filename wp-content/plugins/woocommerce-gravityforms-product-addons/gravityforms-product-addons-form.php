<?php
class woocommerce_gravityforms_product_form {
	private $current_page;
	private $next_page;
	private $form_id = 0;
	private $product_id = 0;

	public function __construct($form_id, $product_id) {
		$this->form_id = $form_id;
		$this->product_id = $product_id;

		add_filter('gform_form_tag', array(&$this, 'on_form_tag'), 10, 2);
		add_filter('gform_submit_button', array(&$this, 'on_submit_button'), 10, 2);
	}

	function get_form($options) {
		global $woocommerce;

		$product = null;
		if (function_exists('get_product')) {
			$product = get_product($this->product_id);
		} else {
			$product = new WC_Product($this->product_id);
		}

		extract(shortcode_atts(array(
		    'display_title' => true,
		    'display_description' => true,
		    'display_inactive' => false,
		    'field_values' => false,
		    'ajax' => false,
		    'tabindex' => 1,
		    'label_subtotal' => __('Subtotal', 'wc_gf_addons'),
		    'label_options' => __('Options', 'wc_gf_addons'),
		    'label_total' => __('Total', 'wc_gf_addons'),
		    'disable_label_subtotal' => 'no',
		    'disable_label_options' => 'no',
		    'disable_label_total' => 'no',
		    'disable_calculations' => 'no',
		    'disable_anchor' => 'no',
		), $options));

		//Get the form meta so we can make sure the form exists.
		$form_meta = RGFormsModel::get_form_meta($this->form_id);
		if (!empty($form_meta)) {

			if (!empty($_POST)) {
				$_POST['gform_submit'] = isset($_POST['gform_old_submit']) ? $_POST['gform_old_submit'] : '';
				$_POST['gform_old_submit'] = $_POST['gform_submit'];
			}

			$form = RGForms::get_form($this->form_id, $display_title, $display_description, $display_inactive, $field_values, $ajax, $tabindex);
			
			unset($_POST['gform_submit']);
			$form = str_replace('</form>', '', $form);

			$form = str_replace('gform_submit', 'gform_old_submit', $form);
			$form .= wp_nonce_field( 'gform_submit_' . $this->form_id, '_gform_submit_nonce_' . $this->form_id, true, false );
			
			$this->current_page = GFFormDisplay::get_current_page($this->form_id);
			$this->next_page = $this->current_page + 1;
			$this->previous_page = $this->current_page - 1;
			$this->next_page = $this->next_page > $this->get_max_page_number($form_meta) ? 0 : $this->next_page;

			if ($product->product_type == 'variable' || $product->product_type == 'variable-subscription') {
				echo '<div class="gform_variation_wrapper gform_wrapper single_variation_wrap">';
			} else {
				echo '<div class="gform_variation_wrapper gform_wrapper">';
			}

			if ($product->is_type('variable')) :
				//echo '<input type="hidden" name="add-to-cart" value="variation" />';
				echo '<input type="hidden" id="product_id" name="product_id" value="' . $this->product_id . '" />';
			elseif ($product->has_child()) :
				//echo '<input type="hidden" name="add-to-cart" value="group" />';
				echo '<input type="hidden" id="product_id" name="product_id" value="' . $this->product_id . '" />';
			else :
				//echo '<input type="hidden" name="add-to-cart" value="' . $this->product_id . '" />';
				echo '<input type="hidden" id="product_id" name="product_id" value="' . $this->product_id . '" />';
			endif;

			if (wc_is_21x()) {
				wp_nonce_field('add_to_cart');
			} else {
				$woocommerce->nonce_field('add_to_cart');
			}
			
			if ($disable_anchor != 'yes'){
				echo '<a id="_form_' . $this->form_id . '" href="#_form_' . $this->form_id . '" class="gform_anchor"></a>';
			}
			
			echo $form;
			
			echo '<input type="hidden" name="gform_form_id" id="gform_form_id" value="' . $this->form_id . '" />';
			echo '<input type="hidden" id="woocommerce_get_action" value="" />';
			echo '<input type="hidden" id="woocommerce_product_base_price" value="' . $product->get_price() . '" />';

			$description_class = rgar($form_meta, "descriptionPlacement") == "above" ? "description_above" : "description_below";
			?>

			<?php
			$this->on_print_scripts();

			if ($disable_calculations == 'no') :
				?>

				<div class="product_totals">
					<ul id="gform_totals_<?php echo $this->form_id; ?>" class="gform_fields <?php echo $form_meta['labelPlacement'] . ' ' . $description_class; ?>">
						<li class="gfield" <?php
						if ($disable_label_subtotal == 'yes')
							echo 'style="display:none !important;"';
						?> >
							<label class="gfield_label"><?php echo $label_subtotal; ?></label>
							<div class="ginput_container">
								<span class="formattedBasePrice ginput_total"></span>
							</div>
						</li>
						<li class="gfield" <?php
						if ($disable_label_options == 'yes')
							echo 'style="display:none !important;"';
						?> >
							<label class="gfield_label"><?php echo $label_options; ?></label>
							<div class="ginput_container">
								<span class="formattedVariationTotal ginput_total"></span>
							</div>
						</li>
						<li class="gfield" <?php
						if ($disable_label_total == 'yes')
							echo 'style="display:none !important;"';
						?> >
							<label class="gfield_label"><?php echo $label_total; ?></label>
							<div class="ginput_container">
								<span class="formattedTotalPrice ginput_total"></span>
							</div>
						</li>
					</ul>
				</div>
				<?php if ($product->product_type != 'bundle') : ?>
				<style>
					.single_variation .price {
						display:none !important;
					}
				</style>
				<?php endif; ?>
			<?php endif; ?>
			<style>
				.hidden-total {
					display:none !important;
				}
			</style>



			<?php
			echo '</div>';
		}
	}

	// filter out the Gravity Form form tag so all we have are the fields
	function on_form_tag($form_tag, $form) {
		if ($form['id'] != $this->form_id) {
			return $form_tag;
		}

		return '';
	}

	// filter the Gravity Forms button type
	function on_submit_button($button, $form) {
		if ($form['id'] != $this->form_id) {
			return $button;
		}

		return '';
	}

	function on_print_scripts() {


		$garvityforms_params = array(
		    'form_id' => $this->form_id,
		    'next_page' => $this->next_page,
		    'previous_page' => $this->previous_page,
		);

		wp_localize_script('wc-gravityforms-product-addons', 'gravityforms_params', $garvityforms_params);
		?>
		<script>
			gravityforms_params = <?php echo json_encode($garvityforms_params); ?>;

			gform.addFilter('gform_product_total', function(total, formId) {
				return update_dynamic_price(total);
			});

		</script>
		<?php
	}

	private function get_max_page_number($form) {
		$page_number = 0;
		foreach ($form["fields"] as $field) {
			if ($field["type"] == "page") {
				$page_number++;
			}
		}
		return $page_number == 0 ? 0 : $page_number + 1;
	}

}
?>