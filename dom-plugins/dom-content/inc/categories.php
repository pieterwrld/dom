<?php
/**
 * Categoriescherm.
 *
 * Per productcategorie staan hier de drie plaatjes die het thema gebruikt:
 * de foto in het collectieraster, het kleine plaatje in het menu en de brede
 * banner bovenaan de categoriepagina.
 *
 * De rasterfoto is de gewone WooCommerce-categorieafbeelding (thumbnail_id),
 * zodat bestaande foto's blijven staan en ook elders in WooCommerce kloppen.
 * De andere twee zijn eigen term meta.
 */

defined('ABSPATH') || exit;

/** De velden per categorie, in de volgorde waarin ze getoond worden. */
function dom_content_cat_fields() {
    return [
        'thumbnail_id' => [
            'label' => __('Foto in het raster', 'dom-content'),
            'help'  => __('Ook gebruikt in het accessoireblok. Staand of vierkant werkt het beste.', 'dom-content'),
            'type'  => 'image',
        ],
        'dom_menu_thumb' => [
            'label' => __('Plaatje in het menu', 'dom-content'),
            'help'  => __('Het kleine staande plaatje naast de categorienaam in het schuifmenu. Leeg laten = de rasterfoto.', 'dom-content'),
            'type'  => 'image',
        ],
        'dom_banner_id' => [
            'label' => __('Banner op de categoriepagina', 'dom-content'),
            'help'  => __('Liggend beeld, komt tussen de filters en de producten.', 'dom-content'),
            'type'  => 'image',
        ],
    ];
}

/** Het hele scherm. */
function dom_content_render_categories() {
    $termen = get_terms([
        'taxonomy'   => 'product_cat',
        'hide_empty' => false,
        'exclude'    => [(int) get_option('default_product_cat')],
    ]);
    if (is_wp_error($termen)) {
        $termen = [];
    }

    // Dezelfde volgorde als op de site, zodat het scherm het menu volgt.
    if (function_exists('dom_ordered_categories')) {
        $geordend = dom_ordered_categories();
        if ($geordend) {
            $termen = $geordend;
        }
    }

    $velden = dom_content_cat_fields();

    foreach ($termen as $term) {
        if (!is_object($term) || empty($term->term_id)) {
            continue;
        }
        ?>
        <section class="domc-kaart domc-kaart--cat">
            <header class="domc-kaart__kop">
                <h2><?php echo esc_html($term->name); ?></h2>
                <p>
                    <?php
                    printf(
                        /* translators: %d is het aantal producten. */
                        esc_html(_n('%d product', '%d producten', (int) $term->count, 'dom-content')),
                        (int) $term->count
                    );
                    ?>
                    &middot;
                    <a href="<?php echo esc_url(get_edit_term_link($term->term_id, 'product_cat')); ?>">
                        <?php esc_html_e('naam en omschrijving bewerken', 'dom-content'); ?>
                    </a>
                </p>
            </header>
            <div class="domc-kaart__body domc-kaart__body--kolommen">
                <?php
                foreach ($velden as $sleutel => $veld) {
                    $waarde = (int) get_term_meta($term->term_id, $sleutel, true);
                    $naam   = 'dom_cat[' . (int) $term->term_id . '][' . $sleutel . ']';
                    dom_content_render_field($naam, $veld, $waarde);
                }
                ?>
            </div>
        </section>
        <?php
    }

    if (!$termen) {
        echo '<p>' . esc_html__('Er zijn nog geen productcategorieën.', 'dom-content') . '</p>';
    }
}

/** Opslaan. */
function dom_content_save_categories($input) {
    if (!is_array($input)) {
        return;
    }
    $velden = array_keys(dom_content_cat_fields());

    foreach ($input as $term_id => $waarden) {
        $term_id = (int) $term_id;
        if (!$term_id || !is_array($waarden)) {
            continue;
        }
        foreach ($velden as $sleutel) {
            if (!array_key_exists($sleutel, $waarden)) {
                continue;
            }
            $id = (int) $waarden[$sleutel];
            if ($id > 0) {
                update_term_meta($term_id, $sleutel, $id);
            } else {
                delete_term_meta($term_id, $sleutel);
            }
        }
    }
}

/* ===================================================================
 *  Uitlezen vanuit het thema
 * =================================================================== */

/**
 * Een categorieplaatje ophalen, met een nette reeks terugvallen:
 * eigen veld -> rasterfoto -> eerste productfoto.
 *
 * @param int    $term_id
 * @param string $rol  raster | menu | banner
 * @param string $size
 * @return string URL, of een lege string.
 */
function dom_content_cat_image($term_id, $rol = 'raster', $size = 'medium') {
    $term_id = (int) $term_id;
    if (!$term_id) {
        return '';
    }

    $volgorde = [
        'raster' => ['thumbnail_id'],
        'menu'   => ['dom_menu_thumb', 'thumbnail_id'],
        'banner' => ['dom_banner_id'],
    ];
    $sleutels = $volgorde[$rol] ?? ['thumbnail_id'];

    foreach ($sleutels as $sleutel) {
        $id = (int) get_term_meta($term_id, $sleutel, true);
        if ($id) {
            $url = wp_get_attachment_image_url($id, $size);
            if ($url) {
                return $url;
            }
        }
    }

    // De banner heeft geen productfoto als terugval: een staande productfoto
    // in een liggende banner ziet er niet uit.
    if ($rol === 'banner') {
        return (string) dom_content_cat_banner_legacy($term_id);
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
    if ($producten) {
        return (string) (get_the_post_thumbnail_url($producten[0], $size) ?: '');
    }

    return '';
}

/**
 * De oude bannerinstelling: een losse URL in term meta. Blijft werken voor wat
 * er al ingevuld was.
 */
function dom_content_cat_banner_legacy($term_id) {
    $url = get_term_meta($term_id, 'category_banner', true);
    return is_string($url) ? $url : '';
}
