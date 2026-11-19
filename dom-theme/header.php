<?php defined('ABSPATH') || exit; ?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preload" href="<?php echo get_template_directory_uri(); ?>/assets/fonts/barlow-condensed/BarlowCondensed-ExtraBold.woff2" as="font" type="font/woff2" crossorigin>
    <link rel="preload" href="<?php echo get_template_directory_uri(); ?>/assets/fonts/inter/Inter-latin.woff2" as="font" type="font/woff2" crossorigin>
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<div class="grain" aria-hidden="true"></div>

<?php get_template_part('template-parts/marquee-bar'); ?>

<header class="hdr" id="siteHeader" role="banner">
    <div class="hdr-in wrap">
        <div class="hdr-l">
            <button class="burger js-menu-open" aria-label="<?php esc_attr_e('Menu openen', 'dom-theme'); ?>" aria-expanded="false" aria-controls="siteMenu">
                <span class="bars" aria-hidden="true"><span></span><span></span><span></span></span>
                <em>Menu</em>
            </button>
        </div>

        <?php
        // Beheer: De Outfitman > Algemeen > Merk. Wat eerder via de Customizer
        // was ingesteld blijft gelden zolang daar niets gekozen is.
        $dom_logo = dom_c_img('algemeen.merk.logo');
        if (!$dom_logo && function_exists('dom_setting_image_url')) {
            $dom_logo = dom_setting_image_url('header_logo');
        }
        ?>
        <a href="<?php echo esc_url(home_url('/')); ?>" class="brand<?php echo $dom_logo ? '' : ' txt'; ?>" aria-label="<?php bloginfo('name'); ?>">
            <?php if ($dom_logo) : ?>
                <img class="logo" src="<?php echo esc_url($dom_logo); ?>" alt="<?php bloginfo('name'); ?>" width="160" height="40">
            <?php endif; ?>
            <span class="brand-txt">De Outf<i>i</i>tman</span>
        </a>

        <div class="hdr-r">
            <button class="ic js-search-open" aria-label="<?php esc_attr_e('Zoeken', 'dom-theme'); ?>">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" aria-hidden="true"><circle cx="10.5" cy="10.5" r="7"/><line x1="16" y1="16" x2="21" y2="21"/></svg>
            </button>
            <button class="cart-b js-cart-open" aria-label="<?php esc_attr_e('Winkelwagen', 'dom-theme'); ?>">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 01-8 0"/></svg>
                <span class="n js-cart-count"><?php echo (function_exists('WC') && WC()->cart) ? WC()->cart->get_cart_contents_count() : '0'; ?></span>
            </button>
        </div>
    </div>
</header>

<?php get_template_part('template-parts/bf-strip'); ?>

<!-- Overlay for menu + cart -->
<div class="ovl js-overlay" aria-hidden="true"></div>

<!-- Slide-in menu -->
<nav class="menu" id="siteMenu" role="dialog" aria-label="<?php esc_attr_e('Navigatie', 'dom-theme'); ?>">
    <div class="menu-bf">
        <div class="mb">
            <div>
                <b>Black Friday Deals</b>
                <?php $dom_hdr_pct = function_exists('dom_max_sale_percentage') ? dom_max_sale_percentage() : 0; ?>
                <?php if ($dom_hdr_pct > 0) : ?><span>Tot <?php echo (int) $dom_hdr_pct; ?>% korting op geselecteerde items</span><?php endif; ?>
            </div>
            <a href="<?php echo esc_url(dom_term_url('sale', 'product_cat')); ?>" class="btn btn-ow" style="height:34px;padding:0 14px;font-size:12px">Shop Sale <span class="arw">&rarr;</span></a>
        </div>
    </div>

    <div class="menu-h">
        <span class="lbl">Menu</span>
        <button class="ic js-menu-close" aria-label="<?php esc_attr_e('Menu sluiten', 'dom-theme'); ?>">
            <svg viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
        </button>
    </div>

    <div class="menu-b">
        <?php
        // Categories live under "Heren" parent
        $menu_categories = function_exists('dom_ordered_categories') ? dom_ordered_categories() : [];
        if (!empty($menu_categories)) :
            foreach ($menu_categories as $cat) :
                // Eigen menuplaatje, anders de categoriefoto, anders het eerste
                // product. Beheer: De Outfitman > Categorieën.
                $thumb_url = dom_c_cat_img($cat->term_id, 'menu', 'thumbnail');
                $is_sale   = ($cat->slug === 'sale');
        ?>
            <a href="<?php echo esc_url(dom_term_url($cat)); ?>" class="ml<?php echo $is_sale ? ' sale' : ''; ?>">
                <span class="mth">
                    <?php if ($thumb_url) : ?>
                        <img src="<?php echo esc_url($thumb_url); ?>" alt="" loading="lazy" width="46" height="58">
                    <?php endif; ?>
                </span>
                <b><?php echo esc_html($cat->name); ?></b>
                <span class="mar" aria-hidden="true">
                    <svg viewBox="0 0 24 24"><polyline points="9,6 15,12 9,18"/></svg>
                </span>
            </a>
        <?php
            endforeach;
        endif;
        ?>
    </div>

</nav>

<!-- Search overlay -->
<div class="ovl js-search-overlay" id="searchOverlay" aria-hidden="true" role="dialog" aria-label="<?php esc_attr_e('Zoeken', 'dom-theme'); ?>">
    <div class="wrap" style="max-width:680px;padding-top:clamp(80px,12vw,160px)">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px">
            <span class="lbl">Zoeken</span>
            <button class="ic js-search-close" aria-label="<?php esc_attr_e('Sluiten', 'dom-theme'); ?>">
                <svg viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
        </div>
        <form role="search" method="get" action="<?php echo esc_url(home_url('/')); ?>">
            <input type="search" name="s" placeholder="Waar ben je naar op zoek?" autocomplete="off" autofocus style="width:100%;background:transparent;border:0;border-bottom:2px solid var(--ink);padding:12px 0;font-family:var(--disp);font-size:clamp(1.5rem,4vw,2.4rem);font-weight:800;text-transform:uppercase;color:var(--ink);outline:none">
            <input type="hidden" name="post_type" value="product">
        </form>
    </div>
</div>

<main id="content" role="main">
