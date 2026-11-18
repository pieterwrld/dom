<?php
/**
 * Plugin Name: De Outfitman Launcher
 * Plugin URI:  https://github.com/pieterwrld/dom
 * Description: Zet het nieuwe thema klaar, laat het alleen aan jou zien, en ruimt een paar dingen op die de winkel in de weg zitten. Alles vanuit WP Admin, geen SSH nodig.
 * Version:     1.3.0
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
    /**
     * Dit is het technische scherm (thema uitrollen, terugdraaien, cache). De
     * dagelijkse teksten en foto's staan in De Outfitman Content; die houdt de
     * naam "De Outfitman" bovenaan het menu, dit scherm staat onderaan bij de
     * andere systeeminstellingen zodat er geen twee gelijke menu's zijn.
     */
    add_menu_page(
        'Thema & deploy',
        'Thema & deploy',
        'update_themes',
        'dom-launcher',
        'dom_launcher_page',
        'dashicons-update',
        81
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

        <hr>

        <h2>Op afstand beheren</h2>
        <p>Zolang deze plugin actief is, kunnen de bovenstaande stappen ook op afstand worden
           uitgevoerd, zodat je ze niet zelf hoeft te klikken. Dat loopt over
           <code>/wp-json/dom/v1/</code> en vraagt dezelfde rechten als wp-admin: alleen een
           ingelogde beheerder komt erdoor.</p>
        <p>Wil je dat niet meer? Deactiveer deze plugin. Daarmee vervallen de routes,
           de preview en de opschoning in één keer, en verandert er niets aan je winkel.</p>
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
 * 2b. REST-routes
 *
 * WordPress heeft zelf geen route om een thema te activeren, waardoor elke
 * deploy handwerk in wp-admin werd. Deze routes vullen dat gat, zodat het
 * hele traject (code ophalen, activeren, cache legen, terugdraaien) op
 * afstand kan. Alles is afgeschermd met dezelfde rechten die wp-admin
 * ervoor vraagt, dus dit geeft niemand meer rechten dan hij al had.
 * ===================================================================== */

add_action('rest_api_init', function () {
    $can_themes = function () {
        return current_user_can('switch_themes') && current_user_can('update_themes');
    };

    register_rest_route('dom/v1', '/status', [
        'methods'             => 'GET',
        'callback'            => 'dom_launcher_rest_status',
        'permission_callback' => function () { return current_user_can('update_themes'); },
    ]);

    register_rest_route('dom/v1', '/deploy', [
        'methods'             => 'POST',
        'callback'            => 'dom_launcher_rest_deploy',
        'permission_callback' => $can_themes,
    ]);

    register_rest_route('dom/v1', '/theme', [
        'methods'             => 'POST',
        'callback'            => 'dom_launcher_rest_activate',
        'permission_callback' => $can_themes,
        'args'                => [
            'stylesheet' => ['required' => true, 'type' => 'string'],
        ],
    ]);

    register_rest_route('dom/v1', '/rollback', [
        'methods'             => 'POST',
        'callback'            => 'dom_launcher_rest_rollback',
        'permission_callback' => $can_themes,
    ]);

    register_rest_route('dom/v1', '/cache', [
        'methods'             => 'POST',
        'callback'            => 'dom_launcher_rest_cache',
        'permission_callback' => function () { return current_user_can('manage_options'); },
    ]);
});

function dom_launcher_rest_status() {
    $themes = [];
    foreach (wp_get_themes() as $slug => $theme) {
        $themes[$slug] = [
            'name'    => $theme->get('Name'),
            'version' => $theme->get('Version'),
            'active'  => get_stylesheet() === $slug,
            'valid'   => dom_launcher_looks_like_theme($theme->get_stylesheet_directory()),
        ];
    }

    if (!function_exists('get_plugins')) {
        require_once ABSPATH . 'wp-admin/includes/plugin.php';
    }

    $plugins = [];
    foreach (get_plugins() as $file => $data) {
        $plugins[$file] = [
            'name'   => $data['Name'],
            'active' => is_plugin_active($file),
        ];
    }

    return new WP_REST_Response([
        'active_theme'    => get_stylesheet(),
        'previous_theme'  => get_option('dom_launcher_previous_theme', ''),
        'themes'          => $themes,
        'plugins'         => $plugins,
        'php'             => PHP_VERSION,
        'wp'              => get_bloginfo('version'),
    ], 200);
}

function dom_launcher_rest_deploy() {
    $result = dom_launcher_deploy();
    if (is_wp_error($result)) {
        return new WP_REST_Response(['ok' => false, 'error' => $result->get_error_message()], 500);
    }
    return new WP_REST_Response(['ok' => true, 'message' => $result], 200);
}

function dom_launcher_rest_activate(WP_REST_Request $request) {
    $stylesheet = sanitize_key($request->get_param('stylesheet'));
    $theme      = wp_get_theme($stylesheet);

    if (!$theme->exists()) {
        return new WP_REST_Response(['ok' => false, 'error' => 'Dat thema staat er niet.'], 404);
    }

    $error = $theme->errors();
    if ($error instanceof WP_Error) {
        return new WP_REST_Response(
            ['ok' => false, 'error' => 'WordPress keurt dit thema af: ' . $error->get_error_message()],
            400
        );
    }

    // Een thema zonder deze bestanden activeren levert een witte winkel op.
    if (!dom_launcher_looks_like_theme($theme->get_stylesheet_directory())) {
        return new WP_REST_Response(
            ['ok' => false, 'error' => 'Dit thema is onvolledig, activeren geweigerd.'],
            400
        );
    }

    $current = get_stylesheet();
    if ($current === $stylesheet) {
        return new WP_REST_Response(['ok' => true, 'message' => 'Stond al actief.', 'active' => $current], 200);
    }

    update_option('dom_launcher_previous_theme', $current);
    switch_theme($stylesheet);

    if (has_action('litespeed_purge_all')) {
        do_action('litespeed_purge_all');
    }
    wp_cache_flush();

    return new WP_REST_Response([
        'ok'       => true,
        'message'  => 'Geactiveerd. Terugdraaien kan via /dom/v1/rollback.',
        'active'   => get_stylesheet(),
        'previous' => $current,
    ], 200);
}

function dom_launcher_rest_rollback() {
    $previous = get_option('dom_launcher_previous_theme', '');

    if (!$previous || !wp_get_theme($previous)->exists()) {
        return new WP_REST_Response(
            ['ok' => false, 'error' => 'Geen eerder thema bekend om naar terug te gaan.'],
            400
        );
    }

    $current = get_stylesheet();
    update_option('dom_launcher_previous_theme', $current);
    switch_theme($previous);

    if (has_action('litespeed_purge_all')) {
        do_action('litespeed_purge_all');
    }
    wp_cache_flush();

    return new WP_REST_Response(['ok' => true, 'active' => get_stylesheet()], 200);
}

function dom_launcher_rest_cache() {
    $done = [];

    if (has_action('litespeed_purge_all')) {
        do_action('litespeed_purge_all');
        $done[] = 'litespeed';
    }
    wp_cache_flush();
    $done[] = 'object-cache';

    return new WP_REST_Response(['ok' => true, 'purged' => $done], 200);
}

/* =====================================================================
 * 3. Preview: alleen voor beheerders
 * ===================================================================== */

/**
 * De preview wisselt het thema om via de filters 'template' en 'stylesheet'.
 *
 * Die filters draaien zodra íémand get_stylesheet() aanroept, en dat gebeurt
 * ook tijdens het laden van plugins: dus voordat WordPress pluggable.php heeft
 * ingelezen en dus voordat is_user_logged_in() bestaat. Een eerdere versie
 * vroeg daar naar de ingelogde gebruiker en haalde zo de hele winkel onderuit
 * zodra een andere plugin vroeg naar het thema vroeg.
 *
 * Daarom wordt hier alles één keer uitgerekend op 'setup_theme', ruim nadat
 * pluggable.php geladen is, en zijn de filters daarna niets meer dan het
 * teruggeven van een kant-en-klare string.
 */

/** @var array{template:string,stylesheet:string,name:string}|null */
$GLOBALS['dom_launcher_preview'] = null;

function dom_launcher_preview_theme() {
    $preview = $GLOBALS['dom_launcher_preview'] ?? null;
    return is_array($preview) ? $preview['name'] : '';
}

add_action('setup_theme', function () {
    // Geen preview gevraagd? Dan raken we de thema-filters niet aan, en kan
    // een gewone bezoeker hier per definitie geen last van hebben.
    if (!isset($_GET['dom_preview']) && !isset($_COOKIE[DOM_LAUNCHER_COOKIE])) {
        return;
    }
    if (is_admin() || wp_doing_ajax()) {
        return;
    }

    try {
        if (!is_user_logged_in() || !current_user_can('update_themes')) {
            return;
        }

        $name = '';
        if (isset($_GET['dom_preview'])) {
            $requested = sanitize_key(wp_unslash($_GET['dom_preview']));
            if ($requested !== '' && $requested !== 'off') {
                $name = $requested;
            }
        } elseif (isset($_COOKIE[DOM_LAUNCHER_COOKIE])) {
            $name = sanitize_key(wp_unslash($_COOKIE[DOM_LAUNCHER_COOKIE]));
        }

        if ($name === '') {
            return;
        }

        $theme = wp_get_theme($name);
        if (!$theme->exists() || $theme->errors() instanceof WP_Error) {
            return;
        }
        if (!dom_launcher_looks_like_theme($theme->get_stylesheet_directory())) {
            return;
        }

        // Nu vastleggen, zodat de filters hierna niets meer hoeven op te zoeken.
        $GLOBALS['dom_launcher_preview'] = [
            'name'       => $name,
            'template'   => $theme->get_template(),
            'stylesheet' => $theme->get_stylesheet(),
        ];

        add_filter('template', 'dom_launcher_filter_template', 99);
        add_filter('stylesheet', 'dom_launcher_filter_stylesheet', 99);
    } catch (Throwable $e) {
        // Een preview is comfort. Gaat hier iets mis, dan laten we de winkel
        // gewoon het echte thema tonen in plaats van om te vallen.
        $GLOBALS['dom_launcher_preview'] = null;
        error_log('[dom-launcher] preview uitgeschakeld: ' . $e->getMessage());
    }
}, 1);

function dom_launcher_filter_template($value) {
    $preview = $GLOBALS['dom_launcher_preview'] ?? null;
    return is_array($preview) ? $preview['template'] : $value;
}

function dom_launcher_filter_stylesheet($value) {
    $preview = $GLOBALS['dom_launcher_preview'] ?? null;
    return is_array($preview) ? $preview['stylesheet'] : $value;
}

add_action('init', function () {
    if (!isset($_GET['dom_preview']) || headers_sent()) {
        return;
    }
    if (!is_user_logged_in() || !current_user_can('update_themes')) {
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
