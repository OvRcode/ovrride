<?php do_action( 'woocommerce_before_add_to_cart_button' ); ?>

<div class="wc-trip-cost" style="display:none"></div>

</div>
<input type="hidden" id="base_price" value="<?php echo $base_price; ?>">
<strong>Total:</strong><br />
<p class="price">
<span id="trip_price" class="amount"></span>
</p>
<input type="hidden" name="add-to-cart" value="<?php echo esc_attr( $product->id ); ?>" />

<button type="submit" class="single_add_to_cart_button button alt wc_trip_add"><?php echo $product->single_add_to_cart_text(); ?></button>

<?php do_action( 'woocommerce_after_add_to_cart_button' ); ?>

</form>
