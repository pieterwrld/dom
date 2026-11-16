/**
 * De Outfitman — Shop Filters
 * Price range and size filters with AJAX product loading
 */
(function () {
  'use strict';

  // Placeholder for AJAX filter implementation
  // Will be connected to WooCommerce REST API or admin-ajax.php
  document.querySelectorAll('.dom-filter-toggle').forEach((btn) => {
    btn.addEventListener('click', () => {
      // Toggle filter dropdown
      btn.classList.toggle('border-fg');
    });
  });
})();
