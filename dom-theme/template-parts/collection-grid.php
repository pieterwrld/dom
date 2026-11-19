<?php
defined('ABSPATH') || exit;

// Dezelfde volgorde als het navigatiemenu, uit één bron.
$ordered = function_exists('dom_ordered_categories') ? dom_ordered_categories(true) : [];

$tiles = array_slice($ordered, 0, 9);

$sale_slugs = ['sale'];

/**
 * Badges komen uit WooCommerce, nooit uit een vaste lijst. Een kortingspercentage
 * dat niet klopt met de werkelijke prijzen is misleidend, dus we tonen alleen de
 * echte maximale korting binnen een categorie, en anders geen badge.
 */
$on_sale_ids = function_exists('wc_get_product_ids_on_sale') ? wc_get_product_ids_on_sale() : [];

$dom_category_badge = static function ($slug) use ($on_sale_ids) {
    if (!$on_sale_ids) {
        return null;
    }
    $ids = get_posts([
        'post_type'      => 'product',
        'post_status'    => 'publish',
        'posts_per_page' => 60,
        'post__in'       => $on_sale_ids,
        'tax_query'      => [['taxonomy' => 'product_cat', 'field' => 'slug', 'terms' => $slug]],
        'fields'         => 'ids',
        'no_found_rows'  => true,
    ]);
    if (!$ids) {
        return null;
    }
    $best = 0;
    foreach ($ids as $id) {
        $product = function_exists('wc_get_product') ? wc_get_product($id) : null;
        if (!$product) {
            continue;
        }
        $p = dom_prices($product);
        if ($p['sale'] && $p['pct'] > 0) {
            $best = max($best, $p['pct']);
        }
    }
    if ($best <= 0) {
        return ['label' => 'Sale', 'class' => 'bf'];
    }
    return ['label' => 'Tot -' . $best . '%', 'class' => 'bf'];
};

$shop_url = dom_shop_url();
?>
<div class="sec-t" data-rv="up">
  <div class="eyebrow"><i></i><span class="lbl"><?php echo esc_html(dom_c('home.collecties.eyebrow', 'Collecties')); ?></span></div>
  <div class="sec-h">
    <h2 class="d2 split"><?php echo esc_html(dom_c('home.collecties.titel', 'Shop per categorie')); ?></h2>
    <a href="<?php echo esc_url($shop_url); ?>" class="lk"><?php echo esc_html(dom_c('home.collecties.link', 'Alle producten')); ?> <span class="arw">&rarr;</span></a>
  </div>
</div>

<div class="cgrid">
  <?php foreach ($tiles as $i => $cat) :
    $img_url  = dom_c_cat_img($cat->term_id, 'raster', 'product-card');
    $link     = dom_term_url($cat);
    $delay    = $i * 50;
    $is_sale  = in_array($cat->slug, $sale_slugs, true);
    $badge    = $dom_category_badge($cat->slug);

  ?>
    <a href="<?php echo esc_url($link); ?>"
       class="ctile<?php echo $is_sale ? ' saletile' : ''; ?>"
       data-rv="scale"<?php if ($delay) echo ' style="--d:' . $delay . 'ms"'; ?>>
      <?php if ($is_sale) : ?>
        <?php // Sale krijgt geen foto maar een rood vlak met een draaiend sale-zegel. ?>
        <span class="cim sale-vlak" aria-hidden="true">
          <span class="sale-zegel">
            <svg viewBox="0 0 120 120" aria-hidden="true">
              <path class="sz-ring" d="M60 6 69 17l14-5 4 14 14 4-5 14 11 12-11 9 5 14-14 4-4 14-14-5-9 11-9-11-14 5-4-14-14-4 5-14L6 60l11-12-5-14 14-4 4-14 14 5z"/>
              <text class="sz-tekst" x="60" y="68" text-anchor="middle">SALE</text>
            </svg>
          </span>
        </span>
      <?php elseif ($img_url) : ?>
        <img class="cim" src="<?php echo esc_url($img_url); ?>" alt="<?php echo esc_attr($cat->name); ?>" loading="lazy">
      <?php else : ?>
        <div class="cim ph"><span class="ph-l"><?php echo esc_html($cat->name); ?></span></div>
      <?php endif; ?>
      <span class="shade"></span>
      <?php if ($badge) : ?>
        <span class="cbadge<?php echo $badge['class'] ? ' ' . $badge['class'] : ''; ?>"><?php echo $badge['label']; ?></span>
      <?php endif; ?>
      <span class="cline"></span>
      <div class="cbody">
        <div class="cname"><?php echo esc_html($cat->name); ?></div>
        <span class="cgo"><?php esc_html_e('Bekijk', 'dom-theme'); ?> <span class="arw">&rarr;</span></span>
      </div>
    </a>
  <?php endforeach; ?>
</div>

