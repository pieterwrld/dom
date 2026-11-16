<?php
defined('ABSPATH') || exit;

global $product;
if (!$product) return;

$upsell_ids = $product->get_upsell_ids();
$cross_sell_ids = $product->get_cross_sell_ids();
$related_ids = array_merge($upsell_ids, $cross_sell_ids);

if (empty($related_ids)) {
    $related_ids = wc_get_related_products($product->get_id(), 8);
}

if (empty($related_ids)) return;

$related_products = array_filter(array_map('wc_get_product', array_slice($related_ids, 0, 8)));
if (empty($related_products)) return;
?>
<div class="reveal mt-10">
    <div class="flex items-center justify-between mb-5">
        <div>
            <span class="block text-2xs font-bold uppercase tracking-label text-muted mb-1">Aanbevolen</span>
            <h3 class="text-lg font-bold tracking-tight">Maak je outfit compleet</h3>
        </div>
        <div class="flex gap-1">
            <button class="dom-upsell-prev w-8 h-8 flex items-center justify-center rounded-full border border-border hover:border-fg transition-colors" aria-label="Vorige">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><polyline points="15,18 9,12 15,6"/></svg>
            </button>
            <button class="dom-upsell-next w-8 h-8 flex items-center justify-center rounded-full border border-border hover:border-fg transition-colors" aria-label="Volgende">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><polyline points="9,18 15,12 9,6"/></svg>
            </button>
        </div>
    </div>

    <div class="flex gap-3 overflow-x-auto scrollbar-none scroll-smooth snap-x snap-mandatory pb-2">
        <?php foreach ($related_products as $i => $rp) :
            $thumb = get_the_post_thumbnail_url($rp->get_id(), 'product-card');
            $brand = function_exists('get_field') ? get_field('product_brand', $rp->get_id()) : '';
            $on_sale = $rp->is_on_sale();
        ?>
            <a href="<?php echo esc_url($rp->get_permalink()); ?>" class="shrink-0 w-[140px] md:w-[180px] snap-start group">
                <div class="relative aspect-[3/4] bg-surface rounded-lg mb-2 overflow-hidden">
                    <?php if ($thumb) : ?>
                        <img src="<?php echo esc_url($thumb); ?>" alt="<?php echo esc_attr($rp->get_name()); ?>" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105" loading="lazy">
                    <?php endif; ?>
                    <?php if ($on_sale) : ?>
                        <span class="absolute top-2 left-2 px-2 py-0.5 bg-urgency text-white text-[9px] font-bold rounded-full uppercase">Sale</span>
                    <?php endif; ?>
                </div>
                <?php if ($brand) : ?>
                    <span class="block text-[10px] text-muted uppercase tracking-label font-medium"><?php echo esc_html($brand); ?></span>
                <?php endif; ?>
                <div class="text-xs font-semibold truncate mt-0.5"><?php echo esc_html($rp->get_name()); ?></div>
                <div class="text-sm font-extrabold tabular-nums mt-0.5">
                    &euro;<?php echo number_format((float)$rp->get_price(), 0, ',', '.'); ?>
                    <?php if ($on_sale && $rp->get_regular_price()) : ?>
                        <span class="text-muted font-normal text-xs line-through ml-1">&euro;<?php echo number_format((float)$rp->get_regular_price(), 0, ',', '.'); ?></span>
                    <?php endif; ?>
                </div>
            </a>
        <?php endforeach; ?>
    </div>
</div>
