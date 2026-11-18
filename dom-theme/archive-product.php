<?php
/**
 * Shop / Product Archive — Black Friday Chrome
 *
 * @package DomTheme
 */
defined('ABSPATH') || exit;
get_header();

$current_cat    = get_queried_object();
$is_category    = is_product_category();
$shop_url       = get_permalink(wc_get_page_id('shop'));
$product_count  = $wp_query->found_posts;
$page_title     = 'Shop';
$page_copy      = '';

if ($is_category && !is_wp_error($current_cat)) {
    $page_title = $current_cat->name;
    $page_copy  = $current_cat->description ?: '';
}

$cat_order = [
    'jassen', 'jeans', 'sneakers', 't-shirts', 'sweaters-hoodies',
    'brillen', 'tassen', 'trainingspakken', 'slippers', 'zwembroeken', 'petten', 'sale',
];
$cats = get_terms([
    'taxonomy'   => 'product_cat',
    'hide_empty' => false,
    'exclude'    => [get_option('default_product_cat')],
]);
if (is_wp_error($cats)) $cats = [];
$cats = array_filter($cats, fn($c) => $c->slug !== 'bestsellers');

$cat_map = [];
foreach ($cats as $c) $cat_map[$c->slug] = $c;
$ordered_cats = [];
foreach ($cat_order as $slug) {
    if (isset($cat_map[$slug])) $ordered_cats[] = $cat_map[$slug];
}
foreach ($cats as $c) {
    if (!in_array($c->slug, $cat_order)) $ordered_cats[] = $c;
}

// Banner per categorie. Beheer: De Outfitman > Categorieën.
$cat_banner_img = '';
$cat_banner_tag = 'Collectie';
if ($is_category) {
    $cat_banner_img = dom_c_cat_img($current_cat->term_id, 'banner', 'full');
    if (!$cat_banner_img && function_exists('dom_category_banner_url')) {
        $cat_banner_img = dom_category_banner_url($current_cat->term_id);
    }
}

$brands = get_terms([
    'taxonomy'   => 'merk',
    'hide_empty' => true,
    'orderby'    => 'name',
]);
if (is_wp_error($brands)) $brands = [];
?>

<main>
  <div class="wrap">
    <nav class="crumb">
      <a href="<?php echo esc_url(home_url('/')); ?>">Home</a>
      <span>/</span>
      <a href="<?php echo esc_url($shop_url); ?>">Shop</a>
      <?php if ($is_category) : ?>
        <span>/</span>
        <b><?php echo esc_html($page_title); ?></b>
      <?php endif; ?>
    </nav>

    <div class="chead">
      <div class="chead-r">
        <div>
          <h1 class="chars"><?php echo esc_html($page_title); ?></h1>
          <?php if ($page_copy) : ?>
            <p class="tx" style="--d:260ms"><?php echo esc_html($page_copy); ?></p>
          <?php endif; ?>
        </div>
      </div>
    </div>

    <!-- SNEL NAAR JOUW GEWENSTE CATEGORIE -->
    <div class="quickcat">
      <div class="qc-h"><span>Snel naar jouw gewenste categorie</span></div>
      <div class="qc" id="qc">
        <?php foreach ($ordered_cats as $cat) :
          $is_active = ($is_category && $current_cat->term_id === $cat->term_id);
          $is_sale   = (strtolower($cat->slug) === 'sale');
        ?>
          <a href="<?php echo esc_url(dom_term_url($cat)); ?>"
             class="qcb<?php echo $is_active ? ' on' : ''; ?><?php echo $is_sale ? ' bf' : ''; ?>">
            <b><?php echo esc_html($cat->name); ?></b>
          </a>
        <?php endforeach; ?>
      </div>
    </div>
  </div>

  <!-- STICKY FILTER BAR -->
  <div class="fwrap">
    <div class="wrap">
      <div class="fbar">
        <!-- Prijs -->
        <div class="fgrp">
          <button class="fbtn" id="fpBtn">Prijs <b id="fpVal">Alles</b>
            <svg viewBox="0 0 24 24"><path d="M6 9l6 6 6-6"/></svg>
          </button>
          <div class="fpop" id="fpPop">
            <div class="fgh">Maximale prijs</div>
            <input type="range" class="rng" id="rng" min="30" max="500" step="10" value="500">
            <div class="rngv"><span>&euro;30</span><span id="rngNow">&euro;500+</span></div>
            <button class="fclear" data-clear="price">Wissen</button>
          </div>
        </div>
        <!-- Maat -->
        <div class="fgrp">
          <button class="fbtn" id="fsBtn">Maat <b id="fsVal">Alles</b>
            <svg viewBox="0 0 24 24"><path d="M6 9l6 6 6-6"/></svg>
          </button>
          <div class="fpop" id="fsPop">
            <div class="fgh">Kleding</div>
            <div class="szg" id="szClothes"></div>
            <div class="fgh" style="margin-top:13px">Schoenen</div>
            <div class="szg" id="szShoes"></div>
            <button class="fclear" data-clear="size">Wissen</button>
          </div>
        </div>
        <!-- Merk -->
        <?php if (!empty($brands)) : ?>
        <div class="fgrp">
          <button class="fbtn" id="fbBtn">Merk <b id="fbVal">Alles</b>
            <svg viewBox="0 0 24 24"><path d="M6 9l6 6 6-6"/></svg>
          </button>
          <div class="fpop" id="fbPop">
            <div class="fgh">Merken</div>
            <div class="brg" id="brandList">
              <?php foreach ($brands as $brand) : ?>
                <button class="brb" data-b="<?php echo esc_attr($brand->slug); ?>">
                  <i><svg viewBox="0 0 24 24"><path d="M4 12l5 5L20 6"/></svg></i>
                  <?php echo esc_html($brand->name); ?>
                  <span><?php echo $brand->count; ?></span>
                </button>
              <?php endforeach; ?>
            </div>
            <button class="fclear" data-clear="brand">Wissen</button>
          </div>
        </div>
        <?php endif; ?>
        <!-- Sorteer -->
        <div class="sortw">
          <div class="fgrp">
            <button class="fbtn" id="soBtn"><span id="soVal">Populair</span>
              <svg viewBox="0 0 24 24"><path d="M6 9l6 6 6-6"/></svg>
            </button>
            <div class="fpop" id="soPop" style="left:auto;right:0;min-width:215px">
              <div class="fgh">Sorteer op</div>
              <div style="display:grid;gap:1px">
                <button class="fclear" style="margin:0;border:0;text-align:left;padding:0 7px;height:32px" data-sort="popularity">Populair</button>
                <button class="fclear" style="margin:0;border:0;text-align:left;padding:0 7px;height:32px" data-sort="price">Prijs laag &rarr; hoog</button>
                <button class="fclear" style="margin:0;border:0;text-align:left;padding:0 7px;height:32px" data-sort="price-desc">Prijs hoog &rarr; laag</button>
                <button class="fclear" style="margin:0;border:0;text-align:left;padding:0 7px;height:32px" data-sort="date">Nieuwste</button>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="chips" id="chips" style="display:none"></div>
    </div>
  </div>

  <div class="wrap">
    <?php if ($cat_banner_img) : ?>
      <a class="cbanner" href="<?php echo $is_category ? esc_url(dom_term_url($current_cat)) : '#'; ?>">
        <img src="<?php echo esc_url($cat_banner_img); ?>" alt="<?php echo esc_attr($page_title); ?>" loading="lazy">
        <div class="cb-in">
          <div>
            <span class="lbl"><?php echo esc_html($cat_banner_tag); ?></span>
            <h2 style="margin-top:8px"><?php echo esc_html($page_title); ?></h2>
            <?php if ($page_copy) : ?>
              <p><?php echo esc_html($page_copy); ?></p>
            <?php endif; ?>
          </div>
          <span class="btn btn-ow">Shop de collectie <span class="arw">&rarr;</span></span>
        </div>
      </a>
    <?php endif; ?>

    <!-- PRODUCT GRID -->
    <?php if (have_posts()) : ?>
      <div class="pgrid" id="grid">
        <?php
        $idx = 0;
        while (have_posts()) : the_post();
          wc_get_template_part('content', 'product');
          $idx++;
        endwhile;
        ?>
      </div>
    <?php else : ?>
      <div class="empty" id="empty">
        <h3>Geen producten gevonden</h3>
        <p class="cap" style="margin-top:6px">Pas je filters aan of bekijk alle producten.</p>
        <a href="<?php echo esc_url($shop_url); ?>" class="btn btn-o" style="margin-top:16px">Filters wissen</a>
      </div>
    <?php endif; ?>

    <!-- PAGINATION -->
    <?php if ($wp_query->max_num_pages > 1) :
      $current_page = max(1, get_query_var('paged'));
      $total_pages  = $wp_query->max_num_pages;
      $total_shown  = min($wp_query->found_posts, $current_page * get_option('posts_per_page'));
    ?>
      <div class="loadw">
        <div class="pbar"><i style="width:<?php echo round($total_shown / $wp_query->found_posts * 100); ?>%"></i></div>
        <div class="pc"><?php echo $total_shown; ?> van <?php echo $wp_query->found_posts; ?> producten</div>
        <?php if ($current_page < $total_pages) : ?>
          <a href="<?php echo esc_url(get_pagenum_link($current_page + 1)); ?>" class="btn btn-o">Meer laden</a>
        <?php endif; ?>
      </div>
    <?php endif; ?>
  </div>
</main>

<?php get_footer(); ?>
