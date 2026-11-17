<?php
/**
 * De Outfitman — Theme Updater
 * Place in theme root. Call via REST API to pull latest from GitHub.
 * Self-contained: does not depend on ACF or any plugin.
 */
defined('ABSPATH') || exit;

add_action('rest_api_init', function () {
    register_rest_route('dom/v1', '/update', [
        'methods'  => 'POST',
        'callback' => 'dom_theme_update_from_github',
        'permission_callback' => function () {
            return current_user_can('manage_options');
        },
    ]);
});

function dom_theme_update_from_github() {
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
        return new WP_REST_Response(['ok' => false, 'error' => $unzip->get_error_message()], 500);
    }

    $extracted = glob($tmp_dir . '/dom-*');
    if (empty($extracted)) {
        return new WP_REST_Response(['ok' => false, 'error' => 'No extracted directory found'], 500);
    }
    $repo_dir = $extracted[0];

    $results = ['theme' => false, 'plugins' => []];

    // Update theme
    $theme_src = $repo_dir . '/dom-theme';
    $theme_dst = get_theme_root() . '/dom-theme';
    if (is_dir($theme_src)) {
        $wp_filesystem->delete($theme_dst, true);
        $results['theme'] = !is_wp_error(copy_dir($theme_src, $theme_dst));
    }

    // Update plugins
    $plugins_src = $repo_dir . '/dom-plugins';
    if (is_dir($plugins_src)) {
        foreach (glob($plugins_src . '/dom-*') as $psrc) {
            $slug = basename($psrc);
            $pdst = WP_PLUGIN_DIR . '/' . $slug;
            $wp_filesystem->delete($pdst, true);
            $results['plugins'][$slug] = !is_wp_error(copy_dir($psrc, $pdst));
        }
    }

    // Cleanup
    $wp_filesystem->delete($tmp_dir, true);

    // Flush
    if (function_exists('flush_rewrite_rules')) {
        flush_rewrite_rules();
    }

    return new WP_REST_Response(['ok' => true, 'results' => $results], 200);
}
