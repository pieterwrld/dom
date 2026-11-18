<?php
/**
 * Wat het thema aanroept.
 *
 * De sjablonen roepen dom_c(), dom_c_img() en dom_c_rows() aan. Die functies
 * bestaan ook in het thema zelf, maar dan als lege terugval: staat deze plugin
 * uit, dan gebruikt het thema de tekst die het zelf meegeeft en blijft de
 * winkel er hetzelfde uitzien.
 *
 * Het thema definieert zijn eigen versies pas op 'after_setup_theme' als deze
 * er niet zijn, dus de volgorde klopt altijd: een plugin laadt vóór een thema.
 */

defined('ABSPATH') || exit;

/**
 * Let op de volgorde.
 *
 * Bij een gewone paginaweergave laadt deze plugin vóór het thema, dus winnen
 * de functies hieronder. Maar op het moment dat iemand de plugin áánzet, is
 * het thema in diezelfde request al geladen en bestaan de terugvalfuncties uit
 * inc/content-api.php al. Zonder de function_exists-controles hieronder gaf dat
 * een fatale fout ("Cannot redeclare dom_c") en kon de plugin niet aan.
 *
 * Die ene request gebruikt dan nog de terugvalteksten van het thema; vanaf de
 * volgende request laadt de plugin weer eerst en klopt alles.
 */

/**
 * Tekst of getal.
 *
 * @param string $pad     "home.hero.cta"
 * @param mixed  $default Wat de sjabloon toont als er niets staat.
 */
if (!function_exists('dom_c')) :
function dom_c($pad, $default = '') {
    return dom_content($pad, $default);
}
endif;

/** Schakelaar. */
if (!function_exists('dom_c_bool')) :
function dom_c_bool($pad, $default = true) {
    return dom_content_bool($pad, $default);
}
endif;

/** Afbeelding of video als URL. */
if (!function_exists('dom_c_img')) :
function dom_c_img($pad, $size = 'full') {
    return dom_content_url($pad, $size);
}
endif;

/** Rijen van een herhaalveld. */
if (!function_exists('dom_c_rows')) :
function dom_c_rows($pad, array $default = []) {
    $rijen = dom_content_rows($pad);
    return $rijen ?: $default;
}
endif;

/** Categorieplaatje. */
if (!function_exists('dom_c_cat_img')) :
function dom_c_cat_img($term_id, $rol = 'raster', $size = 'medium') {
    return dom_content_cat_image($term_id, $rol, $size);
}
endif;

/**
 * De reviewscreenshots als lijst URL's.
 *
 * Staat er niets in het paneel, dan geeft deze functie een lege lijst terug en
 * valt het thema terug op de afbeeldingen die het zelf meelevert.
 *
 * @return array<int, array{url:string, naam:string}>
 */
if (!function_exists('dom_c_reviews')) :
function dom_c_reviews() {
    $uit = [];
    foreach (dom_content_rows('reviews.lijst.items') as $rij) {
        $id = (int) ($rij['afbeelding'] ?? 0);
        if (!$id) {
            continue;
        }
        $url = wp_get_attachment_image_url($id, 'large');
        if (!$url) {
            continue;
        }
        $uit[] = [
            'url'  => $url,
            'naam' => (string) ($rij['naam'] ?? ''),
        ];
    }
    return $uit;
}
endif;

/**
 * De video's van Gedragen door.
 *
 * @return array<int, array{src:string, poster:string, naam:string, sub:string, link:string}>
 */
if (!function_exists('dom_c_ugc')) :
function dom_c_ugc() {
    $uit = [];
    foreach (dom_content_rows('ugc.lijst.items') as $rij) {
        $video_id = (int) ($rij['video'] ?? 0);
        $src = $video_id ? wp_get_attachment_url($video_id) : '';
        if (!$src) {
            continue;
        }
        $poster_id = (int) ($rij['poster'] ?? 0);
        $uit[] = [
            'src'    => $src,
            'poster' => $poster_id ? (string) (wp_get_attachment_image_url($poster_id, 'large') ?: '') : '',
            'naam'   => (string) ($rij['naam'] ?? ''),
            'sub'    => (string) ($rij['sub'] ?? ''),
            'link'   => (string) ($rij['link'] ?? ''),
        ];
    }
    return $uit;
}
endif;

/* ===================================================================
 *  Snelkoppelingen in de adminbalk
 * =================================================================== */

/**
 * Een knop "Content bewerken" in de zwarte balk bovenaan de site, die naar het
 * juiste tabblad springt. Scheelt zoeken in het menu.
 */
add_action('admin_bar_menu', function (WP_Admin_Bar $bar) {
    if (!current_user_can('manage_options')) {
        return;
    }

    $bar->add_node([
        'id'    => 'dom-content',
        'title' => __('Content', 'dom-content'),
        'href'  => admin_url('admin.php?page=' . DOM_CONTENT_SLUG),
    ]);

    $snel = [
        'home'        => __('Homepage', 'dom-content'),
        'reviews'     => __('Reviews', 'dom-content'),
        'ugc'         => __('Gedragen door', 'dom-content'),
        'categorieen' => __('Categorieën', 'dom-content'),
        'product'     => __('Productpagina', 'dom-content'),
        'winkelwagen' => __('Winkelwagen', 'dom-content'),
        'footer'      => __('Footer', 'dom-content'),
    ];

    foreach ($snel as $tab => $titel) {
        $bar->add_node([
            'parent' => 'dom-content',
            'id'     => 'dom-content-' . $tab,
            'title'  => $titel,
            'href'   => admin_url('admin.php?page=' . DOM_CONTENT_SLUG . '&tab=' . $tab),
        ]);
    }
}, 80);
