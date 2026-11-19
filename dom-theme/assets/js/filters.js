/**
 * De Outfitman — Shop Filters (Black Friday Chrome)
 * Popover filters for prijs/maat/merk + sort + card carousel
 */
(function () {
  'use strict';

  var $ = function (s, c) { return (c || document).querySelector(s); };
  var $$ = function (s, c) { return Array.prototype.slice.call((c || document).querySelectorAll(s)); };
  var eur = function (v) { return '€' + v.toFixed(2).replace('.', ','); };

  /* ============ SIZE DATA ============ */
  var SZ_CLOTHES = ['XS', 'S', 'M', 'L', 'XL', 'XXL'];
  var SZ_SHOES = ['40', '41', '42', '43', '44', '45', '46'];

  function populateSizes() {
    var cl = $('#szClothes');
    var sh = $('#szShoes');
    if (cl) {
      cl.innerHTML = SZ_CLOTHES.map(function (s) {
        return '<button class="szb" data-sz="' + s + '">' + s + '</button>';
      }).join('');
    }
    if (sh) {
      sh.innerHTML = SZ_SHOES.map(function (s) {
        return '<button class="szb" data-sz="' + s + '">' + s + '</button>';
      }).join('');
    }
  }
  populateSizes();

  /* ============ POPOVER TOGGLE ============ */
  var pairs = [['#fpBtn', '#fpPop'], ['#fsBtn', '#fsPop'], ['#fbBtn', '#fbPop'], ['#soBtn', '#soPop']];
  pairs.forEach(function (pr) {
    var b = $(pr[0]), p = $(pr[1]);
    if (!b || !p) return;
    b.addEventListener('click', function (e) {
      e.stopPropagation();
      var was = p.classList.contains('on');
      closeAllPops();
      if (!was) { p.classList.add('on'); b.classList.add('on'); }
    });
    p.addEventListener('click', function (e) { e.stopPropagation(); });
  });
  document.addEventListener('click', closeAllPops);
  function closeAllPops() {
    pairs.forEach(function (q) {
      var b = $(q[0]), p = $(q[1]);
      if (b) b.classList.remove('on');
      if (p) p.classList.remove('on');
    });
  }

  /* ============ PRICE RANGE ============ */
  var rng = $('#rng');
  if (rng) {
    rng.addEventListener('input', function () {
      var v = parseInt(this.value, 10);
      var max = parseInt(this.max, 10);
      var label = v >= max ? '€' + max + '+' : eur(v);
      var valEl = $('#rngNow');
      if (valEl) valEl.textContent = label;
      var fpVal = $('#fpVal');
      if (fpVal) fpVal.textContent = v >= max ? 'Alles' : '≤ ' + eur(v);
      var fpBtn = $('#fpBtn');
      if (fpBtn) fpBtn.classList.toggle('act', v < max);
    });
  }

  /* ============ SIZE SELECT ============ */
  document.addEventListener('click', function (e) {
    var szb = e.target.closest('.szb');
    if (!szb) return;
    var was = szb.classList.contains('on');
    $$('.szb').forEach(function (s) { s.classList.remove('on'); });
    if (!was) szb.classList.add('on');
    var fsVal = $('#fsVal');
    var fsBtn = $('#fsBtn');
    if (fsVal) fsVal.textContent = was ? 'Alles' : szb.dataset.sz;
    if (fsBtn) fsBtn.classList.toggle('act', !was);
  });

  /* ============ BRAND SELECT ============ */
  document.addEventListener('click', function (e) {
    var brb = e.target.closest('.brb');
    if (!brb) return;
    brb.classList.toggle('on');
    var chosen = $$('.brb.on');
    var fbVal = $('#fbVal');
    var fbBtn = $('#fbBtn');
    if (fbVal) fbVal.textContent = chosen.length ? chosen.length + ' gekozen' : 'Alles';
    if (fbBtn) fbBtn.classList.toggle('act', chosen.length > 0);
  });

  /* ============ SORT ============ */
  $$('[data-sort]').forEach(function (b) {
    b.addEventListener('click', function () {
      var sort = b.dataset.sort;
      var url = new URL(window.location);
      url.searchParams.set('orderby', sort);
      window.location = url.toString();
    });
  });

  /* ============ CLEAR BUTTONS ============ */
  $$('[data-clear]').forEach(function (b) {
    b.addEventListener('click', function () {
      var type = b.dataset.clear;
      if (type === 'price' && rng) {
        rng.value = rng.max;
        rng.dispatchEvent(new Event('input'));
      }
      if (type === 'size') {
        $$('.szb').forEach(function (s) { s.classList.remove('on'); });
        var fsVal = $('#fsVal'), fsBtn = $('#fsBtn');
        if (fsVal) fsVal.textContent = 'Alles';
        if (fsBtn) fsBtn.classList.remove('act');
      }
      if (type === 'brand') {
        $$('.brb').forEach(function (s) { s.classList.remove('on'); });
        var fbVal = $('#fbVal'), fbBtn = $('#fbBtn');
        if (fbVal) fbVal.textContent = 'Alles';
        if (fbBtn) fbBtn.classList.remove('act');
      }
    });
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
