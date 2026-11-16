<?php
defined('ABSPATH') || exit;

$video_url = '';
$fallback_image = '';
$heading_line1 = 'Jouw plek voor';
$heading_line2 = 'betaalbare outfits';
$subtext = 'Bij De Outfitman vind je de mooiste designer items voor de beste prijzen.';
$cta_text = 'Shop alle collecties';
$cta_url = get_permalink(wc_get_page_id('shop'));

if (function_exists('get_field')) {
    $video_url = get_field('hero_video', 'option') ?: '';
    $fallback_image = get_field('hero_image', 'option') ?: '';
    $heading_line1 = get_field('hero_heading_line1', 'option') ?: $heading_line1;
    $heading_line2 = get_field('hero_heading_line2', 'option') ?: $heading_line2;
    $subtext = get_field('hero_subtext', 'option') ?: $subtext;
    $cta_text = get_field('hero_cta_text', 'option') ?: $cta_text;
    $cta_url = get_field('hero_cta_url', 'option') ?: $cta_url;
}

$bf_active = function_exists('get_field') && get_field('bf_active', 'option');
$bf_date = strtotime('2026-11-27 23:59:59');
?>
<section class="relative min-h-[100svh] flex flex-col justify-end overflow-hidden">
    <?php if ($video_url) : ?>
        <video class="absolute inset-0 w-full h-full object-cover scale-105" autoplay muted loop playsinline preload="metadata" poster="<?php echo esc_url($fallback_image); ?>">
            <source src="<?php echo esc_url($video_url); ?>" type="video/mp4">
        </video>
    <?php elseif ($fallback_image) : ?>
        <img src="<?php echo esc_url($fallback_image); ?>" alt="" class="absolute inset-0 w-full h-full object-cover scale-105" loading="eager" fetchpriority="high">
    <?php else : ?>
        <div class="absolute inset-0 bg-gradient-to-br from-surface via-bg to-surface-2"></div>
    <?php endif; ?>

    <!-- Cinematic gradient overlay -->
    <div class="absolute inset-0 bg-gradient-to-t from-white via-white/50 to-white/10"></div>

    <div class="relative z-10 max-w-7xl mx-auto px-4 pb-16 md:pb-24 pt-48 w-full">
        <div class="max-w-2xl">
            <?php if ($bf_active) : ?>
            <div class="hero-reveal inline-flex items-center gap-2.5 px-4 py-2 bg-fg text-white rounded-full text-2xs font-bold uppercase tracking-label mb-6">
                <span class="w-1.5 h-1.5 rounded-full bg-urgency animate-pulse"></span>
                Black Friday — Tot 50% korting
            </div>
            <?php endif; ?>

            <h1 class="hero-reveal mb-5">
                <span class="block text-4xl md:text-6xl lg:text-7xl font-extrabold tracking-tight leading-[0.95] text-muted/50"><?php echo esc_html($heading_line1); ?></span>
                <span class="block text-4xl md:text-6xl lg:text-7xl font-black tracking-tight leading-[0.95] mt-1"><?php echo esc_html($heading_line2); ?></span>
            </h1>

            <p class="hero-reveal text-base md:text-lg text-muted max-w-md mb-8 leading-relaxed">
                <?php echo esc_html($subtext); ?>
            </p>

            <?php if ($bf_active) : ?>
            <div class="hero-reveal flex gap-2.5 mb-8" data-countdown="<?php echo esc_attr(date('c', $bf_date)); ?>">
                <?php foreach ([['dom-cd-d', 'Dagen'], ['dom-cd-h', 'Uren'], ['dom-cd-m', 'Min'], ['dom-cd-s', 'Sec']] as $cd) : ?>
                <div class="text-center bg-fg text-white rounded-lg px-3.5 py-3 min-w-[60px] md:min-w-[72px]">
                    <span class="<?php echo $cd[0]; ?> text-xl md:text-2xl font-extrabold block leading-none font-mono">--</span>
                    <span class="text-[10px] text-white/40 uppercase tracking-label mt-1 block"><?php echo $cd[1]; ?></span>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>

            <div class="hero-reveal flex flex-wrap items-center gap-3">
                <a href="<?php echo esc_url($cta_url); ?>" class="group inline-flex items-center gap-3 px-8 py-4 bg-fg text-white rounded-lg text-sm font-bold uppercase tracking-label hover:gap-4 transition-all duration-300">
                    <?php echo esc_html($cta_text); ?>
                    <svg class="w-4.5 h-4.5 transition-transform group-hover:translate-x-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12,5 19,12 12,19"/></svg>
                </a>
                <a href="<?php echo esc_url(get_permalink(wc_get_page_id('shop')) . '?orderby=date'); ?>" class="inline-flex items-center gap-2 px-6 py-4 border border-fg/15 rounded-lg text-sm font-semibold hover:border-fg/40 transition-colors">
                    Nieuw binnen
                </a>
            </div>
        </div>

        <!-- Scroll indicator -->
        <div class="hidden md:flex absolute bottom-8 left-1/2 -translate-x-1/2 flex-col items-center gap-2 text-muted/40">
            <span class="text-[10px] uppercase tracking-[0.2em]">Scroll</span>
            <div class="w-px h-8 bg-gradient-to-b from-muted/30 to-transparent scroll-line"></div>
        </div>
    </div>
</section>
