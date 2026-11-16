<?php
defined('ABSPATH') || exit;

$video_url = '';
$fallback_image = '';
$heading = 'Black Friday Deals';
$subtext = 'De beste designer items voor de laagste prijzen van het jaar.';
$cta_text = 'Shop alle deals';
$cta_url = get_permalink(wc_get_page_id('shop'));

if (function_exists('get_field')) {
    $video_url = get_field('hero_video', 'option') ?: '';
    $fallback_image = get_field('hero_image', 'option') ?: '';
    $heading = get_field('hero_heading', 'option') ?: $heading;
    $subtext = get_field('hero_subtext', 'option') ?: $subtext;
    $cta_text = get_field('hero_cta_text', 'option') ?: $cta_text;
    $cta_url = get_field('hero_cta_url', 'option') ?: $cta_url;
}

$bf_date = strtotime('2026-11-27 00:00:00');
?>
<section class="relative min-h-[80vh] flex flex-col justify-end overflow-hidden">
    <?php if ($video_url) : ?>
        <video class="absolute inset-0 w-full h-full object-cover" autoplay muted loop playsinline preload="metadata" poster="<?php echo esc_url($fallback_image); ?>">
            <source src="<?php echo esc_url($video_url); ?>" type="video/mp4">
        </video>
    <?php elseif ($fallback_image) : ?>
        <img src="<?php echo esc_url($fallback_image); ?>" alt="" class="absolute inset-0 w-full h-full object-cover" loading="eager">
    <?php else : ?>
        <div class="absolute inset-0 bg-gradient-to-br from-surface to-surface-2"></div>
    <?php endif; ?>

    <div class="absolute inset-0 bg-gradient-to-t from-white via-white/60 to-transparent"></div>

    <div class="relative z-10 max-w-7xl mx-auto px-4 pb-12 pt-32">
        <div class="max-w-lg">
            <div class="reveal inline-flex items-center gap-2 px-3.5 py-1.5 bg-fg/5 border border-border rounded-full text-2xs font-bold uppercase tracking-label text-muted mb-5">
                <span class="w-1.5 h-1.5 rounded-full silver-gradient animate-pulse"></span>
                Black Friday 2026
            </div>

            <h1 class="reveal text-5xl md:text-6xl font-black uppercase tracking-tight leading-none mb-4">
                <?php echo esc_html($heading); ?>
            </h1>

            <p class="reveal text-base text-muted mb-6 max-w-sm">
                <?php echo esc_html($subtext); ?>
            </p>

            <!-- Countdown -->
            <div class="reveal flex gap-3 mb-7" data-countdown="<?php echo esc_attr(date('c', $bf_date)); ?>">
                <div class="text-center bg-surface border border-border rounded-md px-4 py-3 min-w-[64px]">
                    <span class="dom-cd-d text-2xl font-extrabold block leading-none font-mono">--</span>
                    <span class="text-[10px] text-muted uppercase tracking-label mt-1 block">Dagen</span>
                </div>
                <div class="text-center bg-surface border border-border rounded-md px-4 py-3 min-w-[64px]">
                    <span class="dom-cd-h text-2xl font-extrabold block leading-none font-mono">--</span>
                    <span class="text-[10px] text-muted uppercase tracking-label mt-1 block">Uren</span>
                </div>
                <div class="text-center bg-surface border border-border rounded-md px-4 py-3 min-w-[64px]">
                    <span class="dom-cd-m text-2xl font-extrabold block leading-none font-mono">--</span>
                    <span class="text-[10px] text-muted uppercase tracking-label mt-1 block">Min</span>
                </div>
                <div class="text-center bg-surface border border-border rounded-md px-4 py-3 min-w-[64px]">
                    <span class="dom-cd-s text-2xl font-extrabold block leading-none font-mono">--</span>
                    <span class="text-[10px] text-muted uppercase tracking-label mt-1 block">Sec</span>
                </div>
            </div>

            <a href="<?php echo esc_url($cta_url); ?>" class="reveal inline-flex items-center gap-2.5 px-8 py-4 silver-gradient text-fg rounded-md text-sm font-bold uppercase tracking-label hover:-translate-y-0.5 hover:shadow-lg transition-all">
                <?php echo esc_html($cta_text); ?>
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12,5 19,12 12,19"/></svg>
            </a>
        </div>
    </div>
</section>
