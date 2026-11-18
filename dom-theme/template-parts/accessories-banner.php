<?php
/**
 * Accessoireblok: één brede band met bril, pet en tas.
 *
 * Welke categorieën erin staan en welke merken eronder komen, stel je in bij
 * De Outfitman > Homepage > Accessoireblok. De foto's komen van de categorieën
 * zelf (tabblad Categorieën).
 */

defined('ABSPATH') || exit;

$acc_rijen = dom_c_rows('home.accessoires.items', [
    ['slug' => 'brillen', 'merken' => 'Dita &middot; Cartier'],
    ['slug' => 'petten',  'merken' => 'Loro Piana'],
    ['slug' => 'tassen',  'merken' => 'Hermes &middot; Goyard'],
]);

$acc_data = [];
foreach ($acc_rijen as $rij) {
    $slug = (string) ($rij['slug'] ?? '');
    if ($slug === '') {
        continue;
    }
    $term = get_term_by('slug', $slug, 'product_cat');
    if (!$term) {
        continue;
    }
    $acc_data[] = [
        'name'   => $term->name,
        'slug'   => $slug,
        'url'    => dom_term_url($term),
        'img'    => dom_c_cat_img($term->term_id, 'raster', 'product-card'),
        'brands' => (string) ($rij['merken'] ?? ''),
    ];
}

if (!$acc_data) {
    return;
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
      <span class="tagred"><?php echo esc_html(dom_c('home.accessoires.label', 'Accessoires')); ?></span>
      <h2 style="margin-top:10px" class="split"><?php echo esc_html(dom_c('home.accessoires.titel', 'Maak je look compleet')); ?></h2>
      <p class="tx" style="--d:300ms"><?php echo esc_html(dom_c('home.accessoires.tekst', 'Het verschil tussen een outfit en een look. Bril, pet en tas uit dezelfde collectie.')); ?></p>
    </div>
    <div class="acc-btns">
      <?php foreach ($acc_data as $a) : ?>
        <a href="<?php echo esc_url($a['url']); ?>" class="accb">
          <svg class="ar" width="12" height="12" viewBox="0 0 24 24" stroke="#fff" stroke-width="2" fill="none"><path d="M7 17L17 7M9 7h8v8"/></svg>
          <b><?php echo esc_html($a['name']); ?></b>
          <?php if ($a['brands']) : ?><span><?php echo esc_html(html_entity_decode($a['brands'], ENT_QUOTES, 'UTF-8')); ?></span><?php endif; ?>
        </a>
      <?php endforeach; ?>
    </div>
  </div>
</div>
