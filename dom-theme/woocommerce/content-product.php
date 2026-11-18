<?php
/**
 * Product card in shop loop — Black Friday Chrome
 *
 * @package DomTheme
 */
defined('ABSPATH') || exit;

global $product;
if (!$product || !$product->is_visible()) return;

static $pcard_idx = 0;
$delay = ($pcard_idx % 8) * 50;
$pcard_idx++;

$gallery_ids  = $product->get_gallery_image_ids();
$thumbnail_id = $product->get_image_id();
$all_images   = $thumbnail_id ? array_merge([$thumbnail_id], $gallery_ids) : $gallery_ids;
$all_images   = array_slice($all_images, 0, 5);

// Prijzen via de gedeelde helper: bij producten met maten staan ze op de
// variaties, en dan geeft get_regular_price() een lege waarde.
$prijs    = dom_prices($product);
$on_sale  = $prijs['sale'];
$sale_pct = $prijs['pct'] > 0 ? '-' . $prijs['pct'] . '%' : '';

$is_new = (strtotime($product->get_date_created()) > strtotime('-14 days'));
$is_out = !$product->is_in_stock();

$sizes = [];
if ($product->is_type('variable')) {
    $attrs = $product->get_variation_attributes();
    foreach ($attrs as $attr => $vals) {
        $sizes = array_values(array_filter($vals));
        break;
    }
}


?>
<a href="<?php echo esc_url($product->get_permalink()); ?>"
   class="pcard" data-rv="up" style="--d:<?php echo $delay; ?>ms">

  <div class="pmedia">
    <div class="ptrack">
      <?php foreach ($all_images as $img_id) :
        $src = wp_get_attachment_image_url($img_id, 'product-card');
        if (!$src) continue;
      ?>
        <img src="<?php echo esc_url($src); ?>"
             alt="<?php echo esc_attr($product->get_name()); ?>"
             loading="lazy" draggable="false">
      <?php endforeach; ?>
      <?php if (empty($all_images)) : ?>
        <img src="<?php echo esc_url(wc_placeholder_img_src('product-card')); ?>"
             alt="<?php echo esc_attr($product->get_name()); ?>"
             loading="lazy" draggable="false">
      <?php endif; ?>
    </div>
    <span class="sheen"></span>

    <!-- Badges -->
    <div class="pbadges">
      <?php if ($sale_pct) : ?>
        <span class="pb pb-bf"><?php echo esc_html($sale_pct); ?></span>
      <?php endif; ?>
      <?php if ($is_new && !$sale_pct) : ?>
        <span class="pb pb-new">Nieuw</span>
      <?php endif; ?>
      <?php if ($is_out) : ?>
        <span class="pb pb-out">Uitverkocht</span>
      <?php endif; ?>
    </div>

    <?php if (count($all_images) > 1) : ?>
      <button class="parrow l" data-d="-1" aria-label="Vorige"><svg viewBox="0 0 24 24"><path d="M15 5l-7 7 7 7"/></svg></button>
      <button class="parrow r" data-d="1" aria-label="Volgende"><svg viewBox="0 0 24 24"><path d="M9 5l7 7-7 7"/></svg></button>
      <div class="slidebars">
        <?php foreach ($all_images as $k => $img_id) : ?>
          <i<?php echo $k === 0 ? ' class="on"' : ''; ?>></i>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>

  <div class="pinfo">
    <div class="ptitle"><?php echo esc_html($product->get_name()); ?></div>
    <div class="pprice<?php echo $on_sale ? ' sale' : ''; ?>">
      <?php echo dom_price_html($product); // phpcs:ignore WordPress.Security.EscapeOutput ?>
    </div>
    <?php if (!empty($sizes)) : ?>
      <div class="psizes">
        <?php foreach (array_slice($sizes, 0, 6) as $sz) : ?>
          <span><?php echo esc_html($sz); ?></span>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>
</a>
