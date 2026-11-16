/**
 * De Outfitman — Cart AJAX
 * Quantity updates, remove items, cart fragment refresh, add-to-cart interception
 */
(function () {
  'use strict';

  if (typeof domCart === 'undefined') return;

  function updateCart(key, direction) {
    const item = document.querySelector(`[data-cart-item="${key}"]`);
    if (item) item.style.opacity = '0.5';

    const data = new FormData();
    data.append('action', 'dom_update_cart_qty');
    data.append('nonce', domCart.nonce);
    data.append('cart_key', key);
    data.append('direction', direction);

    fetch(domCart.ajaxUrl, { method: 'POST', body: data })
      .then((r) => r.json())
      .then((res) => {
        if (res.success && res.data.fragments) {
          Object.entries(res.data.fragments).forEach(([sel, html]) => {
            const el = document.querySelector(sel);
            if (el) el.outerHTML = html;
          });
        }
      })
      .catch(() => {
        if (item) item.style.opacity = '1';
      });
  }

  document.addEventListener('click', function (e) {
    const qtyBtn = e.target.closest('.dom-qty-btn');
    if (qtyBtn) {
      const { action, key } = qtyBtn.dataset;
      if (action && key) updateCart(key, action);
      return;
    }

    const removeBtn = e.target.closest('.dom-remove-item');
    if (removeBtn) {
      const key = removeBtn.dataset.key;
      if (key) updateCart(key, 'remove');
    }
  });

  // ATC button feedback
  document.querySelectorAll('form .dom-atc').forEach((btn) => {
    btn.addEventListener('click', function () {
      const original = btn.textContent;
      btn.textContent = 'Toegevoegd!';
      btn.classList.add('scale-95');
      setTimeout(() => {
        btn.classList.remove('scale-95');
        btn.textContent = original;
      }, 1200);
    });
  });
})();
