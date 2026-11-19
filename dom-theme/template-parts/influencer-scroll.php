<?php
defined('ABSPATH') || exit;

/**
 * "Gedragen door": coverflow met video's.
 *
 * De kaarten zijn 9:16, dus de verticale clips passen precies. Alleen de
 * actieve kaart speelt af; de rest staat stil, anders decodeert een telefoon
 * vijf video's tegelijk.
 *
 * Namen horen bij hun eigen clip. Een eerdere versie zette namen van echte
 * personen onder willekeurig beeld, dat gebeurt hier niet: staat een bestand
 * er niet, dan valt die kaart weg.
 */

$clips = [];

/**
 * Het beheerscherm gaat voor: De Outfitman > Gedragen door. Staat daar nog
 * niets, dan vallen we terug op de clips die al in de mediabibliotheek staan.
 */
foreach (dom_c_ugc() as $rij) {
    $clips[] = [
        'name'   => $rij['naam'] ?: 'De Outfitman',
        'sub'    => $rij['sub'] ?: __('Shop de look', 'dom-theme'),
        'src'    => $rij['src'],
        'poster' => $rij['poster'],
        'link'   => $rij['link'],
    ];
}

if (!$clips) {
    $uploads  = wp_get_upload_dir();
    $base_url = $uploads['baseurl'] ?? '';
    $base_dir = $uploads['basedir'] ?? '';

    foreach ([
        ['Furkan',                      'Shop de look', '/2026/11/Furkan-DKM.mp4'],
        ['Fato YT',                     'Shop de look', '/2026/11/DOM-FATO.mp4'],
        ['Essentials Trainingspakken',  'Shop de look', '/2026/11/DOM-VIDEO4.mp4'],
        ['De Outfitman',                'Shop de look', '/2026/11/DOM-Naam.mp4'],
        ['De Outfitman',                'Shop de look', '/2026/11/DOM-ALL.mp4'],
    ] as [$name, $sub, $file]) {
        if (!$base_url || !file_exists($base_dir . $file)) {
            continue;
        }
        $clips[] = ['name' => $name, 'sub' => $sub, 'src' => $base_url . $file, 'poster' => '', 'link' => ''];
    }
}

if (!$clips) {
    return;
}

$dom_cover_shop = dom_shop_url();
?>
<section class="sec cover-sec" id="wornby">
  <div class="wrap">
    <div class="cover-head" data-rv="up">
      <div class="eyebrow"><i></i><span class="lbl"><?php echo esc_html(dom_c('home.ugc.eyebrow', __('Community', 'dom-theme'))); ?></span></div>
      <h2 class="split"><?php echo esc_html(dom_c('home.ugc.titel', __('Gedragen door', 'dom-theme'))); ?></h2>
      <p class="tx"><?php echo esc_html(dom_c('home.ugc.tekst', __('Ontdek De Outfitman door de ogen van onze creators.', 'dom-theme'))); ?></p>
    </div>
  </div>

  <div class="cover" id="cover">
    <?php foreach ($clips as $clip) : ?>
      <div class="cv" data-name="<?php echo esc_attr($clip['name']); ?>" data-sub="<?php echo esc_attr($clip['sub']); ?>">
        <video src="<?php echo esc_url($clip['src']); ?>" muted loop playsinline preload="metadata"
               <?php if (!empty($clip['poster'])) : ?>poster="<?php echo esc_url($clip['poster']); ?>"<?php endif; ?>
               aria-label="<?php echo esc_attr($clip['name']); ?>"></video>
        <button type="button" class="cv-snd" aria-pressed="false"
                aria-label="<?php esc_attr_e('Geluid aanzetten', 'dom-theme'); ?>">
          <svg class="i-off" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <path d="M11 5L6 9H3v6h3l5 4z"/><line x1="23" y1="9" x2="17" y2="15"/><line x1="17" y1="9" x2="23" y2="15"/>
          </svg>
          <svg class="i-on" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <path d="M11 5L6 9H3v6h3l5 4z"/><path d="M15.5 8.5a5 5 0 0 1 0 7"/><path d="M18.5 5.5a9 9 0 0 1 0 13"/>
          </svg>
        </button>
        <span class="rim"></span>
        <div class="cvb">
          <b><?php echo esc_html($clip['name']); ?></b>
          <a class="shop" href="<?php echo esc_url(!empty($clip['link']) ? $clip['link'] : $dom_cover_shop); ?>"><?php esc_html_e('Shop nu', 'dom-theme'); ?> <span class="arw">&rarr;</span></a>
        </div>
      </div>
    <?php endforeach; ?>
  </div>

  <div class="cover-f">
    <button class="cvnav" id="cvPrev" aria-label="<?php esc_attr_e('Vorige', 'dom-theme'); ?>">
      <svg viewBox="0 0 24 24"><path d="M15 5l-7 7 7 7"/></svg>
    </button>
    <div class="nm">
      <b id="cvName"></b>
      <span id="cvSub"></span>
    </div>
    <button class="cvnav" id="cvNext" aria-label="<?php esc_attr_e('Volgende', 'dom-theme'); ?>">
      <svg viewBox="0 0 24 24"><path d="M9 5l7 7-7 7"/></svg>
    </button>
  </div>
  <div class="cvdots" id="cvDots"></div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function () {
  if (window.DOM && DOM.buildCover) DOM.buildCover('#cover');

  var host = document.getElementById('cover');
  var sectie = document.getElementById('wornby');
  if (!host || !sectie) return;

  var kaarten = Array.prototype.slice.call(host.querySelectorAll('.cv'));
  if (!kaarten.length) return;

  var rustig = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  /**
   * Eén plek bepaalt wat er speelt en wat er te horen is.
   *
   * Eerder zette elke gebeurtenis zijn eigen stukje aan en uit. Daardoor bleef
   * een clip doorspelen als je naar de volgende ging, hoorde je er soms twee
   * tegelijk, en liep de stand van de knop niet gelijk met het geluid. Nu
   * wordt na elke gebeurtenis dezelfde functie gedraaid die alle kaarten
   * langsloopt en ze in de juiste stand zet.
   *
   * geluidOp houdt bij welke kaart geluid mág hebben. Dat wordt alleen gezet
   * door op de luidspreker te klikken, en het verdwijnt zodra je verder
   * schuift of de sectie uit beeld scrollt.
   */
  var geluidOp = null;
  var inBeeld = false;

  function actieveKaart() {
    for (var i = 0; i < kaarten.length; i++) {
      if (kaarten[i].classList.contains('act')) return kaarten[i];
    }
    return null;
  }

  function pasAan() {
    var actief = actieveKaart();

    // Geluid hoort bij één kaart, en alleen bij de kaart die vooraan staat.
    if (geluidOp && (geluidOp !== actief || !inBeeld)) {
      geluidOp = null;
    }

    kaarten.forEach(function (kaart) {
      var video = kaart.querySelector('video');
      if (!video) return;

      var magGeluid = (kaart === geluidOp);
      var magSpelen = (kaart === actief) && inBeeld && !rustig;

      if (video.muted !== !magGeluid) video.muted = !magGeluid;

      kaart.classList.toggle('snd-on', magGeluid);
      var knop = kaart.querySelector('.cv-snd');
      if (knop) {
        knop.setAttribute('aria-pressed', magGeluid ? 'true' : 'false');
        knop.setAttribute('aria-label', magGeluid ? 'Geluid uitzetten' : 'Geluid aanzetten');
      }

      if (magSpelen) {
        var p = video.play();
        if (p && p.catch) p.catch(function () {});
      } else if (!video.paused) {
        video.pause();
      }
    });
  }

  // De kaart die vooraan staat verandert: buildCover wisselt de klasse 'act'.
  new MutationObserver(pasAan).observe(host, {
    subtree: true, attributes: true, attributeFilter: ['class']
  });

  // Uit beeld gescrold: alles stil, en het geluid gaat uit.
  if ('IntersectionObserver' in window) {
    new IntersectionObserver(function (entries) {
      entries.forEach(function (e) {
        inBeeld = e.isIntersecting && e.intersectionRatio > 0.25;
      });
      pasAan();
    }, { threshold: [0, 0.25, 0.5] }).observe(sectie);
  } else {
    inBeeld = true;
  }

  // Tabblad naar de achtergrond: ook stil.
  document.addEventListener('visibilitychange', function () {
    if (document.hidden) { geluidOp = null; }
    pasAan();
  });

  /* Browsers starten video alleen vanzelf als hij gedempt is, dus alles begint
     zonder geluid en de bezoeker zet het zelf aan. */
  host.addEventListener('click', function (e) {
    var knop = e.target.closest('.cv-snd');
    if (!knop) return;
    e.preventDefault();
    e.stopPropagation();

    var kaart = knop.closest('.cv');
    if (!kaart || kaart !== actieveKaart()) return;

    geluidOp = (geluidOp === kaart) ? null : kaart;
    pasAan();
  });

  pasAan();
});
</script>
