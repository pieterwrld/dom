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

/**
 * Register ACF options page
 */
if (function_exists('acf_add_options_page')) {
    acf_add_options_page([
        'page_title' => 'De Outfitman Instellingen',
        'menu_title' => 'DOM Instellingen',
        'menu_slug'  => 'dom-settings',
        'capability' => 'manage_options',
        'icon_url'   => 'dashicons-admin-customizer',
        'position'   => 2,
    ]);

    acf_add_options_sub_page([
        'page_title'  => 'Homepage',
        'menu_title'  => 'Homepage',
        'parent_slug' => 'dom-settings',
    ]);

    acf_add_options_sub_page([
        'page_title'  => 'Marquee / Announcement Bar',
        'menu_title'  => 'Marquee',
        'parent_slug' => 'dom-settings',
    ]);

    acf_add_options_sub_page([
        'page_title'  => 'Partners (Klokjesman & Parfum4car)',
        'menu_title'  => 'Partners',
        'parent_slug' => 'dom-settings',
    ]);
}
