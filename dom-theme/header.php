<?php defined('ABSPATH') || exit; ?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preload" href="<?php echo DOM_THEME_URI; ?>/assets/fonts/GeistVF.woff2" as="font" type="font/woff2" crossorigin>
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<?php get_template_part('template-parts/marquee-bar'); ?>

<header class="sticky top-0 z-50 bg-white/90 backdrop-blur-md border-b border-transparent transition-[border-color,box-shadow] duration-300" role="banner" id="siteHeader">
    <div class="max-w-7xl mx-auto px-4 flex items-center justify-between h-14 md:h-16">
        <!-- Left: hamburger + logo (desktop) -->
        <div class="flex items-center gap-2 md:gap-3 flex-1 md:flex-none">
            <button class="dom-menu-toggle w-9 h-9 md:w-10 md:h-10 flex items-center justify-center rounded-full border border-border hover:border-fg transition-colors" aria-label="<?php esc_attr_e('Menu', 'dom-theme'); ?>" aria-expanded="false">
                <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><line x1="4" y1="7" x2="20" y2="7"/><line x1="4" y1="12" x2="16" y2="12"/><line x1="4" y1="17" x2="20" y2="17"/></svg>
            </button>
            <a href="<?php echo esc_url(home_url('/')); ?>" class="hidden md:block text-lg font-extrabold tracking-tight uppercase" aria-label="<?php bloginfo('name'); ?>">
                De Outf<span class="silver-text">i</span>tman
            </a>
        </div>

        <!-- Center: logo (mobile only) -->
        <a href="<?php echo esc_url(home_url('/')); ?>" class="md:hidden absolute left-1/2 -translate-x-1/2 text-lg font-extrabold tracking-tight uppercase" aria-label="<?php bloginfo('name'); ?>">
            De Outf<span class="silver-text">i</span>tman
        </a>

        <!-- Right icons -->
        <div class="flex items-center gap-1.5 md:gap-2 flex-1 md:flex-none justify-end">
            <button class="dom-search-toggle w-9 h-9 md:w-10 md:h-10 flex items-center justify-center rounded-full border border-border hover:border-fg transition-colors" aria-label="<?php esc_attr_e('Zoeken', 'dom-theme'); ?>">
                <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><circle cx="11" cy="11" r="7"/><line x1="16.5" y1="16.5" x2="21" y2="21"/></svg>
            </button>

            <button class="dom-cart-toggle w-9 h-9 md:w-10 md:h-10 flex items-center justify-center rounded-full border border-border hover:border-fg transition-colors relative" aria-label="<?php esc_attr_e('Winkelwagen', 'dom-theme'); ?>">
                <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 01-8 0"/></svg>
                <?php if (function_exists('WC') && WC()->cart && WC()->cart->get_cart_contents_count() > 0) : ?>
                    <span class="dom-cart-count absolute -top-0.5 -right-0.5 min-w-[18px] h-[18px] silver-gradient text-fg text-[10px] font-bold rounded-full flex items-center justify-center px-1 animate-[scaleIn_0.3s_ease]"><?php echo WC()->cart->get_cart_contents_count(); ?></span>
                <?php endif; ?>
            </button>
        </div>
    </div>
</header>

<!-- Mobile Menu -->
<div class="mobile-menu" id="mobileMenu" aria-label="<?php esc_attr_e('Navigatie', 'dom-theme'); ?>" role="dialog">
    <div class="flex flex-col h-full">
        <!-- Menu header -->
        <div class="flex items-center justify-between px-5 py-4 border-b border-border">
            <span class="text-lg font-extrabold tracking-tight uppercase">De Outf<span class="silver-text">i</span>tman</span>
            <button class="dom-menu-close w-9 h-9 flex items-center justify-center rounded-full border border-border hover:border-fg transition-colors" aria-label="<?php esc_attr_e('Sluiten', 'dom-theme'); ?>">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
        </div>

        <!-- Menu links -->
        <nav class="flex-1 overflow-y-auto px-5 py-6">
            <ul class="space-y-1">
                <?php
                $menu_cats = [
                    'sneakers' => 'Sneakers',
                    'jassen' => 'Jassen',
                    'jeans' => 'Jeans',
                    'trainingspakken' => 'Trainingspakken',
                    'sweaters-hoodies' => 'Sweaters & Hoodies',
                    't-shirts' => 'T-Shirts',
                    'zwembroeken' => 'Zwembroeken',
                    'slippers' => 'Slippers',
                    'petten' => 'Petten',
                    'brillen' => 'Brillen',
                    'tassen' => 'Tassen',
                ];
                foreach ($menu_cats as $slug => $label) :
                    $term = get_term_by('slug', $slug, 'product_cat');
                    $url = $term ? get_term_link($term) : get_permalink(wc_get_page_id('shop'));
                ?>
                    <li>
                        <a href="<?php echo esc_url($url); ?>" class="flex items-center justify-between py-3 text-base font-semibold hover:text-muted transition-colors border-b border-border/50">
                            <?php echo esc_html($label); ?>
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" class="text-muted"><polyline points="9,6 15,12 9,18"/></svg>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>

            <!-- Sale link -->
            <a href="<?php echo esc_url(get_term_link('sale', 'product_cat')); ?>" class="flex items-center justify-center gap-2 mt-6 py-3 bg-urgency text-white text-sm font-bold uppercase tracking-label rounded-lg">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><path d="M20.59 13.41l-7.17 7.17a2 2 0 01-2.83 0L2 12V2h10l8.59 8.59a2 2 0 010 2.82z"/><line x1="7" y1="7" x2="7.01" y2="7"/></svg>
                Sale
            </a>

            <!-- Secondary links -->
            <div class="mt-8 pt-6 border-t border-border space-y-3">
                <a href="/contact" class="block text-sm text-muted hover:text-fg transition-colors">Contact</a>
                <a href="/faq" class="block text-sm text-muted hover:text-fg transition-colors">Veelgestelde vragen</a>
                <a href="/verzending" class="block text-sm text-muted hover:text-fg transition-colors">Verzending & retourneren</a>
                <a href="<?php echo esc_url(wc_get_page_permalink('myaccount')); ?>" class="block text-sm text-muted hover:text-fg transition-colors">Mijn account</a>
            </div>

            <!-- Sister brands -->
            <div class="mt-8 pt-6 border-t border-border">
                <span class="block text-2xs font-bold uppercase tracking-label text-muted mb-3">Meer van ons</span>
                <div class="flex gap-3">
                    <a href="https://deklokjesman.com" target="_blank" rel="noopener" class="flex-1 py-3 border border-border rounded-lg text-center text-xs font-semibold hover:border-fg transition-colors">De Klokjesman</a>
                    <a href="https://parfum4car.nl" target="_blank" rel="noopener" class="flex-1 py-3 border border-border rounded-lg text-center text-xs font-semibold hover:border-fg transition-colors">Parfum4Car</a>
                </div>
            </div>
        </nav>

        <!-- Menu footer -->
        <div class="px-5 py-4 border-t border-border">
            <a href="https://wa.me/31612345678" target="_blank" rel="noopener" class="flex items-center justify-center gap-2 py-3 bg-[#25D366] text-white text-sm font-bold rounded-lg">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                Chat met ons
            </a>
        </div>
    </div>
</div>

<!-- Search Overlay -->
<div class="search-overlay" id="searchOverlay">
    <div class="max-w-2xl mx-auto px-4 pt-20 md:pt-32">
        <div class="flex items-center justify-between mb-8">
            <span class="text-2xs font-bold uppercase tracking-label text-muted">Zoeken</span>
            <button class="dom-search-close w-9 h-9 flex items-center justify-center rounded-full border border-border hover:border-fg transition-colors" aria-label="<?php esc_attr_e('Sluiten', 'dom-theme'); ?>">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
        </div>
        <form role="search" method="get" action="<?php echo esc_url(home_url('/')); ?>">
            <input type="search" name="s" placeholder="Waar ben je naar op zoek?" class="w-full text-3xl md:text-4xl font-bold bg-transparent border-b-2 border-fg pb-4 focus:outline-none placeholder:text-muted/40" autocomplete="off" autofocus>
            <input type="hidden" name="post_type" value="product">
        </form>
        <div class="mt-8">
            <span class="block text-2xs font-bold uppercase tracking-label text-muted mb-3">Populair</span>
            <div class="flex flex-wrap gap-2">
                <?php
                $popular_terms = ['Sneakers', 'Jassen', 'Trainingspakken', 'Sale', 'Nike', 'Essentials'];
                foreach ($popular_terms as $term_name) : ?>
                    <a href="<?php echo esc_url(home_url('/?s=' . urlencode($term_name) . '&post_type=product')); ?>" class="px-4 py-2 border border-border rounded-full text-sm font-medium text-muted hover:border-fg hover:text-fg transition-colors"><?php echo esc_html($term_name); ?></a>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<!-- WhatsApp Floating Button -->
<a href="https://wa.me/31612345678" target="_blank" rel="noopener" class="fixed bottom-5 right-5 z-[90] w-14 h-14 bg-[#25D366] rounded-full flex items-center justify-center shadow-lg shadow-[#25D366]/30 hover:scale-110 hover:shadow-xl transition-all" aria-label="WhatsApp">
    <svg width="26" height="26" viewBox="0 0 24 24" fill="white"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
</a>

<main id="content" role="main">
