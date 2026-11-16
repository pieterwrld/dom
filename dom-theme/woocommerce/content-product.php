<?php
/**
 * Product card in shop loop
 *
 * @package DomTheme
 */
defined('ABSPATH') || exit;

global $product;

if (!$product || !$product->is_visible()) return;

$gallery_ids = $product->get_gallery_image_ids();
$thumbnail_id = $product->get_image_id();
$all_images = $thumbnail_id ? array_merge([$thumbnail_id], $gallery_ids) : $gallery_ids;
$all_images = array_slice($all_images, 0, 4);

$on_sale = $product->is_on_sale();
$sale_pct = '';
if ($on_sale && $product->get_regular_price() > 0) {
    $pct = round((1 - ($product->get_sale_price() / $product->get_regular_price())) * 100);
    $sale_pct = '-' . $pct . '%';
}

$brand = '';
if (function_exists('get_field')) {
    $brand = get_field('product_brand', $product->get_id());
}

$terms = wc_get_product_category_list($product->get_id());
?>
<a href="<?php echo esc_url($product->get_permalink()); ?>" class="group block">
    <!-- Image -->
    <div class="relative aspect-[3/4] bg-surface border border-border rounded-md mb-2 overflow-hidden">
        <?php if ($thumbnail_id) : ?>
            <?php echo wp_get_attachment_image($thumbnail_id, 'product-card', false, [
                'class' => 'w-full h-full object-cover transition-transform duration-500 group-hover:scale-105',
                'loading' => 'lazy',
            ]); ?>
        <?php endif; ?>

        <?php if ($sale_pct) : ?>
            <span class="absolute top-2 left-2 px-2 py-0.5 bg-urgency text-white text-[10px] font-bold rounded uppercase tracking-wide"><?php echo esc_html($sale_pct); ?></span>
        <?php elseif ($product->is_featured()) : ?>
            <span class="absolute top-2 left-2 px-2 py-0.5 silver-gradient text-fg text-[10px] font-bold rounded uppercase tracking-wide">Nieuw</span>
        <?php endif; ?>

        <!-- Image dots -->
        <?php if (count($all_images) > 1) : ?>
            <div class="absolute bottom-2 left-1/2 -translate-x-1/2 flex gap-1">
                <?php foreach ($all_images as $i => $img_id) : ?>
                    <span class="w-1.5 h-1.5 rounded-full <?php echo $i === 0 ? 'bg-fg' : 'bg-fg/20'; ?>"></span>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- Info -->
    <?php if ($brand) : ?>
        <span class="text-2xs text-muted uppercase tracking-label font-medium"><?php echo esc_html($brand); ?></span>
    <?php endif; ?>
    <h3 class="text-sm font-semibold truncate mt-0.5"><?php echo esc_html($product->get_name()); ?></h3>
    <div class="font-extrabold text-base tabular-nums mt-1">
        &euro;<?php echo number_format((float)$product->get_price(), 0, ',', '.'); ?>
        <?php if ($on_sale && $product->get_regular_price()) : ?>
            <span class="text-muted font-normal text-sm line-through ml-1.5">&euro;<?php echo number_format((float)$product->get_regular_price(), 0, ',', '.'); ?></span>
        <?php endif; ?>
    </div>
</a>
