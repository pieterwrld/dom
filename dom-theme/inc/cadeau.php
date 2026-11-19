<?php
/**
 * De twee beloftes in de winkelwagen echt laten kloppen.
 *
 * 1. Gratis verzending vanaf een drempelbedrag. De balk in de lade beloofde
 *    dat al, maar in WooCommerce stond alleen een vast tarief van €9,99 en
 *    €14,99 — de klant betaalde dus gewoon. Nu ligt er een verzendmethode
 *    "Gratis verzending" met een minimumbedrag in beide zones, en hieronder
 *    verbergen we de betaalde methodes zodra die gratis methode beschikbaar is.
 *    Anders zou de klant nog steeds kunnen kiezen om te betalen.
 *
 * 2. Het gratis mystery shirt vanaf een hoger bedrag. Dat was alleen een
 *    plaatje in de balk; nu komt het product er echt bij, voor €0, en het
 *    verdwijnt weer zodra het bedrag onder de drempel zakt.
 *
 * Beide drempels staan in het beheerscherm onder Winkelwagen & checkout.
 */

defined('ABSPATH') || exit;

/** De slug van het cadeauproduct. */
const DOM_CADEAU_SLUG = 'gratis-mystery-shirt';

/** Het id van het cadeauproduct, of 0 als het er niet is. */
function dom_cadeau_product_id() {
    static $id = null;
    if ($id !== null) {
        return $id;
    }
    $gevonden = get_posts([
        'post_type'      => 'product',
        'post_status'    => 'publish',
        'name'           => DOM_CADEAU_SLUG,
        'posts_per_page' => 1,
        'fields'         => 'ids',
        'no_found_rows'  => true,
    ]);
    $id = $gevonden ? (int) $gevonden[0] : 0;
    return $id;
}

/** Vanaf welk bedrag de verzending gratis is. */
function dom_drempel_verzending() {
    return (float) dom_c('winkelwagen.drempels.verzending', 220);
}

/** Vanaf welk bedrag het cadeau meegaat. */
function dom_drempel_cadeau() {
    return (float) dom_c('winkelwagen.drempels.cadeau', 350);
}

/**
 * Het bedrag waar de drempels tegenaan gehouden worden: alles in de wagen
 * behalve het cadeau zelf.
 */
function dom_cart_subtotaal() {
    if (!function_exists('WC') || !WC()->cart) {
        return 0.0;
    }
    $cadeau_id = dom_cadeau_product_id();
    $som = 0.0;
    foreach (WC()->cart->get_cart() as $item) {
        if (!empty($item['dom_cadeau']) || (int) ($item['product_id'] ?? 0) === $cadeau_id) {
            continue;
        }
        if (empty($item['data']) || !is_a($item['data'], 'WC_Product')) {
            continue;
        }
        $som += (float) $item['data']->get_price() * (int) $item['quantity'];
    }
    return $som;
}

/* ===================================================================
 *  Het cadeau in en uit de wagen
 * =================================================================== */

/**
 * Kijken of het cadeau erbij hoort en dat zo nodig rechtzetten.
 *
 * Wordt na elke wijziging aan de wagen aangeroepen. De statische vlag
 * voorkomt dat het toevoegen zichzelf opnieuw aanroept.
 */
function dom_cadeau_bijwerken() {
    static $bezig = false;
    if ($bezig || is_admin() || !function_exists('WC') || !WC()->cart) {
        return;
    }

    $cadeau_id = dom_cadeau_product_id();
    if (!$cadeau_id) {
        return;
    }

    $bezig = true;

    $drempel   = dom_drempel_cadeau();
    $subtotaal = dom_cart_subtotaal();
    $verdiend  = $drempel > 0 && $subtotaal >= $drempel;

    $in_wagen = null;
    foreach (WC()->cart->get_cart() as $sleutel => $item) {
        if (!empty($item['dom_cadeau']) || (int) ($item['product_id'] ?? 0) === $cadeau_id) {
            $in_wagen = $sleutel;
            break;
        }
    }

    if ($verdiend && !$in_wagen) {
        WC()->cart->add_to_cart($cadeau_id, 1, 0, [], [
            'dom_cadeau'      => true,
            'dom_cadeau_maat' => '',
        ]);
    } elseif (!$verdiend && $in_wagen) {
        WC()->cart->remove_cart_item($in_wagen);
    }

    $bezig = false;
}

add_action('woocommerce_cart_loaded_from_session', 'dom_cadeau_bijwerken', 20);
add_action('woocommerce_add_to_cart', 'dom_cadeau_bijwerken', 20);
add_action('woocommerce_after_cart_item_quantity_update', 'dom_cadeau_bijwerken', 20);
add_action('woocommerce_cart_item_removed', 'dom_cadeau_bijwerken', 20);
add_action('woocommerce_applied_coupon', 'dom_cadeau_bijwerken', 20);
add_action('woocommerce_removed_coupon', 'dom_cadeau_bijwerken', 20);

/** Het cadeau kost niets, wat er ook als prijs op het product staat. */
add_action('woocommerce_before_calculate_totals', function ($cart) {
    if (is_admin() && !defined('DOING_AJAX')) {
        return;
    }
    $cadeau_id = dom_cadeau_product_id();
    foreach ($cart->get_cart() as $item) {
        if (!empty($item['dom_cadeau']) || (int) ($item['product_id'] ?? 0) === $cadeau_id) {
            $item['data']->set_price(0);
        }
    }
}, 20);

/** Is dit regeltje in de wagen het cadeau? */
function dom_is_cadeau_regel($item) {
    return !empty($item['dom_cadeau'])
        || (int) ($item['product_id'] ?? 0) === dom_cadeau_product_id();
}

/** Geen kruisje en geen aantalkiezer bij het cadeau. */
add_filter('woocommerce_cart_item_remove_link', function ($link, $sleutel) {
    $item = WC()->cart->get_cart_item($sleutel);
    return $item && dom_is_cadeau_regel($item) ? '' : $link;
}, 10, 2);

add_filter('woocommerce_cart_item_quantity', function ($html, $sleutel) {
    $item = WC()->cart->get_cart_item($sleutel);
    return $item && dom_is_cadeau_regel($item) ? '1' : $html;
}, 10, 2);

/** In de wagen en op de bon staat erbij dat het gratis is. */
add_filter('woocommerce_cart_item_name', function ($naam, $item) {
    if (!dom_is_cadeau_regel($item)) {
        return $naam;
    }
    return $naam . ' <span class="dom-gratis">' . esc_html__('Cadeau', 'dom-theme') . '</span>';
}, 10, 2);

/* ===================================================================
 *  De maat van het cadeau
 * =================================================================== */

/**
 * Uit welke maten de klant mag kiezen.
 *
 * De lijst staat op het cadeauproduct zelf, als gewoon productkenmerk
 * Kledingmaat. Zo is hij in de beheeromgeving aan te passen zonder dat hier
 * iets hoeft te veranderen. Staat er niets, dan vallen we terug op S t/m XXL.
 */
function dom_cadeau_maten() {
    static $maten = null;
    if ($maten !== null) {
        return $maten;
    }

    $maten   = [];
    $id      = dom_cadeau_product_id();
    $product = $id && function_exists('wc_get_product') ? wc_get_product($id) : null;

    if ($product) {
        $ruw = $product->get_attribute('pa_kledingmaat');
        if (!$ruw) {
            $ruw = $product->get_attribute('Kledingmaat');
        }
        foreach (explode(',', (string) $ruw) as $maat) {
            $maat = trim($maat);
            if ($maat !== '') {
                $maten[] = $maat;
            }
        }
    }

    if (!$maten) {
        $maten = ['S', 'M', 'L', 'XL', 'XXL'];
    }

    // WooCommerce geeft de termen alfabetisch terug (L, M, S, XL), wat op een
    // maatbalk raar leest. Daarom zelf op maat sorteren.
    $volgorde = ['XS' => 1, 'S' => 2, 'M' => 3, 'L' => 4, 'XL' => 5, 'XXL' => 6, '3XL' => 7, 'XXXL' => 7];
    usort($maten, function ($a, $b) use ($volgorde) {
        $pa = $volgorde[strtoupper($a)] ?? 99;
        $pb = $volgorde[strtoupper($b)] ?? 99;
        return $pa === $pb ? strcmp($a, $b) : $pa - $pb;
    });

    return $maten;
}

/** De maat die nu bij het cadeau in de wagen staat, of '' als er geen is. */
function dom_cadeau_gekozen_maat() {
    if (!function_exists('WC') || !WC()->cart) {
        return '';
    }
    foreach (WC()->cart->get_cart() as $item) {
        if (dom_is_cadeau_regel($item)) {
            return trim((string) ($item['dom_cadeau_maat'] ?? ''));
        }
    }
    return '';
}

/**
 * De gekozen maat op de cadeauregel zetten.
 *
 * Alleen een maat uit de lijst wordt geaccepteerd; wat de bezoeker verder
 * meestuurt, belandt niet in de order.
 */
function dom_cadeau_maat_zetten($maat) {
    if (!function_exists('WC') || !WC()->cart) {
        return false;
    }

    $gekozen = '';
    foreach (dom_cadeau_maten() as $toegestaan) {
        if (strcasecmp($toegestaan, (string) $maat) === 0) {
            $gekozen = $toegestaan;
            break;
        }
    }
    if ($gekozen === '') {
        return false;
    }

    $geraakt = false;
    foreach (WC()->cart->get_cart() as $sleutel => $item) {
        if (dom_is_cadeau_regel($item)) {
            WC()->cart->cart_contents[$sleutel]['dom_cadeau_maat'] = $gekozen;
            $geraakt = true;
        }
    }

    if ($geraakt) {
        WC()->cart->set_session();
    }

    return $geraakt;
}

/** De maatkiezer, gebruikt in de lade en in de ordersamenvatting. */
function dom_cadeau_maatkiezer_html($extra_klasse = '') {
    $maten = dom_cadeau_maten();
    if (!$maten) {
        return '';
    }

    $gekozen = dom_cadeau_gekozen_maat();
    $klassen = 'cdmaat' . ($extra_klasse ? ' ' . $extra_klasse : '') . ($gekozen === '' ? ' leeg' : '');

    $uit  = '<div class="' . esc_attr($klassen) . '">';
    $uit .= '<span class="cdmaat-k">'
         . ($gekozen === ''
             ? esc_html__('Kies maat', 'dom-theme')
             : esc_html__('Maat', 'dom-theme'))
         . '</span>';
    $uit .= '<span class="cdmaat-r">';
    foreach ($maten as $maat) {
        $aan  = strcasecmp($maat, $gekozen) === 0;
        $uit .= '<button type="button" class="cdmaat-b' . ($aan ? ' on' : '') . '"'
             . ' data-cadeau-maat="' . esc_attr($maat) . '"'
             . ' aria-pressed="' . ($aan ? 'true' : 'false') . '"'
             . ' aria-label="' . esc_attr(sprintf(__('Maat %s kiezen', 'dom-theme'), $maat)) . '">'
             . esc_html($maat) . '</button>';
    }
    $uit .= '</span></div>';

    return $uit;
}

/** De maat kiezen vanuit de lade of de checkout. */
function dom_ajax_cadeau_maat() {
    check_ajax_referer('dom-cart-nonce', 'nonce');

    $maat = isset($_POST['maat']) ? sanitize_text_field(wp_unslash($_POST['maat'])) : '';
    if (!dom_cadeau_maat_zetten($maat)) {
        wp_send_json_error(['message' => __('Die maat kan niet gekozen worden.', 'dom-theme')]);
    }

    WC()->cart->calculate_totals();

    wp_send_json_success([
        'maat'      => dom_cadeau_gekozen_maat(),
        'fragments' => apply_filters('woocommerce_add_to_cart_fragments', []),
    ]);
}
add_action('wp_ajax_dom_cadeau_maat', 'dom_ajax_cadeau_maat');
add_action('wp_ajax_nopriv_dom_cadeau_maat', 'dom_ajax_cadeau_maat');

/**
 * De maat meenemen in de wagen, op de checkout en in de order.
 *
 * woocommerce_get_item_data zet de regel onder de productnaam in de
 * ordersamenvatting; de tweede haak schrijft hem als orderregel weg, zodat de
 * maat ook in de bestelmail en in de beheeromgeving staat.
 */
add_filter('woocommerce_get_item_data', function ($data, $item) {
    if (!dom_is_cadeau_regel($item)) {
        return $data;
    }
    // Op de checkout staat de kiezer er al onder; dan is deze regel dubbelop.
    if (function_exists('is_checkout') && is_checkout()) {
        return $data;
    }
    $maat = trim((string) ($item['dom_cadeau_maat'] ?? ''));
    $data[] = [
        'key'   => __('Maat', 'dom-theme'),
        'value' => $maat !== '' ? $maat : __('nog te kiezen', 'dom-theme'),
    ];
    return $data;
}, 10, 2);

add_action('woocommerce_checkout_create_order_line_item', function ($regel, $sleutel, $waarden) {
    if (!dom_is_cadeau_regel($waarden)) {
        return;
    }
    $maat = trim((string) ($waarden['dom_cadeau_maat'] ?? ''));
    $regel->add_meta_data(
        __('Maat', 'dom-theme'),
        $maat !== '' ? $maat : __('nog te kiezen', 'dom-theme'),
        true
    );
}, 10, 3);

/** Op de checkout staat de kiezer onder de naam van het cadeau. */
add_filter('woocommerce_cart_item_name', function ($naam, $item) {
    if (!dom_is_cadeau_regel($item)) {
        return $naam;
    }
    if (!function_exists('is_checkout') || !is_checkout()) {
        return $naam;
    }
    return $naam . dom_cadeau_maatkiezer_html('cdmaat-co');
}, 20, 2);

/* ===================================================================
 *  Verzending
 * =================================================================== */

/**
 * Is gratis verzending beschikbaar, dan verdwijnen de betaalde methodes.
 *
 * Zonder dit staan "Gratis verzending" en "PostNL €9,99" naast elkaar en kan
 * iemand per ongeluk de betaalde kiezen, terwijl de site gratis verzending
 * belooft.
 */
add_filter('woocommerce_package_rates', function ($rates) {
    $gratis = [];
    foreach ($rates as $id => $rate) {
        if ('free_shipping' === $rate->get_method_id()) {
            $gratis[$id] = $rate;
        }
    }
    return $gratis ?: $rates;
}, 100);

/* ===================================================================
 *  Checkout
 * =================================================================== */

/**
 * "Verzenden naar een ander adres" weg. Het vinkje blijft in het formulier
 * staan — WooCommerce heeft geen nette haak om het te verwijderen — maar het
 * staat nooit aan en is niet zichtbaar, dus het factuuradres wordt gebruikt.
 */
add_filter('woocommerce_ship_to_different_address_checked', '__return_false');

add_action('wp_head', function () {
    if (function_exists('is_checkout') && is_checkout()) {
        echo '<style>#ship-to-different-address{display:none!important}</style>';
    }
});
