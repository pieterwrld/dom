<?php
/**
 * Black Friday: echte kortingen in WooCommerce.
 *
 * Niet alleen een label, maar de werkelijke prijzen. Per product (en bij maten
 * per variant) blijft de normale prijs staan als vergelijkingsprijs en zetten
 * we daarnaast een aanbiedingsprijs. Zo klopt de korting overal vanzelf: op de
 * productpagina, in de collecties, in de winkelwagen en bij het afrekenen.
 *
 * Wat we overschrijven bewaren we eerst. Bij het uitzetten gaat alleen terug
 * wat wij hebben gezet: is een prijs daarna met de hand aangepast, dan blijft
 * die staan en melden we hem als overgeslagen.
 */

defined('ABSPATH') || exit;

const DOM_BF_ACTIEF      = 'dom_bf_actief';
const DOM_BF_SAMENVAT    = 'dom_bf_samenvatting';

/** Meta op het product of de variant. */
const DOM_BF_VORIGE_SALE = '_dom_bf_vorige_sale';
const DOM_BF_GEZET       = '_dom_bf_gezette_sale';
const DOM_BF_TERM        = '_dom_bf_sale_term_toegevoegd';

/**
 * De korting per categorie, in procenten.
 *
 * De sleutel is de slug van de productcategorie. Categorieën die hier niet in
 * staan krijgen geen korting; trainingspakken en petten dus niet.
 *
 * @return array<string,int>
 */
function dom_bf_kortingen() {
    return apply_filters('dom_bf_kortingen', [
        't-shirts'         => 20,
        'jeans'            => 20,
        'sweaters-hoodies' => 20,
        'truien-vesten'    => 20,
        'brillen'          => 20,
        'slippers'         => 20,
        'zwembroeken'      => 20,
        'sneakers'         => 10,
        'jassen'           => 10,
        'tassen'           => 10,
    ]);
}

/** Staat de actie aan? */
function dom_bf_is_actief() {
    return get_option(DOM_BF_ACTIEF) === '1';
}

/**
 * Alle gepubliceerde producten in een categorie die korting krijgt.
 *
 * @return int[]
 */
function dom_bf_product_ids() {
    static $ids = null;
    if ($ids !== null) {
        return $ids;
    }

    $ids = get_posts([
        'post_type'      => 'product',
        'post_status'    => 'publish',
        'posts_per_page' => -1,
        'fields'         => 'ids',
        'orderby'        => 'ID',
        'order'          => 'ASC',
        'no_found_rows'  => true,
        'tax_query'      => [[
            'taxonomy' => 'product_cat',
            'field'    => 'slug',
            'terms'    => array_keys(dom_bf_kortingen()),
        ]],
    ]);

    return $ids;
}

/**
 * Welke korting hoort bij dit product?
 *
 * @return array{pct:int,reden:string,slugs:array}
 */
function dom_bf_bepaal_korting($product_id) {
    $kortingen = dom_bf_kortingen();
    $termen    = wp_get_post_terms($product_id, 'product_cat', ['fields' => 'slugs']);
    if (is_wp_error($termen)) {
        return ['pct' => 0, 'reden' => 'categorie onleesbaar', 'slugs' => []];
    }

    $gevonden = [];
    foreach ($termen as $slug) {
        if (isset($kortingen[$slug])) {
            $gevonden[$slug] = $kortingen[$slug];
        }
    }

    if (!$gevonden) {
        return ['pct' => 0, 'reden' => 'geen categorie met korting', 'slugs' => $termen];
    }

    $uniek = array_unique(array_values($gevonden));
    if (count($uniek) > 1) {
        // Niet gokken: een product in zowel Sneakers (10%) als Slippers (20%)
        // laten we met rust en melden we apart.
        return ['pct' => 0, 'reden' => 'tegenstrijdige categorieën', 'slugs' => array_keys($gevonden)];
    }

    return ['pct' => (int) reset($uniek), 'reden' => '', 'slugs' => array_keys($gevonden)];
}

/** De aanbiedingsprijs bij een normale prijs. */
function dom_bf_sale_prijs($regular, $pct) {
    return round(((float) $regular) * (1 - $pct / 100), 2);
}

/**
 * De onderdelen waar een prijs op staat: het product zelf, of al zijn varianten.
 *
 * @return WC_Product[]
 */
function dom_bf_prijsdragers(WC_Product $product) {
    if ($product->is_type('variable')) {
        $uit = [];
        foreach ($product->get_children() as $kind_id) {
            $kind = wc_get_product($kind_id);
            if ($kind) {
                $uit[] = $kind;
            }
        }
        return $uit;
    }
    if ($product->is_type('simple')) {
        return [$product];
    }
    return [];
}

/* ===================================================================
 *  Aanzetten
 * =================================================================== */

/**
 * Eén portie producten afprijzen.
 *
 * @return array tellers voor deze portie
 */
function dom_bf_toepassen($offset, $limit) {
    $ids   = array_slice(dom_bf_product_ids(), $offset, $limit);
    $sale_term = get_term_by('slug', 'sale', 'product_cat');

    $t = dom_bf_lege_telling();

    foreach ($ids as $id) {
        $product = wc_get_product($id);
        if (!$product) {
            $t['overgeslagen']++;
            continue;
        }

        $plan = dom_bf_bepaal_korting($id);
        if ($plan['pct'] <= 0) {
            $t['overgeslagen']++;
            if ($plan['reden'] === 'tegenstrijdige categorieën') {
                $t['conflicten'][] = ['id' => $id, 'naam' => $product->get_name(), 'slugs' => $plan['slugs']];
            }
            continue;
        }

        $dragers = dom_bf_prijsdragers($product);
        if (!$dragers) {
            $t['overgeslagen']++;
            $t['anders'][] = ['id' => $id, 'naam' => $product->get_name(), 'reden' => 'producttype ' . $product->get_type()];
            continue;
        }

        $iets_gezet = false;

        foreach ($dragers as $drager) {
            $regular = $drager->get_regular_price('edit');
            if ($regular === '' || (float) $regular <= 0) {
                $t['zonder_prijs']++;
                continue;
            }

            $nieuw = dom_bf_sale_prijs($regular, $plan['pct']);
            if ($nieuw <= 0 || $nieuw >= (float) $regular) {
                $t['zonder_prijs']++;
                continue;
            }

            $huidige_sale = $drager->get_sale_price('edit');

            /**
             * Een bestaande aanbieding die al lager is laten we staan. De
             * klant een hogere prijs geven omdat er een actie begint, is geen
             * actie; de korting is daar al groter dan deze.
             */
            if ($huidige_sale !== '' && (float) $huidige_sale > 0 && (float) $huidige_sale <= $nieuw) {
                $t['al_lager']++;
                continue;
            }

            $post_id = $drager->get_id();
            if (get_post_meta($post_id, DOM_BF_GEZET, true) === '') {
                // Alleen de eerste keer vastleggen wat er stond.
                update_post_meta($post_id, DOM_BF_VORIGE_SALE, (string) $huidige_sale);
            }

            $drager->set_sale_price((string) $nieuw);
            $drager->save();

            update_post_meta($post_id, DOM_BF_GEZET, (string) $nieuw);

            $iets_gezet = true;
            $t['varianten']++;

            if (count($t['voorbeelden']) < 40) {
                $t['voorbeelden'][] = [
                    'cat'  => $plan['slugs'][0] ?? '',
                    'pct'  => $plan['pct'],
                    'naam' => $product->get_name(),
                    'van'  => (float) $regular,
                    'naar' => $nieuw,
                ];
            }
        }

        if (!$iets_gezet) {
            continue;
        }

        $t['producten']++;

        // In de Sale-collectie zetten, zodat die pagina klopt.
        if ($sale_term && !has_term($sale_term->term_id, 'product_cat', $id)) {
            wp_set_post_terms($id, [(int) $sale_term->term_id], 'product_cat', true);
            update_post_meta($id, DOM_BF_TERM, '1');
            $t['aan_sale_toegevoegd']++;
        }

        if ($product->is_type('variable')) {
            WC_Product_Variable::sync($id);
        }
        wc_delete_product_transients($id);
    }

    return $t;
}

/* ===================================================================
 *  Uitzetten
 * =================================================================== */

function dom_bf_terugdraaien($offset, $limit) {
    $ids = array_slice(dom_bf_product_ids(), $offset, $limit);
    $sale_term = get_term_by('slug', 'sale', 'product_cat');

    $t = dom_bf_lege_telling();

    foreach ($ids as $id) {
        $product = wc_get_product($id);
        if (!$product) {
            continue;
        }

        $iets_terug = false;

        foreach (dom_bf_prijsdragers($product) as $drager) {
            $post_id = $drager->get_id();
            $gezet   = get_post_meta($post_id, DOM_BF_GEZET, true);
            if ($gezet === '') {
                continue;
            }

            $huidig = $drager->get_sale_price('edit');

            /**
             * Staat er nu iets anders dan wat wij gezet hebben, dan heeft
             * iemand de prijs daarna met de hand aangepast. Die laten we staan.
             */
            if ((float) $huidig !== (float) $gezet) {
                $t['handmatig_gewijzigd']++;
                delete_post_meta($post_id, DOM_BF_GEZET);
                delete_post_meta($post_id, DOM_BF_VORIGE_SALE);
                continue;
            }

            $vorige = (string) get_post_meta($post_id, DOM_BF_VORIGE_SALE, true);
            $drager->set_sale_price($vorige);
            $drager->save();

            delete_post_meta($post_id, DOM_BF_GEZET);
            delete_post_meta($post_id, DOM_BF_VORIGE_SALE);

            $iets_terug = true;
            $t['varianten']++;
        }

        if (get_post_meta($id, DOM_BF_TERM, true) === '1') {
            if ($sale_term) {
                wp_remove_object_terms($id, (int) $sale_term->term_id, 'product_cat');
            }
            delete_post_meta($id, DOM_BF_TERM);
            $t['uit_sale_gehaald']++;
        }

        if ($iets_terug) {
            $t['producten']++;
            if ($product->is_type('variable')) {
                WC_Product_Variable::sync($id);
            }
            wc_delete_product_transients($id);
        }
    }

    return $t;
}

/* ===================================================================
 *  Proefdraaien: tellen zonder iets te wijzigen
 * =================================================================== */

function dom_bf_rapport($offset, $limit) {
    $ids = array_slice(dom_bf_product_ids(), $offset, $limit);
    $t   = dom_bf_lege_telling();

    foreach ($ids as $id) {
        $product = wc_get_product($id);
        if (!$product) {
            continue;
        }

        $plan = dom_bf_bepaal_korting($id);
        if ($plan['pct'] <= 0) {
            $t['overgeslagen']++;
            if ($plan['reden'] === 'tegenstrijdige categorieën') {
                $t['conflicten'][] = ['id' => $id, 'naam' => $product->get_name(), 'slugs' => $plan['slugs']];
            }
            continue;
        }

        $dragers = dom_bf_prijsdragers($product);
        if (!$dragers) {
            $t['overgeslagen']++;
            $t['anders'][] = ['id' => $id, 'naam' => $product->get_name(), 'reden' => 'producttype ' . $product->get_type()];
            continue;
        }

        $raakt = false;
        foreach ($dragers as $drager) {
            $regular = $drager->get_regular_price('edit');
            if ($regular === '' || (float) $regular <= 0) {
                $t['zonder_prijs']++;
                continue;
            }
            $nieuw = dom_bf_sale_prijs($regular, $plan['pct']);
            $huidige_sale = $drager->get_sale_price('edit');
            if ($huidige_sale !== '' && (float) $huidige_sale > 0 && (float) $huidige_sale <= $nieuw) {
                $t['al_lager']++;
                continue;
            }
            $raakt = true;
            $t['varianten']++;

            $cat = $plan['slugs'][0] ?? '';
            if (!isset($t['per_categorie'][$cat])) {
                $t['per_categorie'][$cat] = ['pct' => $plan['pct'], 'varianten' => 0, 'voorbeeld' => null];
            }
            $t['per_categorie'][$cat]['varianten']++;
            if ($t['per_categorie'][$cat]['voorbeeld'] === null) {
                $t['per_categorie'][$cat]['voorbeeld'] = [
                    'naam' => $product->get_name(),
                    'van'  => (float) $regular,
                    'naar' => $nieuw,
                ];
            }
        }

        if ($raakt) {
            $t['producten']++;
        }
    }

    return $t;
}

function dom_bf_lege_telling() {
    return [
        'producten'           => 0,
        'varianten'           => 0,
        'overgeslagen'        => 0,
        'zonder_prijs'        => 0,
        'al_lager'            => 0,
        'handmatig_gewijzigd' => 0,
        'aan_sale_toegevoegd' => 0,
        'uit_sale_gehaald'    => 0,
        'conflicten'          => [],
        'anders'              => [],
        'voorbeelden'         => [],
        'per_categorie'       => [],
    ];
}

/* ===================================================================
 *  REST
 * =================================================================== */

add_action('rest_api_init', function () {
    register_rest_route('dom/v1', '/black-friday', [
        'methods'             => 'POST',
        'callback'            => 'dom_bf_rest',
        'permission_callback' => function () {
            return current_user_can('manage_woocommerce') || current_user_can('manage_options');
        },
        'args' => [
            'actie'  => ['type' => 'string', 'required' => true],
            'offset' => ['type' => 'integer', 'default' => 0],
            'limit'  => ['type' => 'integer', 'default' => 25],
        ],
    ]);
});

function dom_bf_rest(WP_REST_Request $request) {
    if (!function_exists('wc_get_product')) {
        return new WP_REST_Response(['ok' => false, 'fout' => 'WooCommerce is niet actief'], 500);
    }

    @set_time_limit(300);

    $actie  = $request->get_param('actie');
    $offset = max(0, (int) $request->get_param('offset'));
    $limit  = min(100, max(1, (int) $request->get_param('limit')));
    $totaal = count(dom_bf_product_ids());

    switch ($actie) {
        case 'rapport':
            $t = dom_bf_rapport($offset, $limit);
            break;
        case 'aan':
            $t = dom_bf_toepassen($offset, $limit);
            break;
        case 'uit':
            $t = dom_bf_terugdraaien($offset, $limit);
            break;
        default:
            return new WP_REST_Response(['ok' => false, 'fout' => 'onbekende actie'], 400);
    }

    $volgende = $offset + $limit;
    $klaar    = $volgende >= $totaal;

    if ($klaar && $actie === 'aan') {
        update_option(DOM_BF_ACTIEF, '1');
    }
    if ($klaar && $actie === 'uit') {
        update_option(DOM_BF_ACTIEF, '');
    }

    return new WP_REST_Response([
        'ok'       => true,
        'actie'    => $actie,
        'totaal'   => $totaal,
        'offset'   => $offset,
        'volgende' => $klaar ? null : $volgende,
        'klaar'    => $klaar,
        'telling'  => $t,
    ], 200);
}
