<?php
/**
 * Plugin Name: De Outfitman Launcher
 * Plugin URI:  https://github.com/pieterwrld/dom
 * Description: Zet het nieuwe thema klaar, laat het alleen aan jou zien, en ruimt een paar dingen op die de winkel in de weg zitten. Alles vanuit WP Admin, geen SSH nodig.
 * Version:     1.0.0
 * Author:      De Outfitman
 * Requires PHP: 8.0
 */

defined('ABSPATH') || exit;

define('DOM_LAUNCHER_THEME', 'dom-theme');
define('DOM_LAUNCHER_ZIP', 'https://github.com/pieterwrld/dom/archive/refs/heads/main.zip');
define('DOM_LAUNCHER_COOKIE', 'dom_preview_theme');

/* =====================================================================
 * 1. Beheerpagina
 * ===================================================================== */

add_action('admin_menu', function () {
    add_menu_page(
        'De Outfitman',
        'De Outfitman',
        'update_themes',
        'dom-launcher',
        'dom_launcher_page',
        'dashicons-store',
        3
    );
});

function dom_launcher_page() {
    if (!current_user_can('update_themes')) {
        wp_die(esc_html__('Je hebt hier geen toegang toe.', 'dom-launcher'));
    }

    $notice = '';

    if (isset($_POST['dom_launcher_action']) && check_admin_referer('dom_launcher')) {
        if ($_POST['dom_launcher_action'] === 'deploy') {
            $result = dom_launcher_deploy();
            $notice = is_wp_error($result)
                ? '<div class="notice notice-error"><p><strong>Mislukt.</strong> ' . esc_html($result->get_error_message()) . '</p></div>'
                : '<div class="notice notice-success"><p><strong>Klaar.</strong> ' . esc_html($result) . '</p></div>';
        }
    }

    $theme     = wp_get_theme(DOM_LAUNCHER_THEME);
    $installed = $theme->exists();
    $active    = get_stylesheet() === DOM_LAUNCHER_THEME;
    $preview   = add_query_arg('dom_preview', DOM_LAUNCHER_THEME, home_url('/'));
    ?>
    <div class="wrap">
        <h1>De Outfitman</h1>
        <?php echo $notice; // phpcs:ignore WordPress.Security.EscapeOutput ?>

        <h2>Stap 1 — Haal de nieuwste versie binnen</h2>
        <p>Dit downloadt het thema van GitHub en zet het klaar. De winkel verandert hier niet van:
           zolang je het thema niet activeert, zien klanten precies wat ze nu zien.</p>
        <p>
            Status:
            <?php if ($installed) : ?>
                <strong>geïnstalleerd</strong>, versie <?php echo esc_html($theme->get('Version')); ?>
                <?php echo $active ? ' — <strong>actief</strong>' : ' — nog niet actief'; ?>
            <?php else : ?>
                <strong>nog niet geïnstalleerd</strong>
            <?php endif; ?>
        </p>
        <form method="post">
            <?php wp_nonce_field('dom_launcher'); ?>
            <input type="hidden" name="dom_launcher_action" value="deploy">
            <?php submit_button('Nieuwste versie ophalen', 'primary', 'submit', false); ?>
        </form>

        <hr>

        <h2>Stap 2 — Bekijk het eerst</h2>
        <?php if ($installed) : ?>
            <p>Deze link laat het nieuwe thema alleen aan jou zien. Klanten houden de huidige site.
               Onderin beeld staat een rode balk zodat je nooit denkt dat het al live is.</p>
            <p><a class="button button-secondary" href="<?php echo esc_url($preview); ?>" target="_blank" rel="noopener">Preview openen</a></p>
        <?php else : ?>
            <p><em>Doe eerst stap 1.</em></p>
        <?php endif; ?>

        <hr>

        <h2>Stap 3 — Live zetten</h2>
        <p>Pas als de preview goed is: ga naar <a href="<?php echo esc_url(admin_url('themes.php')); ?>">Weergave &rarr; Thema's</a>
           en activeer <strong>De Outfitman</strong>. Let op dat je niet <em>De Outfitman v2</em> kiest; dat is een oude kopie.</p>
        <p>Valt het tegen, activeer dan gewoon Ecomus Child weer. Je raakt niets kwijt.</p>
    </div>
    <?php
}

/* =====================================================================
 * 2. Thema ophalen van GitHub
 * ===================================================================== */

function dom_launcher_looks_like_theme($dir) {
    return is_dir($dir)
        && file_exists($dir . '/style.css')
        && file_exists($dir . '/functions.php')
        && file_exists($dir . '/index.php');
}

function dom_launcher_deploy() {
    require_once ABSPATH . 'wp-admin/includes/file.php';
    WP_Filesystem();
    global $wp_filesystem;

    $zip = download_url(DOM_LAUNCHER_ZIP, 180);
    if (is_wp_error($zip)) {
        return new WP_Error('download', 'Downloaden van GitHub lukte niet: ' . $zip->get_error_message());
    }

    $tmp = wp_tempnam('dom-launcher');
    @unlink($tmp);
    @mkdir($tmp, 0755, true);

    $unzip = unzip_file($zip, $tmp);
    @unlink($zip);

    if (is_wp_error($unzip)) {
        $wp_filesystem->delete($tmp, true);
        return new WP_Error('unzip', 'Uitpakken lukte niet: ' . $unzip->get_error_message());
    }

    $extracted = glob($tmp . '/dom-*');
    if (empty($extracted)) {
        $wp_filesystem->delete($tmp, true);
        return new WP_Error('empty', 'Het gedownloade pakket zag er leeg uit.');
    }

    $src = $extracted[0] . '/dom-theme';
    if (!dom_launcher_looks_like_theme($src)) {
        $wp_filesystem->delete($tmp, true);
        return new WP_Error('invalid', 'Het gedownloade thema is onvolledig. Er is niets gewijzigd.');
    }

    $dst     = get_theme_root() . '/' . DOM_LAUNCHER_THEME;
    $staging = $dst . '-incoming';

    // Eerst volledig ernaast klaarzetten, zodat een halve kopie nooit live staat.
    $wp_filesystem->delete($staging, true);
    $copied = copy_dir($src, $staging);

    if (is_wp_error($copied) || !dom_launcher_looks_like_theme($staging)) {
        $wp_filesystem->delete($staging, true);
        $wp_filesystem->delete($tmp, true);
        $msg = is_wp_error($copied) ? $copied->get_error_message() : 'klaarzetten mislukt';
        return new WP_Error('stage', 'Kon de nieuwe versie niet klaarzetten (' . $msg . '). Er is niets gewijzigd.');
    }

    $message = 'Thema klaargezet.';

    if (is_dir($dst)) {
        $backup = $dst . '-backup-' . gmdate('Ymd-His');
        if (!$wp_filesystem->move($dst, $backup)) {
            $wp_filesystem->delete($staging, true);
            $wp_filesystem->delete($tmp, true);
            return new WP_Error('swap', 'Kon de huidige versie niet opzijzetten. Er is niets gewijzigd.');
        }
        if (!$wp_filesystem->move($staging, $dst)) {
            $wp_filesystem->move($backup, $dst);
            $wp_filesystem->delete($staging, true);
            $wp_filesystem->delete($tmp, true);
            return new WP_Error('swap', 'Omwisselen mislukt. De vorige versie is teruggezet.');
        }
        $message = 'Thema bijgewerkt. De vorige versie staat als back-up in ' . basename($backup) . '.';
    } else {
        if (!$wp_filesystem->move($staging, $dst)) {
            $wp_filesystem->delete($staging, true);
            $wp_filesystem->delete($tmp, true);
            return new WP_Error('install', 'Kon het thema niet op zijn plek zetten.');
        }
        $message = 'Thema geïnstalleerd.';
    }

    $wp_filesystem->delete($tmp, true);
    wp_cache_flush();

    if (has_action('litespeed_purge_all')) {
        do_action('litespeed_purge_all');
        $message .= ' Cache geleegd.';
    }

    return $message;
}

/* =====================================================================
 * 3. Preview: alleen voor beheerders
 * ===================================================================== */

function dom_launcher_may_preview() {
    return is_user_logged_in() && current_user_can('update_themes');
}

function dom_launcher_preview_theme() {
    static $theme = null;
    if (null !== $theme) {
        return $theme;
    }

    $theme = '';

    if (!dom_launcher_may_preview()) {
        return $theme;
    }

    if (isset($_GET['dom_preview'])) {
        $requested = sanitize_key(wp_unslash($_GET['dom_preview']));
        if ($requested !== '' && $requested !== 'off' && wp_get_theme($requested)->exists()) {
            $theme = $requested;
        }
    } elseif (isset($_COOKIE[DOM_LAUNCHER_COOKIE])) {
        $stored = sanitize_key(wp_unslash($_COOKIE[DOM_LAUNCHER_COOKIE]));
        if ($stored !== '' && wp_get_theme($stored)->exists()) {
            $theme = $stored;
        }
    }

    return $theme;
}

add_action('init', function () {
    if (!isset($_GET['dom_preview']) || !dom_launcher_may_preview() || headers_sent()) {
        return;
    }
    $theme = dom_launcher_preview_theme();
    setcookie(DOM_LAUNCHER_COOKIE, $theme, [
        'expires'  => $theme ? time() + DAY_IN_SECONDS : time() - 3600,
        'path'     => COOKIEPATH ? COOKIEPATH : '/',
        'domain'   => COOKIE_DOMAIN,
        'secure'   => is_ssl(),
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
});

foreach (['template', 'stylesheet'] as $dom_launcher_filter) {
    add_filter($dom_launcher_filter, function ($value) {
        if (is_admin() || wp_doing_ajax()) {
            return $value;
        }
        $theme = dom_launcher_preview_theme();
        if (!$theme) {
            return $value;
        }
        $preview = wp_get_theme($theme);
        return current_filter() === 'template'
            ? $preview->get_template()
            : $preview->get_stylesheet();
    }, 99);
}

// Een preview mag nooit in de cache belanden en zo bij een klant terechtkomen.
add_action('template_redirect', function () {
    if (!dom_launcher_preview_theme()) {
        return;
    }
    if (!defined('DONOTCACHEPAGE')) {
        define('DONOTCACHEPAGE', true);
    }
    do_action('litespeed_control_set_nocache', 'DOM preview');
    nocache_headers();
}, 0);

add_action('wp_footer', function () {
    $theme = dom_launcher_preview_theme();
    if (!$theme) {
        return;
    }
    printf(
        '<div style="position:fixed;left:0;right:0;bottom:0;z-index:2147483647;background:#D0142C;color:#fff;'
        . 'font:700 12px/1.4 system-ui,sans-serif;letter-spacing:.06em;text-transform:uppercase;'
        . 'padding:8px 14px;display:flex;gap:14px;align-items:center;justify-content:center;flex-wrap:wrap">'
        . '<span>Preview: %s &mdash; alleen jij ziet dit</span>'
        . '<a href="%s" style="color:#fff;text-decoration:underline">Terug naar de live site</a>'
        . '</div>',
        esc_html($theme),
        esc_url(add_query_arg('dom_preview', 'off', home_url('/')))
    );
}, 999);

/* =====================================================================
 * 4. Opruimen: WooLentor-assets buiten de shoppagina's
 * ===================================================================== */

add_action('wp_enqueue_scripts', function () {
    if (!function_exists('is_shop')) {
        return;
    }
    if (is_shop() || is_product_taxonomy() || is_post_type_archive('product')) {
        return;
    }

    $styles = [
        'woolentor-product-grid-modern',
        'woolentor-product-grid-luxury',
        'woolentor-product-grid-editorial',
        'woolentor-product-grid-magazine',
        'woolentor-block-common',
        'woolentor-block-default',
        'simple-line-icons-wl',
        'htflexboxgrid',
        'woolentor-widgets',
        'woolentor-quickview',
    ];
    $scripts = [
        'woolentor-block-main',
        'woolentor-accordion-min',
        'woolentor-quickview',
    ];

    foreach ($styles as $handle) {
        wp_dequeue_style($handle);
    }
    foreach ($scripts as $handle) {
        wp_dequeue_script($handle);
    }
}, 999);

/* =====================================================================
 * 5. De kapotte /checkout/ doorsturen
 * ===================================================================== */

add_action('template_redirect', function () {
    if (is_admin() || !function_exists('wc_get_checkout_url') || !is_page()) {
        return;
    }

    $checkout_id = (int) wc_get_page_id('checkout');
    $current_id  = (int) get_queried_object_id();

    if ($checkout_id <= 0 || $current_id === $checkout_id) {
        return;
    }
    if (get_post_field('post_name', $current_id) !== 'checkout') {
        return;
    }

    nocache_headers();
    wp_safe_redirect(wc_get_checkout_url(), 301);
    exit;
}, 1);
