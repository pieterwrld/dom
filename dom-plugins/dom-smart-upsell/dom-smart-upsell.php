<?php
/**
 * Plugin Name: DOM Smart Upsell
 * Description: Category-aware cross-sell and upsell engine for product pages and cart drawer.
 * Version: 1.0.0
 * Author: De Outfitman
 * Requires Plugins: woocommerce
 * Text Domain: dom-smart-upsell
 *
 * @package DomSmartUpsell
 */
defined('ABSPATH') || exit;

class DOM_Smart_Upsell {

    private static $instance = null;

    private $complement_map = [
        'sneakers'          => ['jeans', 'trainingspakken', 'tassen'],
        'jassen'            => ['sweaters-hoodies', 't-shirts', 'petten'],
        'jeans'             => ['sneakers', 't-shirts', 'brillen'],
        'trainingspakken'   => ['sneakers', 'petten', 'tassen'],
        'sweaters-hoodies'  => ['jeans', 'sneakers', 'petten'],
        't-shirts'          => ['jeans', 'sneakers', 'brillen'],
        'zwembroeken'       => ['slippers', 't-shirts', 'brillen'],
        'slippers'          => ['zwembroeken', 't-shirts', 'brillen'],
        'petten'            => ['sneakers', 'jassen', 't-shirts'],
        'brillen'           => ['petten', 't-shirts', 'jeans'],
        'tassen'            => ['sneakers', 'jassen', 'jeans'],
    ];

    public static function instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        add_filter('dom_smart_upsell_products', [$this, 'get_upsell_products'], 10, 2);
        add_filter('dom_cart_upsell_products', [$this, 'get_cart_recommendations']);
    }

    public function get_upsell_products($products, $product_id) {
        $product = wc_get_product($product_id);
        if (!$product) return [];

        $manual = $product->get_upsell_ids();
        if (!empty($manual)) {
            return array_map('wc_get_product', array_slice($manual, 0, 8));
        }

        $cats = wp_get_post_terms($product_id, 'product_cat', ['fields' => 'slugs']);
        $complement_slugs = [];

        foreach ($cats as $cat_slug) {
            if (isset($this->complement_map[$cat_slug])) {
                $complement_slugs = array_merge($complement_slugs, $this->complement_map[$cat_slug]);
            }
        }

        $complement_slugs = array_unique($complement_slugs);
        if (empty($complement_slugs)) {
            return wc_get_related_products($product_id, 8);
        }

        $price = (float) $product->get_price();
        $price_range = [max(0, $price * 0.5), $price * 2];

        $args = [
            'status'   => 'publish',
            'limit'    => 8,
            'orderby'  => 'rand',
            'exclude'  => [$product_id],
            'category' => $complement_slugs,
        ];

        $query = new WC_Product_Query($args);
        $results = $query->get_products();

        $filtered = array_filter($results, function ($p) use ($price_range) {
            $p_price = (float) $p->get_price();
            return $p_price >= $price_range[0] && $p_price <= $price_range[1];
        });

        if (count($filtered) < 4) {
            return array_slice($results, 0, 8);
        }

        return array_slice(array_values($filtered), 0, 8);
    }

    public function get_cart_recommendations($products = []) {
        if (!function_exists('WC') || !WC()->cart) return [];

        $cart_product_ids = [];
        $cart_cats = [];

        foreach (WC()->cart->get_cart() as $item) {
            $cart_product_ids[] = $item['product_id'];
            $cats = wp_get_post_terms($item['product_id'], 'product_cat', ['fields' => 'slugs']);
            $cart_cats = array_merge($cart_cats, $cats);
        }

        $cart_cats = array_unique($cart_cats);
        $complement_slugs = [];

        foreach ($cart_cats as $cat_slug) {
            if (isset($this->complement_map[$cat_slug])) {
                $complement_slugs = array_merge($complement_slugs, $this->complement_map[$cat_slug]);
            }
        }

        $complement_slugs = array_unique(array_diff($complement_slugs, $cart_cats));
        if (empty($complement_slugs)) return [];

        $args = [
            'status'   => 'publish',
            'limit'    => 6,
            'orderby'  => 'popularity',
            'exclude'  => $cart_product_ids,
            'category' => $complement_slugs,
        ];

        $query = new WC_Product_Query($args);
        return $query->get_products();
    }
}

DOM_Smart_Upsell::instance();
