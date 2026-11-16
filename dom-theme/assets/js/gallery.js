/**
 * De Outfitman — Product Page JS (Black Friday Chrome)
 * Gallery, swipe, lightbox, size/stock, accordion, delivery, BF timer
 */
(function(){
'use strict';
var $  = function(s,c){return (c||document).querySelector(s);};
var $$ = function(s,c){return Array.prototype.slice.call((c||document).querySelectorAll(s));};
var P  = window.domProd || {};

/* ======================= GALLERY ======================= */
var gi = 0;
var track = $('#galTrack'), dots = $('#galDots'), thumbs = $('#thumbs');
var stage = $('#stage');
var N = P.gallery ? P.gallery.length : 0;

function setG(i, instant){
  if(N < 2) return;
  gi = Math.max(0, Math.min(N - 1, i));
  track.style.transition = instant ? 'none' : 'transform .66s cubic-bezier(.16,1,.3,1)';
  track.style.transform = 'translate3d(' + (-gi * 100) + '%,0,0)';
  if(dots) $$('.pdot', dots).forEach(function(d,k){ d.classList.toggle('on', k === gi); });
  if(thumbs) $$('button', thumbs).forEach(function(b,k){ b.classList.toggle('on', k === gi); });
  var galI = $('#galI'); if(galI) galI.textContent = gi + 1;
  var galL = $('#galL'), galR = $('#galR');
  if(galL) galL.disabled = gi === 0;
  if(galR) galR.disabled = gi === N - 1;
}

/* arrows */
var galL = $('#galL'), galR = $('#galR');
if(galL) galL.addEventListener('click', function(e){ e.stopPropagation(); setG(gi - 1); });
if(galR) galR.addEventListener('click', function(e){ e.stopPropagation(); setG(gi + 1); });

/* thumbnails */
if(thumbs) thumbs.addEventListener('click', function(e){
  var b = e.target.closest('button'); if(b) setG(parseInt(b.dataset.k, 10));
});

/* dots click guard */
if(dots) dots.addEventListener('click', function(e){ e.stopPropagation(); });

/* pointer glow */
if(stage) stage.addEventListener('pointermove', function(e){
  var r = stage.getBoundingClientRect();
  stage.style.setProperty('--gx', ((e.clientX - r.left) / r.width * 100) + '%');
  stage.style.setProperty('--gy', ((e.clientY - r.top) / r.height * 100) + '%');
});

/* swipe */
(function(){
  if(!stage || N < 2) return;
  var down = false, x0 = 0, dx = 0, w = 0, moved = false;
  stage.addEventListener('pointerdown', function(e){
    down = true; moved = false; x0 = e.clientX; dx = 0;
    w = stage.getBoundingClientRect().width; track.style.transition = 'none';
  });
  window.addEventListener('pointermove', function(e){
    if(!down) return;
    dx = e.clientX - x0;
    if(Math.abs(dx) > 5) moved = true;
    track.style.transform = 'translate3d(' + (-gi * w + dx) + 'px,0,0)';
  });
  window.addEventListener('pointerup', function(){
    if(!down) return;
    down = false;
    if(Math.abs(dx) > 42) setG(gi + (dx < 0 ? 1 : -1)); else setG(gi);
    stage._moved = moved;
  });
})();

/* lightbox */
var lb = $('#lb'), lbImg = $('#lbImg'), lbX = $('#lbX');
if(stage && lb && lbImg){
  stage.addEventListener('click', function(){
    if(stage._moved){ stage._moved = false; return; }
    var src = P.gallery && P.gallery[gi];
    if(!src) return;
    lbImg.src = src;
    lb.classList.add('on'); document.body.classList.add('lock');
  });
  if(lbX) lbX.addEventListener('click', function(){
    lb.classList.remove('on','zoom'); lbImg.style.transform = '';
    document.body.classList.remove('lock');
  });
  lb.addEventListener('click', function(e){
    if(e.target === lbImg){
      lb.classList.toggle('zoom');
      lbImg.style.transform = lb.classList.contains('zoom') ? 'scale(1.7)' : '';
      return;
    }
    if(e.target === lb){
      lb.classList.remove('on','zoom'); lbImg.style.transform = '';
      document.body.classList.remove('lock');
    }
  });
  lbImg.addEventListener('pointermove', function(e){
    if(!lb.classList.contains('zoom')) return;
    var r = lbImg.getBoundingClientRect();
    var px = (e.clientX - r.left) / r.width - .5;
    var py = (e.clientY - r.top) / r.height - .5;
    lbImg.style.transform = 'scale(1.7) translate(' + (-px * 22) + '%,' + (-py * 22) + '%)';
  });
}

/* keyboard nav */
document.addEventListener('keydown', function(e){
  if(e.key === 'ArrowRight') setG(gi + 1);
  if(e.key === 'ArrowLeft') setG(gi - 1);
  if(e.key === 'Escape' && lb){
    lb.classList.remove('on','zoom');
    if(lbImg) lbImg.style.transform = '';
    document.body.classList.remove('lock');
  }
});

/* ======================= SIZE + STOCK ======================= */
var szI = -1;
var sizes = P.sizes || [];

function stockText(q){
  if(q === 1) return ['Laatste op voorraad — wees er snel bij!', false];
  if(q === 2) return ['Nog 2 op voorraad — twijfel niet!', false];
  if(q >= 3 && q <= 6) return ['Nog ' + q + ' op voorraad — bijna uitverkocht', false];
  if(q > 6) return ['Op voorraad — voor 17:00 besteld, morgen in huis', true];
  return ['Deze maat is uitverkocht', false];
}

var szRow = $('#szRow');
if(szRow) szRow.addEventListener('click', function(e){
  var b = e.target.closest('.szp'); if(!b || b.disabled) return;
  szI = parseInt(b.dataset.k, 10);
  $$('.szp').forEach(function(q,k){ q.classList.toggle('on', k === szI); });
  var s = sizes[szI]; if(!s) return;
  var pick = $('#szPick'); if(pick) pick.textContent = 'Maat ' + s.s;
  var r = stockText(s.q);
  var stk = $('#stockTxt'); if(stk) stk.textContent = r[0];
  var stock = $('#stock'); if(stock) stock.classList.toggle('ok', r[1]);

  /* set hidden form fields */
  var vid = $('#variationId'); if(vid) vid.value = s.v;
  var sav = $('#sizeAttrVal'); if(sav) sav.value = s.s;
});

/* ======================= ADD TO CART ======================= */
var atcForm = $('#atcForm');
if(atcForm) atcForm.addEventListener('submit', function(e){
  e.preventDefault();
  var btn = $('#atc'); if(!btn) return;

  /* variable product: require size selection */
  if(P.variable && szI < 0){
    var pick = $('#szPick'); if(pick) pick.textContent = 'Kies eerst een maat';
    var row = $('#szRow');
    if(row && row.animate) row.animate(
      [{transform:'translateX(0)'},{transform:'translateX(-5px)'},{transform:'translateX(5px)'},{transform:'translateX(0)'}],
      {duration:300, easing:'ease-in-out'});
    return;
  }

  btn.disabled = true;
  btn.innerHTML = '<span>Toevoegen...</span>';

  var fd = new FormData(atcForm);
  fetch(atcForm.action, {
    method: 'POST',
    body: new URLSearchParams(Array.from(fd.entries())),
    credentials: 'same-origin',
    headers: {'X-Requested-With': 'XMLHttpRequest'},
  }).then(function(resp){
    if(!resp.ok) throw new Error(resp.status);
    /* refresh WooCommerce cart fragments */
    if(window.jQuery) jQuery(document.body).trigger('wc_fragment_refresh');
    /* open cart drawer */
    if(window.DOM && window.DOM.openCart) window.DOM.openCart();
    btn.classList.add('ok');
    btn.innerHTML = 'Toegevoegd ✓';
    setTimeout(function(){
      btn.classList.remove('ok');
      btn.disabled = false;
      btn.innerHTML = 'In winkelwagen <span class="arw">&rarr;</span>';
    }, 1800);
  }).catch(function(){
    btn.disabled = false;
    btn.innerHTML = 'In winkelwagen <span class="arw">&rarr;</span>';
  });
});

/* ======================= ACCORDION ======================= */
$$('.ai').forEach(function(ai){
  var b = $('.ai-b', ai), c = $('.ai-c', ai), inr = $('.inr', ai);
  if(!b || !c || !inr) return;
  b.addEventListener('click', function(){
    var on = ai.classList.contains('on');
    ai.classList.toggle('on', !on);
    c.style.height = on ? '0px' : (inr.scrollHeight + 'px');
  });
});
$$('[data-open]').forEach(function(a){
  a.addEventListener('click', function(e){
    e.preventDefault();
    var ai = document.getElementById(a.dataset.open);
    if(!ai) return;
    if(!ai.classList.contains('on')){ var b = $('.ai-b', ai); if(b) b.click(); }
    ai.scrollIntoView({behavior:'smooth', block:'center'});
  });
});

/* ======================= BF DEAL COUNTDOWN ======================= */
(function(){
  var el = $('#pdT'); if(!el) return;
  var end = new Date(2026, 10, 27, 23, 59, 59);
  function p(n){ return String(n).padStart(2, '0'); }
  (function t(){
    var ms = Math.max(0, end - new Date());
    el.textContent = Math.floor(ms / 864e5) + 'd ' +
      p(Math.floor(ms / 36e5) % 24) + ':' +
      p(Math.floor(ms / 6e4) % 60) + ':' +
      p(Math.floor(ms / 1e3) % 60);
    if(ms > 0) setTimeout(t, 1000);
  })();
})();

/* ======================= DELIVERY DAY ======================= */
(function(){
  var el = $('#delivDay'); if(!el) return;
  var d = new Date();
  d.setDate(d.getDate() + (d.getHours() < 17 ? 1 : 2));
  var dag = ['zondag','maandag','dinsdag','woensdag','donderdag','vrijdag','zaterdag'][d.getDay()];
  var mnd = ['januari','februari','maart','april','mei','juni','juli','augustus','september','oktober','november','december'][d.getMonth()];
  el.textContent = dag + ' ' + d.getDate() + ' ' + mnd + ' in huis';
})();

/* ======================= REVEAL ======================= */
(function(){
  if(!window.IntersectionObserver) return;
  var rio = new IntersectionObserver(function(es){
    es.forEach(function(e){ if(e.isIntersecting){ e.target.classList.add('in'); rio.unobserve(e.target); } });
  }, {threshold: 0.05});
  $$('[data-rv]').forEach(function(el){ if(!el.classList.contains('in')) rio.observe(el); });
  if(window.DOM){
    $$('.split').forEach(function(el){ window.DOM.words(el); });
  }
})();

})();
