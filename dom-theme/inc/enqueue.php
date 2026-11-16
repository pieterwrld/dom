<?php
/**
 * Additional enqueue logic (beyond functions.php)
 *
 * @package DomTheme
 */
defined('ABSPATH') || exit;

/**
 * Add preconnect hints
 */
add_action('wp_head', function () {
    echo '<link rel="dns-prefetch" href="//www.googletagmanager.com">' . "\n";
}, 1);

/**
 * Defer non-critical scripts
 */
add_filter('script_loader_tag', function ($tag, $handle) {
    $defer_handles = ['dom-main', 'dom-cart', 'dom-gallery', 'dom-filters'];
    if (in_array($handle, $defer_handles) && !str_contains($tag, 'defer')) {
        $tag = str_replace(' src=', ' defer src=', $tag);
    }
    return $tag;
}, 10, 2);
