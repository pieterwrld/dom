<?php
/**
 * Het beheerscherm: één menu met een tabblad per onderdeel.
 */

defined('ABSPATH') || exit;

define('DOM_CONTENT_SLUG', 'dom-content');

add_action('admin_menu', function () {
    $schema = dom_content_schema();

    add_menu_page(
        __('De Outfitman', 'dom-content'),
        __('De Outfitman', 'dom-content'),
        'manage_options',
        DOM_CONTENT_SLUG,
        'dom_content_render_page',
        'dashicons-store',
        3
    );

    // Elk tabblad krijgt een eigen regel in het menu, zodat je er met één klik
    // bent in plaats van eerst de pagina te openen en dan het tabblad.
    foreach ($schema as $tab => $def) {
        add_submenu_page(
            DOM_CONTENT_SLUG,
            $def['titel'],
            $def['titel'],
            'manage_options',
            DOM_CONTENT_SLUG . ($tab === array_key_first($schema) ? '' : '&tab=' . $tab),
            'dom_content_render_page'
        );
    }
});

/** Welk tabblad is open. */
function dom_content_huidige_tab() {
    $schema = dom_content_schema();
    $tab    = isset($_GET['tab']) ? sanitize_key(wp_unslash($_GET['tab'])) : '';
    return isset($schema[$tab]) ? $tab : (string) array_key_first($schema);
}

add_action('admin_enqueue_scripts', function ($hook) {
    if (strpos((string) $hook, DOM_CONTENT_SLUG) === false) {
        return;
    }

    wp_enqueue_media();
    wp_enqueue_style(
        'dom-content-admin',
        DOM_CONTENT_URL . 'assets/admin.css',
        [],
        DOM_CONTENT_VERSION
    );
    wp_enqueue_script(
        'dom-content-admin',
        DOM_CONTENT_URL . 'assets/admin.js',
        ['jquery', 'jquery-ui-sortable'],
        DOM_CONTENT_VERSION,
        true
    );
    wp_localize_script('dom-content-admin', 'domContentL10n', [
        'kiesAfbeelding' => __('Afbeelding kiezen', 'dom-content'),
        'kiesVideo'      => __('Video kiezen', 'dom-content'),
        'gebruiken'      => __('Gebruiken', 'dom-content'),
        'vervangen'      => __('Vervangen', 'dom-content'),
        'kiezen'         => __('Kiezen', 'dom-content'),
        'nieuw'          => __('Nieuw', 'dom-content'),
        'weetJeHetZeker' => __('Deze rij verwijderen?', 'dom-content'),
    ]);
});

/** Opslaan gebeurt vóór het tekenen, zodat je meteen het resultaat ziet. */
function dom_content_handle_save() {
    if (!isset($_POST['dom_content_nonce'])) {
        return null;
    }
    if (!current_user_can('manage_options')) {
        return null;
    }
    if (!wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['dom_content_nonce'])), 'dom_content_opslaan')) {
        return ['soort' => 'error', 'tekst' => __('De pagina stond te lang open. Probeer het nog een keer.', 'dom-content')];
    }

    $tab = isset($_POST['dom_content_tab']) ? sanitize_key(wp_unslash($_POST['dom_content_tab'])) : '';
    $schema = dom_content_schema();
    if (!isset($schema[$tab])) {
        return null;
    }

    if (($schema[$tab]['soort'] ?? '') === 'categorieen') {
        dom_content_save_categories($_POST['dom_cat'] ?? []);
    } else {
        // Bewust geen sanitize over de hele array heen: elk veld wordt in
        // dom_content_save_tab op zijn eigen type opgeschoond, en een rich
        // text-veld mag geen tekstopschoning krijgen.
        $input = isset($_POST['dom_content']) && is_array($_POST['dom_content'])
            ? wp_unslash($_POST['dom_content'])
            : [];
        dom_content_save_tab($tab, $input);
    }

    if (function_exists('wp_cache_flush')) {
        wp_cache_flush();
    }
    if (has_action('litespeed_purge_all')) {
        do_action('litespeed_purge_all');
    }

    return ['soort' => 'success', 'tekst' => __('Opgeslagen. De wijziging staat live.', 'dom-content')];
}

function dom_content_render_page() {
    if (!current_user_can('manage_options')) {
        wp_die(esc_html__('Je hebt geen toegang tot deze pagina.', 'dom-content'));
    }

    $melding = dom_content_handle_save();
    $schema  = dom_content_schema();
    $tab     = dom_content_huidige_tab();
    $def     = $schema[$tab];
    ?>
    <div class="wrap domc">
        <h1 class="domc-titel">
            <?php esc_html_e('De Outfitman', 'dom-content'); ?>
            <span><?php esc_html_e('content', 'dom-content'); ?></span>
        </h1>

        <?php if ($melding) : ?>
            <div class="notice notice-<?php echo esc_attr($melding['soort']); ?> is-dismissible">
                <p><?php echo esc_html($melding['tekst']); ?></p>
            </div>
        <?php endif; ?>

        <nav class="domc-tabs">
            <?php foreach ($schema as $sleutel => $t) :
                $url = add_query_arg(['page' => DOM_CONTENT_SLUG, 'tab' => $sleutel], admin_url('admin.php'));
            ?>
                <a href="<?php echo esc_url($url); ?>" class="domc-tab<?php echo $sleutel === $tab ? ' is-actief' : ''; ?>">
                    <span class="dashicons <?php echo esc_attr($t['icoon'] ?? 'dashicons-admin-generic'); ?>"></span>
                    <?php echo esc_html($t['titel']); ?>
                </a>
            <?php endforeach; ?>
            <a href="<?php echo esc_url(home_url('/')); ?>" class="domc-tab domc-tab--extern" target="_blank" rel="noopener">
                <span class="dashicons dashicons-external"></span>
                <?php esc_html_e('Bekijk de site', 'dom-content'); ?>
            </a>
        </nav>

        <form method="post" class="domc-form">
            <?php wp_nonce_field('dom_content_opslaan', 'dom_content_nonce'); ?>
            <input type="hidden" name="dom_content_tab" value="<?php echo esc_attr($tab); ?>">

            <?php if (!empty($def['uitleg'])) : ?>
                <p class="domc-uitleg"><?php echo esc_html($def['uitleg']); ?></p>
            <?php endif; ?>
            <p class="domc-uitleg domc-uitleg--klein">
                <?php esc_html_e('Een veld leeg laten betekent: gebruik de standaardtekst die er nu staat. Je hoeft dus alleen in te vullen wat je wilt veranderen.', 'dom-content'); ?>
            </p>

            <?php
            if (($def['soort'] ?? '') === 'categorieen') {
                dom_content_render_categories();
            } else {
                dom_content_render_groups($tab, $def);
            }
            ?>

            <div class="domc-balk">
                <button type="submit" class="button button-primary button-hero">
                    <?php esc_html_e('Opslaan', 'dom-content'); ?>
                </button>
                <span class="domc-balk__hint"><?php esc_html_e('De site is meteen bijgewerkt.', 'dom-content'); ?></span>
            </div>
        </form>
    </div>
    <?php
}

function dom_content_render_groups($tab, array $def) {
    $data = dom_content_all();

    foreach ($def['groepen'] as $groep_key => $groep) {
        ?>
        <section class="domc-kaart">
            <header class="domc-kaart__kop">
                <h2><?php echo esc_html($groep['titel']); ?></h2>
                <?php if (!empty($groep['uitleg'])) : ?>
                    <p><?php echo esc_html($groep['uitleg']); ?></p>
                <?php endif; ?>
            </header>
            <div class="domc-kaart__body">
                <?php
                foreach ($groep['velden'] as $veld_key => $veld) {
                    $naam   = 'dom_content[' . $groep_key . '][' . $veld_key . ']';
                    $pad    = $tab . '.' . $groep_key . '.' . $veld_key;
                    $opgeslagen = $data[$tab][$groep_key][$veld_key] ?? null;

                    // Een leeg veld toont de standaardwaarde als startpunt, zodat
                    // je ziet wat er nu op de site staat in plaats van een leeg vak.
                    $waarde = $opgeslagen;
                    if ($waarde === null || $waarde === '' || $waarde === []) {
                        $waarde = $veld['default'] ?? ($veld['type'] === 'repeater' ? [] : '');
                    }
                    if (($veld['type'] ?? '') === 'toggle') {
                        $waarde = dom_content_bool($pad);
                    }

                    dom_content_render_field($naam, $veld, $waarde);
                }
                ?>
            </div>
        </section>
        <?php
    }
}
