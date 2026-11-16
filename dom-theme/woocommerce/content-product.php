<?php
/**
 * Product card in shop loop — next-level design
 *
 * @package DomTheme
 */
defined('ABSPATH') || exit;

global $product, $dom_product_index;

if (!$product || !$product->is_visible()) return;

if (!isset($dom_product_index)) $dom_product_index = 0;
$stagger_delay = $dom_product_index * 60;
$dom_product_index++;

$gallery_ids = $product->get_gallery_image_ids();
$thumbnail_id = $product->get_image_id();
$second_image_id = !empty($gallery_ids) ? $gallery_ids[0] : null;
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

$is_new = (strtotime($product->get_date_created()) > strtotime('-14 days'));
?>
<a href="<?php echo esc_url($product->get_permalink()); ?>"
   class="reveal group block transition-all duration-300 hover:-translate-y-0.5 hover:shadow-lg rounded-md"
   style="transition-delay: <?php echo $stagger_delay; ?>ms">

    <!-- Image container -->
    <div class="relative aspect-[3/4] bg-surface border border-border rounded-md mb-2.5 overflow-hidden">
        <?php if ($thumbnail_id) : ?>
            <?php echo wp_get_attachment_image($thumbnail_id, 'product-card', false, [
                'class' => 'absolute inset-0 w-full h-full object-cover transition-opacity duration-500 ' . ($second_image_id ? 'group-hover:opacity-0' : 'group-hover:scale-105 transition-transform duration-700'),
                'loading' => 'lazy',
            ]); ?>
        <?php endif; ?>

        <?php if ($second_image_id) : ?>
            <?php echo wp_get_attachment_image($second_image_id, 'product-card', false, [
                'class' => 'absolute inset-0 w-full h-full object-cover opacity-0 group-hover:opacity-100 transition-opacity duration-500 scale-[1.02]',
                'loading' => 'lazy',
            ]); ?>
        <?php endif; ?>

        <!-- Badges (top-left) -->
        <div class="absolute top-2.5 left-2.5 flex flex-col gap-1.5 z-10">
            <?php if ($sale_pct) : ?>
                <span class="px-2.5 py-1 bg-urgency text-white text-[10px] font-bold rounded-full uppercase tracking-wide"><?php echo esc_html($sale_pct); ?></span>
            <?php endif; ?>
            <?php if ($is_new && !$sale_pct) : ?>
                <span class="px-2.5 py-1 silver-gradient text-fg text-[10px] font-bold rounded-full uppercase tracking-wide">Nieuw</span>
            <?php endif; ?>
        </div>

        <!-- Wishlist heart (top-right, hover only) -->
        <button type="button"
                class="absolute top-2.5 right-2.5 w-8 h-8 flex items-center justify-center rounded-full bg-white/80 backdrop-blur-sm opacity-0 group-hover:opacity-100 transition-all duration-300 scale-90 group-hover:scale-100 z-10 hover:bg-white"
                aria-label="Toevoegen aan favorieten"
                onclick="event.preventDefault(); event.stopPropagation();">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20.84 4.61a5.5 5.5 0 00-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 00-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 000-7.78z"/></svg>
        </button>

        <!-- Quick view overlay (bottom, hover only) -->
        <div class="absolute bottom-0 left-0 right-0 p-2.5 opacity-0 group-hover:opacity-100 translate-y-2 group-hover:translate-y-0 transition-all duration-300 z-10">
            <span class="flex items-center justify-center w-full py-2.5 bg-white/90 backdrop-blur-sm rounded-md text-xs font-bold uppercase tracking-label text-fg">
                Bekijk product
            </span>
        </div>

        <!-- Image dots -->
        <?php if (count($all_images) > 1) : ?>
            <div class="absolute bottom-2 left-1/2 -translate-x-1/2 flex gap-1 group-hover:opacity-0 transition-opacity z-[5]">
                <?php foreach ($all_images as $i => $img_id) : ?>
                    <span class="w-1.5 h-1.5 rounded-full <?php echo $i === 0 ? 'bg-fg' : 'bg-fg/20'; ?>"></span>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- Info -->
    <div class="px-0.5">
        <?php if ($brand) : ?>
            <span class="block text-2xs text-muted uppercase tracking-label font-medium"><?php echo esc_html($brand); ?></span>
        <?php endif; ?>
        <h3 class="text-sm font-semibold truncate mt-0.5 group-hover:text-muted transition-colors"><?php echo esc_html($product->get_name()); ?></h3>
        <div class="font-extrabold text-base tabular-nums mt-1">
            &euro;<?php echo number_format((float)$product->get_price(), 0, ',', '.'); ?>
            <?php if ($on_sale && $product->get_regular_price()) : ?>
                <span class="text-muted font-normal text-sm line-through ml-1.5">&euro;<?php echo number_format((float)$product->get_regular_price(), 0, ',', '.'); ?></span>
            <?php endif; ?>
        </div>
    </div>
</a>
