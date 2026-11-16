<?php
defined('ABSPATH') || exit;

$slides = [];
if (function_exists('get_field')) {
    $slides = get_field('parfum4car_slides', 'option') ?: [];
}

$fallback_images = [];
for ($n = 2; $n <= 10; $n++) {
    $fallback_images[] = content_url('/uploads/2025/09/' . $n . '.jpg');
}
?>
<div>
    <div class="reveal flex items-end justify-between gap-4 mb-8 md:mb-10">
        <div>
            <span class="block text-2xs font-bold uppercase tracking-label text-muted mb-2">Partner</span>
            <h2 class="text-3xl md:text-4xl font-black tracking-tight leading-none">Parfum<span class="silver-text">4</span>Car</h2>
        </div>
        <a href="https://parfum4car.nl/" target="_blank" rel="noopener" class="hidden md:inline-flex items-center gap-2 text-sm font-semibold text-muted hover:text-fg transition-colors whitespace-nowrap group">
            Bekijk alles
            <svg class="w-4 h-4 transition-transform group-hover:translate-x-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12,5 19,12 12,19"/></svg>
        </a>
    </div>

    <div class="flex gap-3 md:gap-4 overflow-x-auto scrollbar-none scroll-smooth snap-x snap-mandatory pb-2" style="scroll-padding: 0 16px">
        <?php
        $images = [];
        if (!empty($slides)) {
            foreach ($slides as $slide) {
                $images[] = $slide['image']['url'] ?? '';
            }
        } else {
            $images = $fallback_images;
        }

        foreach ($images as $i => $img) :
            if (!$img) continue;
        ?>
            <div class="reveal flex-shrink-0 w-[70vw] md:w-[320px] snap-start group" style="transition-delay: <?php echo $i * 60; ?>ms">
                <div class="relative aspect-[4/5] rounded-xl overflow-hidden bg-surface">
                    <img src="<?php echo esc_url($img); ?>" alt="Parfum4Car" class="w-full h-full object-cover transition-transform duration-700 ease-out group-hover:scale-[1.04]" loading="lazy">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/40 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <a href="https://parfum4car.nl/" target="_blank" rel="noopener" class="md:hidden flex items-center justify-center gap-2 mt-5 py-3 border border-border rounded-lg text-sm font-semibold hover:bg-surface transition-colors">
        Bekijk Parfum4Car
        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12,5 19,12 12,19"/></svg>
    </a>
</div>
