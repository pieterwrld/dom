<?php
/**
 * ACF Field Registration (fallback if ACF JSON is not loaded)
 *
 * @package DomTheme
 */

defined('ABSPATH') || exit;

if (!function_exists('acf_add_local_field_group')) return;

/* ─── Homepage Hero ──────────────────────────────────── */
acf_add_local_field_group([
    'key'      => 'group_dom_hero',
    'title'    => 'Homepage Hero',
    'location' => [[[
        'param'    => 'page_type',
        'operator' => '==',
        'value'    => 'front_page',
    ]]],
    'fields'   => [
        [
            'key'   => 'field_hero_video',
            'label' => 'Hero Video',
            'name'  => 'hero_video',
            'type'  => 'file',
            'return_format' => 'url',
            'mime_types'    => 'mp4,webm',
            'instructions'  => 'Upload de hero video (MP4 of WebM).',
        ],
        [
            'key'   => 'field_hero_fallback',
            'label' => 'Hero Fallback Afbeelding',
            'name'  => 'hero_fallback_image',
            'type'  => 'image',
            'return_format' => 'url',
            'preview_size'  => 'medium',
            'instructions'  => 'Wordt getoond als video niet beschikbaar is.',
        ],
        [
            'key'   => 'field_hero_heading',
            'label' => 'Hero Heading',
            'name'  => 'hero_heading',
            'type'  => 'text',
            'default_value' => 'BLACK FRIDAY',
        ],
        [
            'key'   => 'field_hero_subheading',
            'label' => 'Hero Subheading',
            'name'  => 'hero_subheading',
            'type'  => 'text',
            'default_value' => 'Black Friday deals',
        ],
        [
            'key'   => 'field_hero_cta_text',
            'label' => 'CTA Tekst',
            'name'  => 'hero_cta_text',
            'type'  => 'text',
            'default_value' => 'SHOP ALLE COLLECTIES',
        ],
        [
            'key'   => 'field_hero_cta_link',
            'label' => 'CTA Link',
            'name'  => 'hero_cta_link',
            'type'  => 'url',
            'default_value' => '/shop/',
        ],
    ],
]);

/* ─── Homepage Sections ──────────────────────────────── */
acf_add_local_field_group([
    'key'      => 'group_dom_homepage',
    'title'    => 'Homepage Secties',
    'location' => [[[
        'param'    => 'page_type',
        'operator' => '==',
        'value'    => 'front_page',
    ]]],
    'fields'   => [
        [
            'key'   => 'field_accessories_image',
            'label' => 'Accessories Banner Afbeelding',
            'name'  => 'accessories_banner_image',
            'type'  => 'image',
            'return_format' => 'url',
            'preview_size'  => 'large',
        ],
        [
            'key'   => 'field_klokjesman_images',
            'label' => 'De Klokjesman Afbeeldingen',
            'name'  => 'klokjesman_images',
            'type'  => 'gallery',
            'return_format' => 'url',
            'preview_size'  => 'medium',
            'min'   => 1,
            'max'   => 10,
        ],
        [
            'key'   => 'field_influencers',
            'label' => 'Influencers',
            'name'  => 'influencers',
            'type'  => 'repeater',
            'sub_fields' => [
                [
                    'key'   => 'field_inf_name',
                    'label' => 'Naam',
                    'name'  => 'name',
                    'type'  => 'text',
                ],
                [
                    'key'   => 'field_inf_handle',
                    'label' => 'Handle',
                    'name'  => 'handle',
                    'type'  => 'text',
                    'prepend' => '@',
                ],
                [
                    'key'   => 'field_inf_video',
                    'label' => 'Video Thumbnail',
                    'name'  => 'video_thumbnail',
                    'type'  => 'image',
                    'return_format' => 'url',
                    'preview_size'  => 'medium',
                ],
                [
                    'key'   => 'field_inf_video_url',
                    'label' => 'Video URL',
                    'name'  => 'video_url',
                    'type'  => 'url',
                ],
            ],
        ],
    ],
]);

/* ─── Category Banner (taxonomy) ─────────────────────── */
acf_add_local_field_group([
    'key'      => 'group_dom_cat_banner',
    'title'    => 'Categorie Banner',
    'location' => [[[
        'param'    => 'taxonomy',
        'operator' => '==',
        'value'    => 'product_cat',
    ]]],
    'fields'   => [
        [
            'key'   => 'field_cat_banner',
            'label' => 'Banner Afbeelding',
            'name'  => 'category_banner',
            'type'  => 'image',
            'return_format' => 'url',
            'preview_size'  => 'large',
            'instructions'  => 'Horizontaal/landscape formaat. Wordt getoond tussen filters en product grid.',
        ],
    ],
]);

/* ─── Product Extra Fields ───────────────────────────── */
acf_add_local_field_group([
    'key'      => 'group_dom_product',
    'title'    => 'Product Extra Velden',
    'location' => [[[
        'param'    => 'post_type',
        'operator' => '==',
        'value'    => 'product',
    ]]],
    'fields'   => [
        [
            'key'   => 'field_color_siblings',
            'label' => 'Kleurvarianten (andere producten)',
            'name'  => 'color_siblings',
            'type'  => 'relationship',
            'post_type' => ['product'],
            'return_format' => 'id',
            'min'   => 0,
            'max'   => 12,
            'instructions' => 'Selecteer de andere kleurvarianten van dit product. Elk kleur is een apart product.',
        ],
        [
            'key'   => 'field_color_hex',
            'label' => 'Kleurcode',
            'name'  => 'product_color_hex',
            'type'  => 'color_picker',
            'default_value' => '#000000',
            'instructions'  => 'De kleur van dit product (wordt getoond als swatch).',
        ],
        [
            'key'   => 'field_color_name',
            'label' => 'Kleurnaam',
            'name'  => 'product_color_name',
            'type'  => 'text',
            'instructions'  => 'Bijv. "Zwart", "Navy", "Wit".',
        ],
        [
            'key'   => 'field_size_advice',
            'label' => 'Maatadvies',
            'name'  => 'size_advice',
            'type'  => 'wysiwyg',
            'toolbar' => 'basic',
            'media_upload' => 0,
            'instructions' => 'Maatadvies specifiek voor dit product. Laat leeg voor categorie-standaard.',
        ],
        [
            'key'   => 'field_bf_deal_text',
            'label' => 'Black Friday Deal Tekst',
            'name'  => 'bf_deal_text',
            'type'  => 'text',
            'instructions' => 'Bijv. "3 VOOR €130". Laat leeg als dit product geen BF-deal heeft.',
        ],
    ],
]);

/* ─── Size Advice per Category ───────────────────────── */
acf_add_local_field_group([
    'key'      => 'group_dom_cat_size',
    'title'    => 'Maatadvies per Categorie',
    'location' => [[[
        'param'    => 'taxonomy',
        'operator' => '==',
        'value'    => 'product_cat',
    ]]],
    'fields'   => [
        [
            'key'   => 'field_cat_size_advice',
            'label' => 'Standaard Maatadvies',
            'name'  => 'category_size_advice',
            'type'  => 'wysiwyg',
            'toolbar' => 'basic',
            'media_upload' => 0,
            'instructions' => 'Wordt getoond op productpagina als het product geen eigen maatadvies heeft.',
        ],
    ],
]);

/* ─── Theme Options Page ─────────────────────────────── */
if (function_exists('acf_add_options_page')) {
    acf_add_options_page([
        'page_title' => 'De Outfitman Instellingen',
        'menu_title' => 'DOM Instellingen',
        'menu_slug'  => 'dom-settings',
        'capability' => 'manage_options',
        'icon_url'   => 'dashicons-admin-customizer',
        'position'   => 59,
    ]);

    acf_add_local_field_group([
        'key'      => 'group_dom_options',
        'title'    => 'Site-brede Instellingen',
        'location' => [[[
            'param'    => 'options_page',
            'operator' => '==',
            'value'    => 'dom-settings',
        ]]],
        'fields'   => [
            [
                'key'   => 'field_whatsapp_number',
                'label' => 'WhatsApp Nummer',
                'name'  => 'whatsapp_number',
                'type'  => 'text',
                'default_value' => '31612345678',
                'instructions'  => 'Zonder + teken. Bijv. 31612345678',
            ],
            [
                'key'   => 'field_whatsapp_message',
                'label' => 'WhatsApp Standaard Bericht',
                'name'  => 'whatsapp_default_message',
                'type'  => 'text',
                'default_value' => 'Hoi! Ik heb een vraag over',
            ],
            [
                'key'   => 'field_free_shipping_min',
                'label' => 'Gratis Verzending Minimum',
                'name'  => 'free_shipping_minimum',
                'type'  => 'number',
                'default_value' => 200,
                'prepend' => '€',
            ],
            [
                'key'   => 'field_mystery_shirt_min',
                'label' => 'Mystery Shirt Minimum',
                'name'  => 'mystery_shirt_minimum',
                'type'  => 'number',
                'default_value' => 350,
                'prepend' => '€',
            ],
            [
                'key'   => 'field_bf_active',
                'label' => 'Black Friday Actief',
                'name'  => 'bf_active',
                'type'  => 'true_false',
                'default_value' => 0,
                'ui' => 1,
                'instructions' => 'Zet aan om Black Friday mode te activeren op de hele site.',
            ],
            [
                'key'   => 'field_bf_end_date',
                'label' => 'Black Friday Einddatum',
                'name'  => 'bf_end_date',
                'type'  => 'date_time_picker',
                'display_format' => 'd/m/Y H:i',
                'return_format' => 'Y-m-d H:i:s',
            ],
            [
                'key'   => 'field_shipping_returns',
                'label' => 'Verzending & Retournering Tekst',
                'name'  => 'shipping_returns_text',
                'type'  => 'wysiwyg',
                'toolbar' => 'basic',
                'media_upload' => 0,
                'default_value' => '<p><strong>Verzending</strong><br>Gratis verzending vanaf €200. Onder €200 bedragen de verzendkosten €5,95. Bestellingen voor 17:00 worden dezelfde dag verzonden.</p><p><strong>Retourneren</strong><br>Niet tevreden? Retourneer je bestelling binnen 14 dagen gratis. Stuur een bericht via WhatsApp en wij regelen het voor je.</p>',
            ],
            [
                'key'   => 'field_newsletter_heading',
                'label' => 'Newsletter Heading',
                'name'  => 'newsletter_heading',
                'type'  => 'text',
                'default_value' => 'BLIJF OP DE HOOGTE',
            ],
            [
                'key'   => 'field_newsletter_text',
                'label' => 'Newsletter Tekst',
                'name'  => 'newsletter_text',
                'type'  => 'text',
                'default_value' => 'Schrijf je in en ontvang als eerste onze drops en deals.',
            ],
        ],
    ]);
}
