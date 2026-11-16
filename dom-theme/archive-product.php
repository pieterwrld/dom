<?php
/**
 * Shop / Product Archive template — next-level design
 *
 * @package DomTheme
 */
defined('ABSPATH') || exit;
get_header();

$current_cat = get_queried_object();
$is_category = is_product_category();
$cat_banner = '';
$page_title = 'Shop';
$page_subtitle = '';

if ($is_category && !is_wp_error($current_cat)) {
    $page_title = $current_cat->name;
    $page_subtitle = $current_cat->description ?: '';
    if (function_exists('get_field')) {
        $cat_banner = get_field('category_banner', 'product_cat_' . $current_cat->term_id);
    }
}

$product_count = $wp_query->found_posts;

$cats = get_terms([
    'taxonomy'   => 'product_cat',
    'hide_empty' => true,
    'exclude'    => [get_option('default_product_cat')],
    'orderby'    => 'menu_order',
]);
if (is_wp_error($cats)) $cats = [];

$bestsellers_slug = 'bestsellers';
$cats = array_filter($cats, function($cat) use ($bestsellers_slug) {
    return $cat->slug !== $bestsellers_slug;
});
?>

<!-- Page hero -->
<section class="bg-bg-off border-b border-border">
    <div class="max-w-7xl mx-auto px-4 pt-10 pb-8 md:pt-14 md:pb-10">
        <div class="hero-reveal">
            <span class="block text-2xs font-bold uppercase tracking-label text-muted mb-2">Collectie</span>
            <div class="flex items-end justify-between gap-4">
                <h1 class="text-4xl md:text-5xl font-black tracking-tight leading-none"><?php echo esc_html($page_title); ?></h1>
                <span class="hidden md:inline-flex items-center gap-1.5 px-3 py-1.5 bg-surface border border-border rounded-full text-2xs font-bold text-muted tabular-nums whitespace-nowrap">
                    <?php echo $product_count; ?> <?php echo $product_count === 1 ? 'product' : 'producten'; ?>
                </span>
            </div>
            <?php if ($page_subtitle) : ?>
                <p class="text-muted text-base mt-3 max-w-xl"><?php echo esc_html($page_subtitle); ?></p>
            <?php endif; ?>
        </div>
    </div>
</section>

<div class="max-w-7xl mx-auto px-4 pt-6 pb-16">
    <!-- Category pills -->
    <div class="pills-scroll gap-2 mb-5 pb-1">
        <a href="<?php echo esc_url(get_permalink(wc_get_page_id('shop'))); ?>"
           class="shrink-0 px-5 py-2.5 border-[1.5px] rounded-full text-xs font-bold uppercase tracking-label whitespace-nowrap transition-all <?php
               echo !$is_category
                   ? 'bg-fg text-white border-fg'
                   : 'border-border text-muted hover:border-fg hover:text-fg';
           ?>">Alles</a>
        <?php foreach ($cats as $cat) :
            $active = ($is_category && $current_cat->term_id === $cat->term_id);
            $is_sale = (strtolower($cat->slug) === 'sale');
        ?>
            <a href="<?php echo esc_url(get_term_link($cat)); ?>"
               class="shrink-0 px-5 py-2.5 border-[1.5px] rounded-full text-xs font-bold uppercase tracking-label whitespace-nowrap transition-all <?php
                   if ($active) echo 'bg-fg text-white border-fg';
                   elseif ($is_sale) echo 'border-urgency text-urgency hover:bg-urgency hover:text-white';
                   else echo 'border-border text-muted hover:border-fg hover:text-fg';
               ?>"><?php echo esc_html($cat->name); ?></a>
        <?php endforeach; ?>
    </div>

    <!-- Filters + Sort -->
    <div class="flex items-center gap-2 pb-4 border-b border-border mb-6">
        <div class="flex items-center gap-2">
            <button class="dom-filter-toggle flex items-center gap-2 px-4 py-2.5 border-[1.5px] border-border rounded-lg text-xs font-bold uppercase tracking-label hover:border-fg transition-colors" data-filter="price">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><line x1="4" y1="6" x2="20" y2="6"/><line x1="8" y1="12" x2="20" y2="12"/><line x1="12" y1="18" x2="20" y2="18"/></svg>
                Prijs
            </button>
            <button class="dom-filter-toggle flex items-center gap-2 px-4 py-2.5 border-[1.5px] border-border rounded-lg text-xs font-bold uppercase tracking-label hover:border-fg transition-colors" data-filter="size">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg>
                Maat
            </button>
        </div>

        <div class="ml-auto flex items-center gap-3">
            <span class="text-xs text-muted tabular-nums md:hidden"><?php echo $product_count; ?> items</span>
            <select class="appearance-none bg-surface border border-border rounded-lg px-4 py-2.5 text-xs font-bold uppercase tracking-label text-muted cursor-pointer hover:border-fg transition-colors focus:outline-none" onchange="if(this.value) window.location=this.value">
                <option value="">Sorteren</option>
                <option value="?orderby=popularity">Populair</option>
                <option value="?orderby=price">Prijs laag → hoog</option>
                <option value="?orderby=price-desc">Prijs hoog → laag</option>
                <option value="?orderby=date">Nieuwste</option>
            </select>
        </div>
    </div>

    <!-- Category banner (between filters and grid) -->
    <?php if ($cat_banner) :
        $banner_url = is_array($cat_banner) ? $cat_banner['url'] : $cat_banner;
    ?>
        <div class="reveal relative mb-8 rounded-xl overflow-hidden aspect-[4/1] md:aspect-[5/1]">
            <img src="<?php echo esc_url($banner_url); ?>" alt="<?php echo esc_attr($current_cat->name); ?>" class="w-full h-full object-cover" loading="lazy">
            <div class="absolute inset-0 bg-gradient-to-r from-black/50 to-transparent"></div>
            <div class="absolute bottom-0 left-0 p-5 md:p-8">
                <h2 class="text-2xl md:text-3xl font-black text-white tracking-tight"><?php echo esc_html($current_cat->name); ?></h2>
                <?php if ($page_subtitle) : ?>
                    <p class="text-sm text-white/60 mt-1 max-w-md"><?php echo esc_html($page_subtitle); ?></p>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>

    <!-- Product grid -->
    <?php if (have_posts()) : ?>
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-2 md:gap-4">
            <?php
            $item_index = 0;
            while (have_posts()) : the_post();
                echo '<div class="reveal" style="transition-delay:' . ($item_index * 40) . 'ms">';
                wc_get_template_part('content', 'product');
                echo '</div>';
                $item_index++;
            endwhile;
            ?>
        </div>
    <?php else : ?>
        <div class="flex flex-col items-center justify-center py-20 text-center">
            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="text-muted mb-4"><circle cx="11" cy="11" r="7"/><line x1="16.5" y1="16.5" x2="21" y2="21"/></svg>
            <p class="text-lg font-bold mb-1">Geen producten gevonden</p>
            <p class="text-sm text-muted mb-4">Probeer een andere categorie of filter</p>
            <a href="<?php echo esc_url(get_permalink(wc_get_page_id('shop'))); ?>" class="px-6 py-3 bg-fg text-white text-xs font-bold uppercase tracking-label rounded-lg hover:bg-fg/90 transition-colors">Bekijk alles</a>
        </div>
    <?php endif; ?>

    <!-- Pagination -->
    <?php if ($wp_query->max_num_pages > 1) :
        $current_page = max(1, get_query_var('paged'));
        $total_pages = $wp_query->max_num_pages;
    ?>
        <nav class="mt-12 flex items-center justify-center gap-2" aria-label="Paginering">
            <?php if ($current_page > 1) : ?>
                <a href="<?php echo esc_url(get_pagenum_link($current_page - 1)); ?>" class="w-10 h-10 flex items-center justify-center border-[1.5px] border-border rounded-lg text-sm hover:border-fg transition-colors" aria-label="Vorige">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><polyline points="15,18 9,12 15,6"/></svg>
                </a>
            <?php endif; ?>

            <?php for ($i = 1; $i <= $total_pages; $i++) :
                if ($total_pages > 7 && abs($i - $current_page) > 2 && $i !== 1 && $i !== $total_pages) {
                    if ($i === 2 || $i === $total_pages - 1) echo '<span class="w-10 h-10 flex items-center justify-center text-muted text-sm">…</span>';
                    continue;
                }
            ?>
                <a href="<?php echo esc_url(get_pagenum_link($i)); ?>"
                   class="w-10 h-10 flex items-center justify-center border-[1.5px] rounded-lg text-sm font-bold transition-colors <?php echo $i === $current_page ? 'bg-fg text-white border-fg' : 'border-border text-muted hover:border-fg hover:text-fg'; ?>">
                    <?php echo $i; ?>
                </a>
            <?php endfor; ?>

            <?php if ($current_page < $total_pages) : ?>
                <a href="<?php echo esc_url(get_pagenum_link($current_page + 1)); ?>" class="w-10 h-10 flex items-center justify-center border-[1.5px] border-border rounded-lg text-sm hover:border-fg transition-colors" aria-label="Volgende">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><polyline points="9,6 15,12 9,18"/></svg>
                </a>
            <?php endif; ?>
        </nav>
    <?php endif; ?>
</div>

<?php get_footer(); ?>
