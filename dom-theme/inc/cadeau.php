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
        WC()->cart->add_to_cart($cadeau_id, 1, 0, [], ['dom_cadeau' => true]);
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
