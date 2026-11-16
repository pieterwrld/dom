/**
 * De Outfitman — Shop Filters
 * Price range and size filter dropdowns
 */
(function () {
  'use strict';

  // Filter toggle
  document.querySelectorAll('.dom-filter-toggle').forEach((btn) => {
    const filterType = btn.dataset.filter;
    let dropdown = btn.nextElementSibling;

    btn.addEventListener('click', (e) => {
      e.stopPropagation();
      const isOpen = btn.classList.contains('border-fg');

      // Close all first
      document.querySelectorAll('.dom-filter-toggle').forEach((b) => {
        b.classList.remove('border-fg', 'bg-surface');
        const dd = b.nextElementSibling;
        if (dd && dd.classList.contains('dom-filter-dropdown')) {
          dd.classList.add('hidden');
        }
      });

      if (!isOpen) {
        btn.classList.add('border-fg', 'bg-surface');
        if (dropdown && dropdown.classList.contains('dom-filter-dropdown')) {
          dropdown.classList.remove('hidden');
        }
      }
    });
  });

  // Close on outside click
  document.addEventListener('click', () => {
    document.querySelectorAll('.dom-filter-toggle').forEach((b) => {
      b.classList.remove('border-fg', 'bg-surface');
    });
    document.querySelectorAll('.dom-filter-dropdown').forEach((dd) => {
      dd.classList.add('hidden');
    });
  });

  // Sort select
  const sortSelect = document.querySelector('.dom-sort-select');
  if (sortSelect) {
    sortSelect.addEventListener('change', function () {
      const url = new URL(window.location);
      url.searchParams.set('orderby', this.value);
      window.location = url.toString();
    });
  }
})();
