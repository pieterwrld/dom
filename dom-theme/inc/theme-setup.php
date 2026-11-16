<?php
/**
 * Additional theme setup
 *
 * @package DomTheme
 */
defined('ABSPATH') || exit;

/**
 * Add custom body classes
 */
add_filter('body_class', function ($classes) {
    $classes[] = 'dom-theme';

    if (is_product()) {
        $classes[] = 'dom-product-page';
    }
    if (is_shop() || is_product_category()) {
        $classes[] = 'dom-shop-page';
    }

    return $classes;
});

/**
 * Custom excerpt length
 */
add_filter('excerpt_length', function () {
    return 20;
});

/**
 * Disable Gutenberg for pages (we use ACF)
 */
add_filter('use_block_editor_for_post', function ($use, $post) {
    if ($post->post_type === 'page') return false;
    return $use;
}, 10, 2);

/* ACF options page is registered in inc/acf-fields.php */
