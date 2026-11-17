<?php
/**
 * Fatale fouten vastleggen waar ze uit te lezen zijn.
 *
 * Op deze hosting is het PHP-foutenlog niet te benaderen en WP_DEBUG_LOG staat
 * uit, dus een wit scherm liet niets achter om op te zoeken. Dat maakte het
 * onmogelijk om een crash te verhelpen zonder gokwerk.
 *
 * Deze handler bewaart de laatste paar fatale fouten in een optie, zodat ze
 * opvraagbaar zijn. Hij draait alleen bij een fatale fout en voegt verder
 * niets toe aan een normale paginaweergave.
 */

defined('ABSPATH') || exit;

const DOM_FATAL_OPTION = 'dom_last_fatals';
const DOM_FATAL_KEEP   = 5;

register_shutdown_function(function () {
    $error = error_get_last();

    if (!$error || !in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR, E_USER_ERROR], true)) {
        return;
    }

    // Alles vastleggen: een crash die het thema uitlokt slaat vaak neer in een
    // bestand van WooCommerce, dus filteren op de themamap mist juist die.
    if (!function_exists('get_option') || !function_exists('update_option')) {
        return;
    }

    $entry = [
        'time'    => gmdate('c'),
        'message' => $error['message'],
        'file'    => str_replace(ABSPATH, '', $error['file']),
        'line'    => $error['line'],
        'url'     => isset($_SERVER['REQUEST_URI']) ? esc_url_raw(wp_unslash($_SERVER['REQUEST_URI'])) : '',
    ];

    $stored = get_option(DOM_FATAL_OPTION, []);
    if (!is_array($stored)) {
        $stored = [];
    }

    array_unshift($stored, $entry);
    $stored = array_slice($stored, 0, DOM_FATAL_KEEP);

    update_option(DOM_FATAL_OPTION, $stored, false);
});
