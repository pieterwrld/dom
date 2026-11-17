<?php
defined('ABSPATH') || exit;

$acc_cats    = ['brillen', 'petten', 'tassen'];
$brand_hints = [
    'brillen' => 'Dita &middot; Cartier',
    'petten'  => 'Loro Piana',
    'tassen'  => 'Hermes &middot; Goyard',
];
$acc_data = [];
foreach ($acc_cats as $slug) {
    $term = get_term_by('slug', $slug, 'product_cat');
    if (!$term) continue;
    $thumb_id = get_term_meta($term->term_id, 'thumbnail_id', true);
    $img      = $thumb_id ? wp_get_attachment_image_url($thumb_id, 'product-card') : '';
    // Fallback: use first product's featured image
    if (!$img) {
        $prods = get_posts([
            'post_type'      => 'product',
            'posts_per_page' => 1,
            'post_status'    => 'publish',
            'tax_query'      => [['taxonomy' => 'product_cat', 'field' => 'slug', 'terms' => $slug]],
            'fields'         => 'ids',
        ]);
        if ($prods) $img = get_the_post_thumbnail_url($prods[0], 'product-card');
    }
    $acc_data[] = [
        'name'   => $term->name,
        'slug'   => $slug,
        'url'    => dom_term_url($term),
        'img'    => $img ?: '',
        'brands' => $brand_hints[$slug] ?? '',
    ];
}
?>
<div class="accblk" data-rv="up">
  <div class="acc-ims">
    <?php foreach ($acc_data as $a) : ?>
      <figure>
        <?php if ($a['img']) : ?>
          <img src="<?php echo esc_url($a['img']); ?>" alt="<?php echo esc_attr($a['name']); ?>" loading="lazy">
        <?php else : ?>
          <div class="ph" style="position:absolute;inset:0"><span class="ph-l"><?php echo esc_html($a['name']); ?></span></div>
        <?php endif; ?>
      </figure>
    <?php endforeach; ?>
  </div>
  <div class="acc-in">
    <div>
      <span class="tagred">Accessoires</span>
      <h2 style="margin-top:10px" class="split">Maak je look compleet</h2>
      <p class="tx" style="--d:300ms">Het verschil tussen een outfit en een look. Bril, pet en tas uit dezelfde collectie.</p>
    </div>
    <div class="acc-btns">
      <?php foreach ($acc_data as $a) : ?>
        <a href="<?php echo esc_url($a['url']); ?>" class="accb">
          <svg class="ar" width="12" height="12" viewBox="0 0 24 24" stroke="#fff" stroke-width="2" fill="none"><path d="M7 17L17 7M9 7h8v8"/></svg>
          <b><?php echo esc_html($a['name']); ?></b>
          <?php if ($a['brands']) : ?><span><?php echo $a['brands']; ?></span><?php endif; ?>
        </a>
      <?php endforeach; ?>
    </div>
  </div>
</div>
