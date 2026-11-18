<?php
/**
 * Lezen en opslaan.
 *
 * Alles staat in één optie, genest als tabblad -> groep -> veld. Een leeg veld
 * valt terug op de standaardwaarde uit het schema, zodat de site er bij een
 * verse installatie precies zo uitziet als nu.
 */

defined('ABSPATH') || exit;

/**
 * Alles in één keer.
 *
 * Geen eigen static cache: de optie staat op autoload en komt dus al uit het
 * geheugen van WordPress zelf. Een eigen kopie zou na opslaan verouderd zijn,
 * waardoor het beheerscherm meteen na bewaren de oude waarden terugtoont.
 */
function dom_content_all() {
    $data = get_option(DOM_CONTENT_OPTION, []);
    return is_array($data) ? $data : [];
}

/**
 * De schemadefinitie van één pad, bijvoorbeeld "home.hero.cta".
 *
 * @return array<string, mixed>|null
 */
function dom_content_field($path) {
    $parts = explode('.', $path);
    if (count($parts) !== 3) {
        return null;
    }
    [$tab, $groep, $veld] = $parts;
    $schema = dom_content_schema();
    return $schema[$tab]['groepen'][$groep]['velden'][$veld] ?? null;
}

/**
 * Een waarde ophalen.
 *
 * @param string $path    Bijvoorbeeld "home.hero.cta".
 * @param mixed  $default Wat de sjabloon wil tonen als er niets is ingevuld én
 *                        het schema geen standaard heeft.
 * @return mixed
 */
function dom_content($path, $default = '') {
    $parts = explode('.', $path);
    if (count($parts) !== 3) {
        return $default;
    }
    [$tab, $groep, $veld] = $parts;

    $data  = dom_content_all();
    $value = $data[$tab][$groep][$veld] ?? null;

    $leeg = $value === null || $value === '' || $value === [];

    if ($leeg) {
        $field = dom_content_field($path);
        if ($field !== null && array_key_exists('default', $field)) {
            return $field['default'];
        }
        return $default;
    }

    return $value;
}

/**
 * Een schakelaar. Apart, omdat "uit" een geldige waarde is die niet terug mag
 * vallen op de standaard "aan".
 */
function dom_content_bool($path, $default = true) {
    $parts = explode('.', $path);
    if (count($parts) !== 3) {
        return $default;
    }
    [$tab, $groep, $veld] = $parts;
    $data = dom_content_all();

    if (isset($data[$tab][$groep][$veld])) {
        return (bool) $data[$tab][$groep][$veld];
    }

    $field = dom_content_field($path);
    if ($field !== null && array_key_exists('default', $field)) {
        return (bool) $field['default'];
    }
    return $default;
}

/**
 * Een afbeelding of video als URL. In de database staat een bijlage-id.
 *
 * @param string $path
 * @param string $size Afbeeldingsformaat; genegeerd bij video.
 * @return string Lege string als er niets gekozen is.
 */
function dom_content_url($path, $size = 'full') {
    $value = dom_content($path, '');

    if (is_array($value)) {
        $value = $value['id'] ?? ($value['url'] ?? '');
    }

    if (is_numeric($value)) {
        $url = wp_get_attachment_image_url((int) $value, $size);
        if (!$url) {
            $url = wp_get_attachment_url((int) $value);
        }
        return (string) ($url ?: '');
    }

    return is_string($value) ? $value : '';
}

/**
 * Een herhaalveld als nette lijst rijen, met de lege rijen eruit.
 *
 * @return array<int, array<string, mixed>>
 */
function dom_content_rows($path) {
    $rows = dom_content($path, []);
    if (!is_array($rows)) {
        return [];
    }

    $schoon = [];
    foreach ($rows as $row) {
        if (!is_array($row)) {
            continue;
        }
        // Een rij waarin alles leeg is, hoort niet op de site.
        $gevuld = false;
        foreach ($row as $v) {
            if ($v !== '' && $v !== null && $v !== false && $v !== '0') {
                $gevuld = true;
                break;
            }
        }
        if ($gevuld) {
            $schoon[] = $row;
        }
    }
    return $schoon;
}

/* ===================================================================
 *  Opslaan
 * =================================================================== */

/**
 * Eén ingestuurd tabblad opslaan. Wat niet in het schema staat, komt de
 * database niet in.
 *
 * @param string $tab
 * @param array  $input Rauwe $_POST-data van dat tabblad.
 */
function dom_content_save_tab($tab, array $input) {
    $schema = dom_content_schema();
    if (!isset($schema[$tab]['groepen'])) {
        return;
    }

    $data = dom_content_all();
    $schoon = [];

    foreach ($schema[$tab]['groepen'] as $groep_key => $groep) {
        foreach ($groep['velden'] as $veld_key => $veld) {
            $rauw = $input[$groep_key][$veld_key] ?? null;
            $schoon[$groep_key][$veld_key] = dom_content_sanitize($veld, $rauw);
        }
    }

    $data[$tab] = $schoon;

    update_option(DOM_CONTENT_OPTION, $data);
}

/**
 * Eén waarde opschonen op basis van het veldtype.
 *
 * @param array $veld
 * @param mixed $rauw
 * @return mixed
 */
function dom_content_sanitize(array $veld, $rauw) {
    $type = $veld['type'] ?? 'text';

    switch ($type) {
        case 'toggle':
            return !empty($rauw);

        case 'number':
            return $rauw === '' || $rauw === null ? '' : (float) $rauw;

        case 'image':
        case 'video':
            return $rauw === '' || $rauw === null ? '' : (int) $rauw;

        case 'url':
            return esc_url_raw((string) $rauw);

        case 'richtext':
            return wp_kses_post((string) $rauw);

        case 'textarea':
            return sanitize_textarea_field((string) $rauw);

        case 'select':
            $opties = array_keys($veld['options'] ?? []);
            return in_array($rauw, $opties, true) ? $rauw : ($opties[0] ?? '');

        case 'categorie':
            return sanitize_title((string) $rauw);

        case 'datetime':
            // Alleen het formaat jjjj-mm-ddThh:mm(:ss) toestaan.
            $rauw = trim((string) $rauw);
            return preg_match('/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}(:\d{2})?$/', $rauw) ? $rauw : '';

        case 'repeater':
            if (!is_array($rauw)) {
                return [];
            }
            $rijen = [];
            foreach ($rauw as $rij) {
                if (!is_array($rij)) {
                    continue;
                }
                $schoon = [];
                foreach (($veld['velden'] ?? []) as $sub_key => $sub) {
                    $schoon[$sub_key] = dom_content_sanitize($sub, $rij[$sub_key] ?? null);
                }
                $rijen[] = $schoon;
            }
            return $rijen;

        case 'text':
        default:
            return sanitize_text_field((string) $rauw);
    }
}
