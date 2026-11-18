<?php
/**
 * Eén productkaartje in de winkelwagen-drawer.
 *
 * Stond eerst alleen in het paneel "Maak het compleet"; nu gedeeld, zodat
 * "Aanbevolen voor jou" en "Recent bekeken" dezelfde kaart gebruiken in
 * plaats van leeg te blijven.
 *
 * @var int $args['id'] Product-ID.
 */

defined('ABSPATH') || exit;

$product_id = isset($args['id']) ? (int) $args['id'] : 0;
$product    = $product_id && function_exists('wc_get_product') ? wc_get_product($product_id) : null;

if (!$product || 'publish' !== get_post_status($product_id)) {
    return;
}

$thumb = get_the_post_thumbnail_url($product_id, 'woocommerce_thumbnail')
      ?: get_the_post_thumbnail_url($product_id, 'medium')
      ?: get_the_post_thumbnail_url($product_id, 'full');

$sizes = [];
if ($product->is_type('variable')) {
    foreach ($product->get_variation_attributes() as $values) {
        $sizes = $values;
        break;
    }
}
?>
<div class="cmini">
  <a href="<?php echo esc_url(get_permalink($product_id)); ?>" class="im">
    <?php if ($thumb) : ?>
      <img src="<?php echo esc_url($thumb); ?>" alt="<?php echo esc_attr(get_the_title($product_id)); ?>" loading="lazy">
    <?php endif; ?>
  </a>
  <div class="t"><?php echo esc_html(get_the_title($product_id)); ?></div>
  <div class="p"><?php echo wp_kses_post(wc_price((float) $product->get_price())); ?></div>
  <div class="row2">
    <?php if ($sizes) : ?>
      <select aria-label="<?php esc_attr_e('Maat', 'dom-theme'); ?>">
        <?php foreach ($sizes as $size) : ?>
          <option><?php echo esc_html($size); ?></option>
        <?php endforeach; ?>
      </select>
    <?php else : ?>
      <span></span>
    <?php endif; ?>
    <button class="add" data-id="<?php echo (int) $product_id; ?>" aria-label="<?php esc_attr_e('Toevoegen', 'dom-theme'); ?>">
      <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M6 7h12l-1 13H7L6 7z"/><path d="M9 7V5a3 3 0 0 1 6 0v2"/></svg>
    </button>
  </div>
</div>
