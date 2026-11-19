<?php
/**
 * Maten: welke er zijn, welke op voorraad liggen, en erop filteren.
 *
 * De winkel gebruikt drie maatkenmerken naast elkaar: pa_schoenmaat (36–46),
 * pa_kledingmaat (XS–3XL) en pa_broekmaat (27/XXS–38/XXL). Het filter op de
 * collectiepagina had een vaste lijst XS–XXL en 40–46, die bij jeans dus
 * nergens op sloeg, en klikken deed niets: er werd alleen een label bijgewerkt.
 *
 * Om te kunnen filteren op "maat 42 en ook echt leverbaar" is per product
 * bekend welke maten op voorraad liggen. Dat per keer uitrekenen zou voor elk
 * product al zijn varianten moeten laden — op een pagina met 24 producten zijn
 * dat er al snel honderden. Daarom staat het als index in twee metavelden:
 *
 *   _dom_maten_alle      |pa_schoenmaat:40|pa_schoenmaat:41|…
 *   _dom_maten_voorraad  dezelfde opzet, maar alleen wat leverbaar is
 *
 * Met die opzet is filteren één LIKE op een geïndexeerd metaveld.
 */

defined('ABSPATH') || exit;

const DOM_MATEN_ALLE     = '_dom_maten_alle';
const DOM_MATEN_VOORRAAD = '_dom_maten_voorraad';

/** De maatkenmerken, in de volgorde waarin ze in het filter komen. */
function dom_maat_taxonomieen() {
    return apply_filters('dom_maat_taxonomieen', [
        'pa_kledingmaat' => __('Kleding', 'dom-theme'),
        'pa_broekmaat'   => __('Broeken', 'dom-theme'),
        'pa_schoenmaat'  => __('Schoenen', 'dom-theme'),
    ]);
}

/** Een sleutel die in het metaveld past: |taxonomie:slug| */
function dom_maat_sleutel($taxonomie, $slug) {
    return '|' . $taxonomie . ':' . $slug . '|';
}

/* ===================================================================
 *  De index opbouwen
 * =================================================================== */

/**
 * De index van één product herberekenen en opslaan.
 *
 * @return array{alle:string, voorraad:string}
 */
function dom_maten_index_bouw($product_id) {
    $product = wc_get_product($product_id);
    if (!$product) {
        return ['alle' => '', 'voorraad' => ''];
    }

    $taxonomieen = array_keys(dom_maat_taxonomieen());
    $alle = [];
    $op_voorraad = [];

    if ($product->is_type('variable')) {
        foreach ($product->get_children() as $kind_id) {
            $variant = wc_get_product($kind_id);
            if (!$variant) {
                continue;
            }
            $leverbaar = $variant->is_in_stock() && $variant->is_purchasable();

            foreach ($variant->get_attributes() as $taxonomie => $slug) {
                if ($slug === '' || !in_array($taxonomie, $taxonomieen, true)) {
                    continue;
                }
                $sleutel = dom_maat_sleutel($taxonomie, $slug);
                $alle[$sleutel] = true;
                if ($leverbaar) {
                    $op_voorraad[$sleutel] = true;
                }
            }
        }
    } else {
        // Een eenvoudig product heeft geen varianten; de maten staan dan als
        // gewoon kenmerk op het product zelf.
        foreach ($taxonomieen as $taxonomie) {
            $termen = $product->get_attribute($taxonomie);
            if (!$termen) {
                continue;
            }
            foreach (array_map('trim', explode(',', $termen)) as $naam) {
                $term = get_term_by('name', $naam, $taxonomie);
                if (!$term) {
                    continue;
                }
                $sleutel = dom_maat_sleutel($taxonomie, $term->slug);
                $alle[$sleutel] = true;
                if ($product->is_in_stock()) {
                    $op_voorraad[$sleutel] = true;
                }
            }
        }
    }

    $waarden = [
        'alle'     => implode('', array_keys($alle)),
        'voorraad' => implode('', array_keys($op_voorraad)),
    ];

    update_post_meta($product_id, DOM_MATEN_ALLE, $waarden['alle']);
    update_post_meta($product_id, DOM_MATEN_VOORRAAD, $waarden['voorraad']);

    return $waarden;
}

/**
 * De index van een product lezen; ontbreekt hij, dan bouwen we hem alsnog.
 *
 * @return array<string, array<string, bool>> taxonomie => slug => leverbaar
 */
function dom_maten_van_product($product_id) {
    $alle = get_post_meta($product_id, DOM_MATEN_ALLE, true);

    if ($alle === '' && get_post_meta($product_id, DOM_MATEN_VOORRAAD, true) === '') {
        $gebouwd  = dom_maten_index_bouw($product_id);
        $alle     = $gebouwd['alle'];
        $voorraad = $gebouwd['voorraad'];
    } else {
        $voorraad = (string) get_post_meta($product_id, DOM_MATEN_VOORRAAD, true);
    }

    $uit = [];
    foreach (dom_maten_ontleed($alle) as $taxonomie => $slugs) {
        foreach ($slugs as $slug) {
            $uit[$taxonomie][$slug] = strpos($voorraad, dom_maat_sleutel($taxonomie, $slug)) !== false;
        }
    }
    return $uit;
}

/**
 * |tax:slug||tax:slug| terug naar een lijst.
 *
 * @return array<string, string[]>
 */
function dom_maten_ontleed($waarde) {
    $uit = [];
    foreach (explode('|', (string) $waarde) as $stuk) {
        if ($stuk === '' || strpos($stuk, ':') === false) {
            continue;
        }
        [$taxonomie, $slug] = explode(':', $stuk, 2);
        $uit[$taxonomie][] = $slug;
    }
    return $uit;
}

/* ===================================================================
 *  Bijhouden
 * =================================================================== */

add_action('woocommerce_update_product', 'dom_maten_index_bouw', 20);
add_action('woocommerce_new_product', 'dom_maten_index_bouw', 20);

/** Een variant die wijzigt raakt de index van het hoofdproduct. */
function dom_maten_index_via_variant($variant_id) {
    $ouder = wp_get_post_parent_id($variant_id);
    if ($ouder) {
        dom_maten_index_bouw($ouder);
    }
}
add_action('woocommerce_update_product_variation', 'dom_maten_index_via_variant', 20);
add_action('woocommerce_save_product_variation', 'dom_maten_index_via_variant', 20);

/** Voorraadmutaties, ook die uit een bestelling. */
add_action('woocommerce_variation_set_stock', function ($variant) {
    if (is_a($variant, 'WC_Product')) {
        dom_maten_index_via_variant($variant->get_id());
    }
}, 20);

add_action('woocommerce_product_set_stock', function ($product) {
    if (is_a($product, 'WC_Product')) {
        $id = $product->get_parent_id() ?: $product->get_id();
        dom_maten_index_bouw($id);
    }
}, 20);

/* ===================================================================
 *  Filteren
 * =================================================================== */

/**
 * De gekozen maten uit het adres. Meerdere maten mogen, gescheiden door komma's.
 *
 * @return string[] sleutels als "pa_schoenmaat:42"
 */
function dom_gekozen_maten() {
    if (empty($_GET['maat'])) {
        return [];
    }
    $rauw = sanitize_text_field(wp_unslash($_GET['maat']));
    $uit  = [];
    foreach (explode(',', $rauw) as $stuk) {
        $stuk = trim($stuk);
        if ($stuk === '' || strpos($stuk, ':') === false) {
            continue;
        }
        [$taxonomie, $slug] = explode(':', $stuk, 2);
        if (!array_key_exists($taxonomie, dom_maat_taxonomieen())) {
            continue;
        }
        $slug = sanitize_title($slug);
        if ($slug !== '') {
            $uit[] = $taxonomie . ':' . $slug;
        }
    }
    return array_values(array_unique($uit));
}

/** De gekozen maximumprijs, of 0. */
function dom_gekozen_maxprijs() {
    return isset($_GET['max_prijs']) ? max(0, (float) $_GET['max_prijs']) : 0;
}

/**
 * De filters op de hoofdquery van een collectiepagina zetten.
 *
 * Op maat filteren betekent hier: het product heeft die maat én die maat ligt
 * op voorraad. Een maat die overal uitverkocht is levert dus geen resultaten
 * op, in plaats van een lijst producten die je toch niet kunt bestellen.
 */
add_action('pre_get_posts', function (WP_Query $query) {
    if (is_admin() || !$query->is_main_query()) {
        return;
    }
    if (!$query->is_post_type_archive('product') && !$query->is_tax(get_object_taxonomies('product'))) {
        return;
    }

    $maten   = dom_gekozen_maten();
    $maxprijs = dom_gekozen_maxprijs();
    if (!$maten && !$maxprijs) {
        return;
    }

    $meta = (array) $query->get('meta_query');

    if ($maten) {
        $maat_query = ['relation' => 'OR'];
        foreach ($maten as $sleutel) {
            [$taxonomie, $slug] = explode(':', $sleutel, 2);
            $maat_query[] = [
                'key'     => DOM_MATEN_VOORRAAD,
                'value'   => dom_maat_sleutel($taxonomie, $slug),
                'compare' => 'LIKE',
            ];
        }
        $meta[] = $maat_query;
    }

    if ($maxprijs > 0) {
        $meta[] = [
            'key'     => '_price',
            'value'   => $maxprijs,
            'type'    => 'DECIMAL(10,2)',
            'compare' => '<=',
        ];
    }

    $query->set('meta_query', $meta);
});

/* ===================================================================
 *  Wat er te kiezen valt op deze pagina
 * =================================================================== */

/**
 * De maten die binnen de huidige collectie voorkomen, met per maat of er nog
 * iets van op voorraad ligt.
 *
 * Alleen tonen wat er is: op een pagina met jeans hoeven geen schoenmaten te
 * staan. Het resultaat wordt een uur bewaard, want het verandert alleen als de
 * voorraad verandert.
 *
 * @return array<string, array{label:string, maten:array<string, array{naam:string, voorraad:bool}>}>
 */
function dom_beschikbare_maten() {
    $term = is_product_taxonomy() ? get_queried_object() : null;
    $cache_sleutel = 'dom_maten_' . ($term && !is_wp_error($term) ? $term->taxonomy . '_' . $term->term_id : 'shop');

    $gevonden = get_transient($cache_sleutel);
    if (is_array($gevonden)) {
        return $gevonden;
    }

    $args = [
        'post_type'      => 'product',
        'post_status'    => 'publish',
        'posts_per_page' => 400,
        'fields'         => 'ids',
        'no_found_rows'  => true,
    ];
    if ($term && !is_wp_error($term)) {
        $args['tax_query'] = [[
            'taxonomy' => $term->taxonomy,
            'field'    => 'term_id',
            'terms'    => $term->term_id,
        ]];
    }

    $ids = get_posts($args);
    if (!$ids) {
        set_transient($cache_sleutel, [], HOUR_IN_SECONDS);
        return [];
    }

    // Eén keer ophalen in plaats van per product een query.
    update_meta_cache('post', $ids);

    $verzameld = [];
    foreach ($ids as $id) {
        $alle = (string) get_post_meta($id, DOM_MATEN_ALLE, true);
        if ($alle === '') {
            continue;
        }
        $voorraad = (string) get_post_meta($id, DOM_MATEN_VOORRAAD, true);
        foreach (dom_maten_ontleed($alle) as $taxonomie => $slugs) {
            foreach ($slugs as $slug) {
                if (!isset($verzameld[$taxonomie][$slug])) {
                    $verzameld[$taxonomie][$slug] = false;
                }
                if (strpos($voorraad, dom_maat_sleutel($taxonomie, $slug)) !== false) {
                    $verzameld[$taxonomie][$slug] = true;
                }
            }
        }
    }

    $uit = [];
    foreach (dom_maat_taxonomieen() as $taxonomie => $label) {
        if (empty($verzameld[$taxonomie])) {
            continue;
        }
        $termen = get_terms([
            'taxonomy'   => $taxonomie,
            'hide_empty' => false,
            'slug'       => array_keys($verzameld[$taxonomie]),
        ]);
        if (is_wp_error($termen) || !$termen) {
            continue;
        }

        // De volgorde die in WooCommerce is ingesteld aanhouden.
        $maten = [];
        foreach ($termen as $t) {
            $maten[$t->slug] = [
                'naam'     => $t->name,
                'voorraad' => !empty($verzameld[$taxonomie][$t->slug]),
            ];
        }
        $uit[$taxonomie] = ['label' => $label, 'maten' => $maten];
    }

    set_transient($cache_sleutel, $uit, HOUR_IN_SECONDS);
    return $uit;
}

/** De transients vergeten zodra er iets aan een product verandert. */
function dom_maten_cache_leegmaken() {
    global $wpdb;
    $wpdb->query(
        "DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_dom_maten_%'
         OR option_name LIKE '_transient_timeout_dom_maten_%'"
    );
}
add_action('woocommerce_update_product', 'dom_maten_cache_leegmaken', 30);
add_action('woocommerce_product_set_stock', 'dom_maten_cache_leegmaken', 30);
add_action('woocommerce_variation_set_stock', 'dom_maten_cache_leegmaken', 30);

/* ===================================================================
 *  De index in één keer opbouwen
 * =================================================================== */

add_action('rest_api_init', function () {
    register_rest_route('dom/v1', '/maten-index', [
        'methods'             => 'POST',
        'callback'            => 'dom_maten_rest',
        'permission_callback' => function () {
            return current_user_can('manage_woocommerce') || current_user_can('manage_options');
        },
        'args' => [
            'offset' => ['type' => 'integer', 'default' => 0],
            'limit'  => ['type' => 'integer', 'default' => 40],
        ],
    ]);
});

function dom_maten_rest(WP_REST_Request $request) {
    if (!function_exists('wc_get_product')) {
        return new WP_REST_Response(['ok' => false, 'fout' => 'WooCommerce is niet actief'], 500);
    }
    @set_time_limit(300);

    $offset = max(0, (int) $request->get_param('offset'));
    $limit  = min(100, max(1, (int) $request->get_param('limit')));

    $alle_ids = get_posts([
        'post_type'      => 'product',
        'post_status'    => 'publish',
        'posts_per_page' => -1,
        'fields'         => 'ids',
        'orderby'        => 'ID',
        'order'          => 'ASC',
        'no_found_rows'  => true,
    ]);

    $totaal = count($alle_ids);
    $metMaten = 0;

    foreach (array_slice($alle_ids, $offset, $limit) as $id) {
        $gebouwd = dom_maten_index_bouw($id);
        if ($gebouwd['alle'] !== '') {
            $metMaten++;
        }
    }

    $volgende = $offset + $limit;
    $klaar    = $volgende >= $totaal;

    if ($klaar) {
        dom_maten_cache_leegmaken();
    }

    return new WP_REST_Response([
        'ok'         => true,
        'totaal'     => $totaal,
        'offset'     => $offset,
        'volgende'   => $klaar ? null : $volgende,
        'klaar'      => $klaar,
        'met_maten'  => $metMaten,
    ], 200);
}
