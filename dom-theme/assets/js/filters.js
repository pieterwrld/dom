/**
 * De Outfitman — filters en sliders op de collectiepagina.
 *
 * Het filter liep via het adres: elke maat is een gewone link en de server
 * filtert de lijst. Eerder gebeurde hier alleen het bijwerken van een label,
 * waardoor klikken op een maat niets deed. De JavaScript hieronder opent en
 * sluit alleen de panelen en vertaalt de prijsschuif naar een adres.
 */
(function () {
  'use strict';

  var $ = function (s, c) { return (c || document).querySelector(s); };
  var $$ = function (s, c) { return Array.prototype.slice.call((c || document).querySelectorAll(s)); };

  /* ============ PANELEN OPENEN EN SLUITEN ============ */
  var paren = [['#fpBtn', '#fpPop'], ['#fsBtn', '#fsPop'], ['#fbBtn', '#fbPop'], ['#soBtn', '#soPop']];
  paren.forEach(function (pr) {
    var b = $(pr[0]), p = $(pr[1]);
    if (!b || !p) return;
    b.addEventListener('click', function (e) {
      e.stopPropagation();
      var was = p.classList.contains('on');
      sluitAlles();
      if (!was) { p.classList.add('on'); b.classList.add('open'); }
    });
    p.addEventListener('click', function (e) { e.stopPropagation(); });
  });
  document.addEventListener('click', sluitAlles);
  document.addEventListener('keydown', function (e) { if (e.key === 'Escape') sluitAlles(); });

  function sluitAlles() {
    paren.forEach(function (q) {
      var b = $(q[0]), p = $(q[1]);
      if (b) b.classList.remove('open');
      if (p) p.classList.remove('on');
    });
  }

  /* ============ PRIJSSCHUIF ============
     Tijdens het schuiven verandert alleen het label; bij loslaten gaat de
     pagina naar het gefilterde adres. Het adressjabloon komt van de server en
     bevat __p__ op de plek van het bedrag. */
  var rng = $('#rng');
  if (rng) {
    var toon = function () {
      var v = parseInt(rng.value, 10);
      var max = parseInt(rng.max, 10);
      var label = v >= max ? '€' + max + '+' : '€' + v;
      var nu = $('#rngNow');
      if (nu) nu.textContent = label;
      var val = $('#fpVal');
      if (val) val.textContent = v >= max ? 'Alles' : '≤ €' + v;
    };

    var ga = function () {
      var basis = rng.dataset.basis;
      if (!basis) return;
      var v = parseInt(rng.value, 10);
      var max = parseInt(rng.max, 10);
      // Helemaal rechts betekent geen prijsgrens.
      window.location = v >= max
        ? basis.replace(/[?&]max_prijs=__p__/, '').replace(/\?$/, '')
        : basis.replace('__p__', String(v));
    };

    rng.addEventListener('input', toon);
    rng.addEventListener('change', ga);
    toon();
  }

  /* ============ SORTEREN ============ */
  $$('[data-sort]').forEach(function (b) {
    b.addEventListener('click', function () {
      var url = new URL(window.location);
      url.searchParams.set('orderby', b.dataset.sort);
      window.location = url.toString();
    });
  });

  /* ============ MERKEN ============
     Nog geen serverfilter; de knoppen houden alleen hun eigen stand bij. */
  document.addEventListener('click', function (e) {
    var brb = e.target.closest('.brb');
    if (!brb) return;
    brb.classList.toggle('on');
    var gekozen = $$('.brb.on');
    var val = $('#fbVal');
    var btn = $('#fbBtn');
    if (val) val.textContent = gekozen.length ? gekozen.length + ' gekozen' : 'Alles';
    if (btn) btn.classList.toggle('act', gekozen.length > 0);
  });

  /* ============ FOTOSLIDER OP DE KAART ============ */
  var grid = $('#grid');
  if (!grid) return;

  function setIdx(media, idx) {
    var track = $('.ptrack', media);
    if (!track) return;
    var imgs = $$('img', track);
    idx = Math.max(0, Math.min(imgs.length - 1, idx));
    media._i = idx;
    track.style.transition = 'transform .6s cubic-bezier(.16,1,.3,1)';
    track.style.transform = 'translate3d(' + (-idx * 100) + '%,0,0)';
    $$('.slidebars i', media).forEach(function (d, k) { d.classList.toggle('on', k === idx); });
  }

  /**
   * Na een veeg mag de tik die eraan vastzit niet als klik tellen, anders
   * opent het product zodra je de volgende foto wilt zien.
   *
   * Dat werkte eerder met een eenmalige klikvanger op de kaart. Bleef een veeg
   * zonder klik, dan bleef die vanger staan en at hij de eerstvolgende
   * bedoelde klik op: vandaar dat je twee keer moest tikken voordat je op het
   * product kwam. Nu is het een tijdstempel die vanzelf verloopt.
   */
  var blokkeerKlikTot = 0;

  grid.addEventListener('click', function (e) {
    if (Date.now() < blokkeerKlikTot) {
      e.preventDefault();
      e.stopPropagation();
    }
  }, true);

  var act = null, x0 = 0, dx = 0, w = 0;

  grid.addEventListener('pointerdown', function (e) {
    var t = e.target.closest('.ptrack');
    if (!t) return;
    if ($$('img', t).length < 2) return;
    act = t; x0 = e.clientX; dx = 0; w = t.getBoundingClientRect().width;
    t.classList.add('drag'); t.style.transition = 'none';
    t.closest('.pmedia').classList.add('touched');
  });

  window.addEventListener('pointermove', function (e) {
    if (!act) return;
    dx = e.clientX - x0;
    var m = act.closest('.pmedia');
    act.style.transform = 'translate3d(' + (-(m._i || 0) * w + dx) + 'px,0,0)';
  });

  window.addEventListener('pointerup', function () {
    if (!act) return;
    var m = act.closest('.pmedia');
    act.classList.remove('drag');
    act = null;
    // Een vinger staat bij een gewone tik zelden helemaal stil. Onder de 12px
    // is het een tik, daarboven een veeg.
    var veeg = Math.abs(dx) > 12;
    setIdx(m, (m._i || 0) + (veeg ? (dx < 0 ? 1 : -1) : 0));
    if (veeg) {
      blokkeerKlikTot = Date.now() + 350;
    }
    dx = 0;
  });

  /* ============ DOORLADEN BIJ HET SCROLLEN ============
     De knop blijft in de pagina staan voor bezoekers zonder JavaScript. Met
     JavaScript verbergen we hem en komt de volgende pagina er vanzelf achter,
     zodra het einde van de lijst in zicht komt. */
  (function () {
    var loadw = document.querySelector('.loadw');
    if (!loadw) return;

    var knop = loadw.querySelector('a');
    if (!knop || !('IntersectionObserver' in window) || !window.fetch) return;

    loadw.classList.add('js');

    var bezig = false;
    var volgende = knop.getAttribute('href');
    var waker;

    function verwerk(html) {
      var doc = new DOMParser().parseFromString(html, 'text/html');

      Array.prototype.forEach.call(doc.querySelectorAll('#grid > *'), function (kaart) {
        var el = document.importNode(kaart, true);
        grid.appendChild(el);
        if (window.DOM && window.DOM.reveal && el.hasAttribute && el.hasAttribute('data-rv')) {
          window.DOM.reveal(el);
        }
      });

      var teller = doc.querySelector('.loadw .pc');
      var eigenTeller = loadw.querySelector('.pc');
      if (teller && eigenTeller) { eigenTeller.textContent = teller.textContent; }

      var balk = doc.querySelector('.loadw .pbar i');
      var eigenBalk = loadw.querySelector('.pbar i');
      if (balk && eigenBalk) { eigenBalk.style.width = balk.style.width; }

      var volgendeKnop = doc.querySelector('.loadw a');
      volgende = volgendeKnop ? volgendeKnop.getAttribute('href') : null;

      if (!volgende) {
        waker.disconnect();
        loadw.classList.add('klaar');
      } else {
        knop.setAttribute('href', volgende);
      }
    }

    function laad() {
      if (bezig || !volgende) { return; }
      bezig = true;
      loadw.classList.add('bezig');
      fetch(volgende, { credentials: 'same-origin' })
        .then(function (r) { return r.ok ? r.text() : Promise.reject(new Error(r.status)); })
        .then(verwerk)
        .catch(function () {
          // Lukt het niet, dan komt de knop terug: beter een knop dan een
          // halve lijst zonder uitweg.
          loadw.classList.remove('js');
          waker.disconnect();
          volgende = knop.getAttribute('href');
        })
        .then(function () {
          bezig = false;
          loadw.classList.remove('bezig');
        });
    }

    waker = new IntersectionObserver(function (entries) {
      entries.forEach(function (e) { if (e.isIntersecting) { laad(); } });
    }, { rootMargin: '600px 0px' });

    waker.observe(loadw);

    knop.addEventListener('click', function (e) { e.preventDefault(); laad(); });
  })();

  /* ============ NIEUWE KAARTEN LATEN VERSCHIJNEN ============ */
  if (window.DOM && window.DOM.reveal) {
    $$('.pcard[data-rv]', grid).forEach(function (el) {
      window.DOM.reveal(el);
    });
  }
})();
