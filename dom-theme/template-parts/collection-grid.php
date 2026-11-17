<?php
defined('ABSPATH') || exit;

$preferred_order = [
    'sneakers', 'jassen', 'jeans', 'trainingspakken',
    'sweaters-hoodies', 't-shirts', 'zwembroeken', 'petten', 'sale',
];

// Categories live under "Heren" parent
$heren = get_term_by('slug', 'heren', 'product_cat');
$parent_id = $heren ? $heren->term_id : 0;

$all_cats = get_terms([
    'taxonomy'   => 'product_cat',
    'hide_empty' => false,
    'parent'     => $parent_id,
]);

// Fallback to top-level if no children found
if (is_wp_error($all_cats) || empty($all_cats)) {
    $all_cats = get_terms([
        'taxonomy'   => 'product_cat',
        'hide_empty' => false,
        'parent'     => 0,
    ]);
    if (is_wp_error($all_cats)) {
        $all_cats = [];
    }
}

// Also add SALE (top-level) to the mix
$sale_term = get_term_by('slug', 'sale', 'product_cat');
if ($sale_term && !is_wp_error($sale_term)) {
    $all_cats[] = $sale_term;
}

$exclude_slugs = ['uncategorized', 'bestsellers', 'heren', 'nieuwste-collectie', 'truien-vesten'];
$cats_by_slug  = [];
foreach ($all_cats as $cat) {
    if (in_array($cat->slug, $exclude_slugs, true)) continue;
    $cats_by_slug[$cat->slug] = $cat;
}

$ordered = [];
foreach ($preferred_order as $slug) {
    if (isset($cats_by_slug[$slug])) {
        $ordered[] = $cats_by_slug[$slug];
        unset($cats_by_slug[$slug]);
    }
}
foreach ($cats_by_slug as $cat) {
    $ordered[] = $cat;
}

$tiles = array_slice($ordered, 0, 9);

$sale_slugs = ['sale'];
$badge_map  = [
    'sneakers' => ['label' => 'Bestseller', 'class' => ''],
    'jassen'   => ['label' => '-25%',       'class' => 'bf'],
    't-shirts' => ['label' => '3 v. &euro;130', 'class' => 'bf'],
    'sale'     => ['label' => 'Tot -50%',   'class' => 'bf'],
];

$shop_url = get_permalink(wc_get_page_id('shop'));
?>
<div class="sec-t" data-rv="up">
  <div class="eyebrow"><i></i><span class="lbl">Collecties</span></div>
  <div class="sec-h">
    <h2 class="d2 split">Shop per categorie</h2>
    <a href="<?php echo esc_url($shop_url); ?>" class="lk">Alle producten <span class="arw">&rarr;</span></a>
  </div>
</div>

<div class="cgrid">
  <?php foreach ($tiles as $i => $cat) :
    $thumb_id = get_term_meta($cat->term_id, 'thumbnail_id', true);
    $img_url  = $thumb_id ? wp_get_attachment_image_url($thumb_id, 'product-card') : '';
    $link     = get_term_link($cat);
    $delay    = $i * 50;
    $is_sale  = in_array($cat->slug, $sale_slugs, true);
    $badge    = $badge_map[$cat->slug] ?? null;

    // Fallback: use first product's featured image if no category thumbnail
    if (!$img_url) {
        $cat_products = get_posts([
            'post_type'      => 'product',
            'posts_per_page' => 1,
            'post_status'    => 'publish',
            'tax_query'      => [['taxonomy' => 'product_cat', 'field' => 'slug', 'terms' => $cat->slug]],
            'fields'         => 'ids',
        ]);
        if ($cat_products) {
            $img_url = get_the_post_thumbnail_url($cat_products[0], 'product-card');
        }
    }

    $min_price = '';
    $cheapest  = get_posts([
        'post_type'      => 'product',
        'posts_per_page' => 1,
        'tax_query'      => [['taxonomy' => 'product_cat', 'field' => 'slug', 'terms' => $cat->slug]],
        'meta_key'       => '_price',
        'orderby'        => 'meta_value_num',
        'order'          => 'ASC',
        'fields'         => 'ids',
    ]);
    if ($cheapest) {
        $p = wc_get_product($cheapest[0]);
        if ($p) $min_price = 'vanaf &euro;' . number_format((float) $p->get_price(), 0, ',', '.');
    }
  ?>
    <a href="<?php echo esc_url($link); ?>"
       class="ctile<?php echo $is_sale ? ' saletile' : ''; ?>"
       data-rv="scale"<?php if ($delay) echo ' style="--d:' . $delay . 'ms"'; ?>>
      <?php if ($img_url) : ?>
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
        <?php if ($min_price) : ?><div class="cmeta"><?php echo $min_price; ?></div><?php endif; ?>
        <div class="cgo">Shop nu <span class="arw">&rarr;</span></div>
      </div>
    </a>
  <?php endforeach; ?>
</div>

<a href="<?php echo esc_url($shop_url); ?>" class="dealbar" data-rv="up">
  <div>
    <h3>3 voor &euro;130</h3>
    <p>Combineer 3 T&#8209;shirts of zwembroeken. Korting automatisch bij het afrekenen.</p>
  </div>
  <span class="btn btn-ow">Stel je deal samen <span class="arw">&rarr;</span></span>
</a>
