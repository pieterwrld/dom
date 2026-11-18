<?php
/**
 * De invoervelden tekenen.
 *
 * Elk type kent één functie. De naam van het invoerveld is altijd
 * dom_content[groep][veld] zodat het opslaan één structuur terugkrijgt;
 * herhaalvelden zetten daar [index][subveld] achter.
 */

defined('ABSPATH') || exit;

/**
 * Eén veld met label en uitleg.
 *
 * @param string $naam  Naamprefix voor het formulier.
 * @param array  $veld  Schemadefinitie.
 * @param mixed  $waarde
 */
function dom_content_render_field($naam, array $veld, $waarde, $id = '') {
    $type  = $veld['type'] ?? 'text';
    $label = $veld['label'] ?? '';
    $help  = $veld['help'] ?? '';
    $id    = $id ?: 'f' . substr(md5($naam), 0, 8);

    $breed = in_array($type, ['repeater'], true);

    echo '<div class="domc-veld domc-veld--' . esc_attr($type) . ($breed ? ' domc-veld--breed' : '') . '">';

    if ($label !== '') {
        echo '<label class="domc-label" for="' . esc_attr($id) . '">' . esc_html($label) . '</label>';
    }

    switch ($type) {
        case 'textarea':
            printf(
                '<textarea id="%s" name="%s" rows="3" class="domc-input">%s</textarea>',
                esc_attr($id),
                esc_attr($naam),
                esc_textarea((string) $waarde)
            );
            break;

        case 'richtext':
            wp_editor((string) $waarde, $id, [
                'textarea_name' => $naam,
                'textarea_rows' => 6,
                'media_buttons' => false,
                'teeny'         => true,
                'quicktags'     => true,
            ]);
            break;

        case 'number':
            printf(
                '<input type="number" step="any" id="%s" name="%s" value="%s" class="domc-input domc-input--kort">',
                esc_attr($id),
                esc_attr($naam),
                esc_attr((string) $waarde)
            );
            break;

        case 'toggle':
            printf(
                '<label class="domc-schakel"><input type="checkbox" name="%s" value="1" %s><span></span></label>',
                esc_attr($naam),
                checked(!empty($waarde), true, false)
            );
            break;

        case 'select':
            printf('<select id="%s" name="%s" class="domc-input domc-input--kort">', esc_attr($id), esc_attr($naam));
            foreach (($veld['options'] ?? []) as $sleutel => $optie_label) {
                printf(
                    '<option value="%s" %s>%s</option>',
                    esc_attr($sleutel),
                    selected($waarde, $sleutel, false),
                    esc_html($optie_label)
                );
            }
            echo '</select>';
            break;

        case 'categorie':
            dom_content_render_categorie_select($naam, $id, (string) $waarde);
            break;

        case 'datetime':
            printf(
                '<input type="datetime-local" step="1" id="%s" name="%s" value="%s" class="domc-input domc-input--kort">',
                esc_attr($id),
                esc_attr($naam),
                esc_attr((string) $waarde)
            );
            break;

        case 'image':
        case 'video':
            dom_content_render_media($naam, $id, $waarde, $type);
            break;

        case 'repeater':
            dom_content_render_repeater($naam, $veld, is_array($waarde) ? $waarde : []);
            break;

        case 'url':
            printf(
                '<input type="text" id="%s" name="%s" value="%s" class="domc-input" placeholder="https://of /pad">',
                esc_attr($id),
                esc_attr($naam),
                esc_attr((string) $waarde)
            );
            break;

        case 'text':
        default:
            printf(
                '<input type="text" id="%s" name="%s" value="%s" class="domc-input">',
                esc_attr($id),
                esc_attr($naam),
                esc_attr((string) $waarde)
            );
            break;
    }

    if ($help !== '') {
        echo '<p class="domc-help">' . esc_html($help) . '</p>';
    }

    echo '</div>';
}

/** Keuzelijst met de productcategorieën. */
function dom_content_render_categorie_select($naam, $id, $waarde) {
    $termen = get_terms([
        'taxonomy'   => 'product_cat',
        'hide_empty' => false,
    ]);
    if (is_wp_error($termen)) {
        $termen = [];
    }

    printf('<select id="%s" name="%s" class="domc-input domc-input--kort">', esc_attr($id), esc_attr($naam));
    echo '<option value="">— kies —</option>';
    foreach ($termen as $term) {
        printf(
            '<option value="%s" %s>%s</option>',
            esc_attr($term->slug),
            selected($waarde, $term->slug, false),
            esc_html($term->name)
        );
    }
    echo '</select>';
}

/**
 * Afbeelding of video kiezen uit de mediabibliotheek.
 */
function dom_content_render_media($naam, $id, $waarde, $type = 'image') {
    $attachment_id = is_numeric($waarde) ? (int) $waarde : 0;
    $preview       = '';
    $bestandsnaam  = '';

    if ($attachment_id) {
        $bestandsnaam = basename((string) get_attached_file($attachment_id));
        if ($type === 'image') {
            $src = wp_get_attachment_image_url($attachment_id, 'medium');
            if ($src) {
                $preview = '<img src="' . esc_url($src) . '" alt="">';
            }
        } else {
            $poster = wp_get_attachment_image_url($attachment_id, 'medium');
            $preview = $poster
                ? '<img src="' . esc_url($poster) . '" alt="">'
                : '<span class="domc-media__film dashicons dashicons-video-alt3"></span>';
        }
    }

    ?>
    <div class="domc-media" data-type="<?php echo esc_attr($type); ?>">
        <input type="hidden" name="<?php echo esc_attr($naam); ?>" id="<?php echo esc_attr($id); ?>"
               value="<?php echo esc_attr((string) $attachment_id); ?>" class="domc-media__id">
        <div class="domc-media__vak <?php echo $attachment_id ? 'is-gevuld' : ''; ?>">
            <div class="domc-media__preview"><?php echo $preview; // phpcs:ignore ?></div>
            <div class="domc-media__acties">
                <button type="button" class="button domc-media__kies">
                    <?php echo $attachment_id ? esc_html__('Vervangen', 'dom-content') : esc_html__('Kiezen', 'dom-content'); ?>
                </button>
                <button type="button" class="button-link domc-media__wis" <?php echo $attachment_id ? '' : 'hidden'; ?>>
                    <?php esc_html_e('Verwijderen', 'dom-content'); ?>
                </button>
                <span class="domc-media__naam"><?php echo esc_html($bestandsnaam); ?></span>
            </div>
        </div>
    </div>
    <?php
}

/**
 * Herhaalveld: rijen die je kunt toevoegen, slepen en weggooien.
 */
function dom_content_render_repeater($naam, array $veld, array $rijen) {
    $subvelden = $veld['velden'] ?? [];
    $rij_label = $veld['rij_label'] ?? '';
    $compact   = !empty($veld['compact']);

    echo '<div class="domc-rep' . ($compact ? ' domc-rep--compact' : '') . '" data-naam="' . esc_attr($naam) . '">';
    echo '<div class="domc-rep__rijen">';

    foreach (array_values($rijen) as $i => $rij) {
        dom_content_render_repeater_row($naam, $subvelden, is_array($rij) ? $rij : [], $i, $rij_label);
    }

    echo '</div>';

    // Blauwdruk voor nieuwe rijen. __i__ wordt in JS vervangen door een nummer.
    echo '<script type="text/html" class="domc-rep__sjabloon">';
    dom_content_render_repeater_row($naam, $subvelden, [], '__i__', $rij_label);
    echo '</script>';

    printf(
        '<button type="button" class="button domc-rep__toevoegen">+ %s</button>',
        esc_html($veld['knop'] ?? __('Rij toevoegen', 'dom-content'))
    );

    echo '</div>';
}

function dom_content_render_repeater_row($naam, array $subvelden, array $rij, $index, $rij_label) {
    $titel = '';
    if ($rij_label !== '' && isset($rij[$rij_label]) && is_string($rij[$rij_label]) && $rij[$rij_label] !== '') {
        $titel = $rij[$rij_label];
    }
    ?>
    <div class="domc-rij">
        <div class="domc-rij__kop">
            <span class="domc-rij__greep dashicons dashicons-menu" title="<?php esc_attr_e('Sleep om te verplaatsen', 'dom-content'); ?>"></span>
            <span class="domc-rij__titel"><?php echo esc_html($titel ?: __('Nieuw', 'dom-content')); ?></span>
            <button type="button" class="domc-rij__weg" aria-label="<?php esc_attr_e('Rij verwijderen', 'dom-content'); ?>">
                <span class="dashicons dashicons-trash"></span>
            </button>
        </div>
        <div class="domc-rij__body">
            <?php
            foreach ($subvelden as $sub_key => $sub) {
                // De tekstverwerker laat zich niet klonen voor een nieuwe rij,
                // dus binnen een herhaalveld wordt het een gewoon tekstvak.
                if (($sub['type'] ?? '') === 'richtext') {
                    $sub['type'] = 'textarea';
                }
                $sub_naam = $naam . '[' . $index . '][' . $sub_key . ']';
                $waarde   = $rij[$sub_key] ?? ($sub['default'] ?? '');
                // Het id moet uniek blijven, ook in de blauwdruk.
                $sub_id = 'r' . substr(md5($sub_naam), 0, 8);
                dom_content_render_field($sub_naam, $sub, $waarde, $sub_id);
            }
            ?>
        </div>
    </div>
    <?php
}
