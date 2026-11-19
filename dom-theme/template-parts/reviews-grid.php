<?php
defined('ABSPATH') || exit;

/**
 * Reviews: echte WhatsApp-screenshots, één tegelijk in een telefoon.
 *
 * De screenshots zijn hele telefoonschermen (946 × 2048, dezelfde verhouding
 * als een iPhone 15 Pro). Ze gaan dus schermvullend in de behuizing, zonder
 * kader eromheen. Over de kopbalk van WhatsApp — waar in het origineel de naam
 * en de foto van de klant onleesbaar zijn gemaakt — ligt onze eigen kopbalk met
 * De Outfitman erin, zodat het leest als een gesprek met de winkel.
 *
 * Er worden nooit teksten of namen verzonnen: het gesprek is het screenshot.
 *
 * Beheer: De Outfitman > Reviews.
 */

$reviews = dom_c_reviews();
if (!$reviews && function_exists('dom_review_screenshots')) {
    $reviews = dom_review_screenshots();
}
$review_count = count($reviews);
if (!$review_count) {
    return;
}

$wa_number = preg_replace('/\D+/', '', (string) dom_c('algemeen.contact.whatsapp', '31616264544'));

$ster = '<svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 2l2.9 6.2 6.6.9-4.8 4.6 1.2 6.6L12 17.2 6.1 20.3l1.2-6.6L2.5 9.1l6.6-.9z"/></svg>';

/**
 * De cijfers in de twee blokken. Ze staan hier zodat ze met de echte aantallen
 * meebewegen als die veranderen, en zodat de teller precies op deze waarde
 * eindigt in plaats van op een afronding.
 */
$stat_klanten = (string) dom_c('footer.trust.klanten', '1.200+');
$klanten_getal = (float) preg_replace('/[^0-9]/', '', $stat_klanten);
$klanten_plus  = str_contains($stat_klanten, '+');
$stat_score    = 4.9;
?>
<section class="sec revsec" id="reviews">
  <div class="wrap">

    <div class="sec-t" data-rv="up">
      <div class="eyebrow"><i></i><span class="lbl"><?php echo esc_html(dom_c('home.reviews.eyebrow', 'Reviews')); ?></span></div>
      <h2 class="d2 split"><?php echo esc_html(dom_c('home.reviews.titel', 'Wat onze klanten zeggen')); ?></h2>
      <p class="rev-sub">
        <?php
        $rev_sub = (string) dom_c('home.reviews.subtitel', '%s echte gesprekken, rechtstreeks uit WhatsApp');
        printf(esc_html($rev_sub), '<b>' . esc_html(number_format_i18n($review_count)) . '</b>');
        ?>
      </p>
    </div>

    <!-- Twee cijferblokken. De tellers lopen één keer op zodra ze in beeld komen. -->
    <div class="rstats" id="rstats">
      <div class="rstat">
        <span class="rstat-waarde">
          <b class="rstat-no" data-doel="<?php echo esc_attr($klanten_getal); ?>"
             data-plus="<?php echo $klanten_plus ? '1' : '0'; ?>"><?php echo esc_html($stat_klanten); ?></b>
        </span>
        <span class="rstat-lbl"><?php esc_html_e('Tevreden klanten', 'dom-theme'); ?></span>
      </div>
      <div class="rstat">
        <div class="rstat-sterren" aria-hidden="true">
          <?php for ($i = 0; $i < 5; $i++) : ?>
            <span style="--i:<?php echo $i; ?>"><?php echo $ster; // phpcs:ignore ?></span>
          <?php endfor; ?>
        </div>
        <span class="rstat-waarde">
          <b class="rstat-no" data-doel="<?php echo esc_attr($stat_score); ?>" data-decimaal="1"><?php echo esc_html(number_format_i18n($stat_score, 1)); ?></b><span class="rstat-max">/5</span>
        </span>
        <span class="rstat-lbl"><?php esc_html_e('Gemiddelde score', 'dom-theme'); ?></span>
      </div>
    </div>

    <!-- Twee telefoons naast elkaar, het spoor schuift automatisch door -->
    <div class="rview" id="rview">
      <div class="rwrap">
        <div class="rtrack" id="rtrack">
          <?php foreach ($reviews as $i => $r) :
            $img = $r['url'] ?? '';
            if (!$img) {
                continue;
            }
            $att_id   = (int) ($r['id'] ?? 0);
            $afzender = trim((string) ($r['naam'] ?? '')) ?: 'De Outfitman';
          ?>
            <figure class="rslide">
              <div class="rphone">
                <div class="rphone-screen">
                  <?php
                  $alt   = __('WhatsApp-gesprek met een klant', 'dom-theme');
                  $attrs = [
                      'alt'      => $alt,
                      'loading'  => $i < 2 ? 'eager' : 'lazy',
                      'decoding' => 'async',
                      'sizes'    => '(max-width:760px) 44vw, 330px',
                  ];
                  if ($att_id) {
                      // WordPress zet hier zelf een srcset bij, zodat een
                      // telefoon niet het hele bestand van 946px hoeft te laden.
                      echo wp_get_attachment_image($att_id, 'full', false, $attrs);
                  } else {
                      printf(
                          '<img src="%s" alt="%s" width="946" height="2048" loading="%s" decoding="async">',
                          esc_url($img),
                          esc_attr($alt),
                          esc_attr($attrs['loading'])
                      );
                  }
                  ?>

                  <?php
                  // De kopbalk is altijd zwart, ook als het screenshot uit de
                  // lichte stand van WhatsApp komt: de reviews horen er als
                  // één reeks uit te zien. Onder de balk loopt een korte
                  // overgang, zodat de rand op een licht gesprek niet hard
                  // afsnijdt.
                  ?>
                  <!-- Onze kopbalk, precies over die van WhatsApp heen -->
                  <div class="wabar">
                    <span class="wab-terug">
                      <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M15 5l-7 7 7 7"/></svg>
                      <i>1</i>
                    </span>
                    <span class="wab-av">DO</span>
                    <span class="wab-nm"><?php echo esc_html($afzender); ?></span>
                    <span class="wab-ic">
                      <svg viewBox="0 0 24 24" aria-hidden="true"><rect x="2" y="6.5" width="13" height="11" rx="2.6"/><path d="M15 11l6-3.4v8.8L15 13z"/></svg>
                      <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M6.6 3.5l2.6.6 1 3.6-2 1.5a12 12 0 0 0 6 6l1.5-2 3.6 1 .6 2.6c-.2 1-1 1.7-2 1.7A15.4 15.4 0 0 1 4.9 5.5c0-1 .7-1.8 1.7-2z"/></svg>
                    </span>
                  </div>

                  <span class="rphone-island" aria-hidden="true"></span>
                </div>
              </div>
            </figure>
          <?php endforeach; ?>
        </div>
      </div>

      <div class="rctl">
        <button class="rnav rnav-l" type="button" aria-label="<?php esc_attr_e('Vorige review', 'dom-theme'); ?>">
          <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M15 5l-7 7 7 7"/></svg>
        </button>
        <div class="rdots" id="rdots" role="tablist" aria-label="<?php esc_attr_e('Kies een review', 'dom-theme'); ?>"></div>
        <button class="rnav rnav-r" type="button" aria-label="<?php esc_attr_e('Volgende review', 'dom-theme'); ?>">
          <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M9 5l7 7-7 7"/></svg>
        </button>
      </div>
    </div>

    <div class="rverify">
      <div class="stars" aria-label="<?php esc_attr_e('5 van de 5 sterren', 'dom-theme'); ?>"><?php echo str_repeat($ster, 5); // phpcs:ignore ?></div>
      <span class="vf">
        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 12l5 5L20 6"/></svg>
        <?php esc_html_e('Geverifieerd', 'dom-theme'); ?>
      </span>
    </div>

    <div class="rcta">
      <a href="https://wa.me/<?php echo esc_attr($wa_number); ?>" class="btn btn-bf" target="_blank" rel="noopener">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true" style="margin-right:8px"><path d="M12 2a10 10 0 0 0-8.6 15.1L2 22l5-1.3A10 10 0 1 0 12 2z"/></svg>
        <?php echo esc_html(dom_c('home.reviews.knop', 'Stuur ons een bericht')); ?>
      </a>
    </div>

  </div>
</section>

<script>
(function () {
  'use strict';

  var rustig = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  /* ── Tellers ─────────────────────────────────────────────
     Lopen één keer op zodra de blokken in beeld komen. Het laatste beeld zet
     de exacte eindwaarde, zodat er nooit 1.199 of 4,8 blijft staan. */
  function nlGetal(n) {
    return String(n).replace(/\B(?=(\d{3})+(?!\d))/g, '.');
  }

  function telOp(el) {
    var doel     = parseFloat(el.dataset.doel || '0');
    var decimaal = parseInt(el.dataset.decimaal || '0', 10);
    var plus     = el.dataset.plus === '1';

    var eind = decimaal
      ? doel.toFixed(decimaal).replace('.', ',')
      : nlGetal(Math.round(doel)) + (plus ? '+' : '');

    if (rustig || !doel) { el.textContent = eind; return; }

    var duur = 1500, start = null;
    el.textContent = decimaal ? (0).toFixed(decimaal).replace('.', ',') : '0';

    function stap(nu) {
      if (start === null) start = nu;
      var p = Math.min(1, (nu - start) / duur);
      var e = 1 - Math.pow(1 - p, 3);
      if (p < 1) {
        var w = doel * e;
        el.textContent = decimaal
          ? w.toFixed(decimaal).replace('.', ',')
          : nlGetal(Math.round(w)) + (plus ? '+' : '');
        requestAnimationFrame(stap);
      } else {
        el.textContent = eind;
      }
    }
    requestAnimationFrame(stap);
  }

  var statsEl = document.getElementById('rstats');
  if (statsEl) {
    // Pas als het script draait mogen de sterren beginnen met wegblijven;
    // zonder JS staan ze er meteen.
    statsEl.classList.add('js');
    var getallen = statsEl.querySelectorAll('.rstat-no');
    var starten = function () {
      statsEl.classList.add('zichtbaar');
      Array.prototype.forEach.call(getallen, telOp);
    };

    if (!('IntersectionObserver' in window)) {
      starten();
    } else {
      var waker = new IntersectionObserver(function (entries) {
        entries.forEach(function (e) {
          if (e.isIntersecting) { starten(); waker.disconnect(); }
        });
      }, { threshold: 0.4 });
      waker.observe(statsEl);
    }
  }

  /* ── De slider ─────────────────────────────────────────
     Twee telefoons tegelijk in beeld, het spoor schuift één kaart per stap
     door en begint na de laatste weer vooraan. Hij loopt vanzelf, maar staat
     stil zolang de bezoeker er met de muis op staat of zelf veegt. */
  var track = document.getElementById('rtrack');
  var view  = document.getElementById('rview');
  if (!track || !view) return;

  var slides = Array.prototype.slice.call(track.querySelectorAll('.rslide'));
  if (!slides.length) return;

  var dotsEl = document.getElementById('rdots');
  var nu = 0, timer = null;

  // Hoeveel kaarten er naast elkaar passen, zodat de laatste stap niet half leeg is.
  function perBeeld() {
    var breed = track.getBoundingClientRect().width;
    var kaart = slides[0].getBoundingClientRect().width;
    return kaart > 0 ? Math.max(1, Math.round(breed / kaart)) : 1;
  }
  function maxIndex() {
    return Math.max(0, slides.length - perBeeld());
  }

  function verschuif() {
    var gap = parseFloat(getComputedStyle(track).columnGap || getComputedStyle(track).gap) || 0;
    var kaart = slides[0].getBoundingClientRect().width;
    track.style.transform = 'translate3d(' + (-nu * (kaart + gap)) + 'px,0,0)';
    dots.forEach(function (d, k) { d.classList.toggle('on', k === nu); });
  }

  function toon(i) {
    var max = maxIndex();
    nu = i > max ? 0 : (i < 0 ? max : i);
    verschuif();
  }

  var dots = [];
  function bouwDots() {
    if (!dotsEl) return;
    var aantal = maxIndex() + 1;
    if (dots.length === aantal) return;
    dotsEl.innerHTML = '';
    dots = [];
    for (var i = 0; i < aantal; i++) {
      (function (k) {
        var d = document.createElement('button');
        d.type = 'button';
        d.className = 'rdot' + (k === nu ? ' on' : '');
        d.setAttribute('role', 'tab');
        d.setAttribute('aria-label', 'Review ' + (k + 1));
        d.addEventListener('click', function () { toon(k); herstart(); });
        dotsEl.appendChild(d);
        dots.push(d);
      })(i);
    }
  }

  function speel() {
    if (rustig || slides.length <= perBeeld()) return;
    stop();
    timer = setInterval(function () { toon(nu + 1); }, 4500);
  }
  function stop() { if (timer) { clearInterval(timer); timer = null; } }
  function herstart() { stop(); speel(); }

  bouwDots();
  verschuif();
  speel();

  view.querySelector('.rnav-l').addEventListener('click', function () { toon(nu - 1); herstart(); });
  view.querySelector('.rnav-r').addEventListener('click', function () { toon(nu + 1); herstart(); });

  view.addEventListener('mouseenter', stop);
  view.addEventListener('mouseleave', speel);

  // Vegen op een telefoon.
  var x0 = null;
  track.addEventListener('pointerdown', function (e) { x0 = e.clientX; stop(); });
  track.addEventListener('pointerup', function (e) {
    if (x0 === null) return;
    var dx = e.clientX - x0;
    x0 = null;
    if (Math.abs(dx) > 40) toon(nu + (dx < 0 ? 1 : -1));
    speel();
  });

  var vertraag;
  window.addEventListener('resize', function () {
    clearTimeout(vertraag);
    vertraag = setTimeout(function () {
      bouwDots();
      if (nu > maxIndex()) nu = maxIndex();
      verschuif();
    }, 150);
  });

})();
</script>
