<?php
/**
 * Contentlaag van het thema.
 *
 * De homepage-secties hadden ACF Pro nodig voor hun afbeeldingen en teksten.
 * ACF Pro is betaald en staat niet op de site, waardoor al die velden leeg
 * bleven. Deze module levert dezelfde content via de WordPress Customizer,
 * zodat het thema zonder extra plugin te beheren is.
 *
 * Staat ACF er later alsnog op, dan wint ACF: dom_setting() leest dat eerst.
 */

defined('ABSPATH') || exit;

/**
 * Eén waarde ophalen: ACF als die er is, anders de Customizer-instelling.
 *
 * @param string $key     Veldnaam, gelijk in ACF en als theme mod.
 * @param mixed  $default Terugvalwaarde.
 * @return mixed
 */
function dom_setting($key, $default = '') {
    if (function_exists('get_field')) {
        $value = get_field($key, 'option');
        if (!empty($value)) {
            return $value;
        }
    }
    return get_theme_mod($key, $default);
}

/**
 * Een afbeeldingsveld terugbrengen tot een URL. ACF geeft een array of een id,
 * de Customizer geeft een URL, dus alle drie moeten hier doorheen kunnen.
 *
 * @param string $key
 * @return string
 */
function dom_setting_image_url($key) {
    $value = dom_setting($key);

    if (is_array($value)) {
        return $value['url'] ?? '';
    }
    if (is_numeric($value)) {
        return (string) (wp_get_attachment_url((int) $value) ?: '');
    }
    return is_string($value) ? $value : '';
}

/**
 * De marquee-regels. In de Customizer is dit één tekstvak, één bericht per
 * regel. Een regel die met een uitroepteken begint wordt rood uitgelicht.
 *
 * @return array<int, array{text:string, is_hot:bool}>
 */
function dom_marquee_messages() {
    $messages = [];

    // ACF repeater blijft werken wanneer de plugin aanwezig is.
    if (function_exists('have_rows') && have_rows('marquee_messages', 'option')) {
        while (have_rows('marquee_messages', 'option')) {
            the_row();
            $text = (string) get_sub_field('message');
            if ($text !== '') {
                $messages[] = ['text' => $text, 'is_hot' => (bool) get_sub_field('is_hot')];
            }
        }
        if ($messages) {
            return $messages;
        }
    }

    $raw = (string) get_theme_mod('dom_marquee_messages', dom_marquee_default());

    foreach (preg_split('/\r\n|\r|\n/', $raw) as $line) {
        $line = trim($line);
        if ($line === '') {
            continue;
        }
        $is_hot = str_starts_with($line, '!');
        $messages[] = [
            'text'   => trim(ltrim($line, '!')),
            'is_hot' => $is_hot,
        ];
    }

    return $messages;
}

/**
 * Terugvalregels. Bewust alleen beleid dat de winkel echt voert; geen
 * kortingspercentages, want die horen uit WooCommerce te komen.
 */
function dom_marquee_default() {
    return implode("\n", [
        'Gratis verzending vanaf €200',
        '!Black Friday deals',
        '14 dagen retourrecht',
        '24/7 WhatsApp support',
        '100% authentieke producten',
    ]);
}

/**
 * Customizer-paneel.
 */
add_action('customize_register', function (WP_Customize_Manager $wp_customize) {

    $wp_customize->add_panel('dom_content', [
        'title'       => __('De Outfitman content', 'dom-theme'),
        'description' => __('Afbeeldingen en teksten van de homepage-secties.', 'dom-theme'),
        'priority'    => 20,
    ]);

    // --- Hero ---
    $wp_customize->add_section('dom_hero', [
        'title' => __('Hero', 'dom-theme'),
        'panel' => 'dom_content',
    ]);

    $wp_customize->add_setting('hero_video', [
        'default'           => '',
        'sanitize_callback' => 'esc_url_raw',
        'transport'         => 'refresh',
    ]);
    $wp_customize->add_control(new WP_Customize_Media_Control($wp_customize, 'hero_video', [
        'label'       => __('Hero-video', 'dom-theme'),
        'description' => __('MP4. Blijft leeg? Dan toont de hero de afbeelding hieronder.', 'dom-theme'),
        'section'     => 'dom_hero',
        'mime_type'   => 'video',
    ]));

    $wp_customize->add_setting('hero_image', [
        'default'           => '',
        'sanitize_callback' => 'esc_url_raw',
        'transport'         => 'refresh',
    ]);
    $wp_customize->add_control(new WP_Customize_Image_Control($wp_customize, 'hero_image', [
        'label'       => __('Hero-afbeelding', 'dom-theme'),
        'description' => __('Ook de poster van de video. Liggend, minimaal 1920px breed.', 'dom-theme'),
        'section'     => 'dom_hero',
    ]));

    // --- Logo ---
    $wp_customize->add_section('dom_brand', [
        'title' => __('Logo', 'dom-theme'),
        'panel' => 'dom_content',
    ]);

    $wp_customize->add_setting('header_logo', [
        'default'           => '',
        'sanitize_callback' => 'esc_url_raw',
        'transport'         => 'refresh',
    ]);
    $wp_customize->add_control(new WP_Customize_Image_Control($wp_customize, 'header_logo', [
        'label'       => __('Logo in de header', 'dom-theme'),
        'description' => __('Blijft leeg? Dan toont de header de woordmerk-tekst.', 'dom-theme'),
        'section'     => 'dom_brand',
    ]));

    // --- Marquee ---
    $wp_customize->add_section('dom_marquee', [
        'title' => __('Aankondigingsbalk', 'dom-theme'),
        'panel' => 'dom_content',
    ]);

    $wp_customize->add_setting('dom_marquee_messages', [
        'default'           => dom_marquee_default(),
        'sanitize_callback' => 'sanitize_textarea_field',
        'transport'         => 'refresh',
    ]);
    $wp_customize->add_control('dom_marquee_messages', [
        'label'       => __('Berichten', 'dom-theme'),
        'description' => __('Eén bericht per regel. Begin een regel met ! om hem rood uit te lichten.', 'dom-theme'),
        'section'     => 'dom_marquee',
        'type'        => 'textarea',
    ]);

    // --- Klokjesman ---
    $wp_customize->add_section('dom_klokjesman', [
        'title' => __('De Klokjesman', 'dom-theme'),
        'panel' => 'dom_content',
    ]);

    $wp_customize->add_setting('klokjesman_image', [
        'default'           => '',
        'sanitize_callback' => 'esc_url_raw',
        'transport'         => 'refresh',
    ]);
    $wp_customize->add_control(new WP_Customize_Image_Control($wp_customize, 'klokjesman_image', [
        'label'   => __('Afbeelding', 'dom-theme'),
        'section' => 'dom_klokjesman',
    ]));
});

/**
 * Categoriebanner per productcategorie, als vervanging van het ACF-taxonomieveld.
 */
add_action('product_cat_edit_form_fields', function ($term) {
    $value = get_term_meta($term->term_id, 'category_banner', true);
    ?>
    <tr class="form-field">
        <th scope="row"><label for="dom-category-banner"><?php esc_html_e('Categoriebanner', 'dom-theme'); ?></label></th>
        <td>
            <input type="url" name="dom_category_banner" id="dom-category-banner"
                   value="<?php echo esc_attr($value); ?>" style="width:100%" placeholder="https://">
            <p class="description">
                <?php esc_html_e('URL van een liggende banner. Kopieer die uit de mediabibliotheek.', 'dom-theme'); ?>
            </p>
        </td>
    </tr>
    <?php
});

add_action('edited_product_cat', function ($term_id) {
    if (!current_user_can('manage_product_terms')) {
        return;
    }
    if (!isset($_POST['dom_category_banner'])) {
        return;
    }
    $url = esc_url_raw(wp_unslash($_POST['dom_category_banner']));
    if ($url === '') {
        delete_term_meta($term_id, 'category_banner');
    } else {
        update_term_meta($term_id, 'category_banner', $url);
    }
});

/**
 * Categoriebanner uitlezen: ACF eerst, anders de term meta hierboven.
 */
function dom_category_banner_url($term_id) {
    if (function_exists('get_field')) {
        $value = get_field('category_banner', 'product_cat_' . $term_id);
        if (is_array($value) && !empty($value['url'])) {
            return $value['url'];
        }
        if (is_string($value) && $value !== '') {
            return $value;
        }
    }
    return (string) get_term_meta($term_id, 'category_banner', true);
}
