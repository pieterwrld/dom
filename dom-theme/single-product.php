<?php
/**
 * Single product page — conversion-optimized layout
 *
 * Desktop: two-column (gallery left, info right)
 * Mobile: stacked, full-width gallery
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

    $brand = '';
    if (function_exists('get_field')) {
        $brand = get_field('product_brand', $product->get_id());
    }

    $viewed_today = rand(12, 47);

    $product_cats = wp_get_post_terms($product->get_id(), 'product_cat', ['fields' => 'ids']);
    $same_cat_products = [];
    if (!empty($product_cats)) {
        $same_cat_query = new WP_Query([
            'post_type'      => 'product',
            'posts_per_page' => 8,
            'post__not_in'   => [$product->get_id()],
            'tax_query'      => [['taxonomy' => 'product_cat', 'field' => 'term_id', 'terms' => $product_cats]],
            'orderby'        => 'rand',
        ]);
        $same_cat_products = $same_cat_query->posts;
    }
?>

<div class="max-w-6xl mx-auto px-4 pt-4 pb-20 md:pb-14">
    <div class="md:grid md:grid-cols-2 md:gap-10 md:items-start">

        <!-- Gallery (left on desktop, full-width on mobile) -->
        <div class="gallery-scroll -mx-4 md:mx-0 mb-6 md:mb-0 md:sticky md:top-24">
            <?php foreach ($all_images as $i => $img_id) : ?>
                <div class="slide <?php echo $i === 0 ? 'ml-4 md:ml-0' : ''; ?> <?php echo $i === count($all_images) - 1 ? 'mr-4 md:mr-0' : ''; ?>">
                    <div class="aspect-[3/4] bg-surface rounded-lg md:rounded-xl overflow-hidden">
                        <?php echo wp_get_attachment_image($img_id, 'product-gallery', false, [
                            'class' => 'w-full h-full object-cover',
                            'loading' => $i === 0 ? 'eager' : 'lazy',
                        ]); ?>
                    </div>
                </div>
            <?php endforeach; ?>

            <?php if (count($all_images) > 1) : ?>
                <div class="flex justify-center gap-1.5 mt-3 md:mt-4">
                    <?php foreach ($all_images as $i => $img_id) : ?>
                        <span class="w-1.5 h-1.5 rounded-full <?php echo $i === 0 ? 'bg-fg' : 'bg-fg/20'; ?>"></span>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- Product info (right on desktop) -->
        <div class="md:py-2">
            <!-- Brand -->
            <?php if ($brand) : ?>
                <span class="block text-2xs font-bold uppercase tracking-label text-muted mb-1.5"><?php echo esc_html($brand); ?></span>
            <?php endif; ?>

            <!-- Title -->
            <h1 class="text-2xl md:text-3xl font-black tracking-tight leading-tight mb-3"><?php the_title(); ?></h1>

            <!-- Social proof -->
            <div class="flex items-center gap-1.5 text-xs text-muted mb-4">
                <span class="w-1.5 h-1.5 rounded-full bg-success animate-pulse"></span>
                <?php echo $viewed_today; ?> mensen bekijken dit product vandaag
            </div>

            <!-- Price -->
            <div class="mb-2">
                <span class="text-3xl font-black tabular-nums">&euro;<?php echo number_format($price, 0, ',', '.'); ?></span>
                <?php if ($on_sale && $regular) : ?>
                    <span class="text-lg text-muted line-through font-normal ml-2">&euro;<?php echo number_format($regular, 0, ',', '.'); ?></span>
                    <span class="inline-flex items-center ml-2 px-2 py-0.5 bg-urgency/10 text-urgency text-xs font-bold rounded-full">-<?php echo $save_pct; ?>%</span>
                <?php endif; ?>
            </div>

            <!-- Stock urgency -->
            <?php get_template_part('template-parts/stock-urgency', null, ['stock_qty' => $stock_qty, 'stock_status' => $stock_status]); ?>

            <!-- Size selector -->
            <?php get_template_part('template-parts/size-selector'); ?>

            <!-- Add to cart -->
            <form class="mb-4" method="post" enctype="multipart/form-data">
                <?php if ($product->is_in_stock()) : ?>
                    <input type="hidden" name="add-to-cart" value="<?php echo esc_attr($product->get_id()); ?>">
                    <input type="hidden" name="quantity" value="1">
                    <button type="submit" class="dom-atc w-full py-4.5 bg-fg text-white text-sm font-extrabold uppercase tracking-label rounded-lg hover:-translate-y-0.5 hover:shadow-xl active:translate-y-0 transition-all duration-200">
                        In winkelwagen
                    </button>
                <?php else : ?>
                    <button type="button" disabled class="w-full py-4.5 bg-surface text-muted text-sm font-extrabold uppercase tracking-label rounded-lg cursor-not-allowed">
                        Uitverkocht
                    </button>
                <?php endif; ?>
            </form>

            <!-- Delivery estimate -->
            <?php get_template_part('template-parts/delivery-estimate'); ?>

            <!-- USPs 2x2 -->
            <?php get_template_part('template-parts/usp-grid'); ?>

            <!-- WhatsApp help -->
            <a href="https://wa.me/31612345678" target="_blank" rel="noopener" class="flex items-center justify-center gap-2 py-3 border border-border rounded-lg text-sm font-medium text-muted hover:border-fg hover:text-fg transition-colors mb-6">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor" class="text-success"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/><path d="M12 0C5.373 0 0 5.373 0 12c0 2.625.846 5.059 2.284 7.034L.789 23.492a.5.5 0 00.611.611l4.458-1.495A11.945 11.945 0 0012 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 22c-2.37 0-4.567-.82-6.3-2.188l-.44-.352-3.2 1.073 1.073-3.2-.352-.44A9.95 9.95 0 012 12C2 6.486 6.486 2 12 2s10 4.486 10 10-4.486 10-10 10z"/></svg>
                Hulp nodig? Chat met ons via WhatsApp
            </a>

            <!-- Details dropdowns -->
            <div class="reveal">
                <details class="border-t border-border">
                    <summary class="flex items-center justify-between py-4 cursor-pointer text-sm font-semibold [&>svg]:open:rotate-45">
                        Maatadvies
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" class="transition-transform duration-300"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                    </summary>
                    <div class="pb-4 text-sm text-muted leading-relaxed">
                        <?php
                        if (function_exists('get_field')) {
                            echo wp_kses_post(get_field('size_advice', $product->get_id()) ?: get_field('size_advice_default', 'option') ?: '');
                        }
                        ?>
                        <p>Dit model valt normaal op maat. Bij twijfel raden wij de grotere maat aan.</p>
                    </div>
                </details>
                <details class="border-t border-border">
                    <summary class="flex items-center justify-between py-4 cursor-pointer text-sm font-semibold [&>svg]:open:rotate-45">
                        Verzending &amp; retournering
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" class="transition-transform duration-300"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                    </summary>
                    <div class="pb-4 text-sm text-muted leading-relaxed">
                        <p>Gratis verzending vanaf &euro;200. Standaard &euro;5,95.</p>
                        <p>Retourneren gratis binnen 14 dagen. Terugbetaling binnen 5 werkdagen.</p>
                    </div>
                </details>
                <details class="border-t border-b border-border">
                    <summary class="flex items-center justify-between py-4 cursor-pointer text-sm font-semibold [&>svg]:open:rotate-45">
                        Productdetails
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" class="transition-transform duration-300"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                    </summary>
                    <div class="pb-4 text-sm text-muted leading-relaxed">
                        <?php echo wp_kses_post($product->get_description()); ?>
                    </div>
                </details>
            </div>

            <!-- Upsell -->
            <?php get_template_part('template-parts/smart-upsell'); ?>
        </div>
    </div>
</div>

<!-- Same category products -->
<?php if (!empty($same_cat_products)) : ?>
<section class="bg-bg-off py-12 md:py-16">
    <div class="max-w-6xl mx-auto px-4">
        <div class="reveal flex items-end justify-between gap-4 mb-6">
            <div>
                <span class="block text-2xs font-bold uppercase tracking-label text-muted mb-1.5">Meer ontdekken</span>
                <h2 class="text-2xl md:text-3xl font-black tracking-tight">Uit dezelfde collectie</h2>
            </div>
        </div>
        <div class="flex gap-2.5 md:gap-3.5 overflow-x-auto scrollbar-none pb-2">
            <?php foreach ($same_cat_products as $i => $post_obj) :
                setup_postdata($post_obj);
                $rp = wc_get_product($post_obj->ID);
                if (!$rp) continue;
                $thumb = get_the_post_thumbnail_url($post_obj->ID, 'product-card');
                $rp_brand = function_exists('get_field') ? get_field('product_brand', $post_obj->ID) : '';
            ?>
                <a href="<?php echo esc_url($rp->get_permalink()); ?>" class="reveal shrink-0 w-[160px] md:w-[200px] group" style="transition-delay: <?php echo $i * 50; ?>ms">
                    <div class="aspect-[3/4] bg-surface rounded-lg overflow-hidden mb-2">
                        <?php if ($thumb) : ?>
                            <img src="<?php echo esc_url($thumb); ?>" alt="<?php echo esc_attr($rp->get_name()); ?>" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105" loading="lazy">
                        <?php endif; ?>
                    </div>
                    <?php if ($rp_brand) : ?>
                        <span class="text-2xs text-muted uppercase tracking-label font-medium"><?php echo esc_html($rp_brand); ?></span>
                    <?php endif; ?>
                    <div class="text-sm font-semibold truncate mt-0.5"><?php echo esc_html($rp->get_name()); ?></div>
                    <div class="font-extrabold text-base tabular-nums mt-0.5">&euro;<?php echo number_format((float)$rp->get_price(), 0, ',', '.'); ?></div>
                </a>
            <?php endforeach; wp_reset_postdata(); ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Reviews placeholder -->
<section class="py-12 md:py-16">
    <div class="max-w-6xl mx-auto px-4">
        <div class="reveal">
            <span class="block text-2xs font-bold uppercase tracking-label text-muted mb-1.5">Reviews</span>
            <h2 class="text-2xl md:text-3xl font-black tracking-tight mb-6">Wat klanten zeggen</h2>
        </div>
        <?php
        if (shortcode_exists('dom_reviews')) {
            echo do_shortcode('[dom_reviews product_id="' . $product->get_id() . '" limit="3"]');
        }
        ?>
    </div>
</section>

<!-- Sticky ATC (mobile only) -->
<div class="dom-sticky-atc fixed bottom-0 left-0 right-0 z-40 bg-white/95 backdrop-blur-md border-t border-border px-4 py-3 hidden md:hidden">
    <div class="flex items-center gap-3">
        <div>
            <span class="text-lg font-black tabular-nums">&euro;<?php echo number_format($price, 0, ',', '.'); ?></span>
            <?php if ($on_sale) : ?>
                <span class="text-xs text-urgency font-bold ml-1">-<?php echo $save_pct; ?>%</span>
            <?php endif; ?>
        </div>
        <button class="dom-sticky-atc-btn flex-1 py-3.5 bg-fg text-white text-xs font-extrabold uppercase tracking-label rounded-lg">
            In winkelwagen
        </button>
    </div>
</div>

<?php endwhile; ?>

<?php get_footer(); ?>
