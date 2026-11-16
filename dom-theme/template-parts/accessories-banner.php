<?php
defined('ABSPATH') || exit;

$accessories = [
    ['slug' => 'brillen', 'name' => 'Brillen', 'label' => 'Zonnebrillen & meer'],
    ['slug' => 'petten', 'name' => 'Petten', 'label' => 'Caps & beanies'],
    ['slug' => 'tassen', 'name' => 'Tassen', 'label' => 'Backpacks & pouches'],
];

foreach ($accessories as &$acc) {
    $term = get_term_by('slug', $acc['slug'], 'product_cat');
    if ($term && !is_wp_error($term)) {
        $acc['url'] = get_term_link($term);
        $thumb_id = get_term_meta($term->term_id, 'thumbnail_id', true);
        $acc['image'] = $thumb_id ? wp_get_attachment_image_url($thumb_id, 'collection-card') : '';
    } else {
        $acc['url'] = get_permalink(wc_get_page_id('shop'));
        $acc['image'] = '';
    }
}
unset($acc);
?>
<div class="max-w-7xl mx-auto px-4">
    <div class="reveal mb-8 md:mb-10">
        <span class="block text-2xs font-bold uppercase tracking-label text-muted mb-2">Accessoires</span>
        <h2 class="text-3xl md:text-4xl font-black tracking-tight leading-none">Maak je look<br>compleet</h2>
    </div>

    <div class="grid grid-cols-3 gap-2 md:gap-4">
        <?php foreach ($accessories as $i => $acc) : ?>
            <a href="<?php echo esc_url($acc['url']); ?>"
               class="reveal group relative aspect-[2/3] md:aspect-[3/4] rounded-xl overflow-hidden bg-surface"
               style="transition-delay: <?php echo $i * 80; ?>ms">
                <?php if ($acc['image']) : ?>
                    <img src="<?php echo esc_url($acc['image']); ?>" alt="<?php echo esc_attr($acc['name']); ?>" class="absolute inset-0 w-full h-full object-cover transition-transform duration-700 ease-out group-hover:scale-[1.06]" loading="lazy">
                <?php else : ?>
                    <div class="absolute inset-0 bg-gradient-to-br from-surface to-surface-2"></div>
                <?php endif; ?>

                <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/5 to-transparent"></div>

                <div class="absolute bottom-0 left-0 right-0 p-4 md:p-6">
                    <span class="block text-[10px] text-white/50 font-medium uppercase tracking-label mb-1"><?php echo esc_html($acc['label']); ?></span>
                    <h3 class="text-lg md:text-xl font-black uppercase tracking-tight text-white"><?php echo esc_html($acc['name']); ?></h3>
                    <span class="inline-flex items-center gap-1.5 mt-2 text-[11px] text-white/50 font-medium uppercase tracking-label group-hover:text-white/80 transition-colors">
                        Shop nu
                        <svg class="w-3 h-3 transition-transform group-hover:translate-x-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12,5 19,12 12,19"/></svg>
                    </span>
                </div>
            </a>
        <?php endforeach; ?>
    </div>
</div>
