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

    if (dom_has_woo() && is_product()) {
        $classes[] = 'dom-product-page';
    }
    if (dom_has_woo() && (is_shop() || is_product_category())) {
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

/**
 * Reviews als eigen berichttype.
 *
 * Dit was de eerste manier om de WhatsApp-screenshots te beheren. Sinds de
 * plugin De Outfitman Content bestaat, gaan reviews daar via het tabblad
 * Reviews; dan blijft dit berichttype wel bestaan (zodat niets verdwijnt wat
 * er al in staat) maar verdwijnt het uit het menu, zodat er één plek is.
 */
add_action('init', function () {
    $los_scherm = !function_exists('dom_c_reviews');

    register_post_type('dom_review', [
        'labels' => [
            'name'               => __('Reviews', 'dom-theme'),
            'singular_name'      => __('Review', 'dom-theme'),
            'add_new'            => __('Nieuwe review', 'dom-theme'),
            'add_new_item'       => __('Nieuwe review toevoegen', 'dom-theme'),
            'edit_item'          => __('Review bewerken', 'dom-theme'),
            'all_items'          => __('Alle reviews', 'dom-theme'),
            'featured_image'     => __('Screenshot', 'dom-theme'),
            'set_featured_image' => __('Screenshot kiezen', 'dom-theme'),
            'menu_name'          => __('Reviews', 'dom-theme'),
        ],
        'public'              => false,
        'show_ui'             => $los_scherm,
        'show_in_menu'        => $los_scherm,
        'show_in_rest'        => true,
        'menu_position'       => 26,
        'menu_icon'           => 'dashicons-format-chat',
        'supports'            => ['title', 'thumbnail', 'page-attributes'],
        'exclude_from_search' => true,
        'has_archive'         => false,
        'rewrite'             => false,
    ]);
});

/**
 * Korte uitleg boven het scherm, zodat duidelijk is wat er van een review
 * verwacht wordt en waarom het telefoonnummer eraf moet.
 */
add_action('edit_form_after_title', function ($post) {
    if ('dom_review' !== $post->post_type) {
        return;
    }
    echo '<div class="notice notice-info inline"><p>'
        . esc_html__('Kies rechts een screenshot als uitgelichte afbeelding. Staand beeld werkt het beste. Snijd het telefoonnummer van de klant er vooraf af: de kaart toont de afbeelding zoals hij is.', 'dom-theme')
        . '</p></div>';
});

/**
 * De screenshots die in de reviewsectie getoond worden.
 *
 * Staan er reviews in wp-admin, dan tellen alleen die. Anders de afbeeldingen
 * die met het thema meekomen, zodat de sectie nooit leeg valt.
 *
 * @return array<int, array{url:string, id:int}>
 */
function dom_review_screenshots() {
    $uit = [];

    // Het beheerscherm gaat voor.
    if (function_exists('dom_c_reviews')) {
        $uit = dom_c_reviews();
        if ($uit) {
            return $uit;
        }
    }

    $posts = get_posts([
        'post_type'      => 'dom_review',
        'post_status'    => 'publish',
        'posts_per_page' => 40,
        'orderby'        => ['menu_order' => 'ASC', 'date' => 'DESC'],
    ]);

    foreach ($posts as $post) {
        $url = get_the_post_thumbnail_url($post->ID, 'large');
        if ($url) {
            $uit[] = ['url' => $url, 'id' => (int) $post->ID];
        }
    }

    if ($uit) {
        return $uit;
    }

    $dir = get_stylesheet_directory() . '/assets/img/reviews';
    $uri = get_stylesheet_directory_uri() . '/assets/img/reviews';
    foreach (glob($dir . '/review-*.webp') ?: [] as $pad) {
        $uit[] = ['url' => $uri . '/' . basename($pad), 'id' => 0];
    }

    return $uit;
}
