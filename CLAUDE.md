# De Outfitman - Custom WordPress Redesign

## Project Overview

De Outfitman is a Dutch menswear e-commerce store selling branded streetwear and designer fashion at accessible prices. The site targets young Dutch men (18-30) and is heavily influencer-driven (Djezja, Furkan, Kubi de Barber, Fato YT). The current site runs WordPress/WooCommerce with the Ecomus theme and Elementor — both are being fully replaced by a custom-built theme and custom plugins.

- **Live URL:** https://deoutfitman.com
- **Sister sites:** https://deklokjesman.com (watches), https://parfum4car.nl (car perfume)
- **Language:** Dutch (nl_NL)
- **Backend:** WordPress 6.7+ / WooCommerce 10.3+
- **Hosting:** Shared/VPS with SSH access

## Tech Stack

| Layer | Technology |
|---|---|
| CMS | WordPress + WooCommerce (stays) |
| Theme | `dom-theme` — fully custom, zero page-builder dependency |
| Content fields | ACF Pro (Advanced Custom Fields) |
| Styling | Tailwind CSS (compiled, not CDN) + custom CSS where needed |
| Typography | Geist (Sans + Mono) via local @font-face — no Google Fonts dependency |
| JavaScript | Vanilla JS (ES2020+), no jQuery, no React |
| Animations | CSS transitions/animations + IntersectionObserver for scroll reveals |
| Icons | Inline SVG only |
| Build | Tailwind CLI for CSS compilation |
| Deploy | SSH + Git pull on server |
| SEO | AIOSEO (kept) + semantic HTML + structured data |
| Analytics | Site Kit by Google (kept) |

### What we removed

- Elementor and Element Pack Pro — all Elementor data will be cleaned from the database
- Ecomus theme
- nxs-whatsapp-reviews plugin (replaced by dom-reviews)
- jQuery frontend dependency (WooCommerce admin may still use it)

## Design System

### Design Direction

Clean, white-based design with animated silver gradient accents. Premium but not dark. Think luxury menswear editorial — bright, spacious, with metallic silver as the signature accent that subtly shimmers.

### Color Tokens

```
--bg:        #FFFFFF    /* clean white background */
--bg-off:    #FAFAF9    /* off-white for alternating sections */
--fg:        #0A0A0A    /* near-black text */
--accent:    #9CA3AF    /* silver base — used in gradient */
--accent-2:  #D1D5DB    /* lighter silver for gradient endpoint */
--surface:   #F5F5F4    /* cards, raised elements */
--surface-2: #EBEBEA    /* secondary surface (dropdowns, tooltips) */
--border:    #E5E5E4    /* subtle borders */
--muted:     #71717A    /* secondary text */
--urgency:   #EF4444    /* stock warnings, sale badges */
--success:   #22C55E    /* delivery estimates, confirmations */
--overlay:   rgba(0,0,0,0.5)  /* modals, drawers */
```

### Silver Gradient Accent (signature element)

The brand accent is an animated silver gradient used on CTAs, highlights, progress bars, and key UI elements:
```css
.silver-accent {
  background: linear-gradient(135deg, #9CA3AF, #E5E7EB, #9CA3AF, #D1D5DB);
  background-size: 300% 300%;
  animation: silverShimmer 6s ease infinite;
}
@keyframes silverShimmer {
  0% { background-position: 0% 50%; }
  50% { background-position: 100% 50%; }
  100% { background-position: 0% 50%; }
}
```
Text on silver gradient: use `#0A0A0A` (dark) for contrast.

### Black Friday Seasonal Overlay (November only)

During BF period, add BF-specific banners and countdown but keep the white base. BF elements use:
```
--bf-dark:   #0A0A0A    /* BF banner backgrounds */
--bf-silver: animated silver gradient on BF CTAs
```
The site does NOT go fully dark for BF — BF is an accent, not a takeover.

### Typography

- **Display / Headings:** Geist Sans, weight 600-800, tracking tight (-0.02em)
- **Body:** Geist Sans, weight 400-500
- **Mono / Data:** Geist Mono (prices, stock counts, order numbers)
- **Scale:** 12 / 13 / 14 / 16 / 20 / 24 / 32 / 40 / 48 / 64 px
- **Uppercase labels:** always add letter-spacing: 0.06em

### Spacing

Base unit: 4px. Use multiples: 4, 8, 12, 16, 20, 24, 32, 40, 48, 64, 80, 96.

### Border Radius

- `--radius-sm`: 6px (buttons, inputs, small cards)
- `--radius-md`: 10px (product cards, dropdowns)
- `--radius-lg`: 16px (hero, sections, modals)
- `--radius-full`: 9999px (pills, badges, avatars)

## Site Structure

### Product Categories

Sneakers, Jassen, Jeans, Trainingspakken, Sweaters/Hoodies, T-Shirts, Zwembroeken, Slippers, Petten, Brillen, Tassen, Sale

Note: "Bestsellers" category is removed from navigation and shop page. "Petten" is added.

### Pages

| Page | Template | Notes |
|---|---|---|
| Homepage | `front-page.php` | Hero video, collections, klokjesman, reviews, influencers |
| Shop / Archive | `archive-product.php` | Category banner, pills, filter (prijs + maat only) |
| Category | `taxonomy-product_cat.php` | Inherits shop layout + per-category banner |
| Product | `single-product.php` | Conversion-optimized layout (see below) |
| Cart | Slide-in drawer component | Not a separate page |
| Checkout | WooCommerce default (styled) | Keep standard WooCommerce flow |
| My Account | WooCommerce default (styled) | |
| Contact / FAQ | Custom template | |

## Page Specifications

### Homepage Sections (top to bottom)

1. **Marquee Announcement Bar** — continuous scrolling ticker, dark bg, gold accents. Messages: shipping, WhatsApp service, returns, authenticity. Editable via ACF options page.
2. **Site Header** — hamburger menu, logo "DE OUTFITMAN" (the i in silver accent), search, cart icon with count
3. **Hero Section** — full-width video background with Black Friday messaging. Must support video upload via ACF. Fallback to image. CTA: "SHOP ALLE COLLECTIES"
4. **Collection Grid** — 3x3 grid of all categories. Each card: background image + category name overlay. Hover: subtle scale + darker overlay
5. **Accessories Full Banner** — NOT three separate cards. One cohesive full-width section combining Brillen, Petten, Tassen in a single visual banner. Think editorial spread, not three boxes.
6. **De Klokjesman** — slider/carousel cross-promotion section. Watch imagery, silver accent, link to deklokjesman.com
7. **Parfum4car.nl** — slider/carousel cross-promotion with influencer video thumbnails. NOT in footer — dedicated section on homepage.
8. **Reviews** — WhatsApp-style review cards. 3 columns desktop, 2 mobile. Each card: screenshot image + stars + name (NO phone numbers visible). Managed via custom dom-reviews plugin.
9. **Worn By / Influencer Section** — TikTok/Reels-style vertical cards (9:16 aspect). Beautiful slider with navigation controls. Play button overlay. Names + handles. NOT just a horizontal scroll — a proper slider component with arrows and dot indicators.
10. **Footer** — 4 columns (Shop, Klantenservice, Over Ons, Volg Ons) + newsletter signup + payment icons + copyright

### Shop / Category Page

- **Category pills** — horizontal scrollable pill buttons for quick category switching
- **Filters** — ONLY Prijs (price range) and Maat (size). NO brand/merk filter.
- **Category banner** — horizontal banner image per category (ACF taxonomy field). Always horizontal/landscape format. Placed BETWEEN filters and product grid (not at top).
- **Product grid** — 2 columns mobile, 3 tablet, 4 desktop. Each card has swipeable photo carousel (dots indicator), brand name ABOVE product title, price.
- **Sort** — dropdown: Populair, Prijs laag-hoog, Prijs hoog-laag, Nieuwste

### Product Page (exact order, top to bottom)

1. **Photo gallery** — horizontal swipeable carousel, full-width on mobile
2. **Price** — large, prominent. If on sale: new price + strikethrough old price
3. **Stock urgency** — dynamic based on real WooCommerce stock:
   - 1 in stock: "Laatste op voorraad — wees er snel bij!" (pulsing red dot)
   - 2 in stock: "Nog 2 op voorraad — twijfel niet!"
   - 3-6 in stock: "Bijna uitverkocht"
   - 7+: no message
4. **Size selector** — button grid, out-of-stock sizes shown greyed/strikethrough
5. **Color selector** — per product, if applicable (ACF field)
6. **Add to Cart button** — full width, dark/prominent
7. **Delivery estimate** — "Voor 18:00 besteld, [dynamic date] in huis" with truck icon, green color
8. **4 USP's** — grid of 2x2: Snelle levering, 14 dagen retour, Veilig betalen, 24/7 WhatsApp support
9. **Maatadvies** — collapsible dropdown with sizing info per category
10. **Verzending & Retournering** — collapsible dropdown
11. **Smart Upsell** — "Maak je outfit compleet" — horizontal scroll of complementary products from same or related category
12. **Reviews** — product-specific reviews section
13. **Same category products** — carousel of products from same category
14. **FAQ** — collapsible, same questions as current but styled better

### Cart Drawer

- Slide-in from right, overlay background
- **Progress bar with milestones:**
  - €200: gratis verzendkosten
  - €350: gratis mystery shirt
- Cart items: product image, name, size/color, price, quantity +/- controls
- "Aanbevolen voor jou" product carousel
- Subtotal + "AFREKENEN" CTA button
- Trust badges: Veilig betalen, Snelle levering, 14 dagen retour

## Custom Plugins

All plugins live in `/dom-plugins/` and are symlinked or deployed to `/wp-content/plugins/`.

| Plugin | Slug | Purpose |
|---|---|---|
| Smart Upsell | `dom-smart-upsell` | Cross-sell/upsell on product page and cart based on category + price range |
| Stock Urgency | `dom-stock-urgency` | Dynamic urgency messages based on real WooCommerce stock levels |
| Bundle Deals | `dom-bundle-deals` | "3 voor €130" logic, auto-apply discounts, cart progress bar rewards |
| Delivery Estimate | `dom-delivery-estimate` | Dynamic "Voor 18:00 besteld, [date] in huis" calculation (weekdays, cutoff time) |
| Reviews | `dom-reviews` | WhatsApp screenshot reviews, admin upload interface, star ratings, grid display |
| Announcement Bar | `dom-announcement-bar` | Marquee ticker, multiple messages, admin editable, gold dot separators |

## Code Style

- **CSS:** Tailwind utility classes for layout and spacing. Custom CSS (BEM naming) only for complex components that Tailwind handles poorly (marquee animation, carousel, drawer transitions).
- **HTML:** Semantic elements. `<header>`, `<main>`, `<section>`, `<article>`, `<nav>`, `<footer>`. Proper heading hierarchy.
- **PHP:** WordPress coding standards. Template parts in `template-parts/`. WooCommerce overrides in `woocommerce/`.
- **JS:** Vanilla ES2020+. No build step for JS (or minimal esbuild if needed). IntersectionObserver for scroll animations. No heavy frameworks.
- **Accessibility:** aria-labels on icon buttons, focus-visible states, reduced-motion media query respected, color contrast AA minimum.
- **Mobile-first:** All CSS starts at mobile width, scales up with `@media (min-width: ...)`.

## Performance Targets

| Metric | Target |
|---|---|
| PageSpeed (mobile) | 90+ |
| LCP | < 1.5s |
| CLS | < 0.05 |
| FID/INP | < 100ms |
| Total CSS | < 50 KB |
| Total JS | < 80 KB |
| DOM elements | < 800 |

### Performance rules

- All images: WebP format, lazy loading (except hero/above-fold)
- Fonts: local @font-face with `font-display: swap`, preload critical weights
- No render-blocking resources
- Critical CSS inlined in `<head>`
- Defer all non-critical JS
- Redis object cache on server
- Page cache (WP Super Cache or similar)
- CDN: Cloudflare

## Theme File Structure

```
dom-theme/
├── style.css                    # Theme header (required by WP)
├── functions.php                # Theme setup, enqueues, hooks
├── header.php                   # <head>, announcement bar, site header
├── footer.php                   # Site footer, closing tags
├── front-page.php               # Homepage
├── archive-product.php          # Shop / product archive
├── taxonomy-product_cat.php     # Category pages
├── single-product.php           # Product detail page
├── page.php                     # Generic page template
├── 404.php                      # Not found page
├── searchform.php               # Search form
├── template-parts/
│   ├── marquee-bar.php          # Announcement marquee
│   ├── hero-video.php           # Homepage hero
│   ├── collection-grid.php      # 3x3 category grid
│   ├── accessories-banner.php   # Full-width accessory section
│   ├── klokjesman.php           # Watch cross-promo
│   ├── parfum4car.php           # Parfum cross-promo
│   ├── reviews-grid.php         # Reviews display
│   ├── influencer-scroll.php    # Influencer cards
│   ├── product-gallery.php      # Swipeable product images
│   ├── stock-urgency.php        # Dynamic stock message
│   ├── size-selector.php        # Size button grid
│   ├── delivery-estimate.php    # Dynamic delivery date
│   ├── usp-grid.php             # 4 USP items
│   ├── smart-upsell.php         # Upsell carousel
│   └── cart-drawer.php          # Slide-in cart
├── woocommerce/
│   ├── archive-product.php      # Product loop override
│   ├── content-product.php      # Single product in loop
│   ├── single-product/
│   │   ├── title.php
│   │   ├── price.php
│   │   ├── add-to-cart/
│   │   └── meta.php
│   ├── cart/                    # Cart template overrides
│   └── checkout/                # Checkout template overrides
├── assets/
│   ├── css/
│   │   ├── tailwind.config.js   # Tailwind configuration with design tokens
│   │   ├── input.css            # Tailwind directives + custom CSS
│   │   └── style.css            # Compiled output (committed)
│   ├── js/
│   │   ├── main.js              # Core: drawer, marquee, scroll animations
│   │   ├── cart.js              # Cart drawer AJAX logic
│   │   ├── gallery.js           # Product image carousel
│   │   └── filters.js           # Shop page AJAX filters
│   └── fonts/
│       ├── GeistVF.woff2        # Geist Sans variable font
│       └── GeistMonoVF.woff2    # Geist Mono variable font
├── acf-json/                    # ACF field group JSON exports (version controlled)
├── inc/
│   ├── woocommerce.php          # WooCommerce hooks, filters, support
│   ├── acf-fields.php           # ACF field registration (fallback if no ACF JSON)
│   ├── rest-api.php             # Custom REST API endpoints (cart, filters)
│   ├── theme-setup.php          # Menus, widget areas, image sizes
│   └── enqueue.php              # Script and style enqueuing
└── screenshot.png               # Theme screenshot for WP admin
```

## Deploy Workflow

1. Push to `main` branch on GitHub
2. SSH into server
3. `cd /var/www/deoutfitman.com/wp-content/themes/dom-theme`
4. `git pull origin main`
5. Rebuild Tailwind if CSS changed: `npx tailwindcss -i assets/css/input.css -o assets/css/style.css --minify`
6. Clear caches: `wp cache flush && wp rewrite flush`

## Switch Strategy (Old → New Theme)

The old theme stays active while we build. The new theme is installed alongside it.

1. Build and test `dom-theme` in staging or as inactive theme
2. Install and activate custom plugins
3. Configure ACF fields and populate content
4. Final review on staging
5. Switch: `wp theme activate dom-theme`
6. Deactivate Elementor + Element Pack
7. Run DB cleanup: remove Elementor metadata, revisions, transients
8. Verify everything works
9. Remove old theme files

Rollback: `wp theme activate [old-theme-slug]` — instant, no data loss.

## Conversion Optimization Notes

- Every product page must create urgency without being dishonest (stock messages use REAL WooCommerce stock)
- Delivery estimate must be accurate (skip weekends, respect 18:00 cutoff)
- Cart progress bar motivates higher order value (gratis verzending at €200, mystery shirt at €350)
- Upsells are category-aware: suggest complementary items, not random products
- No "what others have viewed" section (removed per brief)
- Size selector shows out-of-stock sizes as greyed out (not hidden) to create scarcity
- WhatsApp link always accessible for customer service (builds trust)
