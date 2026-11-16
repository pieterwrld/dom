<?php
defined('ABSPATH') || exit;

global $product;
if (!$product) return;

$upsell_ids = $product->get_upsell_ids();
$cross_sell_ids = $product->get_cross_sell_ids();
$related_ids = array_merge($upsell_ids, $cross_sell_ids);

if (empty($related_ids)) {
    $related_ids = wc_get_related_products($product->get_id(), 6);
}

if (empty($related_ids)) return;

$related_products = array_filter(array_map('wc_get_product', array_slice($related_ids, 0, 6)));
if (empty($related_products)) return;
?>
<div class="mt-7 p-5 bg-bg-off border border-border rounded-lg">
    <h3 class="text-sm font-bold uppercase tracking-label mb-4">Maak je outfit compleet</h3>
    <div class="flex gap-2.5 overflow-x-auto scrollbar-none -mx-1 px-1">
        <?php foreach ($related_products as $rp) :
            $thumb = get_the_post_thumbnail_url($rp->get_id(), 'thumbnail');
        ?>
            <a href="<?php echo esc_url($rp->get_permalink()); ?>" class="shrink-0 w-[110px] group">
                <div class="aspect-square bg-surface border border-border rounded-md mb-1.5 overflow-hidden">
                    <?php if ($thumb) : ?>
                        <img src="<?php echo esc_url($thumb); ?>" alt="<?php echo esc_attr($rp->get_name()); ?>" class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105" loading="lazy">
                    <?php endif; ?>
                </div>
                <div class="text-xs font-medium truncate"><?php echo esc_html($rp->get_name()); ?></div>
                <div class="text-sm font-extrabold tabular-nums">&euro;<?php echo number_format((float)$rp->get_price(), 0, ',', '.'); ?></div>
            </a>
        <?php endforeach; ?>
    </div>
</div>
