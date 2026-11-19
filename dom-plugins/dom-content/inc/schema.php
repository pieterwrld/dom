<?php
/**
 * Het contentmodel.
 *
 * Alles wat beheerd kan worden staat hier één keer beschreven: label, type,
 * uitleg en de standaardwaarde. Het beheerscherm tekent zich uit dit schema en
 * de sjablonen lezen eruit, dus een nieuw veld toevoegen is één regel hier.
 *
 * Structuur: tabblad -> groep -> veld. Het pad "home.hero.cta" wijst dus naar
 * het knoplabel van de hero.
 */

defined('ABSPATH') || exit;

/**
 * @return array<string, array{titel:string, icoon:string, uitleg?:string, soort?:string, groepen?:array}>
 */
function dom_content_schema() {
    static $schema = null;
    if ($schema !== null) {
        return $schema;
    }

    $iconen = [
        'truck'     => 'Bezorgwagen',
        'shield'    => 'Schild',
        'check'     => 'Vinkje',
        'whatsapp'  => 'WhatsApp',
        'card'      => 'Betaalkaart',
        'return'    => 'Retourdoos',
        'star'      => 'Ster',
        'clock'     => 'Klok',
    ];

    $schema = [

        /* ─────────────── HOMEPAGE ─────────────── */
        'home' => [
            'titel'  => 'Homepage',
            'icoon'  => 'dashicons-admin-home',
            'uitleg' => 'De secties van de voorpagina, van boven naar beneden.',
            'groepen' => [

                'hero' => [
                    'titel'  => 'Hero',
                    'uitleg' => 'Staat er een Black Friday-banner, dan is dát de hero en wordt de video eronder niet gebruikt. Laat beide banners leeg om terug te vallen op de video-hero.',
                    'velden' => [
                        'banner_mobile' => [
                            'type'  => 'image',
                            'label' => 'Black Friday-banner (mobiel)',
                            'help'  => 'Staand beeld. De banner draagt zijn eigen tekst, er komt geen kop overheen.',
                        ],
                        'banner_desktop' => [
                            'type'  => 'image',
                            'label' => 'Black Friday-banner (desktop)',
                            'help'  => 'Liggend beeld, wordt getoond vanaf 761px breed.',
                        ],
                        'cta' => [
                            'type'    => 'text',
                            'label'   => 'Tekst op de knop',
                            'default' => 'Shop alle collecties',
                        ],
                        'cta_link' => [
                            'type'  => 'url',
                            'label' => 'Link van de knop',
                            'help'  => 'Leeg laten = naar de shoppagina.',
                        ],
                        'video' => [
                            'type'  => 'video',
                            'label' => 'Hero-video',
                            'help'  => 'MP4. Alleen gebruikt als er geen banner staat.',
                        ],
                        'image' => [
                            'type'  => 'image',
                            'label' => 'Hero-afbeelding',
                            'help'  => 'Ook de poster van de video. Liggend, minimaal 1920px breed.',
                        ],
                        'titel_1' => [
                            'type'    => 'text',
                            'label'   => 'Kop, eerste regel',
                            'default' => 'Nr. 1 in merkkleding',
                            'help'    => 'Alleen zichtbaar op de video-hero.',
                        ],
                        'titel_2' => [
                            'type'    => 'text',
                            'label'   => 'Kop, tweede regel',
                            'default' => 'Voor elke man',
                        ],
                    ],
                ],

                'bf' => [
                    'titel'  => 'Black Friday-balk',
                    'uitleg' => 'De zwarte balk met de aftelklok onder de header.',
                    'velden' => [
                        'aan' => [
                            'type'    => 'toggle',
                            'label'   => 'Balk tonen',
                            'default' => true,
                        ],
                        'label' => [
                            'type'    => 'text',
                            'label'   => 'Tekst links',
                            'default' => 'BLACK FRIDAY',
                        ],
                        'claim' => [
                            'type'    => 'text',
                            'label'   => 'Kortingszin',
                            'help'    => 'Leeg laten = de hoogste korting wordt uit de echte productprijzen berekend. Vul je hier iets in, controleer dan zelf of het klopt met de prijzen in de winkel.',
                        ],
                        'einde' => [
                            'type'    => 'datetime',
                            'label'   => 'Afteller loopt tot',
                            'default' => '2026-11-27T23:59:59',
                        ],
                        'knop' => [
                            'type'    => 'text',
                            'label'   => 'Tekst op de knop',
                            'default' => 'SHOP NU',
                        ],
                    ],
                ],

                'marquee' => [
                    'titel'  => 'Aankondigingsbalk',
                    'uitleg' => 'De doorlopende regel bovenaan elke pagina.',
                    'velden' => [
                        'berichten' => [
                            'type'      => 'repeater',
                            'label'     => 'Berichten',
                            'knop'      => 'Bericht toevoegen',
                            'rij_label' => 'tekst',
                            'velden'    => [
                                'tekst'     => ['type' => 'text', 'label' => 'Tekst'],
                                'uitgelicht'=> ['type' => 'toggle', 'label' => 'Rood uitlichten'],
                            ],
                            'default' => [
                                ['tekst' => 'Black Friday deals', 'uitgelicht' => true],
                                ['tekst' => 'Voor 17:00 besteld = morgen in huis', 'uitgelicht' => false],
                                ['tekst' => 'Gratis verzending vanaf €220', 'uitgelicht' => false],
                                ['tekst' => '24/7 klantenservice via WhatsApp', 'uitgelicht' => false],
                                ['tekst' => 'Vanaf €350 gratis mystery shirt', 'uitgelicht' => false],
                                ['tekst' => '14 dagen gratis retourneren', 'uitgelicht' => false],
                            ],
                        ],
                    ],
                ],

                'trust' => [
                    'titel'  => 'Beloftebalk onder de hero',
                    'uitleg' => 'De rij die onder de banner langs schuift.',
                    'velden' => [
                        'items' => [
                            'type'      => 'repeater',
                            'label'     => 'Beloftes',
                            'knop'      => 'Belofte toevoegen',
                            'rij_label' => 'tekst',
                            'velden'    => [
                                'icoon' => ['type' => 'select', 'label' => 'Icoon', 'options' => $iconen],
                                'tekst' => ['type' => 'text', 'label' => 'Tekst'],
                            ],
                            'default' => [
                                ['icoon' => 'truck',    'tekst' => 'Voor 17:00 besteld, morgen in huis'],
                                ['icoon' => 'shield',   'tekst' => 'Nr. 1 in kwaliteit'],
                                ['icoon' => 'check',    'tekst' => 'Voor verzending nagekeken'],
                                ['icoon' => 'whatsapp', 'tekst' => 'Persoonlijk advies, 24/7 via WhatsApp'],
                                ['icoon' => 'truck',    'tekst' => 'Gratis verzending vanaf €220'],
                            ],
                        ],
                    ],
                ],

                'collecties' => [
                    'titel'  => 'Collectieraster',
                    'velden' => [
                        'eyebrow' => ['type' => 'text', 'label' => 'Bovenschrift', 'default' => 'Collecties'],
                        'titel'   => ['type' => 'text', 'label' => 'Kop', 'default' => 'Shop per categorie'],
                        'link'    => ['type' => 'text', 'label' => 'Tekst van de link rechts', 'default' => 'Alle producten'],
                    ],
                ],

                'accessoires' => [
                    'titel'  => 'Accessoireblok',
                    'uitleg' => 'De band met bril, pet en tas. De foto\'s komen van de categorieën zelf, die stel je in op het tabblad Categorieën.',
                    'velden' => [
                        'titel'  => ['type' => 'text', 'label' => 'Kop', 'default' => 'Maak je look compleet'],
                        'items'  => [
                            'type'      => 'repeater',
                            'label'     => 'Categorieën in dit blok',
                            'knop'      => 'Categorie toevoegen',
                            'rij_label' => 'slug',
                            'velden'    => [
                                'slug' => ['type' => 'categorie', 'label' => 'Categorie'],
                            ],
                            'default' => [
                                ['slug' => 'brillen'],
                                ['slug' => 'petten'],
                                ['slug' => 'tassen'],
                            ],
                        ],
                    ],
                ],

                'klokjesman' => [
                    'titel'  => 'De Klokjesman',
                    'velden' => [
                        'afbeelding' => ['type' => 'image', 'label' => 'Foto'],
                        'titel_voor'  => ['type' => 'text', 'label' => 'Kop, deel 1', 'default' => 'De'],
                        'titel_accent'=> ['type' => 'text', 'label' => 'Kop, deel 2 (zilver)', 'default' => 'Klokjes'],
                        'titel_na'    => ['type' => 'text', 'label' => 'Kop, deel 3', 'default' => 'man'],
                        'knop' => ['type' => 'text', 'label' => 'Tekst op de knop', 'default' => 'Bekijk horloges'],
                        'link' => ['type' => 'url', 'label' => 'Link', 'default' => 'https://deklokjesman.com/'],
                    ],
                ],

                'reviews' => [
                    'titel'  => 'Reviewsectie',
                    'uitleg' => 'De koppen boven de screenshots. De screenshots zelf staan op het tabblad Reviews.',
                    'velden' => [
                        'eyebrow' => ['type' => 'text', 'label' => 'Bovenschrift', 'default' => 'Reviews'],
                        'titel'   => ['type' => 'text', 'label' => 'Kop', 'default' => 'Wat onze klanten zeggen'],
                        'subtitel'=> [
                            'type'    => 'text',
                            'label'   => 'Zin eronder',
                            'default' => '%s echte gesprekken, rechtstreeks uit WhatsApp',
                            'help'    => '%s wordt vervangen door het aantal screenshots.',
                        ],
                        'knop'    => ['type' => 'text', 'label' => 'Tekst op de knop', 'default' => 'Stuur ons een bericht'],
                    ],
                ],

                'ugc' => [
                    'titel'  => 'Gedragen door',
                    'uitleg' => 'De koppen boven de videoslider. De video\'s zelf staan op het tabblad Gedragen door.',
                    'velden' => [
                        'eyebrow' => ['type' => 'text', 'label' => 'Bovenschrift', 'default' => 'Community'],
                        'titel'   => ['type' => 'text', 'label' => 'Kop', 'default' => 'Gedragen door'],
                        'tekst'   => ['type' => 'text', 'label' => 'Zin eronder', 'default' => 'Ontdek De Outfitman door de ogen van onze creators.'],
                    ],
                ],

                'usp' => [
                    'titel'  => 'Vier beloftes (blokken)',
                    'velden' => [
                        'items' => [
                            'type'      => 'repeater',
                            'label'     => 'Blokken',
                            'knop'      => 'Blok toevoegen',
                            'rij_label' => 'titel',
                            'velden'    => [
                                'icoon' => ['type' => 'select', 'label' => 'Icoon', 'options' => $iconen],
                                'titel' => ['type' => 'text', 'label' => 'Kop'],
                                'tekst' => ['type' => 'textarea', 'label' => 'Tekst'],
                            ],
                            'default' => [
                                ['icoon' => 'shield',   'titel' => 'Kwaliteitsgarantie', 'tekst' => 'Elk product wordt voor verzending gecontroleerd. Topkwaliteit, zonder compromis.'],
                                ['icoon' => 'truck',    'titel' => 'Morgen in huis',     'tekst' => 'Voor 17:00 besteld, morgen bezorgd. Gratis verzending vanaf €220.'],
                                ['icoon' => 'card',     'titel' => 'Veilig betalen',     'tekst' => 'iDEAL, Bancontact, creditcard en Apple Pay — altijd via een beveiligde omgeving.'],
                                ['icoon' => 'whatsapp', 'titel' => '24/7 WhatsApp',      'tekst' => 'Vragen over maten, bestellingen of advies? We staan altijd voor je klaar.'],
                            ],
                        ],
                    ],
                ],
            ],
        ],

        /* ─────────────── REVIEWS ─────────────── */
        'reviews' => [
            'titel'  => 'Reviews',
            'icoon'  => 'dashicons-format-chat',
            'uitleg' => 'De WhatsApp-screenshots op de homepage. Crop telefoonnummers weg vóór je uploadt: de foto komt op de site zoals je hem hier zet. Haal je alle rijen weg, dan toont de site weer de screenshots die met het thema meekomen.',
            'groepen' => [
                'lijst' => [
                    'titel'  => 'Screenshots',
                    'velden' => [
                        'items' => [
                            'type'      => 'repeater',
                            'label'     => '',
                            'knop'      => 'Review toevoegen',
                            'rij_label' => 'naam',
                            'compact'   => true,
                            'velden'    => [
                                'afbeelding' => ['type' => 'image', 'label' => 'Screenshot'],
                                'naam'       => ['type' => 'text', 'label' => 'Naam erbij', 'help' => 'Leeg laten = "De Outfitman".'],
                            ],
                        ],
                    ],
                ],
            ],
        ],

        /* ─────────────── GEDRAGEN DOOR ─────────────── */
        'ugc' => [
            'titel'  => 'Gedragen door',
            'icoon'  => 'dashicons-video-alt3',
            'uitleg' => 'De verticale video\'s in de slider. Staand formaat (9:16) past het beste. Haal je alle rijen weg, dan vallen we terug op de clips die al in de mediabibliotheek staan.',
            'groepen' => [
                'lijst' => [
                    'titel'  => 'Video\'s',
                    'velden' => [
                        'items' => [
                            'type'      => 'repeater',
                            'label'     => '',
                            'knop'      => 'Video toevoegen',
                            'rij_label' => 'naam',
                            'compact'   => true,
                            'velden'    => [
                                'video'  => ['type' => 'video', 'label' => 'Video (MP4)'],
                                'poster' => ['type' => 'image', 'label' => 'Stilstaand beeld', 'help' => 'Optioneel. Wordt getoond voordat de video speelt.'],
                                'naam'   => ['type' => 'text', 'label' => 'Naam'],
                                'sub'    => ['type' => 'text', 'label' => 'Tekst eronder', 'help' => 'Leeg laten = "Shop de look".'],
                                'link'   => ['type' => 'url', 'label' => 'Link', 'help' => 'Leeg laten = naar de shoppagina.'],
                            ],
                        ],
                    ],
                ],
            ],
        ],

        /* ─────────────── CATEGORIEËN ─────────────── */
        'categorieen' => [
            'titel'  => 'Categorieën',
            'icoon'  => 'dashicons-category',
            'soort'  => 'categorieen',
            'uitleg' => 'Per categorie de foto in het raster, het plaatje in het menu en de banner bovenaan de categoriepagina.',
        ],

        /* ─────────────── BLACK FRIDAY ─────────────── */
        'blackfriday' => [
            'titel'  => 'Black Friday',
            'icoon'  => 'dashicons-tickets-alt',
            'soort'  => 'black-friday',
            'uitleg' => 'De actie met één knop aan en uit. Aanzetten geeft elk product in de categorieën hieronder een echte aanbiedingsprijs; de normale prijs blijft als vergelijkingsprijs staan en wordt doorgestreept getoond.',
        ],

        /* ─────────────── PRODUCTPAGINA ─────────────── */
        'product' => [
            'titel'  => 'Productpagina',
            'icoon'  => 'dashicons-tag',
            'uitleg' => 'Teksten die op elke productpagina staan. Wat per product verschilt (omschrijving, prijs, voorraad) blijft gewoon in WooCommerce.',
            'groepen' => [
                'blokken' => [
                    'titel'  => 'Uitklapteksten',
                    'velden' => [
                        'maatadvies' => [
                            'type'    => 'richtext',
                            'label'   => 'Maatadvies',
                            'default' => 'Dit model valt op maat. Zit je tussen twee maten? Kies dan de grotere maat. Of stuur ons je gebruikelijke maat via WhatsApp, dan adviseren wij je persoonlijk.',
                            'help'    => 'Zet je per product iets anders in het veld Maatadvies, dan gaat dat voor.',
                        ],
                        'verzending' => [
                            'type'    => 'richtext',
                            'label'   => 'Verzending & retourneren',
                            'default' => '<p><b>Verzending.</b> Voor 17:00 besteld is doorgaans de volgende dag in huis, uiterlijk binnen 2 dagen. Gratis verzending vanaf €220. Je ontvangt altijd een trackingcode.</p><p><b>Retourneren.</b> Niet goed? Je hebt 14 dagen om te ruilen of te retourneren, zolang het product ongebruikt is en in de originele verpakking zit. Meld je retour aan via WhatsApp en je hoort binnen enkele minuten wat je moet doen.</p>',
                        ],
                        'geen_details' => [
                            'type'    => 'text',
                            'label'   => 'Tekst als een product geen omschrijving heeft',
                            'default' => 'Geen extra productinformatie beschikbaar.',
                        ],
                    ],
                ],
                'levering' => [
                    'titel'  => 'Belofte onder de knop',
                    'velden' => [
                        'vraag'   => ['type' => 'text', 'label' => 'Tekst', 'default' => 'Vandaag besteld?'],
                        'antwoord'=> ['type' => 'text', 'label' => 'Vet gedeelte', 'default' => 'Morgen in huis'],
                        'maathulp'=> ['type' => 'text', 'label' => 'Regel bij WhatsApp', 'default' => 'Twijfel over je maat?'],
                        'maatlink'=> ['type' => 'text', 'label' => 'Linktekst', 'default' => 'Vraag het via WhatsApp'],
                    ],
                ],
                'faq' => [
                    'titel'  => 'Veelgestelde vragen',
                    'velden' => [
                        'items' => [
                            'type'      => 'repeater',
                            'label'     => '',
                            'knop'      => 'Vraag toevoegen',
                            'rij_label' => 'vraag',
                            'velden'    => [
                                'vraag'    => ['type' => 'text', 'label' => 'Vraag'],
                                'antwoord' => ['type' => 'textarea', 'label' => 'Antwoord'],
                            ],
                            'default' => [
                                ['vraag' => 'Hoe snel wordt mijn bestelling bezorgd?', 'antwoord' => 'Voor 17:00 besteld is doorgaans de volgende dag in huis, uiterlijk binnen 2 dagen. Je ontvangt altijd een trackingcode.'],
                                ['vraag' => 'Kan ik ruilen of retourneren?', 'antwoord' => 'Je hebt 14 dagen om te ruilen of retourneren, zolang het product ongebruikt en in de originele verpakking is. Meld je retour aan via WhatsApp.'],
                                ['vraag' => 'Welke maat moet ik kiezen?', 'antwoord' => 'Dit model valt op maat. Zit je tussen twee maten? Kies de grotere maat. Of stuur ons je gebruikelijke maat via WhatsApp, dan adviseren wij je persoonlijk.'],
                                ['vraag' => 'Hoe kan ik betalen?', 'antwoord' => 'iDEAL, Bancontact, creditcard en Apple Pay. Alle betalingen lopen via een beveiligde omgeving.'],
                                ['vraag' => 'Zit alles erbij?', 'antwoord' => 'Ja. Elk paar komt met alle toebehoren en een extra set veters. Wij controleren elk product voor verzending.'],
                                ['vraag' => 'Hoe bereik ik de klantenservice?', 'antwoord' => '24/7 via WhatsApp op 06-16264544. Gemiddelde reactietijd is onder 5 minuten.'],
                            ],
                        ],
                    ],
                ],
            ],
        ],

        /* ─────────────── WINKELWAGEN ─────────────── */
        'winkelwagen' => [
            'titel'  => 'Winkelwagen & checkout',
            'icoon'  => 'dashicons-cart',
            'groepen' => [
                'drempels' => [
                    'titel'  => 'Voortgangsbalk',
                    'uitleg' => 'De bedragen die klanten halen om iets gratis te krijgen. Het bedrag voor gratis verzending staat ook in WooCommerce bij Verzending; pas je het hier aan, pas het daar dan ook aan, anders beloven de balk en de afrekenpagina iets anders.',
                    'velden' => [
                        'verzending'       => ['type' => 'number', 'label' => 'Gratis verzending vanaf', 'default' => 220],
                        'verzending_label' => ['type' => 'text', 'label' => 'Label', 'default' => 'Gratis verzending'],
                        'cadeau'           => ['type' => 'number', 'label' => 'Gratis cadeau vanaf', 'default' => 350],
                        'cadeau_label'     => ['type' => 'text', 'label' => 'Label', 'default' => 'Gratis mystery shirt'],
                    ],
                ],
                'teksten' => [
                    'titel'  => 'Teksten',
                    'velden' => [
                        'titel'   => ['type' => 'text', 'label' => 'Kop van de lade', 'default' => 'Winkelwagen'],
                        'knop'    => ['type' => 'text', 'label' => 'Tekst op de afrekenknop', 'default' => 'Afrekenen'],
                        'leeg'    => ['type' => 'text', 'label' => 'Tekst bij een lege winkelwagen', 'default' => 'Je winkelwagen is nog leeg.'],
                        'trust'   => ['type' => 'text', 'label' => 'Regel onder de knop', 'default' => '1.200+ tevreden klanten'],
                    ],
                ],
            ],
        ],

        /* ─────────────── FOOTER ─────────────── */
        'footer' => [
            'titel'  => 'Footer',
            'icoon'  => 'dashicons-align-wide',
            'groepen' => [
                'trust' => [
                    'titel'  => 'Beloftebalk',
                    'velden' => [
                        'klanten'       => ['type' => 'text', 'label' => 'Aantal klanten', 'default' => '1.200+'],
                        'klanten_label' => ['type' => 'text', 'label' => 'Tekst erachter', 'default' => 'Tevreden klanten'],
                        'items' => [
                            'type'      => 'repeater',
                            'label'     => 'Overige beloftes',
                            'knop'      => 'Belofte toevoegen',
                            'rij_label' => 'tekst',
                            'velden'    => [
                                'icoon' => ['type' => 'select', 'label' => 'Icoon', 'options' => $iconen],
                                'tekst' => ['type' => 'text', 'label' => 'Tekst'],
                            ],
                            'default' => [
                                ['icoon' => 'shield', 'tekst' => 'Veilig betalen'],
                                ['icoon' => 'truck',  'tekst' => 'Snelle levering'],
                                ['icoon' => 'whatsapp', 'tekst' => '24/7 bereikbaar'],
                            ],
                        ],
                    ],
                ],
                'kolom_1' => [
                    'titel'  => 'Kolom 1',
                    'velden' => [
                        'kop'   => ['type' => 'text', 'label' => 'Kop', 'default' => 'Klantenservice'],
                        'links' => [
                            'type'      => 'repeater',
                            'label'     => 'Links',
                            'knop'      => 'Link toevoegen',
                            'rij_label' => 'tekst',
                            'velden'    => [
                                'tekst' => ['type' => 'text', 'label' => 'Tekst'],
                                'url'   => ['type' => 'url', 'label' => 'Link'],
                            ],
                            'default' => [
                                ['tekst' => 'Contact', 'url' => '/contact'],
                                ['tekst' => 'Veelgestelde vragen', 'url' => '/faq'],
                                ['tekst' => 'Verzending & retourneren', 'url' => '/verzending-retourneren'],
                                ['tekst' => 'Mijn account', 'url' => '/my-account'],
                            ],
                        ],
                    ],
                ],
                'kolom_2' => [
                    'titel'  => 'Kolom 2',
                    'velden' => [
                        'kop'   => ['type' => 'text', 'label' => 'Kop', 'default' => 'Over ons'],
                        'links' => [
                            'type'      => 'repeater',
                            'label'     => 'Links',
                            'knop'      => 'Link toevoegen',
                            'rij_label' => 'tekst',
                            'velden'    => [
                                'tekst' => ['type' => 'text', 'label' => 'Tekst'],
                                'url'   => ['type' => 'url', 'label' => 'Link'],
                            ],
                            'default' => [
                                ['tekst' => 'Ons verhaal', 'url' => '/over-ons'],
                                ['tekst' => 'De Klokjesman', 'url' => 'https://deklokjesman.com'],
                            ],
                        ],
                        'tiktok'    => ['type' => 'url', 'label' => 'TikTok', 'default' => 'https://tiktok.com/@deoutfitman'],
                    ],
                ],
            ],
        ],

        /* ─────────────── ALGEMEEN ─────────────── */
        'algemeen' => [
            'titel'  => 'Algemeen',
            'icoon'  => 'dashicons-admin-generic',
            'groepen' => [
                'merk' => [
                    'titel'  => 'Merk',
                    'velden' => [
                        'logo' => ['type' => 'image', 'label' => 'Logo in de header', 'help' => 'Leeg laten = de woordmerktekst.'],
                    ],
                ],
                'contact' => [
                    'titel'  => 'Contact',
                    'velden' => [
                        'whatsapp' => [
                            'type'    => 'text',
                            'label'   => 'WhatsApp-nummer',
                            'default' => '31616264544',
                            'help'    => 'Zonder plus of spaties, met landnummer.',
                        ],
                        'whatsapp_zichtbaar' => ['type' => 'text', 'label' => 'Nummer zoals het getoond wordt', 'default' => '06-16264544'],
                    ],
                ],
            ],
        ],
    ];

    /**
     * Zodat een latere uitbreiding (of een kindthema) velden kan toevoegen
     * zonder dit bestand aan te passen.
     */
    $schema = apply_filters('dom_content_schema', $schema);

    return $schema;
}
