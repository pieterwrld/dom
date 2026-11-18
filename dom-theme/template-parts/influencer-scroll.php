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

$uploads   = wp_get_upload_dir();
$base_url  = $uploads['baseurl'] ?? '';
$base_dir  = $uploads['basedir'] ?? '';

$clips = [];
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
    $clips[] = ['name' => $name, 'sub' => $sub, 'src' => $base_url . $file];
}

// Beheerde data gaat voor, als die er ooit komt.
if (function_exists('get_field')) {
    $acf = get_field('influencers', 'option');
    if (is_array($acf) && $acf) {
        $managed = [];
        foreach ($acf as $row) {
            $src = '';
            if (!empty($row['video'])) {
                $src = is_array($row['video']) ? ($row['video']['url'] ?? '') : $row['video'];
            } elseif (!empty($row['image'])) {
                $src = is_array($row['image']) ? ($row['image']['url'] ?? '') : $row['image'];
            }
            if ($src) {
                $managed[] = ['name' => $row['name'] ?? '', 'sub' => $row['handle'] ?? '', 'src' => $src];
            }
        }
        if ($managed) {
            $clips = $managed;
        }
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
      <div class="eyebrow"><i></i><span class="lbl"><?php esc_html_e('Community', 'dom-theme'); ?></span></div>
      <h2 class="split"><?php esc_html_e('Gedragen door', 'dom-theme'); ?></h2>
      <p class="tx"><?php esc_html_e('Ontdek De Outfitman door de ogen van onze creators.', 'dom-theme'); ?></p>
      <div class="cover-soc">
        <a href="https://www.tiktok.com/@deoutfitman" target="_blank" rel="noopener" aria-label="TikTok">
          <svg viewBox="0 0 24 24"><path d="M16 3c.4 2.3 1.9 3.9 4 4.2v3c-1.5.1-2.9-.3-4.1-1.1v6.2c0 3.6-2.9 6.2-6.2 5.6-2.5-.5-4.4-2.7-4.4-5.3 0-3.3 3-5.9 6.3-5.3v3.1c-1.4-.5-2.8.5-2.8 2 0 1.2 1 2.2 2.2 2.2s2.2-1 2.2-2.2V3z"/></svg>
        </a>
        <a href="https://www.instagram.com/deoutfitman/" target="_blank" rel="noopener" aria-label="Instagram">
          <svg viewBox="0 0 24 24"><path d="M12 7.5A4.5 4.5 0 1 0 16.5 12 4.5 4.5 0 0 0 12 7.5zm0 7.4A2.9 2.9 0 1 1 14.9 12 2.9 2.9 0 0 1 12 14.9zM17 3H7a4 4 0 0 0-4 4v10a4 4 0 0 0 4 4h10a4 4 0 0 0 4-4V7a4 4 0 0 0-4-4zm2.4 14a2.4 2.4 0 0 1-2.4 2.4H7A2.4 2.4 0 0 1 4.6 17V7A2.4 2.4 0 0 1 7 4.6h10A2.4 2.4 0 0 1 19.4 7zm-2-10.3a1 1 0 1 1-1-1 1 1 0 0 1 1 1z"/></svg>
        </a>
      </div>
    </div>
  </div>

  <div class="cover" id="cover">
    <?php foreach ($clips as $clip) : ?>
      <div class="cv" data-name="<?php echo esc_attr($clip['name']); ?>" data-sub="<?php echo esc_attr($clip['sub']); ?>">
        <video src="<?php echo esc_url($clip['src']); ?>" muted loop playsinline preload="metadata"
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
          <a class="shop" href="<?php echo esc_url($dom_cover_shop); ?>"><?php esc_html_e('Shop nu', 'dom-theme'); ?> <span class="arw">&rarr;</span></a>
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

  // Alleen de kaart die vooraan staat speelt af.
  var host = document.getElementById('cover');
  if (!host) return;
  var reduce = matchMedia('(prefers-reduced-motion: reduce)').matches;

  function sync() {
    host.querySelectorAll('.cv').forEach(function (card) {
      var v = card.querySelector('video');
      if (!v) return;
      if (card.classList.contains('act') && !reduce) {
        var p = v.play();
        if (p && p.catch) p.catch(function () {});
      } else {
        v.pause();
      }
    });
  }

  sync();
  new MutationObserver(sync).observe(host, {
    subtree: true, attributes: true, attributeFilter: ['class']
  });

  /* Geluid per clip. Browsers laten video alleen automatisch starten als hij
     gedempt is, dus alles begint zonder geluid en de bezoeker zet het zelf aan.
     Eén clip tegelijk: zet je er een aan, dan gaan de andere uit. */
  host.addEventListener('click', function (e) {
    var knop = e.target.closest('.cv-snd');
    if (!knop) return;
    e.preventDefault();
    e.stopPropagation();

    var kaart = knop.closest('.cv');
    var video = kaart && kaart.querySelector('video');
    if (!video) return;

    var aan = video.muted;

    host.querySelectorAll('.cv').forEach(function (andere) {
      var v = andere.querySelector('video');
      var k = andere.querySelector('.cv-snd');
      if (!v || !k) return;
      v.muted = true;
      k.setAttribute('aria-pressed', 'false');
      k.setAttribute('aria-label', 'Geluid aanzetten');
      andere.classList.remove('snd-on');
    });

    if (aan) {
      video.muted = false;
      knop.setAttribute('aria-pressed', 'true');
      knop.setAttribute('aria-label', 'Geluid uitzetten');
      kaart.classList.add('snd-on');
      var p = video.play();
      if (p && p.catch) p.catch(function () {});
    }
  });
});
</script>
