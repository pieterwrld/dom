<?php
/**
 * De Outfitman — Theme Updater
 *
 * Haalt de laatste versie van GitHub en zet die over het actieve thema heen.
 * Aanroepen met POST /wp-json/dom/v1/update door een ingelogde beheerder.
 *
 * De vorige versie blijft als back-up staan en wordt teruggezet zodra er
 * halverwege iets misgaat, zodat een mislukte deploy de winkel niet op een
 * wit scherm achterlaat.
 */
defined('ABSPATH') || exit;

add_action('rest_api_init', function () {
    register_rest_route('dom/v1', '/update', [
        'methods'             => 'POST',
        'callback'            => 'dom_theme_update_from_github',
        'permission_callback' => function () {
            return current_user_can('update_themes');
        },
    ]);
});

/**
 * Een uitgepakte themamap moet er als een thema uitzien voordat we hem
 * vertrouwen. Een halve download herkennen we hieraan.
 */
function dom_theme_update_looks_valid($dir) {
    return is_dir($dir)
        && file_exists($dir . '/style.css')
        && file_exists($dir . '/functions.php')
        && file_exists($dir . '/index.php');
}

function dom_theme_update_from_github(WP_REST_Request $request) {
    require_once ABSPATH . 'wp-admin/includes/file.php';
    WP_Filesystem();
    global $wp_filesystem;

    $zip_url = 'https://github.com/pieterwrld/dom/archive/refs/heads/main.zip';
    $tmp_zip = download_url($zip_url, 120);

    if (is_wp_error($tmp_zip)) {
        return new WP_REST_Response(['ok' => false, 'error' => $tmp_zip->get_error_message()], 500);
    }

    $tmp_dir = wp_tempnam('dom-update');
    @unlink($tmp_dir);
    @mkdir($tmp_dir, 0755, true);

    $unzip = unzip_file($tmp_zip, $tmp_dir);
    @unlink($tmp_zip);

    if (is_wp_error($unzip)) {
        $wp_filesystem->delete($tmp_dir, true);
        return new WP_REST_Response(['ok' => false, 'error' => $unzip->get_error_message()], 500);
    }

    $extracted = glob($tmp_dir . '/dom-*');
    if (empty($extracted)) {
        $wp_filesystem->delete($tmp_dir, true);
        return new WP_REST_Response(['ok' => false, 'error' => 'Geen uitgepakte map gevonden'], 500);
    }
    $repo_dir = $extracted[0];

    $results = ['theme' => false, 'plugins' => []];

    // ---------- Thema ----------
    $theme_src  = $repo_dir . '/dom-theme';
    $stylesheet = get_stylesheet();
    $theme_dst  = get_theme_root() . '/' . $stylesheet;

    if (!dom_theme_update_looks_valid($theme_src)) {
        $wp_filesystem->delete($tmp_dir, true);
        return new WP_REST_Response(
            ['ok' => false, 'error' => 'De gedownloade versie is onvolledig, er is niets gewijzigd'],
            500
        );
    }

    if (is_dir($theme_dst)) {
        $staging = $theme_dst . '-incoming';
        $backup  = $theme_dst . '-backup-' . gmdate('Ymd-His');

        // Eerst volledig klaarzetten naast het actieve thema.
        $wp_filesystem->delete($staging, true);
        $staged = copy_dir($theme_src, $staging);

        if (is_wp_error($staged) || !dom_theme_update_looks_valid($staging)) {
            $wp_filesystem->delete($staging, true);
            $wp_filesystem->delete($tmp_dir, true);
            $message = is_wp_error($staged) ? $staged->get_error_message() : 'Klaarzetten mislukt';
            return new WP_REST_Response(
                ['ok' => false, 'error' => $message . ' — er is niets gewijzigd'],
                500
            );
        }

        // Pas nu omwisselen. De oude versie blijft als back-up bestaan.
        if (!$wp_filesystem->move($theme_dst, $backup)) {
            $wp_filesystem->delete($staging, true);
            $wp_filesystem->delete($tmp_dir, true);
            return new WP_REST_Response(
                ['ok' => false, 'error' => 'Kon de huidige versie niet opzijzetten — er is niets gewijzigd'],
                500
            );
        }

        if (!$wp_filesystem->move($staging, $theme_dst)) {
            // Terugdraaien: de winkel mag niet zonder thema komen te staan.
            $wp_filesystem->move($backup, $theme_dst);
            $wp_filesystem->delete($staging, true);
            $wp_filesystem->delete($tmp_dir, true);
            return new WP_REST_Response(
                ['ok' => false, 'error' => 'Omwisselen mislukt, de vorige versie is teruggezet'],
                500
            );
        }

        $results['theme']      = true;
        $results['theme_dir']  = $stylesheet;
        $results['backup_dir'] = basename($backup);
    }

    // ---------- Plugins ----------
    $plugins_src = $repo_dir . '/dom-plugins';
    if (is_dir($plugins_src)) {
        foreach (glob($plugins_src . '/dom-*') as $psrc) {
            $slug = basename($psrc);
            $pdst = WP_PLUGIN_DIR . '/' . $slug;
            $pstg = $pdst . '-incoming';

            $wp_filesystem->delete($pstg, true);
            $copied = copy_dir($psrc, $pstg);

            if (is_wp_error($copied)) {
                $wp_filesystem->delete($pstg, true);
                $results['plugins'][$slug] = false;
                continue;
            }

            $wp_filesystem->delete($pdst, true);
            $results['plugins'][$slug] = $wp_filesystem->move($pstg, $pdst);
        }
    }

    $wp_filesystem->delete($tmp_dir, true);

    wp_cache_flush();

    if (has_action('litespeed_purge_all')) {
        do_action('litespeed_purge_all');
        $results['litespeed'] = 'purged';
    }

    return new WP_REST_Response(['ok' => true, 'results' => $results], 200);
}

/**
 * Oude back-upkopieën van het thema opruimen.
 *
 * Elke deploy zet de vorige versie als dom-theme-backup-<datum> opzij. Dat is
 * prettig om op terug te vallen, maar na tientallen deploys staan er evenzoveel
 * volledige kopieën van het thema (inclusief foto's en video's) op de schijf.
 * Deze route gooit ze weg en houdt alleen de nieuwste paar.
 */
add_action('rest_api_init', function () {
    register_rest_route('dom/v1', '/opruimen', [
        'methods'             => 'POST',
        'callback'            => 'dom_theme_prune_backups',
        'permission_callback' => function () {
            return current_user_can('switch_themes') && current_user_can('update_themes');
        },
        'args'                => [
            'bewaar' => ['type' => 'integer', 'default' => 2],
        ],
    ]);
});

function dom_theme_prune_backups(WP_REST_Request $request) {
    require_once ABSPATH . 'wp-admin/includes/file.php';
    WP_Filesystem();
    global $wp_filesystem;

    $keep = max(0, (int) $request->get_param('bewaar'));
    $root = get_theme_root();
    $live = get_stylesheet();

    $backups = [];
    foreach ((array) glob($root . '/dom-theme-backup-*', GLOB_ONLYDIR) as $dir) {
        $slug = basename($dir);
        // Nooit de map weggooien waar de site op dit moment op draait.
        if ($slug === $live || $slug === get_template()) {
            continue;
        }
        $backups[] = $dir;
    }

    // Namen eindigen op jjjjmmdd-uummss, dus alfabetisch sorteren is op datum.
    sort($backups);
    $verwijderen = $keep > 0 ? array_slice($backups, 0, max(0, count($backups) - $keep)) : $backups;

    $weg = [];
    $mislukt = [];
    foreach ($verwijderen as $dir) {
        if ($wp_filesystem->delete($dir, true)) {
            $weg[] = basename($dir);
        } else {
            $mislukt[] = basename($dir);
        }
    }

    return new WP_REST_Response([
        'ok'         => empty($mislukt),
        'verwijderd' => $weg,
        'bewaard'    => array_map('basename', $keep > 0 ? array_slice($backups, -$keep) : []),
        'mislukt'    => $mislukt,
    ], 200);
}
