/**
 * De Outfitman — Product Gallery
 * Touch-friendly horizontal scroll with dot indicator
 */
(function () {
  'use strict';

  const gallery = document.querySelector('.gallery-scroll');
  if (!gallery) return;

  const slides = gallery.querySelectorAll('.slide');
  if (slides.length < 2) return;

  // Create dots
  const dotsWrap = document.createElement('div');
  dotsWrap.className = 'flex justify-center gap-1.5 mt-3';
  gallery.parentElement.insertBefore(dotsWrap, gallery.nextSibling);

  slides.forEach((_, i) => {
    const dot = document.createElement('span');
    dot.className = 'w-2 h-2 rounded-full transition-colors ' + (i === 0 ? 'bg-fg' : 'bg-fg/20');
    dotsWrap.appendChild(dot);
  });

  // Update dots on scroll
  let ticking = false;
  gallery.addEventListener('scroll', () => {
    if (!ticking) {
      requestAnimationFrame(() => {
        const scrollLeft = gallery.scrollLeft;
        const slideWidth = slides[0].offsetWidth + 4;
        const active = Math.round(scrollLeft / slideWidth);
        dotsWrap.querySelectorAll('span').forEach((d, i) => {
          d.className = 'w-2 h-2 rounded-full transition-colors ' + (i === active ? 'bg-fg' : 'bg-fg/20');
        });
        ticking = false;
      });
      ticking = true;
    }
  }, { passive: true });
})();
