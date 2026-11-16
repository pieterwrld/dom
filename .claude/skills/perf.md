---
name: perf
description: Run performance checks on the dom-theme against project targets
---

# Performance Check

## Targets (from CLAUDE.md)

| Metric | Target |
|---|---|
| PageSpeed (mobile) | 90+ |
| LCP | < 1.5s |
| CLS | < 0.05 |
| INP | < 100ms |
| Total CSS | < 50 KB |
| Total JS | < 80 KB |
| DOM elements | < 800 |

## Quick Checks (local)

### 1. Asset sizes
```bash
wc -c dom-theme/assets/css/style.css
wc -c dom-theme/assets/js/*.js
# Compare against 50KB CSS / 80KB JS targets
```

### 2. DOM element count (estimate)
Count elements in template output. A page with >800 elements needs simplification.

### 3. Image optimization
```bash
find dom-theme/ -name "*.png" -o -name "*.jpg" | head -20
# All should be converted to WebP
# Check that lazy loading is set on below-fold images
```

### 4. Render-blocking resources
Check `header.php` — only critical CSS should be in `<head>`. All JS should be deferred or in footer.

### 5. Font loading
Verify Geist fonts use:
- `font-display: swap`
- `<link rel="preload">` for critical weights
- Local @font-face (no external requests)

## Production Checks

### Lighthouse via CLI
```bash
lighthouse https://deoutfitman.com --only-categories=performance --output=json --chrome-flags="--headless --no-sandbox"
```

### Key things to verify
- [ ] No Elementor CSS/JS still loading
- [ ] No jQuery on frontend (unless WooCommerce requires it)
- [ ] Images served as WebP
- [ ] Fonts preloaded
- [ ] No layout shift from lazy-loaded content
- [ ] Cart drawer JS only loads when needed
- [ ] Gallery JS only loads on product pages

## Common fixes

| Issue | Fix |
|---|---|
| LCP too high | Preload hero image/video poster, inline critical CSS |
| CLS > 0 | Set explicit dimensions on images, reserve space for dynamic content |
| Large CSS | Purge unused Tailwind classes in production build |
| Large JS | Split per-page JS, defer non-critical scripts |
| Too many DOM nodes | Simplify template markup, reduce wrapper divs |
