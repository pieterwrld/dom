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

/**
 * De snelkeuzeblokken volgen dezelfde lijst als het menu en het collectieraster.
 * Die laat Heren, Nieuwste collectie en Truien & Vesten weg: de eerste is de
 * overkoepelende categorie en de andere twee overlappen met wat er al staat.
 */
$ordered_cats = function_exists('dom_ordered_categories') ? dom_ordered_categories() : [];

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

/**
 * Het filter werkt via het adres, zodat een gefilterde lijst te delen is en
 * het doorladen bij het scrollen de filters meeneemt.
 */
$maat_groepen   = function_exists('dom_beschikbare_maten') ? dom_beschikbare_maten() : [];
$maat_gekozen   = function_exists('dom_gekozen_maten') ? dom_gekozen_maten() : [];
$prijs_gekozen  = function_exists('dom_gekozen_maxprijs') ? dom_gekozen_maxprijs() : 0;
$filter_basis   = $is_category ? dom_term_url($current_cat) : $shop_url;
$prijs_max      = 500;
$heeft_filter   = $maat_gekozen || $prijs_gekozen;

/** Het huidige adres met één parameter aangepast. */
$filter_url = static function (array $wijziging) use ($filter_basis, $maat_gekozen, $prijs_gekozen) {
    $args = [];
    if ($maat_gekozen) {
        $args['maat'] = implode(',', $maat_gekozen);
    }
    if ($prijs_gekozen) {
        $args['max_prijs'] = $prijs_gekozen;
    }
    foreach ($wijziging as $k => $v) {
        if ($v === null || $v === '' || $v === []) {
            unset($args[$k]);
        } else {
            $args[$k] = is_array($v) ? implode(',', $v) : $v;
        }
    }
    return $args ? add_query_arg($args, $filter_basis) : $filter_basis;
};
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
          <button class="fbtn<?php echo $prijs_gekozen ? ' act' : ''; ?>" id="fpBtn">Prijs <b id="fpVal"><?php echo $prijs_gekozen ? '&le; &euro;' . (int) $prijs_gekozen : 'Alles'; ?></b>
            <svg viewBox="0 0 24 24"><path d="M6 9l6 6 6-6"/></svg>
          </button>
          <div class="fpop" id="fpPop">
            <div class="fgh">Maximale prijs</div>
            <input type="range" class="rng" id="rng" min="30" max="<?php echo (int) $prijs_max; ?>" step="10"
                   value="<?php echo (int) ($prijs_gekozen ?: $prijs_max); ?>"
                   data-basis="<?php echo esc_url($filter_url(['max_prijs' => '__p__'])); ?>">
            <div class="rngv">
              <span>&euro;30</span>
              <span id="rngNow"><?php echo $prijs_gekozen ? '&euro;' . (int) $prijs_gekozen : '&euro;' . (int) $prijs_max . '+'; ?></span>
            </div>
            <a class="fclear" href="<?php echo esc_url($filter_url(['max_prijs' => null])); ?>">Wissen</a>
          </div>
        </div>
        <!-- Maat -->
        <div class="fgrp">
          <button class="fbtn<?php echo $maat_gekozen ? ' act' : ''; ?>" id="fsBtn">Maat <b id="fsVal"><?php
            if (!$maat_gekozen) {
                echo 'Alles';
            } elseif (count($maat_gekozen) === 1) {
                [$mt, $ms] = explode(':', $maat_gekozen[0], 2);
                $mterm = get_term_by('slug', $ms, $mt);
                echo esc_html($mterm ? $mterm->name : $ms);
            } else {
                echo (int) count($maat_gekozen) . ' gekozen';
            }
          ?></b>
            <svg viewBox="0 0 24 24"><path d="M6 9l6 6 6-6"/></svg>
          </button>
          <div class="fpop" id="fsPop">
            <?php if (!$maat_groepen) : ?>
              <div class="fgh"><?php esc_html_e('Geen maten bekend', 'dom-theme'); ?></div>
            <?php else : ?>
              <?php $eerste = true; foreach ($maat_groepen as $taxonomie => $groep) : ?>
                <div class="fgh"<?php echo $eerste ? '' : ' style="margin-top:13px"'; ?>><?php echo esc_html($groep['label']); ?></div>
                <div class="szg">
                  <?php foreach ($groep['maten'] as $slug => $maat) :
                    $sleutel = $taxonomie . ':' . $slug;
                    $aan     = in_array($sleutel, $maat_gekozen, true);
                    $nieuw   = $aan
                        ? array_values(array_diff($maat_gekozen, [$sleutel]))
                        : array_merge($maat_gekozen, [$sleutel]);
                  ?>
                    <a class="szb<?php echo $aan ? ' on' : ''; ?><?php echo $maat['voorraad'] ? '' : ' uit'; ?>"
                       href="<?php echo esc_url($filter_url(['maat' => $nieuw])); ?>"
                       <?php if (!$maat['voorraad']) : ?>title="<?php esc_attr_e('Nergens meer op voorraad', 'dom-theme'); ?>"<?php endif; ?>>
                      <?php echo esc_html($maat['naam']); ?>
                    </a>
                  <?php endforeach; ?>
                </div>
              <?php $eerste = false; endforeach; ?>
            <?php endif; ?>
            <a class="fclear" href="<?php echo esc_url($filter_url(['maat' => null])); ?>">Wissen</a>
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
      <?php if ($heeft_filter) : ?>
        <div class="chips" id="chips">
          <?php foreach ($maat_gekozen as $sleutel) :
            [$mt, $ms] = explode(':', $sleutel, 2);
            $mterm = get_term_by('slug', $ms, $mt);
            $zonder = array_values(array_diff($maat_gekozen, [$sleutel]));
          ?>
            <a class="chip" href="<?php echo esc_url($filter_url(['maat' => $zonder])); ?>">
              <?php echo esc_html($mterm ? $mterm->name : $ms); ?>
              <span aria-hidden="true">&times;</span>
            </a>
          <?php endforeach; ?>
          <?php if ($prijs_gekozen) : ?>
            <a class="chip" href="<?php echo esc_url($filter_url(['max_prijs' => null])); ?>">
              &le; &euro;<?php echo (int) $prijs_gekozen; ?>
              <span aria-hidden="true">&times;</span>
            </a>
          <?php endif; ?>
          <a class="chip chip--wis" href="<?php echo esc_url($filter_basis); ?>"><?php esc_html_e('Alles wissen', 'dom-theme'); ?></a>
        </div>
      <?php endif; ?>
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
      // Het aantal per pagina komt uit de query zelf. get_option('posts_per_page')
      // is de blogwaarde (10) en die klopt niet met het productraster, waardoor
      // er "10 van 445" onder 24 producten stond.
      $per_pagina   = (int) $wp_query->get('posts_per_page');
      $total_shown  = min(
          (int) $wp_query->found_posts,
          ($current_page - 1) * max(1, $per_pagina) + (int) $wp_query->post_count
      );
    ?>
      <div class="loadw">
        <div class="pbar"><i style="width:<?php echo $wp_query->found_posts ? round($total_shown / $wp_query->found_posts * 100) : 0; ?>%"></i></div>
        <div class="pc"><?php echo $total_shown; ?> van <?php echo $wp_query->found_posts; ?> producten</div>
        <?php if ($current_page < $total_pages) : ?>
          <a href="<?php echo esc_url(get_pagenum_link($current_page + 1)); ?>" class="btn btn-o">Meer laden</a>
        <?php endif; ?>
      </div>
    <?php endif; ?>
  </div>
</main>

<?php get_footer(); ?>
