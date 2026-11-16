<?php
defined('ABSPATH') || exit;

$influencers = [
    ['name' => 'Furkan', 'role' => 'Influencer'],
    ['name' => 'Djezja', 'role' => 'Content creator'],
    ['name' => 'Kubi', 'role' => 'Barber & lifestyle'],
    ['name' => 'Fato YT', 'role' => 'YouTuber'],
];

if (function_exists('get_field')) {
    $acf_influencers = get_field('influencers', 'option');
    if ($acf_influencers && is_array($acf_influencers)) {
        $influencers = $acf_influencers;
    }
}
?>
<div class="reveal">
    <div class="flex items-baseline justify-between gap-4 mb-5">
        <h2 class="text-xl md:text-2xl font-bold tracking-tight">Worn By</h2>
    </div>

    <div class="dom-slider relative" data-slider data-slides-visible="auto">
        <div class="dom-slider-track flex gap-3 transition-transform duration-500 ease-out overflow-hidden">
            <?php foreach ($influencers as $inf) :
                $image = $inf['image']['url'] ?? '';
                $video = $inf['video'] ?? '';
            ?>
                <div class="dom-slider-slide flex-shrink-0 w-[180px] md:w-[220px]">
                    <div class="relative aspect-[9/16] rounded-lg overflow-hidden bg-surface group cursor-pointer">
                        <?php if ($image) : ?>
                            <img src="<?php echo esc_url($image); ?>" alt="<?php echo esc_attr($inf['name']); ?>" class="absolute inset-0 w-full h-full object-cover transition-transform duration-500 group-hover:scale-105" loading="lazy">
                        <?php else : ?>
                            <div class="absolute inset-0 bg-gradient-to-br from-surface to-surface-2"></div>
                        <?php endif; ?>

                        <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-transparent to-transparent"></div>

                        <!-- Play button -->
                        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-12 h-12 rounded-full bg-white/15 backdrop-blur-sm border border-white/20 flex items-center justify-center group-hover:bg-white/25 transition-colors">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="white" class="ml-0.5"><polygon points="6,3 20,12 6,21"/></svg>
                        </div>

                        <div class="absolute bottom-0 left-0 right-0 p-4 text-white">
                            <h3 class="text-base font-extrabold uppercase tracking-tight"><?php echo esc_html($inf['name']); ?></h3>
                            <span class="text-xs text-white/50"><?php echo esc_html($inf['role'] ?? ''); ?></span>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <button class="dom-slider-prev absolute -left-3 top-1/2 -translate-y-1/2 w-9 h-9 flex items-center justify-center rounded-full bg-white shadow-md border border-border hover:bg-surface transition-colors z-10" aria-label="Vorige">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><polyline points="15,18 9,12 15,6"/></svg>
        </button>
        <button class="dom-slider-next absolute -right-3 top-1/2 -translate-y-1/2 w-9 h-9 flex items-center justify-center rounded-full bg-white shadow-md border border-border hover:bg-surface transition-colors z-10" aria-label="Volgende">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><polyline points="9,18 15,12 9,6"/></svg>
        </button>

        <div class="dom-slider-dots flex justify-center gap-1.5 mt-4"></div>
    </div>
</div>
