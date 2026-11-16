/**
 * De Outfitman — Core JS
 * Scroll reveals, slider, cart toggle, countdown, sticky ATC, mobile menu
 */
(function () {
  'use strict';

  // Scroll reveal with stagger
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
      { threshold: 0.08, rootMargin: '0px 0px -40px 0px' }
    );
    document.querySelectorAll('.reveal').forEach((el) => obs.observe(el));

    const heroObs = new IntersectionObserver(
      (entries) => {
        entries.forEach((e) => {
          if (e.isIntersecting) {
            e.target.classList.add('visible');
            heroObs.unobserve(e.target);
          }
        });
      },
      { threshold: 0.1 }
    );
    document.querySelectorAll('.hero-reveal').forEach((el) => heroObs.observe(el));
  } else {
    document.querySelectorAll('.reveal, .hero-reveal').forEach((el) => el.classList.add('visible'));
  }

  // Smooth scroll slider (native scroll-snap based)
  document.querySelectorAll('[data-slider]').forEach((slider) => {
    const track = slider.querySelector('.dom-slider-track');
    const slides = slider.querySelectorAll('.dom-slider-slide');
    const prevBtn = slider.querySelector('.dom-slider-prev');
    const nextBtn = slider.querySelector('.dom-slider-next');
    const dotsContainer = slider.querySelector('.dom-slider-dots');

    if (!track || slides.length === 0) return;

    let current = 0;
    const total = slides.length;

    if (dotsContainer) {
      slides.forEach((_, i) => {
        const dot = document.createElement('button');
        dot.className = 'w-2 h-2 rounded-full transition-all duration-300 ' + (i === 0 ? 'bg-white w-6' : 'bg-white/30');
        dot.setAttribute('aria-label', 'Slide ' + (i + 1));
        dot.addEventListener('click', () => goTo(i));
        dotsContainer.appendChild(dot);
      });
    }

    function goTo(index) {
      current = Math.max(0, Math.min(index, total - 1));
      const slideWidth = slides[0].offsetWidth + parseInt(getComputedStyle(track).gap || '0');
      track.scrollTo({ left: current * slideWidth, behavior: 'smooth' });
      updateDots();
    }

    function updateDots() {
      if (!dotsContainer) return;
      dotsContainer.querySelectorAll('button').forEach((d, i) => {
        if (i === current) {
          d.className = 'w-6 h-2 rounded-full transition-all duration-300 bg-white';
        } else {
          d.className = 'w-2 h-2 rounded-full transition-all duration-300 bg-white/30';
        }
      });
    }

    if (prevBtn) prevBtn.addEventListener('click', () => goTo(current - 1));
    if (nextBtn) nextBtn.addEventListener('click', () => goTo(current + 1));

    let startX = 0;
    track.addEventListener('touchstart', (e) => { startX = e.touches[0].clientX; }, { passive: true });
    track.addEventListener('touchend', (e) => {
      const diff = startX - e.changedTouches[0].clientX;
      if (Math.abs(diff) > 50) goTo(current + (diff > 0 ? 1 : -1));
    }, { passive: true });
  });

  // Cart drawer toggle
  const cartOverlay = document.getElementById('cartOverlay');

  function toggleCart(open) {
    if (!cartOverlay) return;
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

  // Mobile menu toggle
  const menuOverlay = document.getElementById('mobileMenu');
  function toggleMenu(open) {
    if (!menuOverlay) return;
    const isOpen = open ?? !menuOverlay.classList.contains('open');
    menuOverlay.classList.toggle('open', isOpen);
    document.body.style.overflow = isOpen ? 'hidden' : '';
  }
  document.querySelectorAll('.dom-menu-toggle').forEach((btn) => {
    btn.addEventListener('click', () => toggleMenu());
  });
  document.querySelectorAll('.dom-menu-close').forEach((btn) => {
    btn.addEventListener('click', () => toggleMenu(false));
  });

  // Escape key closes drawers/menus
  document.addEventListener('keydown', (e) => {
    if (e.key !== 'Escape') return;
    if (cartOverlay && cartOverlay.classList.contains('open')) toggleCart(false);
    if (menuOverlay && menuOverlay.classList.contains('open')) toggleMenu(false);
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

  // Header scroll: compact on scroll
  const header = document.querySelector('header[role="banner"]');
  if (header) {
    let lastY = 0;
    let ticking = false;
    window.addEventListener('scroll', () => {
      if (!ticking) {
        requestAnimationFrame(() => {
          const y = window.scrollY;
          header.classList.toggle('shadow-sm', y > 50);
          lastY = y;
          ticking = false;
        });
        ticking = true;
      }
    }, { passive: true });
  }
})();
