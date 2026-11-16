/**
 * De Outfitman — Cart Drawer AJAX
 * Quantity, remove, upsell add, tab switching, ATC interception
 */
(function () {
  'use strict';

  if (typeof domCart === 'undefined') return;

  var cart = document.getElementById('cart');
  if (!cart) return;

  function ajax(key, direction) {
    var ci = cart.querySelector('.ci[data-key="' + key + '"]');
    if (ci) ci.style.opacity = '0.4';

    var fd = new FormData();
    fd.append('action', 'dom_update_cart_qty');
    fd.append('nonce', domCart.nonce);
    fd.append('cart_key', key);
    fd.append('direction', direction);

    fetch(domCart.ajaxUrl, { method: 'POST', body: fd })
      .then(function (r) { return r.json(); })
      .then(function (res) {
        if (res.success && res.data.fragments) {
          applyFragments(res.data.fragments);
        }
      })
      .catch(function () {
        if (ci) ci.style.opacity = '1';
      });
  }

  function applyFragments(fragments) {
    Object.keys(fragments).forEach(function (sel) {
      var el = document.querySelector(sel);
      if (el) el.outerHTML = fragments[sel];
    });
    bindTabs();
  }

  /* Quantity +/- and remove — delegated */
  document.addEventListener('click', function (e) {
    var qb = e.target.closest('.qty button[data-a]');
    if (qb) {
      var key = qb.dataset.key;
      var dir = qb.dataset.a === '+' ? 'increase' : 'decrease';
      if (key) ajax(key, dir);
      return;
    }

    var rm = e.target.closest('.ci .rm[data-rm]');
    if (rm) {
      ajax(rm.dataset.rm, 'remove');
      return;
    }

    /* Upsell mini-card add */
    var addBtn = e.target.closest('.cmini .add[data-id]');
    if (addBtn) {
      var pid = addBtn.dataset.id;
      var sel = addBtn.parentElement.querySelector('select');
      var size = sel ? sel.value : '';

      addBtn.classList.add('ok');
      setTimeout(function () { addBtn.classList.remove('ok'); }, 1400);

      var fd = new FormData();
      fd.append('action', 'dom_add_to_cart');
      fd.append('nonce', domCart.nonce);
      fd.append('product_id', pid);
      if (size) fd.append('size', size);

      fetch(domCart.ajaxUrl, { method: 'POST', body: fd })
        .then(function (r) { return r.json(); })
        .then(function (res) {
          if (res.success && res.data.fragments) {
            applyFragments(res.data.fragments);
          }
        });
      return;
    }
  });

  /* Tab switching — delegated, re-bound after fragment replace */
  function bindTabs() {
    var c = document.getElementById('cart');
    if (!c) return;
    c.querySelectorAll('.ctab').forEach(function (t) {
      t.addEventListener('click', function () {
        c.querySelectorAll('.ctab').forEach(function (q) { q.classList.remove('on'); });
        c.querySelectorAll('.cpane').forEach(function (q) { q.classList.remove('on'); });
        t.classList.add('on');
        var pane = c.querySelector('#pane-' + t.dataset.p);
        if (pane) pane.classList.add('on');
      });
    });
  }
  bindTabs();

  /* Intercept WooCommerce AJAX add-to-cart and open drawer */
  document.body.addEventListener('added_to_cart', function (e, fragments) {
    if (fragments) applyFragments(fragments);
    if (window.DOM && window.DOM.openCart) window.DOM.openCart();
  });

  /* jQuery event bridge — WooCommerce fires jQuery events */
  if (typeof jQuery !== 'undefined') {
    jQuery(document.body).on('added_to_cart', function (e, fragments) {
      if (fragments) applyFragments(fragments);
      if (window.DOM && window.DOM.openCart) window.DOM.openCart();
    });
  }

  /* ATC button feedback on product pages */
  document.addEventListener('click', function (e) {
    var atc = e.target.closest('.dom-atc');
    if (!atc) return;
    var original = atc.innerHTML;
    atc.innerHTML = '<span>Toegevoegd!</span>';
    atc.style.transform = 'scale(0.97)';
    setTimeout(function () {
      atc.style.transform = '';
      atc.innerHTML = original;
    }, 1200);
  });
})();
