<?php
/**
 * Single product page — conversion-optimized layout
 *
 * Order: gallery → price → stock urgency → sizes → color → ATC → delivery → USPs → details → upsell → reviews
 *
 * @package DomTheme
 */
defined('ABSPATH') || exit;
get_header();

while (have_posts()) : the_post();
    global $product;

    $gallery_ids = $product->get_gallery_image_ids();
    $thumbnail_id = $product->get_image_id();
    $all_images = $thumbnail_id ? array_merge([$thumbnail_id], $gallery_ids) : $gallery_ids;

    $on_sale = $product->is_on_sale();
    $regular = (float) $product->get_regular_price();
    $sale = (float) $product->get_sale_price();
    $price = (float) $product->get_price();
    $save_pct = ($on_sale && $regular > 0) ? round((1 - $sale / $regular) * 100) : 0;

    $stock_qty = $product->get_stock_quantity();
    $stock_status = $product->get_stock_status();
?>

<!-- Gallery -->
<div class="gallery-scroll -mx-4 md:mx-0 mb-6">
    <?php foreach ($all_images as $i => $img_id) : ?>
        <div class="slide <?php echo $i === 0 ? 'ml-4 md:ml-0' : ''; ?> <?php echo $i === count($all_images) - 1 ? 'mr-4 md:mr-0' : ''; ?>">
            <div class="aspect-[3/4] bg-surface border border-border rounded-md overflow-hidden">
                <?php echo wp_get_attachment_image($img_id, 'product-gallery', false, [
                    'class' => 'w-full h-full object-cover',
                    'loading' => $i === 0 ? 'eager' : 'lazy',
                ]); ?>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<div class="max-w-xl mx-auto px-4 pb-20 md:pb-14">
    <!-- Price -->
    <div class="mb-1">
        <span class="text-3xl font-black tabular-nums">&euro;<?php echo number_format($price, 0, ',', '.'); ?></span>
        <?php if ($on_sale && $regular) : ?>
            <span class="text-lg text-muted line-through font-normal ml-2">&euro;<?php echo number_format($regular, 0, ',', '.'); ?></span>
            <span class="text-sm text-urgency font-bold ml-2">-<?php echo $save_pct; ?>%</span>
        <?php endif; ?>
    </div>

    <!-- Stock urgency -->
    <?php get_template_part('template-parts/stock-urgency', null, ['stock_qty' => $stock_qty, 'stock_status' => $stock_status]); ?>

    <!-- Size selector -->
    <?php get_template_part('template-parts/size-selector'); ?>

    <!-- Add to cart -->
    <form class="mb-3" method="post" enctype="multipart/form-data">
        <?php if ($product->is_in_stock()) : ?>
            <input type="hidden" name="add-to-cart" value="<?php echo esc_attr($product->get_id()); ?>">
            <input type="hidden" name="quantity" value="1">
            <button type="submit" class="dom-atc w-full py-4 silver-gradient text-fg text-sm font-extrabold uppercase tracking-label rounded-md hover:-translate-y-0.5 hover:shadow-lg transition-all">
                In winkelwagen
            </button>
        <?php else : ?>
            <button type="button" disabled class="w-full py-4 bg-surface text-muted text-sm font-extrabold uppercase tracking-label rounded-md cursor-not-allowed">
                Uitverkocht
            </button>
        <?php endif; ?>
    </form>

    <!-- Delivery estimate -->
    <?php get_template_part('template-parts/delivery-estimate'); ?>

    <!-- USPs 2x2 -->
    <?php get_template_part('template-parts/usp-grid'); ?>

    <!-- Details dropdowns -->
    <div class="mt-6">
        <details class="border-b border-border">
            <summary class="flex items-center justify-between py-3.5 cursor-pointer text-sm font-semibold [&>svg]:open:rotate-45">
                Maatadvies
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" class="transition-transform"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            </summary>
            <div class="pb-3.5 text-sm text-muted leading-relaxed">
                <?php
                if (function_exists('get_field')) {
                    echo wp_kses_post(get_field('size_advice', $product->get_id()) ?: get_field('size_advice_default', 'option') ?: '');
                }
                ?>
                <p>Dit model valt normaal op maat. Bij twijfel raden wij de grotere maat aan.</p>
            </div>
        </details>
        <details class="border-b border-border">
            <summary class="flex items-center justify-between py-3.5 cursor-pointer text-sm font-semibold [&>svg]:open:rotate-45">
                Verzending &amp; retournering
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" class="transition-transform"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            </summary>
            <div class="pb-3.5 text-sm text-muted leading-relaxed">
                <p>Gratis verzending vanaf &euro;200. Standaard &euro;5,95.</p>
                <p>Retourneren gratis binnen 14 dagen. Terugbetaling binnen 5 werkdagen.</p>
            </div>
        </details>
        <details class="border-b border-border">
            <summary class="flex items-center justify-between py-3.5 cursor-pointer text-sm font-semibold [&>svg]:open:rotate-45">
                Productdetails
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" class="transition-transform"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            </summary>
            <div class="pb-3.5 text-sm text-muted leading-relaxed">
                <?php echo wp_kses_post($product->get_description()); ?>
            </div>
        </details>
    </div>

    <!-- Upsell -->
    <?php get_template_part('template-parts/smart-upsell'); ?>
</div>

<!-- Sticky ATC (mobile only) -->
<div class="dom-sticky-atc fixed bottom-0 left-0 right-0 z-40 bg-white border-t border-border px-4 py-3 hidden md:hidden">
    <div class="flex items-center gap-3">
        <span class="text-lg font-black tabular-nums">&euro;<?php echo number_format($price, 0, ',', '.'); ?></span>
        <button class="dom-sticky-atc-btn flex-1 py-3.5 silver-gradient text-fg text-xs font-extrabold uppercase tracking-label rounded-md">
            In winkelwagen
        </button>
    </div>
</div>

<?php endwhile; ?>

<?php get_footer(); ?>
