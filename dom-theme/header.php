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

<header class="sticky top-0 z-50 bg-white/90 backdrop-blur-md border-b border-border" role="banner">
    <div class="max-w-7xl mx-auto px-4 flex items-center justify-between h-16">
        <!-- Hamburger -->
        <div class="flex items-center gap-3">
            <button class="dom-menu-toggle w-10 h-10 flex items-center justify-center rounded-full border border-border hover:border-accent transition-colors" aria-label="<?php esc_attr_e('Menu', 'dom-theme'); ?>" aria-expanded="false">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><line x1="4" y1="7" x2="20" y2="7"/><line x1="4" y1="12" x2="20" y2="12"/><line x1="4" y1="17" x2="20" y2="17"/></svg>
            </button>

            <!-- Logo -->
            <a href="<?php echo esc_url(home_url('/')); ?>" class="text-xl font-extrabold tracking-tight uppercase" aria-label="<?php bloginfo('name'); ?>">
                De Outf<span class="silver-text">i</span>tman
            </a>
        </div>

        <!-- Right icons -->
        <div class="flex items-center gap-3">
            <button class="dom-search-toggle w-10 h-10 flex items-center justify-center rounded-full border border-border hover:border-accent transition-colors" aria-label="<?php esc_attr_e('Zoeken', 'dom-theme'); ?>">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><circle cx="11" cy="11" r="7"/><line x1="16.5" y1="16.5" x2="21" y2="21"/></svg>
            </button>

            <button class="dom-cart-toggle w-10 h-10 flex items-center justify-center rounded-full border border-border hover:border-accent transition-colors relative" aria-label="<?php esc_attr_e('Winkelwagen', 'dom-theme'); ?>">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 01-8 0"/></svg>
                <?php if (function_exists('WC') && WC()->cart && WC()->cart->get_cart_contents_count() > 0) : ?>
                    <span class="dom-cart-count absolute -top-0.5 -right-0.5 w-5 h-5 silver-gradient text-fg text-[10px] font-bold rounded-full flex items-center justify-center"><?php echo WC()->cart->get_cart_contents_count(); ?></span>
                <?php endif; ?>
            </button>
        </div>
    </div>
</header>

<main id="content" role="main">
