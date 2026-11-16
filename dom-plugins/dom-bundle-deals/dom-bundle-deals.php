<?php
/**
 * Plugin Name: DOM Bundle Deals
 * Description: "3 voor €130" bundle logic, auto-apply discounts, and cart reward milestones (€200 free shipping, €350 mystery shirt).
 * Version: 1.0.0
 * Author: De Outfitman
 * Requires Plugins: woocommerce
 * Text Domain: dom-bundle-deals
 *
 * @package DomBundleDeals
 */
defined('ABSPATH') || exit;

class DOM_Bundle_Deals {

    private static $instance = null;

    private $bundles = [];

    public static function instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        $this->bundles = [
            [
                'categories' => ['t-shirts', 'zwembroeken'],
                'qty'        => 3,
                'price'      => 13000, // cents
                'label'      => '3 voor €130',
            ],
        ];

        add_action('woocommerce_before_calculate_totals', [$this, 'apply_bundle_pricing'], 20);
        add_filter('dom_cart_milestones', [$this, 'get_milestones']);
    }

    public function apply_bundle_pricing($cart) {
        if (is_admin() && !defined('DOING_AJAX')) return;
        if (did_action('woocommerce_before_calculate_totals') >= 2) return;

        foreach ($this->bundles as $bundle) {
            $this->process_bundle($cart, $bundle);
        }
    }

    private function process_bundle($cart, $bundle) {
        $eligible = [];

        foreach ($cart->get_cart() as $key => $item) {
            $product = $item['data'];
            $cats = wp_get_post_terms($item['product_id'], 'product_cat', ['fields' => 'slugs']);

            if (array_intersect($cats, $bundle['categories'])) {
                for ($i = 0; $i < $item['quantity']; $i++) {
                    $eligible[] = [
                        'key'   => $key,
                        'price' => (float) $product->get_regular_price(),
                        'item'  => $item,
                    ];
                }
            }
        }

        if (count($eligible) < $bundle['qty']) return;

        // Sort by price descending so cheapest items get discounted
        usort($eligible, fn($a, $b) => $b['price'] <=> $a['price']);

        $sets = array_chunk($eligible, $bundle['qty']);
        $bundle_price = $bundle['price'] / 100;

        foreach ($sets as $set) {
            if (count($set) < $bundle['qty']) continue;

            $total_original = array_sum(array_column($set, 'price'));
            if ($total_original <= 0) continue;

            foreach ($set as $item_data) {
                $ratio = $item_data['price'] / $total_original;
                $new_price = round($bundle_price * $ratio, 2);

                $cart_item = &$cart->cart_contents[$item_data['key']];
                $cart_item['data']->set_price($new_price);
            }
        }
    }

    public function get_milestones($milestones = []) {
        $cart_total = 0;
        if (function_exists('WC') && WC()->cart) {
            $cart_total = (float) WC()->cart->get_subtotal();
        }

        return [
            [
                'threshold' => 200,
                'label'     => 'Gratis verzending',
                'reached'   => $cart_total >= 200,
                'remaining' => max(0, 200 - $cart_total),
            ],
            [
                'threshold' => 350,
                'label'     => 'Gratis mystery shirt',
                'reached'   => $cart_total >= 350,
                'remaining' => max(0, 350 - $cart_total),
            ],
        ];
    }
}

DOM_Bundle_Deals::instance();
