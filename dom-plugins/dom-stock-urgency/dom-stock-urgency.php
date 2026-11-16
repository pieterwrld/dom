<?php
/**
 * Plugin Name: DOM Stock Urgency
 * Description: Dynamic urgency messages based on real WooCommerce stock levels. No fake scarcity — only real data.
 * Version: 1.0.0
 * Author: De Outfitman
 * Requires Plugins: woocommerce
 * Text Domain: dom-stock-urgency
 *
 * @package DomStockUrgency
 */
defined('ABSPATH') || exit;

class DOM_Stock_Urgency {

    private static $instance = null;

    public static function instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        add_filter('dom_stock_urgency_message', [$this, 'get_message'], 10, 2);
        add_action('wp_ajax_dom_stock_check', [$this, 'ajax_check']);
        add_action('wp_ajax_nopriv_dom_stock_check', [$this, 'ajax_check']);
    }

    public function get_message($message, $product_id) {
        $product = wc_get_product($product_id);
        if (!$product || !$product->managing_stock()) {
            return '';
        }

        $qty = $product->get_stock_quantity();
        $status = $product->get_stock_status();

        if ($status !== 'instock' || $qty === null) {
            return '';
        }

        if ($qty <= 1) {
            return [
                'text'  => __('Laatste op voorraad — wees er snel bij!', 'dom-stock-urgency'),
                'level' => 'critical',
            ];
        }

        if ($qty <= 2) {
            return [
                'text'  => sprintf(__('Nog %d op voorraad — twijfel niet!', 'dom-stock-urgency'), $qty),
                'level' => 'warning',
            ];
        }

        if ($qty <= 6) {
            return [
                'text'  => __('Bijna uitverkocht', 'dom-stock-urgency'),
                'level' => 'notice',
            ];
        }

        return '';
    }

    public function ajax_check() {
        $product_id = absint($_GET['product_id'] ?? 0);
        if (!$product_id) {
            wp_send_json_error();
        }

        $message = apply_filters('dom_stock_urgency_message', '', $product_id);
        wp_send_json_success($message);
    }
}

DOM_Stock_Urgency::instance();
