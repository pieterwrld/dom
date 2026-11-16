<?php
/**
 * Shop / Product Archive template
 *
 * @package DomTheme
 */
defined('ABSPATH') || exit;
get_header();

$current_cat = get_queried_object();
$is_category = is_product_category();
$cat_banner = '';
if ($is_category && function_exists('get_field')) {
    $cat_banner = get_field('category_banner', 'product_cat_' . $current_cat->term_id);
}
?>

<div class="max-w-7xl mx-auto px-4 pt-6 pb-14">
    <!-- Category pills -->
    <div class="pills-scroll gap-1.5 mb-4">
        <?php
        $cats = get_terms(['taxonomy' => 'product_cat', 'hide_empty' => true, 'exclude' => [get_option('default_product_cat')], 'orderby' => 'menu_order']);
        if (!is_wp_error($cats)) :
            foreach ($cats as $cat) :
                $active = ($is_category && $current_cat->term_id === $cat->term_id);
                $is_sale = (strtolower($cat->slug) === 'sale');
        ?>
                <a href="<?php echo esc_url(get_term_link($cat)); ?>" class="shrink-0 px-4 py-2 border rounded-full text-xs font-semibold uppercase tracking-wide whitespace-nowrap transition-all <?php
                    if ($active) echo 'bg-fg text-white border-fg';
                    elseif ($is_sale) echo 'border-urgency text-urgency hover:bg-urgency hover:text-white';
                    else echo 'border-border text-muted hover:border-fg hover:text-fg';
                ?>"><?php echo esc_html($cat->name); ?></a>
        <?php
            endforeach;
        endif;
        ?>
    </div>

    <!-- Filters -->
    <div class="flex items-center gap-2 pb-4 border-b border-border mb-5">
        <button class="dom-filter-toggle flex items-center gap-1.5 px-3.5 py-2 border border-border rounded-md text-sm font-medium hover:border-accent transition-colors" data-filter="price">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><line x1="4" y1="6" x2="20" y2="6"/><line x1="8" y1="12" x2="20" y2="12"/><line x1="12" y1="18" x2="20" y2="18"/></svg>
            Prijs
        </button>
        <button class="dom-filter-toggle flex items-center gap-1.5 px-3.5 py-2 border border-border rounded-md text-sm font-medium hover:border-accent transition-colors" data-filter="size">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg>
            Maat
        </button>
        <span class="ml-auto text-sm text-muted"><?php echo $wp_query->found_posts; ?> items</span>
    </div>

    <!-- Category banner (between filters and grid) -->
    <?php if ($cat_banner) : ?>
        <div class="mb-6 rounded-lg overflow-hidden">
            <img src="<?php echo esc_url(is_array($cat_banner) ? $cat_banner['url'] : $cat_banner); ?>" alt="<?php echo esc_attr($current_cat->name); ?>" class="w-full h-auto" loading="lazy">
        </div>
    <?php endif; ?>

    <!-- Product grid -->
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-2.5 md:gap-3.5">
        <?php if (have_posts()) :
            while (have_posts()) : the_post();
                wc_get_template_part('content', 'product');
            endwhile;
        else : ?>
            <p class="col-span-full text-center text-muted py-12">Geen producten gevonden.</p>
        <?php endif; ?>
    </div>

    <!-- Pagination -->
    <?php if ($wp_query->max_num_pages > 1) : ?>
        <nav class="mt-8 flex justify-center">
            <?php
            echo paginate_links([
                'prev_text' => '&larr;',
                'next_text' => '&rarr;',
                'type'      => 'list',
            ]);
            ?>
        </nav>
    <?php endif; ?>
</div>

<?php get_footer(); ?>
