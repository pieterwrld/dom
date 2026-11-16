<?php
defined('ABSPATH') || exit;

$influencers = [
    ['name' => 'Furkan', 'role' => 'Influencer', 'fallback' => content_url('/uploads/2025/03/19-1-scaled-1.webp')],
    ['name' => 'Djezja', 'role' => 'Content creator', 'fallback' => content_url('/uploads/2025/03/18-1-scaled-1.webp')],
    ['name' => 'Kubi', 'role' => 'Barber & lifestyle', 'fallback' => content_url('/uploads/2025/03/17-1-scaled-1.webp')],
    ['name' => 'Fato YT', 'role' => 'YouTuber', 'fallback' => content_url('/uploads/2025/03/16-1-scaled-1.webp')],
];

if (function_exists('get_field')) {
    $acf_influencers = get_field('influencers', 'option');
    if ($acf_influencers && is_array($acf_influencers)) {
        $influencers = $acf_influencers;
    }
}
?>
<div>
    <div class="reveal flex items-end justify-between gap-4 mb-8 md:mb-10">
        <div>
            <span class="block text-2xs font-bold uppercase tracking-label text-white/40 mb-2">As seen on</span>
            <h2 class="text-3xl md:text-4xl font-black tracking-tight leading-none text-white">Worn by</h2>
        </div>
        <span class="text-sm text-white/30 font-medium hidden md:block">Influencers die kiezen voor De Outfitman</span>
    </div>

    <div class="dom-slider relative" data-slider data-slides-visible="auto">
        <div class="dom-slider-track flex gap-3 md:gap-4 overflow-x-auto scrollbar-none scroll-smooth snap-x snap-mandatory pb-2" style="scroll-padding: 0 16px">
            <?php foreach ($influencers as $i => $inf) :
                $image = $inf['image']['url'] ?? ($inf['fallback'] ?? '');
                $video = $inf['video'] ?? '';
            ?>
                <div class="reveal dom-slider-slide flex-shrink-0 w-[46vw] md:w-[220px] lg:w-[260px] snap-start" style="transition-delay: <?php echo $i * 100; ?>ms">
                    <div class="relative aspect-[9/16] rounded-xl overflow-hidden bg-white/5 group cursor-pointer">
                        <?php if ($image) : ?>
                            <img src="<?php echo esc_url($image); ?>" alt="<?php echo esc_attr($inf['name']); ?>" class="absolute inset-0 w-full h-full object-cover transition-transform duration-700 ease-out group-hover:scale-[1.06]" loading="lazy">
                        <?php else : ?>
                            <div class="absolute inset-0 bg-gradient-to-br from-white/10 to-white/5"></div>
                        <?php endif; ?>

                        <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-transparent to-black/10 opacity-70 group-hover:opacity-80 transition-opacity duration-500"></div>

                        <!-- Play button -->
                        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-14 h-14 rounded-full silver-gradient flex items-center justify-center opacity-0 group-hover:opacity-100 scale-75 group-hover:scale-100 transition-all duration-300">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="#0A0A0A" class="ml-0.5"><polygon points="6,3 20,12 6,21"/></svg>
                        </div>

                        <div class="absolute bottom-0 left-0 right-0 p-5">
                            <h3 class="text-lg font-black uppercase tracking-tight text-white leading-tight"><?php echo esc_html($inf['name']); ?></h3>
                            <span class="text-xs text-white/40 font-medium"><?php echo esc_html($inf['role'] ?? ''); ?></span>
                        </div>

                        <!-- Silver accent line at top -->
                        <div class="absolute top-0 left-0 right-0 h-[2px] silver-gradient opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>
