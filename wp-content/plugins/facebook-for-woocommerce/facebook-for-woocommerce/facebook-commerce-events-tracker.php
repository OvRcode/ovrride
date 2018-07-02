<?php
/**
* @package FacebookCommerce
*/

if (!class_exists('WC_Facebookcommerce_EventsTracker')) :

if (!class_exists('WC_Facebookcommerce_Utils')) {
  include_once 'includes/fbutils.php';
}

if (!class_exists('WC_Facebookcommerce_Pixel')) {
  include_once 'facebook-commerce-pixel-event.php';
}

class WC_Facebookcommerce_EventsTracker {
  private $pixel;
  private static $isEnabled = true;
  const FB_PRIORITY_HIGH = 2;

  public function __construct($user_info) {
    $this->pixel = new WC_Facebookcommerce_Pixel($user_info);

    add_action('wp_head', array($this, 'apply_filters'));

    // Pixel Tracking Hooks
    add_action('wp_head',
      array($this, 'inject_base_pixel'));
    add_action('wp_footer',
      array($this, 'inject_base_pixel_noscript'));
    add_action('woocommerce_after_single_product',
      array($this, 'inject_view_content_event'), self::FB_PRIORITY_HIGH);
    add_action('woocommerce_after_shop_loop',
      array($this, 'inject_view_category_event'));
    add_action('pre_get_posts',
      array($this, 'inject_search_event'));
    add_action('woocommerce_after_cart',
      array($this, 'inject_add_to_cart_redirect_event'));
    add_action('woocommerce_add_to_cart',
      array($this, 'inject_add_to_cart_event'), self::FB_PRIORITY_HIGH);
    add_action('wc_ajax_fb_inject_add_to_cart_event',
      array($this, 'inject_ajax_add_to_cart_event' ), self::FB_PRIORITY_HIGH);
    add_action('woocommerce_after_checkout_form',
      array($this, 'inject_initiate_checkout_event'));
    add_action('woocommerce_thankyou',
      array($this, 'inject_gateway_purchase_event'), self::FB_PRIORITY_HIGH);
    add_action('woocommerce_payment_complete',
      array($this, 'inject_purchase_event'), self::FB_PRIORITY_HIGH);

  }

  public function apply_filters() {
    self::$isEnabled = apply_filters(
        "facebook_for_woocommerce_integration_pixel_enabled",
        self::$isEnabled);
  }

  /**
   * Base pixel code to be injected on page head. Because of this, it's better
   * to echo the return value than using
   * WC_Facebookcommerce_Utils::wc_enqueue_js() in this case
   */
  public function inject_base_pixel() {
    if (self::$isEnabled) {
      echo $this->pixel->pixel_base_code();
    }
  }

  /**
   * Base pixel noscript to be injected on page body. This is to avoid W3
   * validation error.
   */
  public function inject_base_pixel_noscript() {
    if (self::$isEnabled) {
      echo $this->pixel->pixel_base_code_noscript();
    }
  }

  /**
   * Triggers ViewCategory for product category listings
   */
  public function inject_view_category_event() {
    global $wp_query;
    if (!self::$isEnabled) {
      return;
    }

    $products = array_values(array_map(function($item) {
        return wc_get_product($item->ID);
      },
      $wp_query->posts));

    // if any product is a variant, fire the pixel with
    // content_type: product_group
    $content_type = 'product';
    $product_ids = array();
    foreach ($products as $product) {
      if (!$product) {
        continue;
      }
      $product_ids = array_merge(
        $product_ids,
        WC_Facebookcommerce_Utils::get_fb_content_ids($product));
      if (WC_Facebookcommerce_Utils::is_variable_type($product->get_type())) {
        $content_type = 'product_group';
      }
    }

    $categories =
      WC_Facebookcommerce_Utils::get_product_categories(get_the_ID());

    $this->pixel->inject_event(
      'ViewCategory',
      array(
        'content_name' => $categories['name'],
        'content_category' => $categories['categories'],
        'content_ids' => json_encode(array_slice($product_ids, 0, 10)),
        'content_type' => $content_type
      ),
      'trackCustom');
  }

  /**
   * Triggers Search for result pages (deduped)
   */
  public function inject_search_event() {
    if (!self::$isEnabled) {
      return;
    }

    if (!is_admin() && is_search() && get_search_query() !== '') {
      if ($this->pixel->check_last_event('Search')) {
        return;
      }

      if (WC_Facebookcommerce_Utils::isWoocommerceIntegration()) {
        $this->actually_inject_search_event();
      } else {
        add_action('wp_head', array($this, 'actually_inject_search_event'), 11);
      }
    }
  }

  /**
   * Triggers Search for result pages
   */
  public function actually_inject_search_event() {
    if (!self::$isEnabled) {
      return;
    }

    $this->pixel->inject_event(
      'Search',
      array(
        'search_string' => get_search_query()
      ));
  }

  /**
   * Helper function to iterate through a cart and gather all content ids
   */
  private function get_content_ids_from_cart($cart) {
    $product_ids = array();
    foreach ($cart as $item) {
      $product_ids = array_merge(
        $product_ids,
        WC_Facebookcommerce_Utils::get_fb_content_ids($item['data']));
    }
    return $product_ids;
  }

  /**
   * Triggers ViewContent product pages
   */
  public function inject_view_content_event() {
    if (!self::$isEnabled) {
      return;
    }
    global $post;
    $product = wc_get_product($post->ID);
    $content_type = 'product_group';
    if (!$product) {
      return;
    }

    // if product is a variant, fire the pixel with content_type: product_group
    if (WC_Facebookcommerce_Utils::is_variation_type($product->get_type())) {
      $content_type = 'product';
    }

    $content_ids = WC_Facebookcommerce_Utils::get_fb_content_ids($product);
    $this->pixel->inject_event(
      'ViewContent',
      array(
        'content_name' => $product->get_title(),
        'content_ids' => json_encode($content_ids),
        'content_type' => $content_type,
        'value' => $product->get_price(),
        'currency' => get_woocommerce_currency()
      ));
  }

  /**
   * Triggers AddToCart for cart page and add_to_cart button clicks
   */
  public function inject_add_to_cart_event() {
    if (!self::$isEnabled) {
      return;
    }

    $product_ids = $this->get_content_ids_from_cart(WC()->cart->get_cart());

    $this->pixel->inject_event(
      'AddToCart',
      array(
        'content_ids' => json_encode($product_ids),
        'content_type' => 'product',
        'value' => WC()->cart->total,
        'currency' => get_woocommerce_currency()
      ));
  }

  /**
  * Triggered by add_to_cart jquery trigger
  */
  public function inject_ajax_add_to_cart_event() {
    if (!self::$isEnabled) {
      return;
    }

    ob_start();

    echo '<script>';

    $product_ids = $this->get_content_ids_from_cart(WC()->cart->get_cart());

    echo $this->pixel->build_event(
      'AddToCart',
      array(
        'content_ids' => json_encode($product_ids),
        'content_type' => 'product',
        'value' => WC()->cart->total,
        'currency' => get_woocommerce_currency()
      ));
    echo '</script>';

    $pixel = ob_get_clean();

    wp_send_json($pixel);
  }

  /**
  * Trigger AddToCart for cart page and woocommerce_after_cart hook.
  * When set 'redirect to cart', ajax call for button click and
  * woocommerce_add_to_cart will be skipped.
  */
  public function inject_add_to_cart_redirect_event() {
    if (!self::$isEnabled) {
      return;
    }
    $redirect_checked = get_option('woocommerce_cart_redirect_after_add', 'no');
    if ($redirect_checked == 'yes') {
      $this->inject_add_to_cart_event();
    }
  }

  /**
   * Triggers InitiateCheckout for checkout page
   */
  public function inject_initiate_checkout_event() {
    if (!self::$isEnabled ||
        $this->pixel->check_last_event('InitiateCheckout')) {
      return;
    }

    $product_ids = $this->get_content_ids_from_cart(WC()->cart->get_cart());

    $this->pixel->inject_event(
      'InitiateCheckout',
      array(
        'num_items' => WC()->cart->get_cart_contents_count(),
        'content_ids' => json_encode($product_ids),
        'content_type' => 'product',
        'value' => WC()->cart->total,
        'currency' => get_woocommerce_currency()
      ));
  }

  /**
   * Triggers Purchase for payment transaction complete and for the thank you
   * page in cases of delayed payment.
   */
  public function inject_purchase_event($order_id) {
    if (!self::$isEnabled ||
        $this->pixel->check_last_event('Purchase')) {
      return;
    }

    $order = new WC_Order($order_id);
    $content_type = 'product';
    $product_ids = array();
    foreach ($order->get_items() as $item) {
      $product = wc_get_product($item['product_id']);
      $product_ids = array_merge(
        $product_ids,
        WC_Facebookcommerce_Utils::get_fb_content_ids($product));
      if (WC_Facebookcommerce_Utils::is_variable_type($product->get_type())) {
        $content_type = 'product_group';
      }
    }

    $this->pixel->inject_event(
      'Purchase',
      array(
        'content_ids' => json_encode($product_ids),
        'content_type' => $content_type,
        'value' => $order->get_total(),
        'currency' => get_woocommerce_currency()
      ));
  }

  /**
   * Triggers Purchase for thank you page for COD, BACS CHEQUE payment
   * which won't invoke woocommerce_payment_complete.
   */
  public function inject_gateway_purchase_event($order_id) {
    if (!self::$isEnabled ||
        $this->pixel->check_last_event('Purchase')) {
      return;
    }

    $order = new WC_Order($order_id);
    $payment = $order->get_payment_method();
    $this->inject_purchase_event($order_id);
  }
}

endif;
