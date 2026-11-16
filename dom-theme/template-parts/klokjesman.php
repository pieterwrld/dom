<?php
defined('ABSPATH') || exit;

$slides = [];
if (function_exists('get_field')) {
    $slides = get_field('klokjesman_slides', 'option') ?: [];
}
?>
<div class="reveal">
    <div class="flex items-baseline justify-between gap-4 mb-5">
        <h2 class="text-xl md:text-2xl font-bold tracking-tight">De Klokjesman</h2>
        <a href="https://deklokjesman.com" target="_blank" rel="noopener" class="text-sm text-muted hover:text-fg transition-colors whitespace-nowrap">Bekijk collectie &rarr;</a>
    </div>

    <div class="dom-slider relative overflow-hidden rounded-lg border border-border" data-slider>
        <div class="dom-slider-track flex transition-transform duration-500 ease-out">
            <?php if (!empty($slides)) : ?>
                <?php foreach ($slides as $slide) : ?>
                    <div class="dom-slider-slide flex-shrink-0 w-full">
                        <div class="relative aspect-[16/7] bg-surface">
                            <?php if (!empty($slide['image'])) : ?>
                                <img src="<?php echo esc_url($slide['image']['url']); ?>" alt="<?php echo esc_attr($slide['title'] ?? 'De Klokjesman'); ?>" class="w-full h-full object-cover" loading="lazy">
                            <?php endif; ?>
                            <div class="absolute inset-0 bg-gradient-to-r from-black/40 to-transparent"></div>
                            <div class="absolute bottom-0 left-0 p-6 md:p-8 text-white">
                                <span class="text-2xs font-bold uppercase tracking-label silver-text mb-1 block">De Klokjesman</span>
                                <span class="text-lg md:text-xl font-bold"><?php echo esc_html($slide['title'] ?? ''); ?></span>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else : ?>
                <div class="dom-slider-slide flex-shrink-0 w-full">
                    <div class="relative aspect-[16/7] bg-surface flex items-center justify-center">
                        <div class="text-center p-8">
                            <span class="text-2xs font-bold uppercase tracking-label silver-text block mb-2">Partner</span>
                            <span class="text-2xl font-bold block mb-1">De Klokjesman</span>
                            <span class="text-sm text-muted">Premium horloges</span>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <!-- Slider controls -->
        <button class="dom-slider-prev absolute left-3 top-1/2 -translate-y-1/2 w-9 h-9 flex items-center justify-center rounded-full bg-white/80 backdrop-blur border border-border hover:bg-white transition-colors" aria-label="Vorige">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><polyline points="15,18 9,12 15,6"/></svg>
        </button>
        <button class="dom-slider-next absolute right-3 top-1/2 -translate-y-1/2 w-9 h-9 flex items-center justify-center rounded-full bg-white/80 backdrop-blur border border-border hover:bg-white transition-colors" aria-label="Volgende">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><polyline points="9,18 15,12 9,6"/></svg>
        </button>

        <!-- Dots -->
        <div class="dom-slider-dots absolute bottom-3 left-1/2 -translate-x-1/2 flex gap-1.5"></div>
    </div>
</div>
