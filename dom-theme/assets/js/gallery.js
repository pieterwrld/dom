/**
 * De Outfitman — Product Gallery
 * Touch scroll with dots, keyboard nav, counter display
 */
(function () {
  'use strict';

  const gallery = document.querySelector('.gallery-scroll');
  if (!gallery) return;

  const slides = gallery.querySelectorAll('.slide');
  if (slides.length < 2) return;

  // Dots
  const dotsWrap = document.createElement('div');
  dotsWrap.className = 'flex justify-center gap-1.5 mt-3 px-4';
  gallery.parentElement.insertBefore(dotsWrap, gallery.nextSibling);

  slides.forEach((_, i) => {
    const dot = document.createElement('button');
    dot.className = 'w-2 h-2 rounded-full transition-all duration-300 ' + (i === 0 ? 'bg-fg w-5' : 'bg-fg/20');
    dot.setAttribute('aria-label', 'Foto ' + (i + 1));
    dot.addEventListener('click', () => scrollTo(i));
    dotsWrap.appendChild(dot);
  });

  // Counter (e.g. "1 / 5")
  const counter = document.createElement('span');
  counter.className = 'absolute top-3 right-3 px-2.5 py-1 bg-black/40 backdrop-blur-sm text-white text-[11px] font-semibold rounded-full z-10';
  counter.textContent = '1 / ' + slides.length;
  gallery.style.position = 'relative';
  gallery.appendChild(counter);

  function scrollTo(index) {
    const slide = slides[index];
    if (slide) {
      gallery.scrollTo({ left: slide.offsetLeft - gallery.offsetLeft, behavior: 'smooth' });
    }
  }

  function getActive() {
    const scrollLeft = gallery.scrollLeft;
    let closest = 0;
    let minDist = Infinity;
    slides.forEach((s, i) => {
      const dist = Math.abs(s.offsetLeft - gallery.offsetLeft - scrollLeft);
      if (dist < minDist) { minDist = dist; closest = i; }
    });
    return closest;
  }

  function updateIndicators() {
    const active = getActive();
    dotsWrap.querySelectorAll('button').forEach((d, i) => {
      d.className = 'rounded-full transition-all duration-300 h-2 ' + (i === active ? 'bg-fg w-5' : 'bg-fg/20 w-2');
    });
    counter.textContent = (active + 1) + ' / ' + slides.length;
  }

  let ticking = false;
  gallery.addEventListener('scroll', () => {
    if (!ticking) {
      requestAnimationFrame(() => { updateIndicators(); ticking = false; });
      ticking = true;
    }
  }, { passive: true });

  // Keyboard navigation
  gallery.setAttribute('tabindex', '0');
  gallery.addEventListener('keydown', (e) => {
    const active = getActive();
    if (e.key === 'ArrowRight' && active < slides.length - 1) scrollTo(active + 1);
    if (e.key === 'ArrowLeft' && active > 0) scrollTo(active - 1);
  });
})();
