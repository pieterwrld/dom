<?php
defined('ABSPATH') || exit;

$target_cats = ['sneakers', 'jassen', 'jeans', 'trainingspakken', 'sweaters-hoodies', 't-shirts', 'zwembroeken', 'tassen', 'brillen', 'slippers', 'petten', 'sale'];

$categories = get_terms([
    'taxonomy'   => 'product_cat',
    'hide_empty' => false,
    'slug'       => $target_cats,
    'orderby'    => 'slug__in',
]);

if (is_wp_error($categories) || empty($categories)) return;

$ordered = [];
foreach ($target_cats as $slug) {
    foreach ($categories as $cat) {
        if ($cat->slug === $slug) {
            $ordered[] = $cat;
            break;
        }
    }
}
$categories = array_slice($ordered, 0, 9);
?>
<div>
    <div class="reveal flex items-end justify-between gap-4 mb-8 md:mb-10">
        <div>
            <span class="block text-2xs font-bold uppercase tracking-label text-muted mb-2">Shop by category</span>
            <h2 class="text-3xl md:text-4xl font-black tracking-tight leading-none">Ontdek onze<br>collecties</h2>
        </div>
        <a href="<?php echo esc_url(get_permalink(wc_get_page_id('shop'))); ?>" class="hidden md:inline-flex items-center gap-2 text-sm font-semibold text-muted hover:text-fg transition-colors whitespace-nowrap group">
            Bekijk alles
            <svg class="w-4 h-4 transition-transform group-hover:translate-x-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12,5 19,12 12,19"/></svg>
        </a>
    </div>

    <div class="grid grid-cols-2 md:grid-cols-3 gap-2 md:gap-3">
        <?php foreach ($categories as $i => $cat) :
            $thumbnail_id = get_term_meta($cat->term_id, 'thumbnail_id', true);
            $image_url = $thumbnail_id ? wp_get_attachment_image_url($thumbnail_id, 'collection-card') : '';
            $discount = '';
            if (function_exists('get_field')) {
                $discount = get_field('category_discount_label', 'product_cat_' . $cat->term_id);
            }
            $is_large = ($i === 0 || $i === 4);
            $aspect = $is_large ? 'aspect-[4/5]' : 'aspect-[3/4]';
            if ($i === 0) $aspect = 'aspect-[4/5] md:row-span-2 md:aspect-auto md:h-full';
        ?>
            <a href="<?php echo esc_url(get_term_link($cat)); ?>"
               class="reveal group relative <?php echo $aspect; ?> rounded-lg overflow-hidden bg-surface block"
               style="transition-delay: <?php echo $i * 60; ?>ms">
                <?php if ($image_url) : ?>
                    <img src="<?php echo esc_url($image_url); ?>" alt="<?php echo esc_attr($cat->name); ?>" class="absolute inset-0 w-full h-full object-cover transition-transform duration-700 ease-out group-hover:scale-[1.06]" loading="lazy">
                <?php else : ?>
                    <div class="absolute inset-0 bg-gradient-to-br from-surface to-surface-2"></div>
                <?php endif; ?>

                <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/5 to-transparent opacity-80 group-hover:opacity-90 transition-opacity duration-500"></div>

                <div class="absolute bottom-0 left-0 right-0 p-4 md:p-5">
                    <?php if ($discount) : ?>
                        <span class="inline-block px-2.5 py-1 bg-urgency text-white text-[10px] font-bold rounded-sm tracking-wide uppercase mb-2"><?php echo esc_html($discount); ?></span>
                    <?php endif; ?>
                    <h3 class="text-sm md:text-base font-bold uppercase tracking-wide text-white leading-tight"><?php echo esc_html($cat->name); ?></h3>
                    <span class="inline-flex items-center gap-1.5 mt-1.5 text-[11px] text-white/60 font-medium uppercase tracking-label group-hover:text-white/80 transition-colors">
                        Shop nu
                        <svg class="w-3 h-3 transition-transform group-hover:translate-x-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12,5 19,12 12,19"/></svg>
                    </span>
                </div>
            </a>
        <?php endforeach; ?>
    </div>

    <a href="<?php echo esc_url(get_permalink(wc_get_page_id('shop'))); ?>" class="md:hidden flex items-center justify-center gap-2 mt-5 py-3 border border-border rounded-lg text-sm font-semibold hover:bg-surface transition-colors">
        Bekijk alle collecties
        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12,5 19,12 12,19"/></svg>
    </a>
</div>
