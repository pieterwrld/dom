/**
 * De Outfitman — Core JS
 * Handles: scroll reveals, slider, cart toggle, countdown, sticky ATC
 */
(function () {
  'use strict';

  // Scroll reveal
  if ('IntersectionObserver' in window) {
    const obs = new IntersectionObserver(
      (entries) => {
        entries.forEach((e) => {
          if (e.isIntersecting) {
            e.target.classList.add('visible');
            obs.unobserve(e.target);
          }
        });
      },
      { threshold: 0.1 }
    );
    document.querySelectorAll('.reveal').forEach((el) => obs.observe(el));
  } else {
    document.querySelectorAll('.reveal').forEach((el) => el.classList.add('visible'));
  }

  // Generic slider component
  document.querySelectorAll('[data-slider]').forEach((slider) => {
    const track = slider.querySelector('.dom-slider-track');
    const slides = slider.querySelectorAll('.dom-slider-slide');
    const prevBtn = slider.querySelector('.dom-slider-prev');
    const nextBtn = slider.querySelector('.dom-slider-next');
    const dotsContainer = slider.querySelector('.dom-slider-dots');

    if (!track || slides.length === 0) return;

    let current = 0;
    const total = slides.length;

    // Build dots
    if (dotsContainer) {
      slides.forEach((_, i) => {
        const dot = document.createElement('button');
        dot.className = 'w-2 h-2 rounded-full transition-colors ' + (i === 0 ? 'bg-fg' : 'bg-fg/20');
        dot.setAttribute('aria-label', 'Slide ' + (i + 1));
        dot.addEventListener('click', () => goTo(i));
        dotsContainer.appendChild(dot);
      });
    }

    function goTo(index) {
      current = Math.max(0, Math.min(index, total - 1));
      track.style.transform = 'translateX(-' + (current * 100) + '%)';
      if (dotsContainer) {
        dotsContainer.querySelectorAll('button').forEach((d, i) => {
          d.className = 'w-2 h-2 rounded-full transition-colors ' + (i === current ? 'bg-fg' : 'bg-fg/20');
        });
      }
    }

    if (prevBtn) prevBtn.addEventListener('click', () => goTo(current - 1));
    if (nextBtn) nextBtn.addEventListener('click', () => goTo(current + 1));

    // Touch support
    let startX = 0;
    track.addEventListener('touchstart', (e) => { startX = e.touches[0].clientX; }, { passive: true });
    track.addEventListener('touchend', (e) => {
      const diff = startX - e.changedTouches[0].clientX;
      if (Math.abs(diff) > 50) goTo(current + (diff > 0 ? 1 : -1));
    }, { passive: true });
  });

  // Cart drawer toggle
  const cartOverlay = document.getElementById('cartOverlay');
  const cartDrawer = document.getElementById('cartDrawer');

  function toggleCart(open) {
    const isOpen = open ?? !cartOverlay.classList.contains('open');
    cartOverlay.classList.toggle('open', isOpen);
    document.body.style.overflow = isOpen ? 'hidden' : '';
  }

  if (cartOverlay) {
    cartOverlay.addEventListener('click', () => toggleCart(false));
  }
  document.querySelectorAll('.dom-cart-toggle').forEach((btn) => {
    btn.addEventListener('click', () => toggleCart());
  });
  document.querySelectorAll('.dom-cart-close').forEach((btn) => {
    btn.addEventListener('click', () => toggleCart(false));
  });
  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape' && cartOverlay && cartOverlay.classList.contains('open')) {
      toggleCart(false);
    }
  });

  // Countdown timer
  const countdownEl = document.querySelector('[data-countdown]');
  if (countdownEl) {
    const target = new Date(countdownEl.dataset.countdown).getTime();
    const dEl = countdownEl.querySelector('.dom-cd-d');
    const hEl = countdownEl.querySelector('.dom-cd-h');
    const mEl = countdownEl.querySelector('.dom-cd-m');
    const sEl = countdownEl.querySelector('.dom-cd-s');

    function update() {
      const diff = Math.max(0, target - Date.now());
      const d = Math.floor(diff / 86400000);
      const h = Math.floor((diff % 86400000) / 3600000);
      const m = Math.floor((diff % 3600000) / 60000);
      const s = Math.floor((diff % 60000) / 1000);
      if (dEl) dEl.textContent = String(d).padStart(2, '0');
      if (hEl) hEl.textContent = String(h).padStart(2, '0');
      if (mEl) mEl.textContent = String(m).padStart(2, '0');
      if (sEl) sEl.textContent = String(s).padStart(2, '0');
    }
    update();
    setInterval(update, 1000);
  }

  // Sticky ATC on product page
  const atcBtn = document.querySelector('.dom-atc');
  const stickyAtc = document.querySelector('.dom-sticky-atc');
  if (atcBtn && stickyAtc && 'IntersectionObserver' in window) {
    const stickyObs = new IntersectionObserver(
      ([e]) => {
        const show = !e.isIntersecting && window.innerWidth < 768;
        stickyAtc.classList.toggle('hidden', !show);
        stickyAtc.classList.toggle('block', show);
      },
      { threshold: 0 }
    );
    stickyObs.observe(atcBtn);
  }

  // Size selector
  document.querySelectorAll('.dom-size-btn:not([disabled])').forEach((btn) => {
    btn.addEventListener('click', () => {
      document.querySelectorAll('.dom-size-btn').forEach((b) => {
        b.classList.remove('bg-fg', 'text-white', 'border-fg');
      });
      btn.classList.add('bg-fg', 'text-white', 'border-fg');
    });
  });
})();
