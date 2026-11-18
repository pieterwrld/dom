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

  /* ============ PRODUCT CARD CAROUSEL ============ */
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
    var l = $('.parrow.l', media), r = $('.parrow.r', media);
    if (l) l.disabled = idx === 0;
    if (r) r.disabled = idx === imgs.length - 1;
  }

  grid.addEventListener('click', function (e) {
    var ar = e.target.closest('.parrow');
    if (ar) {
      e.preventDefault();
      e.stopPropagation();
      var m = ar.closest('.pmedia');
      setIdx(m, (m._i || 0) + parseInt(ar.dataset.d, 10));
    }
  });

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
    setIdx(m, (m._i || 0) + (Math.abs(dx) > 34 ? (dx < 0 ? 1 : -1) : 0));
    if (Math.abs(dx) > 6) {
      m.closest('.pcard').addEventListener('click', function (ev) {
        ev.preventDefault(); ev.stopPropagation();
      }, { capture: true, once: true });
    }
    dx = 0;
  });

  /* ============ REVEAL NEW CARDS ============ */
  if (window.DOM && window.DOM.reveal) {
    $$('.pcard[data-rv]', grid).forEach(function (el) {
      window.DOM.reveal(el);
    });
  }
})();
