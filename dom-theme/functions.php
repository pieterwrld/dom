<?php
/**
 * De Outfitman Theme Functions
 *
 * @package DomTheme
 */

defined('ABSPATH') || exit;

define('DOM_THEME_VERSION', '1.0.0');
define('DOM_THEME_DIR', get_template_directory());
define('DOM_THEME_URI', get_template_directory_uri());

/**
 * Theme setup
 */
function dom_theme_setup() {
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('html5', [
        'search-form',
        'comment-form',
        'comment-list',
        'gallery',
        'caption',
        'style',
        'script',
    ]);
    add_theme_support('custom-logo', [
        'height'      => 60,
        'width'       => 300,
        'flex-height' => true,
        'flex-width'  => true,
    ]);
    add_theme_support('woocommerce');
    add_theme_support('wc-product-gallery-zoom');
    add_theme_support('wc-product-gallery-lightbox');
    add_theme_support('wc-product-gallery-slider');

    register_nav_menus([
        'primary'   => __('Hoofdmenu', 'dom-theme'),
        'footer'    => __('Footer Menu', 'dom-theme'),
        'mobile'    => __('Mobiel Menu', 'dom-theme'),
        'shop-cats' => __('Shop Categorieën', 'dom-theme'),
    ]);

    add_image_size('product-card', 600, 800, true);
    add_image_size('product-gallery', 900, 1200, true);
    add_image_size('collection-card', 400, 533, true);
    add_image_size('hero-banner', 1920, 1080, true);
    add_image_size('category-banner', 1200, 300, true);
    add_image_size('review-thumb', 400, 533, true);
}
add_action('after_setup_theme', 'dom_theme_setup');

/**
 * Draait WooCommerce op dit moment?
 *
 * De conditionals van WooCommerce (is_shop, is_product, ...) bestaan alleen
 * zolang de plugin actief is. Ze zonder controle aanroepen betekent dat de
 * winkel een wit scherm geeft op het moment dat WooCommerce uitvalt of nog
 * niet geladen is, precies wanneer je dat het minst kunt gebruiken.
 */
function dom_has_woo() {
    return class_exists('WooCommerce') && function_exists('is_shop');
}

/**
 * De URL van een term, of een lege string als die er niet is.
 *
 * get_term_link() geeft een WP_Error terug wanneer de term niet bestaat. Zo'n
 * object is truthy, dus het glipt door een ?:-fallback heen en belandt in
 * esc_url(), waar PHP 8 er een TypeError van maakt en de pagina omvalt. Eén
 * ontbrekende categorie zou zo de hele winkel neerhalen.
 *
 * @param WP_Term|int|string $term
 * @param string             $taxonomy
 * @return string
 */
function dom_term_url($term, $taxonomy = '') {
    if (empty($term)) {
        return '';
    }
    $link = $taxonomy ? get_term_link($term, $taxonomy) : get_term_link($term);
    return (is_string($link) && $link !== '') ? $link : '';
}

/**
 * De shop-URL, of de homepage als WooCommerce er niet is.
 */
function dom_shop_url() {
    if (!function_exists('wc_get_page_id')) {
        return home_url('/');
    }
    $page_id = wc_get_page_id('shop');
    return $page_id > 0 ? get_permalink($page_id) : home_url('/');
}

/**
 * Enqueue styles and scripts
 */
function dom_enqueue_assets() {
    wp_enqueue_style(
        'dom-fonts',
        DOM_THEME_URI . '/assets/css/fonts.css',
        [],
        DOM_THEME_VERSION
    );

    wp_enqueue_style(
        'dom-core',
        DOM_THEME_URI . '/assets/css/core.css',
        ['dom-fonts'],
        DOM_THEME_VERSION
    );

    if (dom_has_woo() && (is_shop() || is_product_category() || is_product_tag())) {
        wp_enqueue_style(
            'dom-collection',
            DOM_THEME_URI . '/assets/css/collection.css',
            ['dom-core'],
            DOM_THEME_VERSION
        );
    }

    if (dom_has_woo() && is_product()) {
        wp_enqueue_style(
            'dom-product',
            DOM_THEME_URI . '/assets/css/product.css',
            ['dom-core'],
            DOM_THEME_VERSION
        );
    }

    if (is_page() || (dom_has_woo() && (is_checkout() || is_account_page() || is_cart()))) {
        wp_enqueue_style(
            'dom-woocommerce',
            DOM_THEME_URI . '/assets/css/woocommerce.css',
            ['dom-core'],
            DOM_THEME_VERSION
        );
    }

    wp_enqueue_script(
        'dom-core',
        DOM_THEME_URI . '/assets/js/core.js',
        [],
        DOM_THEME_VERSION,
        ['strategy' => 'defer', 'in_footer' => true]
    );

    wp_enqueue_script(
        'dom-cart',
        DOM_THEME_URI . '/assets/js/cart.js',
        ['dom-core'],
        DOM_THEME_VERSION,
        ['strategy' => 'defer', 'in_footer' => true]
    );

    wp_localize_script('dom-cart', 'domCart', [
        'ajaxUrl'  => admin_url('admin-ajax.php'),
        'nonce'    => wp_create_nonce('dom-cart-nonce'),
        'currency' => function_exists('get_woocommerce_currency_symbol') ? get_woocommerce_currency_symbol() : '€',
    ]);

    if (dom_has_woo() && is_product()) {
        wp_enqueue_script(
            'dom-gallery',
            DOM_THEME_URI . '/assets/js/gallery.js',
            [],
            DOM_THEME_VERSION,
            ['strategy' => 'defer', 'in_footer' => true]
        );
    }

    if (dom_has_woo() && (is_shop() || is_product_category() || is_product_tag())) {
        wp_enqueue_script(
            'dom-filters',
            DOM_THEME_URI . '/assets/js/filters.js',
            [],
            DOM_THEME_VERSION,
            ['strategy' => 'defer', 'in_footer' => true]
        );
    }
}
add_action('wp_enqueue_scripts', 'dom_enqueue_assets');

/**
 * Remove WordPress bloat from <head>
 */
function dom_clean_head() {
    remove_action('wp_head', 'rsd_link');
    remove_action('wp_head', 'wlwmanifest_link');
    remove_action('wp_head', 'wp_generator');
    remove_action('wp_head', 'wp_shortlink_wp_head');
    remove_action('wp_head', 'rest_output_link_wp_head');
    remove_action('wp_head', 'wp_oembed_add_discovery_links');
    remove_action('wp_head', 'wp_oembed_add_host_js');
    remove_action('wp_head', 'print_emoji_detection_script', 7);
    remove_action('wp_print_styles', 'print_emoji_styles');
    remove_action('admin_print_scripts', 'print_emoji_detection_script');
    remove_action('admin_print_styles', 'print_emoji_styles');

    // Remove default WP block styles on frontend
    wp_dequeue_style('wp-block-library');
    wp_dequeue_style('wp-block-library-theme');
    wp_dequeue_style('wc-blocks-style');
    wp_dequeue_style('global-styles');
}
add_action('wp_enqueue_scripts', 'dom_clean_head', 100);
add_action('init', function () {
    remove_action('wp_head', 'rsd_link');
    remove_action('wp_head', 'wlwmanifest_link');
    remove_action('wp_head', 'wp_generator');
});

/**
 * Disable WordPress emoji
 */
add_filter('emoji_svg_url', '__return_false');
add_filter('wp_resource_hints', function ($urls, $relation_type) {
    if ($relation_type === 'dns-prefetch') {
        $urls = array_filter($urls, function ($url) {
            return !str_contains($url, 's.w.org');
        });
    }
    return $urls;
}, 10, 2);

/**
 * Dequeue jQuery on frontend (WooCommerce may re-add it where needed)
 */
function dom_dequeue_jquery() {
    if (!is_admin() && !(dom_has_woo() && (is_checkout() || is_account_page()))) {
        wp_dequeue_script('jquery');
        wp_deregister_script('jquery');
    }
}
add_action('wp_enqueue_scripts', 'dom_dequeue_jquery', 99);

/**
 * Include theme modules
 */
$dom_includes = [
    '/inc/theme-setup.php',
    '/inc/woocommerce.php',
    '/inc/enqueue.php',
    '/inc/content-settings.php',
    '/inc/acf-fields.php',
    '/dom-update.php',
];

foreach ($dom_includes as $file) {
    $filepath = DOM_THEME_DIR . $file;
    if (file_exists($filepath)) {
        require_once $filepath;
    }
}

/**
 * ACF JSON save/load paths
 */
add_filter('acf/settings/save_json', function () {
    return DOM_THEME_DIR . '/acf-json';
});

add_filter('acf/settings/load_json', function ($paths) {
    $paths[] = DOM_THEME_DIR . '/acf-json';
    return $paths;
});

/**
 * Add WooCommerce AJAX fragments for cart drawer
 */
function dom_cart_fragments($fragments) {
    ob_start();
    get_template_part('template-parts/cart-drawer');
    $fragments['aside.drw'] = ob_get_clean();

    ob_start();
    $count = (function_exists('WC') && WC()->cart) ? WC()->cart->get_cart_contents_count() : 0;
    echo '<span class="n js-cart-count">' . esc_html($count) . '</span>';
    $fragments['.js-cart-count'] = ob_get_clean();

    return $fragments;
}
add_filter('woocommerce_add_to_cart_fragments', 'dom_cart_fragments');
