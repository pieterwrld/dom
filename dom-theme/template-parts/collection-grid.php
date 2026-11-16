<?php
defined('ABSPATH') || exit;

$categories = get_terms([
    'taxonomy'   => 'product_cat',
    'hide_empty' => false,
    'number'     => 9,
    'exclude'    => [get_option('default_product_cat')],
    'orderby'    => 'menu_order',
]);

if (is_wp_error($categories) || empty($categories)) return;
?>
<div class="reveal">
    <div class="flex items-baseline justify-between gap-4 mb-5">
        <h2 class="text-xl md:text-2xl font-bold tracking-tight">Collecties</h2>
        <a href="<?php echo esc_url(get_permalink(wc_get_page_id('shop'))); ?>" class="text-sm text-muted hover:text-fg transition-colors whitespace-nowrap">Alles bekijken &rarr;</a>
    </div>

    <div class="grid grid-cols-3 gap-1.5 md:gap-2.5">
        <?php foreach ($categories as $i => $cat) :
            $thumbnail_id = get_term_meta($cat->term_id, 'thumbnail_id', true);
            $image_url = $thumbnail_id ? wp_get_attachment_image_url($thumbnail_id, 'collection-card') : '';
            $discount = '';
            if (function_exists('get_field')) {
                $discount = get_field('category_discount_label', 'product_cat_' . $cat->term_id);
            }
        ?>
            <a href="<?php echo esc_url(get_term_link($cat)); ?>" class="group relative aspect-[3/4] rounded-md overflow-hidden bg-surface">
                <?php if ($image_url) : ?>
                    <img src="<?php echo esc_url($image_url); ?>" alt="<?php echo esc_attr($cat->name); ?>" class="absolute inset-0 w-full h-full object-cover transition-transform duration-500 group-hover:scale-105" loading="lazy">
                <?php else : ?>
                    <div class="absolute inset-0 bg-gradient-to-br from-surface to-surface-2"></div>
                <?php endif; ?>
                <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent"></div>
                <div class="absolute bottom-0 left-0 right-0 p-3">
                    <span class="text-sm font-bold uppercase tracking-wide text-white"><?php echo esc_html($cat->name); ?></span>
                    <?php if ($discount) : ?>
                        <span class="block mt-1 inline-block px-2 py-0.5 bg-urgency text-white text-[10px] font-bold rounded tracking-wide w-fit"><?php echo esc_html($discount); ?></span>
                    <?php endif; ?>
                </div>
            </a>
        <?php endforeach; ?>
    </div>
</div>
