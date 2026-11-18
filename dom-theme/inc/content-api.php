<?php
/**
 * Terugval voor de contentplugin.
 *
 * De sjablonen vragen hun teksten en afbeeldingen op via dom_c() en vrienden.
 * Staat de plugin De Outfitman Content aan, dan heeft die deze functies al
 * gedefinieerd (plugins laden vóór het thema) en komt de content uit het
 * beheerscherm. Staat de plugin uit, dan vangen de functies hieronder het op
 * en toont elke sjabloon de tekst die hij zelf meegeeft.
 *
 * Zo kan het thema nooit stuk door een uitgeschakelde plugin.
 */

defined('ABSPATH') || exit;

if (!function_exists('dom_c')) {
    /**
     * @param string $pad     Bijvoorbeeld "home.hero.cta".
     * @param mixed  $default Wat de sjabloon toont zonder plugin.
     * @return mixed
     */
    function dom_c($pad, $default = '') {
        return $default;
    }
}

if (!function_exists('dom_c_bool')) {
    function dom_c_bool($pad, $default = true) {
        return $default;
    }
}

if (!function_exists('dom_c_img')) {
    function dom_c_img($pad, $size = 'full') {
        return '';
    }
}

if (!function_exists('dom_c_rows')) {
    function dom_c_rows($pad, array $default = []) {
        return $default;
    }
}

if (!function_exists('dom_c_reviews')) {
    function dom_c_reviews() {
        return [];
    }
}

if (!function_exists('dom_c_ugc')) {
    function dom_c_ugc() {
        return [];
    }
}

if (!function_exists('dom_c_cat_img')) {
    /**
     * Zonder plugin: de gewone WooCommerce-categorieafbeelding, en anders de
     * foto van het eerste product in die categorie.
     */
    function dom_c_cat_img($term_id, $rol = 'raster', $size = 'medium') {
        $term_id = (int) $term_id;
        if (!$term_id) {
            return '';
        }

        $id = (int) get_term_meta($term_id, 'thumbnail_id', true);
        if ($id) {
            $url = wp_get_attachment_image_url($id, $size);
            if ($url) {
                return $url;
            }
        }

        if ($rol === 'banner') {
            $url = get_term_meta($term_id, 'category_banner', true);
            return is_string($url) ? $url : '';
        }

        $producten = get_posts([
            'post_type'      => 'product',
            'posts_per_page' => 1,
            'post_status'    => 'publish',
            'fields'         => 'ids',
            'no_found_rows'  => true,
            'tax_query'      => [[
                'taxonomy' => 'product_cat',
                'field'    => 'term_id',
                'terms'    => $term_id,
            ]],
        ]);

        return $producten ? (string) (get_the_post_thumbnail_url($producten[0], $size) ?: '') : '';
    }
}

/**
 * Een van de iconen uit het beheerscherm tekenen.
 *
 * De keuzelijst in het paneel geeft een sleutel terug; hier staat het bijbehorende
 * lijntekeningetje. Een onbekende sleutel levert niets op in plaats van een fout.
 */
function dom_icon($sleutel, $class = '') {
    $paden = [
        'truck'    => '<path d="M3 7h11v9H3z"/><path d="M14 10h4l3 3v3h-7z"/><circle cx="7" cy="18" r="1.6"/><circle cx="17" cy="18" r="1.6"/>',
        'shield'   => '<path d="M12 3l7 3v6c0 4-3 7-7 9-4-2-7-5-7-9V6z"/><path d="M9 12l2 2 4-4"/>',
        'check'    => '<path d="M20 6L9 17l-5-5"/>',
        'whatsapp' => '<path d="M12 2a10 10 0 0 0-8.6 15.1L2 22l5-1.3A10 10 0 1 0 12 2z"/>',
        'card'     => '<rect x="2" y="5" width="20" height="14" rx="2"/><path d="M2 10h20"/>',
        'return'   => '<rect x="1" y="3" width="22" height="5"/><path d="M21 8v13H3V8"/><path d="M10 12h4"/>',
        'star'     => '<path d="M12 2l2.9 6.2 6.6.9-4.8 4.6 1.2 6.6L12 17.2 6.1 20.3l1.2-6.6L2.5 9.1l6.6-.9z"/>',
        'clock'    => '<circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/>',
    ];

    if (!isset($paden[$sleutel])) {
        return '';
    }

    return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"'
        . ($class ? ' class="' . esc_attr($class) . '"' : '')
        . ' aria-hidden="true">' . $paden[$sleutel] . '</svg>';
}
