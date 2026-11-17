<?php
/**
 * Single Product Page — Black Friday Chrome
 * Fixed order from HANDOVER.md (niet wijzigen)
 *
 * @package DomTheme
 */
defined('ABSPATH') || exit;
get_header();

while (have_posts()) : the_post();
    global $product;

    $eur = function($v) {
        return '€' . number_format((float)$v, 2, ',', '.');
    };

    /* ── data ── */
    $gallery_ids  = $product->get_gallery_image_ids();
    $thumbnail_id = $product->get_image_id();
    $all_images   = $thumbnail_id ? array_merge([$thumbnail_id], $gallery_ids) : $gallery_ids;

    $on_sale    = $product->is_on_sale();
    $regular    = (float) $product->get_regular_price();
    $sale_price = $on_sale ? (float) $product->get_sale_price() : 0;
    $price      = (float) $product->get_price();
    $save_pct   = ($on_sale && $regular > 0) ? round((1 - $sale_price / $regular) * 100) : 0;

    $product_cats = wp_get_post_terms($product->get_id(), 'product_cat', ['fields' => 'all']);
    $primary_cat  = !empty($product_cats) ? $product_cats[0] : null;

    /* sizes + per-size stock (variable products) */
    $sizes_data  = [];
    $size_attr_key = '';
    if ($product->is_type('variable')) {
        $variations = $product->get_available_variations();
        foreach ($variations as $var) {
            $var_product = wc_get_product($var['variation_id']);
            if (!$var_product) continue;
            $attrs = $var['attributes'];
            $size_label = '';
            foreach ($attrs as $key => $val) {
                if (strpos($key, 'schoenmaat') !== false || strpos($key, 'maat') !== false || strpos($key, 'size') !== false) {
                    $size_label = $val;
                    if (!$size_attr_key) $size_attr_key = $key;
                    break;
                }
            }
            if (!$size_label && !empty($attrs)) {
                $size_label = reset($attrs);
                if (!$size_attr_key) $size_attr_key = key($attrs);
            }
            if ($size_label) {
                $qty = $var_product->get_stock_quantity();
                $sizes_data[] = [
                    's'  => $size_label,
                    'q'  => $qty !== null ? (int)$qty : 99,
                    'v'  => (int)$var['variation_id'],
                    'in' => $var_product->is_in_stock(),
                ];
            }
        }
    }

    /* color siblings (separate products sharing pa_model) */
    $color_siblings = [];
    if (taxonomy_exists('pa_model')) {
        $model_terms = wp_get_post_terms($product->get_id(), 'pa_model', ['fields' => 'slugs']);
        if (!is_wp_error($model_terms) && !empty($model_terms)) {
            $sibling_query = new WP_Query([
                'post_type'      => 'product',
                'posts_per_page' => 10,
                'tax_query'      => [['taxonomy' => 'pa_model', 'field' => 'slug', 'terms' => $model_terms]],
                'orderby'        => 'title',
                'order'          => 'ASC',
            ]);
            foreach ($sibling_query->posts as $sib) {
                $sib_product = wc_get_product($sib->ID);
                if (!$sib_product) continue;
                $color_siblings[] = [
                    'id'     => $sib->ID,
                    'name'   => $sib_product->get_name(),
                    'url'    => $sib_product->get_permalink(),
                    'img'    => get_the_post_thumbnail_url($sib->ID, 'product-card') ?: wc_placeholder_img_src('product-card'),
                    'active' => ($sib->ID === $product->get_id()),
                ];
            }
            wp_reset_postdata();
        }
    }
    if (empty($color_siblings) && function_exists('get_field')) {
        $acf_siblings = get_field('color_siblings', $product->get_id());
        if (!empty($acf_siblings) && is_array($acf_siblings)) {
            $color_siblings[] = [
                'id' => $product->get_id(), 'name' => $product->get_name(),
                'url' => $product->get_permalink(),
                'img' => get_the_post_thumbnail_url($product->get_id(), 'product-card') ?: wc_placeholder_img_src('product-card'),
                'active' => true,
            ];
            foreach ($acf_siblings as $sib_id) {
                if (is_object($sib_id)) $sib_id = $sib_id->ID;
                $sib_product = wc_get_product($sib_id);
                if (!$sib_product || $sib_id === $product->get_id()) continue;
                $color_siblings[] = [
                    'id' => $sib_id, 'name' => $sib_product->get_name(),
                    'url' => $sib_product->get_permalink(),
                    'img' => get_the_post_thumbnail_url($sib_id, 'product-card') ?: wc_placeholder_img_src('product-card'),
                    'active' => false,
                ];
            }
        }
    }

    /* gallery arrays for JS */
    $gal_full   = array_values(array_filter(array_map(fn($id) => wp_get_attachment_image_url($id, 'product-gallery') ?: '', $all_images)));
    $gal_thumbs = array_values(array_filter(array_map(fn($id) => wp_get_attachment_image_url($id, 'product-card') ?: '', $all_images)));

    /* upsell + cross-sell */
    $combined_ids = array_unique(array_merge($product->get_upsell_ids(), $product->get_cross_sell_ids()));
    if (empty($combined_ids) && $primary_cat) {
        $fb = new WP_Query([
            'post_type' => 'product', 'posts_per_page' => 6, 'post__not_in' => [$product->get_id()],
            'tax_query' => [['taxonomy' => 'product_cat', 'field' => 'term_id', 'terms' => [$primary_cat->term_id]]],
            'orderby' => 'rand',
        ]);
        $combined_ids = wp_list_pluck($fb->posts, 'ID');
        wp_reset_postdata();
    }
    $upsell_products = [];
    foreach (array_slice($combined_ids, 0, 6) as $uid) {
        $up = wc_get_product($uid);
        if ($up && $up->is_visible()) $upsell_products[] = $up;
    }

    /* same category */
    $same_cat = [];
    if ($primary_cat) {
        $sq = new WP_Query([
            'post_type' => 'product', 'posts_per_page' => 8, 'post__not_in' => [$product->get_id()],
            'tax_query' => [['taxonomy' => 'product_cat', 'field' => 'term_id', 'terms' => [$primary_cat->term_id]]],
            'orderby' => 'rand',
        ]);
        $same_cat = $sq->posts;
        wp_reset_postdata();
    }

    /* pass data to gallery.js */
    wp_localize_script('dom-gallery', 'domProd', [
        'id'       => $product->get_id(),
        'gallery'  => $gal_full,
        'thumbs'   => $gal_thumbs,
        'sizes'    => $sizes_data,
        'sizeAttr' => $size_attr_key,
        'variable' => $product->is_type('variable'),
        'inStock'  => $product->is_in_stock(),
        'ajaxUrl'  => admin_url('admin-ajax.php'),
        'nonce'    => wp_create_nonce('dom-atc'),
        'pageUrl'  => get_permalink(),
    ]);
?>

<main>
  <div class="wrap">
    <!-- BREADCRUMB -->
    <nav class="crumb">
      <a href="<?php echo esc_url(home_url('/')); ?>">Home</a><span>/</span>
      <a href="<?php echo esc_url(get_permalink(wc_get_page_id('shop'))); ?>">Shop</a>
      <?php if ($primary_cat) : ?>
        <span>/</span><a href="<?php echo esc_url(dom_term_url($primary_cat)); ?>"><?php echo esc_html($primary_cat->name); ?></a>
      <?php endif; ?>
      <span>/</span><b><?php echo esc_html($product->get_name()); ?></b>
    </nav>

    <!-- PDP 2-column grid -->
    <div class="pdp">

      <!-- ══════ 1. GALLERY ══════ -->
      <div class="gal">
        <div class="gal-stage" id="stage">
          <div class="gal-track" id="galTrack">
            <?php foreach ($gal_full as $i => $url) : ?>
              <img src="<?php echo esc_url($url); ?>" alt="<?php echo esc_attr($product->get_name()); ?>" draggable="false"<?php echo $i > 0 ? ' loading="lazy"' : ''; ?>>
            <?php endforeach; ?>
            <?php if (empty($gal_full)) : ?>
              <img src="<?php echo esc_url(wc_placeholder_img_src('product-gallery')); ?>" alt="<?php echo esc_attr($product->get_name()); ?>" draggable="false">
            <?php endif; ?>
          </div>
          <span class="glow"></span>
          <?php if ($on_sale && $save_pct > 0) : ?>
            <span class="gal-bf">Black Friday &middot; -<?php echo $save_pct; ?>%</span>
          <?php endif; ?>
          <span class="gal-cnt"><span id="galI">1</span>/<span id="galN"><?php echo max(1, count($gal_full)); ?></span></span>
          <?php if (count($gal_full) > 1) : ?>
            <button class="gal-arw l" id="galL" aria-label="Vorige"><svg viewBox="0 0 24 24"><path d="M15 5l-7 7 7 7"/></svg></button>
            <button class="gal-arw r" id="galR" aria-label="Volgende"><svg viewBox="0 0 24 24"><path d="M9 5l7 7-7 7"/></svg></button>
            <div class="gal-dots" id="galDots">
              <?php for ($i = 0; $i < count($gal_full); $i++) : ?>
                <span class="pdot<?php echo $i === 0 ? ' on' : ''; ?>"></span>
              <?php endfor; ?>
            </div>
          <?php endif; ?>
        </div>
        <?php if (count($gal_full) > 1) : ?>
          <div class="thumbs" id="thumbs">
            <?php foreach ($gal_thumbs as $i => $url) : ?>
              <button class="<?php echo $i === 0 ? 'on' : ''; ?>" data-k="<?php echo $i; ?>">
                <img src="<?php echo esc_url($url); ?>" alt="" loading="lazy">
              </button>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
      </div>

      <!-- ══════ 2–11. INFO PANEL ══════ -->
      <div class="pinf">

        <!-- 2. TITLE -->
        <h1 class="split"><?php echo esc_html($product->get_name()); ?></h1>

        <!-- 3. PRICE -->
        <div class="p-price">
          <?php if ($on_sale) : ?>
            <span class="now"><?php echo $eur($sale_price); ?></span>
            <s><?php echo $eur($regular); ?></s>
            <span class="off">-<?php echo $save_pct; ?>%</span>
          <?php else : ?>
            <span class="now"><?php echo $eur($price); ?></span>
          <?php endif; ?>
          <span class="tax">Incl. btw &middot; excl. verzending</span>
        </div>

        <!-- BF deal timer (sale only) -->
        <?php if ($on_sale) : ?>
          <div class="pdeal">
            <b>Black Friday</b><span>Deze prijs geldt nog</span>
            <span class="t" id="pdT">00d 00:00:00</span>
          </div>
        <?php endif; ?>

        <!-- 4. STOCK URGENCY -->
        <div class="stock" id="stock">
          <span class="sd"></span>
          <span id="stockTxt"><?php
            if ($product->is_type('variable')) {
                echo 'Kies je maat om de voorraad te zien';
            } else {
                $qty = $product->get_stock_quantity();
                if ($qty === null || $qty > 6) echo 'Op voorraad — voor 17:00 besteld, morgen in huis';
                elseif ($qty === 1) echo 'Laatste op voorraad — wees er snel bij!';
                elseif ($qty === 2) echo 'Nog 2 op voorraad — twijfel niet!';
                elseif ($qty >= 3)  echo 'Nog ' . $qty . ' op voorraad — bijna uitverkocht';
                else echo 'Uitverkocht';
            }
          ?></span>
        </div>

        <!-- 5. COLOR (sibling products) -->
        <?php if (!empty($color_siblings) && count($color_siblings) > 1) : ?>
          <div class="blk">
            <div class="blk-h">
              <span class="lbl">Kleur</span>
              <span class="pick" id="colName"><?php
                foreach ($color_siblings as $sib) {
                    if ($sib['active']) { echo esc_html($sib['name']); break; }
                }
              ?></span>
            </div>
            <div class="cols" id="cols">
              <?php foreach ($color_siblings as $sib) : ?>
                <a href="<?php echo esc_url($sib['url']); ?>"
                   class="colb<?php echo $sib['active'] ? ' on' : ''; ?>"
                   title="<?php echo esc_attr($sib['name']); ?>">
                  <img src="<?php echo esc_url($sib['img']); ?>" alt="<?php echo esc_attr($sib['name']); ?>" loading="lazy">
                </a>
              <?php endforeach; ?>
            </div>
          </div>
        <?php endif; ?>

        <!-- 6. SIZE -->
        <?php if (!empty($sizes_data)) : ?>
          <div class="blk">
            <div class="blk-h">
              <span class="lbl">Maat</span>
              <span class="pick" id="szPick">Kies een maat</span>
            </div>
            <div class="szrow" id="szRow">
              <?php foreach ($sizes_data as $i => $sz) : ?>
                <button class="szp" data-k="<?php echo $i; ?>"<?php echo !$sz['in'] ? ' disabled' : ''; ?>><?php echo esc_html($sz['s']); ?></button>
              <?php endforeach; ?>
            </div>
            <div class="szhelp">
              <span class="cap">Valt op maat</span>
              <a href="#" data-open="ai-maat">Maatadvies bekijken</a>
            </div>
          </div>
        <?php endif; ?>

        <!-- 7. ADD TO CART -->
        <div class="atc">
          <form id="atcForm" action="<?php echo esc_url(get_permalink()); ?>" method="post">
            <input type="hidden" name="add-to-cart" value="<?php echo esc_attr($product->get_id()); ?>">
            <input type="hidden" name="product_id" value="<?php echo esc_attr($product->get_id()); ?>">
            <?php if ($product->is_type('variable')) : ?>
              <input type="hidden" name="variation_id" id="variationId" value="">
              <?php if ($size_attr_key) : ?>
                <input type="hidden" name="<?php echo esc_attr($size_attr_key); ?>" id="sizeAttrVal" value="">
              <?php endif; ?>
            <?php endif; ?>
            <input type="hidden" name="quantity" value="1">
            <button type="submit" class="btn btn-chrome" id="atc"<?php echo !$product->is_in_stock() ? ' disabled' : ''; ?>>
              <?php echo $product->is_in_stock() ? 'In winkelwagen <span class="arw">&rarr;</span>' : 'Uitverkocht'; ?>
            </button>
          </form>

          <!-- 8. DELIVERY PROMISE -->
          <div class="deliv">
            <svg viewBox="0 0 24 24"><path d="M3 7h11v9H3z"/><path d="M14 10h4l3 3v3h-7z"/><circle cx="7" cy="18" r="1.6"/><circle cx="17" cy="18" r="1.6"/></svg>
            <span>Vandaag besteld? <b id="delivDay">Morgen in huis</b></span>
          </div>

          <!-- WhatsApp -->
          <div class="wa-link">
            <svg width="17" height="17" viewBox="0 0 32 32" fill="#25D366" aria-hidden="true"><path d="M16 3C8.8 3 3 8.8 3 16c0 2.3.6 4.5 1.7 6.4L3 29l6.8-1.8A13 13 0 1 0 16 3zm0 23.7c-2 0-4-.5-5.7-1.6l-.4-.2-4 1 1.1-3.9-.3-.4A10.7 10.7 0 1 1 16 26.7zm6.1-7.9c-.3-.2-2-1-2.3-1.1-.3-.1-.5-.2-.8.2s-.9 1.1-1.1 1.3c-.2.2-.4.3-.8.1a8.7 8.7 0 0 1-4.3-3.8c-.3-.6.3-.5.9-1.7.1-.2 0-.4 0-.6l-1-2.5c-.3-.7-.6-.6-.8-.6h-.7c-.2 0-.6.1-.9.5-.3.4-1.2 1.2-1.2 2.9s1.2 3.3 1.4 3.6c.2.2 2.4 3.8 6 5.3 2.2.9 3.1 1 4.2.8.7-.1 2-.8 2.3-1.6.3-.8.3-1.5.2-1.6-.1-.2-.3-.3-.6-.4z"/></svg>
            <span>Twijfel over je maat? <a href="https://wa.me/31616264544" target="_blank" rel="noopener">Vraag het via WhatsApp</a></span>
          </div>
        </div>

        <!-- 9. PAYMENT CHIPS -->
        <div class="cpays" style="margin-top:12px;justify-content:flex-start">
          <span class="paychip" style="--sd:0s">iDEAL</span>
          <span class="paychip" style="--sd:.4s">VISA</span>
          <span class="paychip" style="--sd:.8s">Mastercard</span>
          <span class="paychip" style="--sd:1.2s">Apple Pay</span>
          <span class="paychip" style="--sd:1.6s">Bancontact</span>
        </div>

        <!-- 10. 4 USP'S -->
        <div class="usp4">
          <div class="u4"><svg viewBox="0 0 24 24"><path d="M3 7h11v9H3z"/><path d="M14 10h4l3 3v3h-7z"/></svg>Snelle levering</div>
          <div class="u4"><svg viewBox="0 0 24 24"><path d="M9 14l-4-4 4-4"/><path d="M5 10h9a5 5 0 0 1 0 10H9"/></svg>14 dagen retour</div>
          <div class="u4"><svg viewBox="0 0 24 24"><rect x="5" y="11" width="14" height="9" rx="2"/><path d="M8 11V8a4 4 0 0 1 8 0v3"/></svg>Veilig betalen</div>
          <div class="u4"><svg viewBox="0 0 24 24"><path d="M21 12a9 9 0 1 1-4.2-7.6"/><path d="M12 7v5l3 2"/></svg>24/7 WhatsApp</div>
        </div>

        <!-- 11. DROPDOWNS -->
        <div class="acc-l">
          <div class="ai" id="ai-det">
            <button class="ai-b">Productdetails <svg viewBox="0 0 24 24"><path d="M6 9l6 6 6-6"/></svg></button>
            <div class="ai-c"><div class="inr">
              <?php
              $desc = $product->get_description() ?: $product->get_short_description();
              if ($desc) echo wp_kses_post($desc);
              else echo 'Geen extra productinformatie beschikbaar.';
              ?>
            </div></div>
          </div>
          <div class="ai" id="ai-maat">
            <button class="ai-b">Maatadvies <svg viewBox="0 0 24 24"><path d="M6 9l6 6 6-6"/></svg></button>
            <div class="ai-c"><div class="inr">
              <?php
              $advice = function_exists('get_field') ? (get_field('size_advice', $product->get_id()) ?: get_field('size_advice_default', 'option')) : '';
              if ($advice) echo wp_kses_post($advice);
              else echo 'Dit model valt op maat. Zit je tussen twee maten? Kies dan de grotere maat. Of stuur ons je gebruikelijke maat via WhatsApp, dan adviseren wij je persoonlijk.';
              ?>
            </div></div>
          </div>
          <div class="ai" id="ai-ship">
            <button class="ai-b">Verzending &amp; retourneren <svg viewBox="0 0 24 24"><path d="M6 9l6 6 6-6"/></svg></button>
            <div class="ai-c"><div class="inr">
              <b>Verzending.</b> Voor 17:00 besteld is doorgaans de volgende dag in huis, uiterlijk binnen 2 dagen. Gratis verzending vanaf &euro;200. Je ontvangt altijd een trackingcode.<br><br>
              <b>Retourneren.</b> Niet goed? Je hebt 14 dagen om te ruilen of te retourneren, zolang het product ongebruikt is en in de originele verpakking zit. Meld je retour aan via WhatsApp en je hoort binnen enkele minuten wat je moet doen.
            </div></div>
          </div>
        </div>

      </div><!-- /.pinf -->
    </div><!-- /.pdp -->
  </div><!-- /.wrap -->

  <!-- ══════ 12. UPSELL ══════ -->
  <?php if (!empty($upsell_products)) : ?>
    <section class="sec" style="padding-bottom:0">
      <div class="wrap">
        <div class="sec-t" data-rv="up">
          <div class="eyebrow"><i></i><span class="lbl">Compleet maken</span></div>
          <div class="sec-h">
            <h2 class="d3 split">Maak je outfit compleet</h2>
            <div class="rail-nav">
              <button class="rnav" data-rail="up" data-dir="-1" aria-label="Vorige"><svg viewBox="0 0 24 24"><path d="M15 5l-7 7 7 7"/></svg></button>
              <button class="rnav" data-rail="up" data-dir="1" aria-label="Volgende"><svg viewBox="0 0 24 24"><path d="M9 5l7 7-7 7"/></svg></button>
            </div>
          </div>
        </div>
        <div class="rail" id="railUp">
          <?php foreach ($upsell_products as $up) :
            $up_thumb = get_the_post_thumbnail_url($up->get_id(), 'product-card');
            $up_sale  = $up->is_on_sale();
            $up_pct   = ($up_sale && (float)$up->get_regular_price() > 0) ? round((1 - (float)$up->get_sale_price() / (float)$up->get_regular_price()) * 100) : 0;
          ?>
            <a class="pcard pcard-sm" href="<?php echo esc_url($up->get_permalink()); ?>" data-rv="up">
              <div class="pmedia">
                <img class="p1" src="<?php echo esc_url($up_thumb ?: wc_placeholder_img_src('product-card')); ?>" alt="<?php echo esc_attr($up->get_name()); ?>" loading="lazy">
                <span class="sheen"></span>
                <?php if ($up_pct) : ?><div class="pbadges"><span class="pb pb-sale">-<?php echo $up_pct; ?>%</span></div><?php endif; ?>
              </div>
              <div class="pinfo">
                <div class="ptitle"><?php echo esc_html($up->get_name()); ?></div>
                <div class="pprice<?php echo $up_sale ? ' sale' : ''; ?>">
                  <?php if ($up_sale) : ?>
                    <?php echo $eur((float)$up->get_sale_price()); ?> <s><?php echo $eur((float)$up->get_regular_price()); ?></s>
                  <?php else : ?>
                    <?php echo $eur((float)$up->get_price()); ?>
                  <?php endif; ?>
                </div>
              </div>
            </a>
          <?php endforeach; ?>
        </div>
      </div>
    </section>
  <?php endif; ?>

  <!-- ══════ 13. GEDRAGEN DOOR (coverflow) ══════ -->
  <?php get_template_part('template-parts/influencer-scroll'); ?>

  <!-- ══════ 14. REVIEWS ══════ -->
  <?php get_template_part('template-parts/reviews-grid'); ?>

  <!-- ══════ 15. MEER UIT DEZE COLLECTIE ══════ -->
  <?php if (!empty($same_cat)) : ?>
    <section class="sec" style="padding-top:0">
      <div class="wrap">
        <div class="sec-t" data-rv="up">
          <div class="eyebrow"><i></i><span class="lbl"><?php echo $primary_cat ? esc_html($primary_cat->name) : 'Collectie'; ?></span></div>
          <div class="sec-h">
            <h2 class="d3 split">Meer uit deze collectie</h2>
            <div style="display:flex;align-items:center;gap:16px">
              <div class="rail-nav">
                <button class="rnav" data-rail="sim" data-dir="-1" aria-label="Vorige"><svg viewBox="0 0 24 24"><path d="M15 5l-7 7 7 7"/></svg></button>
                <button class="rnav" data-rail="sim" data-dir="1" aria-label="Volgende"><svg viewBox="0 0 24 24"><path d="M9 5l7 7-7 7"/></svg></button>
              </div>
              <?php if ($primary_cat) : ?>
                <a href="<?php echo esc_url(dom_term_url($primary_cat)); ?>" class="lk">Alle <?php echo esc_html(strtolower($primary_cat->name)); ?> <span class="arw">&rarr;</span></a>
              <?php endif; ?>
            </div>
          </div>
        </div>
        <div class="rail" id="railSim">
          <?php foreach ($same_cat as $post_obj) :
            $rp = wc_get_product($post_obj->ID);
            if (!$rp || !$rp->is_visible()) continue;
            $rp_thumb = get_the_post_thumbnail_url($post_obj->ID, 'product-card');
            $rp_sale  = $rp->is_on_sale();
            $rp_pct   = ($rp_sale && (float)$rp->get_regular_price() > 0) ? round((1 - (float)$rp->get_sale_price() / (float)$rp->get_regular_price()) * 100) : 0;
          ?>
            <a class="pcard pcard-sm" href="<?php echo esc_url($rp->get_permalink()); ?>" data-rv="up">
              <div class="pmedia">
                <img class="p1" src="<?php echo esc_url($rp_thumb ?: wc_placeholder_img_src('product-card')); ?>" alt="<?php echo esc_attr($rp->get_name()); ?>" loading="lazy">
                <span class="sheen"></span>
                <?php if ($rp_pct) : ?><div class="pbadges"><span class="pb pb-sale">-<?php echo $rp_pct; ?>%</span></div><?php endif; ?>
              </div>
              <div class="pinfo">
                <div class="ptitle"><?php echo esc_html($rp->get_name()); ?></div>
                <div class="pprice<?php echo $rp_sale ? ' sale' : ''; ?>">
                  <?php if ($rp_sale) : ?>
                    <?php echo $eur((float)$rp->get_sale_price()); ?> <s><?php echo $eur((float)$rp->get_regular_price()); ?></s>
                  <?php else : ?>
                    <?php echo $eur((float)$rp->get_price()); ?>
                  <?php endif; ?>
                </div>
              </div>
            </a>
          <?php endforeach; wp_reset_postdata(); ?>
        </div>
      </div>
    </section>
  <?php endif; ?>

  <!-- ══════ 16. FAQ ══════ -->
  <section class="sec" style="background:var(--off);border-top:1px solid var(--line)">
    <div class="wrap">
      <div class="sec-t" data-rv="up">
        <div class="eyebrow"><i></i><span class="lbl">FAQ</span></div>
        <h2 class="d3 split">Veelgestelde vragen</h2>
      </div>
      <div class="faq" data-rv="up">
        <div>
          <div class="acc-l" style="border-top:1px solid var(--line)">
            <div class="ai"><button class="ai-b">Wanneer is mijn pakket in huis? <svg viewBox="0 0 24 24"><path d="M6 9l6 6 6-6"/></svg></button>
              <div class="ai-c"><div class="inr">Voor 17:00 besteld is doorgaans de volgende dag in huis, uiterlijk binnen 2 dagen. Je ontvangt altijd een trackingcode.</div></div></div>
            <div class="ai"><button class="ai-b">Hoe werkt retourneren? <svg viewBox="0 0 24 24"><path d="M6 9l6 6 6-6"/></svg></button>
              <div class="ai-c"><div class="inr">Je hebt 14 dagen om te ruilen of retourneren, zolang het product ongebruikt en in de originele verpakking is. Meld je retour aan via WhatsApp.</div></div></div>
            <div class="ai"><button class="ai-b">Hoe kies ik de juiste maat? <svg viewBox="0 0 24 24"><path d="M6 9l6 6 6-6"/></svg></button>
              <div class="ai-c"><div class="inr">Dit model valt op maat. Zit je tussen twee maten? Kies de grotere maat. Of stuur ons je gebruikelijke maat via WhatsApp, dan adviseren wij je persoonlijk.</div></div></div>
          </div>
        </div>
        <div>
          <div class="acc-l" style="border-top:1px solid var(--line)">
            <div class="ai"><button class="ai-b">Met welke methodes kan ik betalen? <svg viewBox="0 0 24 24"><path d="M6 9l6 6 6-6"/></svg></button>
              <div class="ai-c"><div class="inr">iDEAL, Bancontact, creditcard en Apple Pay. Alle betalingen lopen via een beveiligde omgeving.</div></div></div>
            <div class="ai"><button class="ai-b">Krijg ik alle toebehoren mee? <svg viewBox="0 0 24 24"><path d="M6 9l6 6 6-6"/></svg></button>
              <div class="ai-c"><div class="inr">Ja. Elk paar komt met alle toebehoren en een extra set veters. Wij controleren elk product voor verzending.</div></div></div>
            <div class="ai"><button class="ai-b">Hoe bereik ik de klantenservice? <svg viewBox="0 0 24 24"><path d="M6 9l6 6 6-6"/></svg></button>
              <div class="ai-c"><div class="inr">24/7 via WhatsApp op 06-16264544. Gemiddelde reactietijd is onder 5 minuten.</div></div></div>
          </div>
        </div>
      </div>
    </div>
  </section>
</main>

<!-- LIGHTBOX -->
<div class="lb" id="lb">
  <button class="lb-x" id="lbX" aria-label="Sluiten"><svg viewBox="0 0 24 24"><path d="M6 6l12 12M18 6L6 18"/></svg></button>
  <img id="lbImg" src="" alt="">
</div>

<?php endwhile; ?>

<?php get_footer(); ?>
