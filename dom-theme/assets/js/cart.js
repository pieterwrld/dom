/**
 * De Outfitman — Cart AJAX
 * Handles quantity updates and cart fragment refresh
 */
(function () {
  'use strict';

  if (typeof domCart === 'undefined') return;

  document.addEventListener('click', function (e) {
    const btn = e.target.closest('.dom-qty-btn');
    if (!btn) return;

    const action = btn.dataset.action;
    const key = btn.dataset.key;
    if (!action || !key) return;

    const data = new FormData();
    data.append('action', 'dom_update_cart_qty');
    data.append('nonce', domCart.nonce);
    data.append('cart_key', key);
    data.append('direction', action);

    fetch(domCart.ajaxUrl, { method: 'POST', body: data })
      .then((r) => r.json())
      .then((res) => {
        if (res.success && res.data.fragments) {
          Object.entries(res.data.fragments).forEach(([sel, html]) => {
            const el = document.querySelector(sel);
            if (el) el.outerHTML = html;
          });
        }
      });
  });
})();
