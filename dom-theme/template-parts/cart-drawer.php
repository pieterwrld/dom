<?php
defined('ABSPATH') || exit;

$cart_count = 0;
$cart_total = 0;
$cart_items = [];
$saved = 0;
$FREE = 200;
$GIFT = 350;

if (function_exists('WC') && WC()->cart) {
    $cart_count = WC()->cart->get_cart_contents_count();
    $cart_total = (float) WC()->cart->get_subtotal();
    $cart_items = WC()->cart->get_cart();
    foreach ($cart_items as $item) {
        $product = $item['data'];
        if ($product->is_on_sale()) {
            $saved += ((float) $product->get_regular_price() - (float) $product->get_price()) * $item['quantity'];
        }
    }
}

$bar_pct   = min(100, ($cart_total / $GIFT) * 100);
$hit200    = $cart_total >= $FREE;
$hit350    = $cart_total >= $GIFT;
$rem_free  = max(0, $FREE - $cart_total);
$rem_gift  = max(0, $GIFT - $cart_total);

$eur = function ($v) {
    return '€' . number_format($v, 2, ',', '.');
};
?>

<aside class="drw" id="cart" aria-label="<?php esc_attr_e('Winkelwagen', 'dom-theme'); ?>">

  <!-- Header -->
  <div class="drw-h">
    <b>Winkelwagen (<span id="cartN2"><?php echo $cart_count; ?></span>)</b>
    <button class="ic js-cart-close" aria-label="Sluiten">
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 6l12 12M18 6L6 18"/></svg>
    </button>
  </div>

  <!-- Shipping ladder -->
  <div class="ship">
    <p id="shipMsg">
      <?php if ($hit350) : ?>
        Gratis verzending <b>+</b> mystery shirt unlocked
      <?php elseif ($hit200) : ?>
        Nog <b><?php echo $eur($rem_gift); ?></b> tot je gratis mystery shirt
      <?php else : ?>
        Nog <b><?php echo $eur($rem_free); ?></b> tot gratis verzending
      <?php endif; ?>
    </p>
    <div class="ladder">
      <i id="shipBar" style="width:<?php echo $bar_pct; ?>%"></i>
      <span class="mile<?php echo $hit200 ? ' hit' : ''; ?>" id="m200" style="left:57.14%">
        <span class="dot"><svg viewBox="0 0 24 24"><path d="M4 12l5 5L20 6"/></svg></span>
      </span>
      <span class="mile<?php echo $hit350 ? ' hit' : ''; ?>" id="m350" style="left:100%">
        <span class="dot"><svg viewBox="0 0 24 24"><path d="M4 12l5 5L20 6"/></svg></span>
      </span>
    </div>
    <div class="mile-lbls">
      <span>&euro;0</span>
      <span id="t200"<?php echo $hit200 ? ' class="hit"' : ''; ?>>&euro;200 verzending</span>
      <span id="t350"<?php echo $hit350 ? ' class="hit"' : ''; ?>>&euro;350 mystery shirt</span>
    </div>
    <div class="gift<?php echo $hit350 ? ' on' : ''; ?>" id="gift">
      <span class="gi">
        <svg viewBox="0 0 24 24"><path d="M20 12v9H4v-9"/><path d="M2 7h20v5H2z"/><path d="M12 22V7"/><path d="M12 7S9 2 6.5 4.5 12 7 12 7zM12 7s3-5 5.5-2.5S12 7 12 7z"/></svg>
      </span>
      <div><b>Gratis mystery shirt</b></div>
      <span class="st" id="giftSt"><?php echo $hit350 ? 'Unlocked' : 'Nog ' . $eur($rem_gift); ?></span>
    </div>
  </div>

  <!-- Body -->
  <div class="drw-b">
    <div class="drw-pad" id="cartItems">
      <?php if (!empty($cart_items)) :
        foreach ($cart_items as $cart_item_key => $cart_item) :
          $product    = $cart_item['data'];
          $product_id = $cart_item['product_id'];
          $quantity   = $cart_item['quantity'];
          $price      = (float) $product->get_price();
          $was        = $product->is_on_sale() ? (float) $product->get_regular_price() : 0;
          $line_total = $price * $quantity;
          $line_was   = $was > 0 ? $was * $quantity : 0;
          $thumbnail  = get_the_post_thumbnail_url($product_id, 'thumbnail');
          $name       = $product->get_name();

          $meta = '';
          if (!empty($cart_item['variation'])) {
              $parts = [];
              foreach ($cart_item['variation'] as $attr => $val) {
                  $label = wc_attribute_label(str_replace('attribute_', '', $attr));
                  // Maten staan in WooCommerce vaak in kleine letters opgeslagen.
                  $parts[] = $label . ' ' . mb_strtoupper($val, 'UTF-8');
              }
              $meta = implode(' · ', $parts);
          }
      ?>
        <div class="ci" data-key="<?php echo esc_attr($cart_item_key); ?>">
          <a href="<?php echo esc_url(get_permalink($product_id)); ?>" class="thumb">
            <?php if ($thumbnail) : ?>
              <img src="<?php echo esc_url($thumbnail); ?>" alt="<?php echo esc_attr($name); ?>" loading="lazy">
            <?php endif; ?>
          </a>
          <div>
            <div class="t"><?php echo esc_html($name); ?></div>
            <?php if ($meta) : ?>
              <div class="m"><?php echo esc_html($meta); ?></div>
            <?php endif; ?>
            <div class="qty">
              <button data-a="-" data-key="<?php echo esc_attr($cart_item_key); ?>" aria-label="Minder">&minus;</button>
              <span><?php echo $quantity; ?></span>
              <button data-a="+" data-key="<?php echo esc_attr($cart_item_key); ?>" aria-label="Meer">+</button>
            </div>
          </div>
          <div>
            <div class="p"><?php echo $eur($line_total); ?><?php if ($line_was > 0) : ?><s><?php echo $eur($line_was); ?></s><?php endif; ?></div>
            <button class="rm" data-rm="<?php echo esc_attr($cart_item_key); ?>">Verwijder</button>
          </div>
        </div>
      <?php endforeach;
      else : ?>
        <div style="padding:40px 0;text-align:center">
          <p class="cap" style="margin-bottom:16px">Je winkelwagen is nog leeg.</p>
          <a href="<?php echo esc_url(dom_shop_url()); ?>" class="btn btn-chrome js-cart-close" style="display:inline-flex">
            <span>Bekijk shop</span>
            <span class="arw">&rarr;</span>
          </a>
        </div>
      <?php endif; ?>
    </div>

    <!-- Tabs -->
    <div class="ctabs">
      <div class="ctabs-h">
        <button class="ctab on" data-p="up">Maak het compleet</button>
        <button class="ctab" data-p="rec">Aanbevolen voor jou</button>
        <button class="ctab" data-p="seen">Recent bekeken</button>
      </div>
      <?php
      /**
       * Drie panelen met dezelfde kaart. "Aanbevolen voor jou" en "Recent
       * bekeken" waren lege divs, waardoor die tabbladen niets leken te doen.
       */
      $in_cart = array_map(static function ($ci) { return (int) $ci['product_id']; }, $cart_items);

      // Maak het compleet: zelfde categorieën als wat er in de wagen zit.
      $cat_ids = [];
      foreach ($cart_items as $ci) {
          $terms = get_the_terms($ci['product_id'], 'product_cat');
          if ($terms && !is_wp_error($terms)) {
              foreach ($terms as $term) {
                  $cat_ids[] = (int) $term->term_id;
              }
          }
      }

      $compleet = [];
      if ($cat_ids) {
          $compleet = get_posts([
              'post_type'      => 'product',
              'post_status'    => 'publish',
              'posts_per_page' => 8,
              'post__not_in'   => $in_cart ?: [0],
              'orderby'        => 'rand',
              'fields'         => 'ids',
              'tax_query'      => [[
                  'taxonomy' => 'product_cat',
                  'field'    => 'term_id',
                  'terms'    => array_unique($cat_ids),
              ]],
          ]);
      }

      // Aanbevolen: best verkocht in de hele winkel.
      $aanbevolen = get_posts([
          'post_type'      => 'product',
          'post_status'    => 'publish',
          'posts_per_page' => 8,
          'post__not_in'   => array_merge($in_cart, $compleet) ?: [0],
          'meta_key'       => 'total_sales',
          'orderby'        => 'meta_value_num',
          'order'          => 'DESC',
          'fields'         => 'ids',
      ]);

      // Recent bekeken: WooCommerce houdt dit in een cookie bij.
      $recent = [];
      if (!empty($_COOKIE['woocommerce_recently_viewed'])) {
          $recent = array_filter(array_map('absint', explode('|', wp_unslash($_COOKIE['woocommerce_recently_viewed']))));
          $recent = array_slice(array_reverse(array_unique($recent)), 0, 8);
      }

      $panelen = [
          'up'   => $compleet,
          'rec'  => $aanbevolen,
          'seen' => $recent,
      ];
      $leeg_tekst = [
          'up'   => __('Nog niets om bij te combineren.', 'dom-theme'),
          'rec'  => __('Nog geen aanbevelingen.', 'dom-theme'),
          'seen' => __('Je hebt nog geen producten bekeken.', 'dom-theme'),
      ];

      foreach ($panelen as $naam => $ids) :
      ?>
        <div class="cpane<?php echo 'up' === $naam ? ' on' : ''; ?>" id="pane-<?php echo esc_attr($naam); ?>">
          <?php if ($ids) : ?>
            <div class="crail">
              <?php foreach ($ids as $pid) {
                  get_template_part('template-parts/cart-mini', 'card', ['id' => $pid]);
              } ?>
            </div>
          <?php else : ?>
            <p class="cpane-leeg"><?php echo esc_html($leeg_tekst[$naam]); ?></p>
          <?php endif; ?>
        </div>
      <?php endforeach; ?>
    </div>
  </div>

  <!-- Footer -->
  <?php if ($cart_count > 0) : ?>
  <div class="drw-f">
    <div class="row">
      <span class="lb">Subtotaal</span>
      <span class="vv" id="cartTotal"><?php echo $eur($cart_total); ?><?php if ($saved > 0) : ?><small>Je bespaart <?php echo $eur($saved); ?></small><?php endif; ?></span>
    </div>
    <a href="<?php echo esc_url(function_exists('wc_get_checkout_url') ? wc_get_checkout_url() : dom_shop_url()); ?>" class="btn btn-chrome">
      <span>Afrekenen</span>
      <span class="arw">&rarr;</span>
    </a>
    <div class="ctrust">
      <div class="stars">
        <svg viewBox="0 0 24 24" fill="#F59E0B" width="10" height="10"><path d="M12 2l3 7h7l-5.5 4.3L18.5 21 12 16.8 5.5 21l2-7.7L2 9h7z"/></svg>
        <svg viewBox="0 0 24 24" fill="#F59E0B" width="10" height="10"><path d="M12 2l3 7h7l-5.5 4.3L18.5 21 12 16.8 5.5 21l2-7.7L2 9h7z"/></svg>
        <svg viewBox="0 0 24 24" fill="#F59E0B" width="10" height="10"><path d="M12 2l3 7h7l-5.5 4.3L18.5 21 12 16.8 5.5 21l2-7.7L2 9h7z"/></svg>
        <svg viewBox="0 0 24 24" fill="#F59E0B" width="10" height="10"><path d="M12 2l3 7h7l-5.5 4.3L18.5 21 12 16.8 5.5 21l2-7.7L2 9h7z"/></svg>
        <svg viewBox="0 0 24 24" fill="#F59E0B" width="10" height="10"><path d="M12 2l3 7h7l-5.5 4.3L18.5 21 12 16.8 5.5 21l2-7.7L2 9h7z"/></svg>
      </div>
      <span><b>1.200+</b> tevreden klanten</span>
    </div>
  </div>
  <?php endif; ?>

</aside>
