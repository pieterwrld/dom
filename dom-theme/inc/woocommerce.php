<?php
/**
 * WooCommerce hooks and customizations
 *
 * @package DomTheme
 */
defined('ABSPATH') || exit;

// Remove default WooCommerce styles
add_filter('woocommerce_enqueue_styles', '__return_empty_array');

// Remove WooCommerce breadcrumbs
remove_action('woocommerce_before_main_content', 'woocommerce_breadcrumb', 20);

// Remove default product page elements (we build our own in single-product.php)
remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_title', 5);
remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_rating', 10);
remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_price', 10);
remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_excerpt', 20);
remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40);
remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_sharing', 50);
remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30);

// Remove default gallery (we use our own)
remove_action('woocommerce_before_single_product_summary', 'woocommerce_show_product_images', 20);

// Remove related products (we use smart-upsell instead)
remove_action('woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20);

// Remove sidebar
remove_action('woocommerce_sidebar', 'woocommerce_get_sidebar', 10);

// Remove result count and ordering on shop
remove_action('woocommerce_before_shop_loop', 'woocommerce_result_count', 20);
remove_action('woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 30);

/**
 * Cart quantity update via AJAX
 */
function dom_ajax_update_cart_qty() {
    check_ajax_referer('dom-cart-nonce', 'nonce');

    $key = sanitize_text_field($_POST['cart_key'] ?? '');
    $direction = sanitize_text_field($_POST['direction'] ?? '');

    if (!$key || !$direction) {
        wp_send_json_error();
    }

    $cart = WC()->cart;
    $item = $cart->get_cart_item($key);

    if (!$item) {
        wp_send_json_error();
    }

    $qty = $item['quantity'];
    if ($direction === 'remove') {
        $new_qty = 0;
    } else {
        $new_qty = ($direction === 'increase') ? $qty + 1 : max(0, $qty - 1);
    }

    if ($new_qty === 0) {
        $cart->remove_cart_item($key);
    } else {
        $cart->set_quantity($key, $new_qty);
    }

    WC()->cart->calculate_totals();

    ob_start();
    wc_get_template('cart/mini-cart.php');
    $mini_cart = ob_get_clean();

    $fragments = apply_filters('woocommerce_add_to_cart_fragments', []);

    wp_send_json_success(['fragments' => $fragments]);
}
add_action('wp_ajax_dom_update_cart_qty', 'dom_ajax_update_cart_qty');
add_action('wp_ajax_nopriv_dom_update_cart_qty', 'dom_ajax_update_cart_qty');

/**
 * Quick add-to-cart from drawer upsell rails
 */
function dom_ajax_add_to_cart() {
    check_ajax_referer('dom-cart-nonce', 'nonce');

    $product_id = absint($_POST['product_id'] ?? 0);
    if (!$product_id) {
        wp_send_json_error();
    }

    $product = wc_get_product($product_id);
    if (!$product) {
        wp_send_json_error();
    }

    $variation_id = 0;
    $variation = [];

    if ($product->is_type('variable') && !empty($_POST['size'])) {
        $size = sanitize_text_field($_POST['size']);
        $data_store = WC_Data_Store::load('product');
        foreach ($product->get_variation_attributes() as $attr_name => $values) {
            $variation['attribute_' . $attr_name] = $size;
            break;
        }
        $variation_id = $data_store->find_matching_product_variation($product, $variation);
        if (!$variation_id) {
            wp_send_json_error(['message' => 'Maat niet beschikbaar']);
        }
    }

    $added = WC()->cart->add_to_cart($product_id, 1, $variation_id, $variation);
    if (!$added) {
        wp_send_json_error();
    }

    WC()->cart->calculate_totals();
    $fragments = apply_filters('woocommerce_add_to_cart_fragments', []);
    wp_send_json_success(['fragments' => $fragments]);
}
add_action('wp_ajax_dom_add_to_cart', 'dom_ajax_add_to_cart');
add_action('wp_ajax_nopriv_dom_add_to_cart', 'dom_ajax_add_to_cart');

/**
 * Products per page
 */
add_filter('loop_shop_per_page', function () {
    return 24;
});

/**
 * Product columns
 */
add_filter('loop_shop_columns', function () {
    return 4;
});

/**
 * Geen "is toegevoegd aan je winkelmand"-melding meer.
 *
 * De winkelwagen schuift zelf open zodra er iets is toegevoegd, dus die melding
 * voegt niets toe. Erger: WooCommerce bewaart hem tot de volgende paginaweergave,
 * waardoor je bij het afrekenen drie groene balken boven het formulier kreeg.
 */
add_filter('wc_add_to_cart_message_html', '__return_empty_string');

/**
 * Korte geruststelling onder de bestelknop: levertijd, retourtermijn en het
 * aantal klanten dat de winkel al heeft geholpen. Dat laatste getal staat ook
 * in de footer en komt uit de eigen tekst van de winkel.
 */
add_action('woocommerce_review_order_after_submit', function () {
    ?>
    <div class="dom-co-trust">
      <span>
        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M20 6L9 17l-5-5"/></svg>
        <?php esc_html_e('Voor 17:00 besteld, morgen in huis', 'dom-theme'); ?>
      </span>
      <span>
        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M20 6L9 17l-5-5"/></svg>
        <?php esc_html_e('14 dagen retour', 'dom-theme'); ?>
      </span>
      <span>
        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M20 6L9 17l-5-5"/></svg>
        <?php echo esc_html(dom_c('winkelwagen.teksten.trust', __('1.200+ tevreden klanten', 'dom-theme'))); ?>
      </span>
    </div>
    <?php
});
