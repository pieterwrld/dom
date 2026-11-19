<?php
/**
 * De Outfitman Theme Functions
 *
 * @package DomTheme
 */

defined('ABSPATH') || exit;

define('DOM_THEME_VERSION', '1.0.0');

/**
 * Versie voor de ?ver= achter stylesheets en scripts.
 *
 * Met de vaste themaversie kreeg elk bestand altijd ?ver=1.0.0, ook nadat de
 * inhoud was gewijzigd. Browsers en LiteSpeed hielden daardoor de oude CSS
 * vast en een uitgerolde aanpassing was simpelweg niet te zien. De wijzigings-
 * datum van het bestand verandert wel bij elke deploy.
 *
 * @param string $relative Pad binnen het thema, bijvoorbeeld /assets/css/core.css
 * @return string
 */
function dom_asset_version($relative) {
    $file = DOM_THEME_DIR . $relative;
    $time = file_exists($file) ? filemtime($file) : 0;
    return $time ? DOM_THEME_VERSION . '.' . $time : DOM_THEME_VERSION;
}
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
 * De eigen voorpagina laten winnen van Elementor.
 *
 * De homepage is een Elementor-pagina met paginatemplate "elementor_header_footer".
 * Elementor dringt dat template via template_include op, waardoor front-page.php
 * van dit thema nooit aan bod kwam en de homepage de oude opmaak bleef tonen.
 *
 * Dit wordt hier opgelost en niet door de pagina zelf aan te passen, zodat de
 * content onaangeroerd blijft en terugschakelen naar het oude thema meteen weer
 * de oude homepage geeft.
 */
add_filter('template_include', function ($template) {
    if (is_admin() || !is_front_page() || is_home()) {
        return $template;
    }
    $front = locate_template('front-page.php');
    return $front ?: $template;
}, 999);

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
 * De hoogste werkelijke korting in de winkel, als percentage. 0 betekent: niets
 * in de aanbieding.
 *
 * Kortingsclaims horen uit de prijzen te komen. Een vast percentage in een
 * template is een bewering die niemand nakijkt en die stilletjes onjuist wordt
 * zodra de aanbiedingen wijzigen.
 */
function dom_max_sale_percentage() {
    static $best = null;
    if (null !== $best) {
        return $best;
    }
    $best = 0;

    if (!function_exists('wc_get_product_ids_on_sale') || !function_exists('wc_get_product')) {
        return $best;
    }

    foreach (array_slice(wc_get_product_ids_on_sale(), 0, 100) as $id) {
        $product = wc_get_product($id);
        if (!$product) {
            continue;
        }
        $p = dom_prices($product);
        if ($p['sale'] && $p['pct'] > 0) {
            $best = max($best, $p['pct']);
        }
    }

    return $best;
}

/**
 * De prijzen van één product, op een manier die ook klopt bij maten.
 *
 * Bij een variabel product (een product met maten) staan de prijzen op de
 * variaties, niet op het hoofdproduct. get_regular_price() en get_sale_price()
 * geven daar een lege waarde terug, en die werd als € 0,00 op de kaart gezet.
 * WooCommerce heeft hier eigen methodes voor, en die gebruiken we hier.
 *
 * Hebben de maten niet allemaal dezelfde prijs, dan is 'bereik' waar en toont
 * de sjabloon "vanaf" bij de laagste prijs.
 *
 * @param WC_Product $product
 * @return array{nu:float, nu_max:float, van:float, sale:bool, pct:int, bereik:bool, geldig:bool}
 */
function dom_prices($product) {
    $leeg = ['nu' => 0.0, 'nu_max' => 0.0, 'van' => 0.0, 'sale' => false, 'pct' => 0, 'bereik' => false, 'geldig' => false];

    if (!$product || !is_a($product, 'WC_Product')) {
        return $leeg;
    }

    if ($product->is_type('variable')) {
        $nu     = (float) $product->get_variation_price('min', true);
        $nu_max = (float) $product->get_variation_price('max', true);
        $van    = (float) $product->get_variation_regular_price('min', true);
    } elseif ($product->is_type('grouped')) {
        // Een groep heeft zelf geen prijs; die van de kinderen bepalen hem.
        $kinderen = [];
        foreach ($product->get_children() as $kind_id) {
            $kind = wc_get_product($kind_id);
            if ($kind && '' !== $kind->get_price()) {
                $kinderen[] = (float) wc_get_price_to_display($kind);
            }
        }
        $nu     = $kinderen ? min($kinderen) : 0.0;
        $nu_max = $kinderen ? max($kinderen) : 0.0;
        $van    = $nu;
    } else {
        $nu     = '' === $product->get_price() ? 0.0 : (float) wc_get_price_to_display($product);
        $nu_max = $nu;
        $regular = $product->get_regular_price();
        $van = '' === $regular || null === $regular
            ? $nu
            : (float) wc_get_price_to_display($product, ['price' => $regular]);
    }

    $sale = $van > 0 && $nu > 0 && $nu < $van;

    return [
        'nu'     => $nu,
        'nu_max' => $nu_max,
        'van'    => $sale ? $van : 0.0,
        'sale'   => $sale,
        'pct'    => $sale ? (int) round((($van - $nu) / $van) * 100) : 0,
        'bereik' => $nu_max > $nu,
        // Zonder bruikbare prijs toont de sjabloon liever niets dan € 0,00.
        'geldig' => $nu > 0,
    ];
}

/** Een bedrag als € 1.234,56. */
function dom_eur($bedrag) {
    return '€' . number_format((float) $bedrag, 2, ',', '.');
}

/**
 * De prijsregel van een product als kant-en-klare HTML.
 *
 * @param WC_Product $product
 * @param bool       $toon_korting Het rode -x% label erbij.
 * @return string
 */
function dom_price_html($product, $toon_korting = false) {
    $p = dom_prices($product);

    if (!$p['geldig']) {
        // Terugval op WooCommerce zelf: beter een prijsbereik of "Prijs op
        // aanvraag" dan een verzonnen nul.
        return $product ? (string) $product->get_price_html() : '';
    }

    $uit = '';
    if ($p['bereik']) {
        $uit .= '<span class="vanaf">' . esc_html__('vanaf', 'dom-theme') . '</span> ';
    }
    $uit .= '<span class="nu">' . esc_html(dom_eur($p['nu'])) . '</span>';

    if ($p['sale']) {
        $uit .= ' <s>' . esc_html(dom_eur($p['van'])) . '</s>';
        if ($toon_korting && $p['pct'] > 0) {
            $uit .= ' <span class="off">-' . (int) $p['pct'] . '%</span>';
        }
    }

    return $uit;
}

/**
 * De categorieën in vaste volgorde.
 *
 * Het menu sorteerde op menu_order en het collectiegrid op een eigen lijst,
 * waardoor dezelfde categorieën in beide een andere volgorde kregen en het
 * menu met Petten begon. Beide gebruiken nu deze functie.
 *
 * @param bool $alleen_grid Laat de categorieën weg die niet op de homepage horen.
 * @return WP_Term[]
 */
function dom_ordered_categories($alleen_grid = false) {
    static $cache = [];
    $sleutel = $alleen_grid ? 'grid' : 'menu';
    if (isset($cache[$sleutel])) {
        return $cache[$sleutel];
    }

    $volgorde = [
        'sneakers', 'jassen', 'jeans', 'trainingspakken',
        'sweaters-hoodies', 't-shirts', 'zwembroeken', 'slippers', 'sale',
        'petten', 'tassen', 'brillen',
    ];

    $heren  = get_term_by('slug', 'heren', 'product_cat');
    $termen = get_terms([
        'taxonomy'   => 'product_cat',
        'hide_empty' => false,
        'parent'     => $heren ? $heren->term_id : 0,
    ]);
    if (is_wp_error($termen)) {
        $termen = [];
    }

    // SALE staat op het hoogste niveau, niet onder Heren.
    $sale = get_term_by('slug', 'sale', 'product_cat');
    if ($sale && !is_wp_error($sale)) {
        $termen[] = $sale;
    }

    $overslaan = ['uncategorized', 'bestsellers', 'heren', 'nieuwste-collectie', 'truien-vesten'];
    $op_slug   = [];
    foreach ($termen as $term) {
        if (!in_array($term->slug, $overslaan, true)) {
            $op_slug[$term->slug] = $term;
        }
    }

    $uit = [];
    foreach ($volgorde as $slug) {
        if (isset($op_slug[$slug])) {
            $uit[] = $op_slug[$slug];
            unset($op_slug[$slug]);
        }
    }
    foreach ($op_slug as $term) {
        $uit[] = $term;
    }

    $cache[$sleutel] = $uit;
    return $uit;
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
        dom_asset_version('/assets/css/fonts.css')
    );

    wp_enqueue_style(
        'dom-core',
        DOM_THEME_URI . '/assets/css/core.css',
        ['dom-fonts'],
        dom_asset_version('/assets/css/core.css')
    );

    if (dom_has_woo() && (is_shop() || is_product_category() || is_product_tag())) {
        wp_enqueue_style(
            'dom-collection',
            DOM_THEME_URI . '/assets/css/collection.css',
            ['dom-core'],
            dom_asset_version('/assets/css/collection.css')
        );
    }

    if (dom_has_woo() && is_product()) {
        wp_enqueue_style(
            'dom-product',
            DOM_THEME_URI . '/assets/css/product.css',
            ['dom-core'],
            dom_asset_version('/assets/css/product.css')
        );
    }

    if (is_page() || (dom_has_woo() && (is_checkout() || is_account_page() || is_cart()))) {
        wp_enqueue_style(
            'dom-woocommerce',
            DOM_THEME_URI . '/assets/css/woocommerce.css',
            ['dom-core'],
            dom_asset_version('/assets/css/woocommerce.css')
        );
    }

    wp_enqueue_script(
        'dom-core',
        DOM_THEME_URI . '/assets/js/core.js',
        [],
        dom_asset_version('/assets/js/core.js'),
        ['strategy' => 'defer', 'in_footer' => true]
    );

    wp_enqueue_script(
        'dom-cart',
        DOM_THEME_URI . '/assets/js/cart.js',
        ['dom-core'],
        dom_asset_version('/assets/js/cart.js'),
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
            dom_asset_version('/assets/js/gallery.js'),
            ['strategy' => 'defer', 'in_footer' => true]
        );
    }

    if (dom_has_woo() && (is_shop() || is_product_category() || is_product_tag())) {
        wp_enqueue_script(
            'dom-filters',
            DOM_THEME_URI . '/assets/js/filters.js',
            [],
            dom_asset_version('/assets/js/filters.js'),
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
/**
 * jQuery alleen weghalen als niemand het nodig heeft.
 *
 * Het thema zelf gebruikt geen jQuery, maar de winkel draait tientallen
 * plugins die dat wel doen. Het onvoorwaardelijk weggooien leverde op elke
 * pagina "jQuery is not defined" op, waarmee die plugins stilvielen: op de
 * contactpagina is dat het formulier dat niet meer verstuurt.
 *
 * Daarom kijken we eerst of een ingeladen script jQuery als afhankelijkheid
 * opgeeft. Zo ja, dan blijft het staan.
 */
function dom_dequeue_jquery() {
    if (is_admin()) {
        return;
    }
    if (dom_has_woo() && (is_checkout() || is_account_page() || is_cart())) {
        return;
    }

    $scripts = wp_scripts();

    foreach ($scripts->queue as $handle) {
        if (!isset($scripts->registered[$handle])) {
            continue;
        }
        $deps = (array) $scripts->registered[$handle]->deps;
        if (in_array('jquery', $deps, true) || in_array('jquery-core', $deps, true)) {
            return; // iets heeft jQuery nodig: laten staan.
        }
    }

    wp_dequeue_script('jquery');
    wp_deregister_script('jquery');
}
add_action('wp_enqueue_scripts', 'dom_dequeue_jquery', 99);

/**
 * Elementor-scripts weghalen op de voorpagina.
 *
 * De voorpagina is in WordPress nog een Elementor-pagina, dus Elementor laadt
 * zijn frontend mee. Wij tonen daar front-page.php van dit thema, zodat die
 * inhoud nooit verschijnt en het script zijn configuratie mist:
 * "elementorFrontendConfig is not defined", wat alles erna in dat bundel stopt.
 */
function dom_dequeue_elementor_on_front() {
    if (is_admin() || !is_front_page() || is_home()) {
        return;
    }
    foreach ([
        'elementor-frontend',
        'elementor-frontend-modules',
        'elementor-webpack-runtime',
        'elementor-pro-frontend',
        'elementor-pro-webpack-runtime',
    ] as $handle) {
        wp_dequeue_script($handle);
    }
}
add_action('wp_enqueue_scripts', 'dom_dequeue_elementor_on_front', 100);

/**
 * Include theme modules
 */
$dom_includes = [
    '/inc/fatal-log.php',
    '/inc/content-api.php',
    '/inc/theme-setup.php',
    '/inc/woocommerce.php',
    '/inc/maten.php',
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
