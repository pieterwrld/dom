<?php
defined('ABSPATH') || exit;

$slides = [];
if (function_exists('get_field')) {
    $slides = get_field('klokjesman_slides', 'option') ?: [];
}

$fallback_image = content_url('/uploads/2025/03/Rolex-day-date-40-green-4.jpg');
?>
<div class="reveal">
    <div class="relative overflow-hidden rounded-2xl bg-fg">
        <div class="grid md:grid-cols-2 items-center">
            <!-- Image side -->
            <div class="relative aspect-square md:aspect-auto md:h-full overflow-hidden">
                <?php if (!empty($slides)) : ?>
                <div class="dom-slider h-full" data-slider>
                    <div class="dom-slider-track flex h-full">
                        <?php foreach ($slides as $slide) : ?>
                        <div class="dom-slider-slide flex-shrink-0 w-full h-full">
                            <img src="<?php echo esc_url($slide['image']['url'] ?? ''); ?>" alt="De Klokjesman" class="w-full h-full object-cover" loading="lazy">
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php if (count($slides) > 1) : ?>
                    <div class="dom-slider-dots absolute bottom-4 left-1/2 -translate-x-1/2 flex gap-1.5 z-10"></div>
                    <?php endif; ?>
                </div>
                <?php else : ?>
                <img src="<?php echo esc_url($fallback_image); ?>" alt="De Klokjesman — Horloges" class="w-full h-full object-cover" loading="lazy">
                <?php endif; ?>
                <div class="absolute inset-0 bg-gradient-to-r from-transparent to-fg/30 hidden md:block"></div>
            </div>

            <!-- Content side -->
            <div class="p-8 md:p-12 lg:p-16 text-white">
                <span class="inline-flex items-center gap-2 px-3 py-1.5 border border-white/10 rounded-full text-2xs font-bold uppercase tracking-label text-white/40 mb-6">
                    <span class="w-1.5 h-1.5 rounded-full silver-gradient"></span>
                    Sister brand
                </span>

                <h2 class="text-3xl md:text-4xl font-black tracking-tight leading-none mb-4">
                    De <span class="silver-text">Klokjes</span>man
                </h2>

                <p class="text-white/50 text-base leading-relaxed mb-8 max-w-sm">
                    De beste kwaliteit horloges voor de scherpste prijzen. Van Rolex tot Audemars Piguet.
                </p>

                <a href="https://deklokjesman.com/" target="_blank" rel="noopener" class="group inline-flex items-center gap-3 px-7 py-3.5 silver-gradient text-fg rounded-lg text-sm font-bold uppercase tracking-label hover:gap-4 transition-all duration-300">
                    Bekijk horloges
                    <svg class="w-4 h-4 transition-transform group-hover:translate-x-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12,5 19,12 12,19"/></svg>
                </a>
            </div>
        </div>
    </div>
</div>
