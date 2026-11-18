<?php
/**
 * Eerste keer inrichten.
 *
 * De reviewscreenshots zaten als bestanden in het thema. Die kun je niet in een
 * mediakiezer aanwijzen, dus bij het activeren zetten we ze één keer over naar
 * de mediabibliotheek en vullen we er de lijst mee. Daarna staan alle achttien
 * in het beheerscherm en kun je ze stuk voor stuk vervangen of weggooien.
 *
 * De import draait precies één keer; daarna onthoudt een optie dat het gedaan
 * is, ook als je de lijst later helemaal leeggooit.
 */

defined('ABSPATH') || exit;

define('DOM_CONTENT_IMPORT_FLAG', 'dom_content_reviews_geimporteerd');

register_activation_hook(DOM_CONTENT_DIR . 'dom-content.php', 'dom_content_activate');

function dom_content_activate() {
    // De import zelf gebeurt op de eerste adminpagina na activeren: bij het
    // activeren is het thema nog niet geladen, dus weten we de themamap niet
    // betrouwbaar en kan wp_handle_sideload nog niet.
    update_option(DOM_CONTENT_IMPORT_FLAG, 'wachtrij');
}

add_action('admin_init', function () {
    if (get_option(DOM_CONTENT_IMPORT_FLAG) !== 'wachtrij') {
        return;
    }
    if (!current_user_can('upload_files')) {
        return;
    }
    dom_content_import_reviews();
});

/**
 * Dezelfde import als route, zodat hij ook zonder wp-admin te openen gedraaid
 * kan worden en later nog eens overgedaan kan worden.
 */
add_action('rest_api_init', function () {
    register_rest_route('dom/v1', '/reviews-importeren', [
        'methods'             => 'POST',
        'callback'            => function () {
            $aantal = dom_content_import_reviews();
            return new WP_REST_Response([
                'ok'           => true,
                'geimporteerd' => $aantal,
                'in_lijst'     => count(dom_content_rows('reviews.lijst.items')),
            ], 200);
        },
        'permission_callback' => function () {
            return current_user_can('manage_options') && current_user_can('upload_files');
        },
    ]);
});

/**
 * De meegeleverde screenshots naar de mediabibliotheek halen.
 *
 * @return int Aantal geïmporteerde afbeeldingen.
 */
function dom_content_import_reviews() {
    update_option(DOM_CONTENT_IMPORT_FLAG, 'bezig');

    $map = get_stylesheet_directory() . '/assets/img/reviews';
    $bestanden = glob($map . '/review-*.webp') ?: [];
    sort($bestanden);

    if (!$bestanden) {
        update_option(DOM_CONTENT_IMPORT_FLAG, 'niets-gevonden');
        return 0;
    }

    require_once ABSPATH . 'wp-admin/includes/file.php';
    require_once ABSPATH . 'wp-admin/includes/media.php';
    require_once ABSPATH . 'wp-admin/includes/image.php';

    $rijen = [];
    foreach ($bestanden as $pad) {
        $naam = basename($pad);

        // Al eerder geïmporteerd? Dan die gebruiken in plaats van een tweede kopie.
        $bestaand = get_posts([
            'post_type'      => 'attachment',
            'post_status'    => 'inherit',
            'posts_per_page' => 1,
            'fields'         => 'ids',
            'meta_query'     => [[
                'key'     => '_dom_review_bron',
                'value'   => $naam,
            ]],
        ]);

        if ($bestaand) {
            $rijen[] = ['afbeelding' => (int) $bestaand[0], 'naam' => ''];
            continue;
        }

        $tijdelijk = wp_tempnam($naam);
        if (!$tijdelijk || !copy($pad, $tijdelijk)) {
            continue;
        }

        $id = media_handle_sideload(
            ['name' => $naam, 'tmp_name' => $tijdelijk],
            0,
            __('WhatsApp review', 'dom-content')
        );

        if (is_wp_error($id)) {
            @unlink($tijdelijk);
            continue;
        }

        update_post_meta($id, '_dom_review_bron', $naam);
        $rijen[] = ['afbeelding' => (int) $id, 'naam' => ''];
    }

    if ($rijen) {
        $data = dom_content_all();
        // Niets overschrijven als er intussen al iets is ingevuld.
        if (empty($data['reviews']['lijst']['items'])) {
            $data['reviews']['lijst']['items'] = $rijen;
            update_option(DOM_CONTENT_OPTION, $data);
        }
    }

    update_option(DOM_CONTENT_IMPORT_FLAG, 'klaar');

    return count($rijen);
}
