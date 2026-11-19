<?php
defined('ABSPATH') || exit;

$cart_count = 0;
$cart_total = 0;
$cart_items = [];
$saved = 0;
// Beheer: De Outfitman > Winkelwagen & checkout.
$FREE = (float) dom_c('winkelwagen.drempels.verzending', 220);
$GIFT = (float) dom_c('winkelwagen.drempels.cadeau', 350);
$FREE_LBL = dom_c('winkelwagen.drempels.verzending_label', 'Gratis verzending');
$GIFT_LBL = dom_c('winkelwagen.drempels.cadeau_label', 'Gratis mystery shirt');

if (function_exists('WC') && WC()->cart) {
    $cart_count = WC()->cart->get_cart_contents_count();
    $cart_total = (float) WC()->cart->get_subtotal();
    $cart_items = WC()->cart->get_cart();
    foreach ($cart_items as $item) {
        $regelprijs = dom_prices($item['data']);
        if ($regelprijs['sale']) {
            $saved += ($regelprijs['van'] - $regelprijs['nu']) * $item['quantity'];
        }
    }
}

$bar_pct   = $GIFT > 0 ? min(100, ($cart_total / $GIFT) * 100) : 0;
// De eerste mijlpaal staat op de plek die bij de ingestelde bedragen hoort.
$mile_pct  = $GIFT > 0 ? min(100, ($FREE / $GIFT) * 100) : 0;
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
    <b><?php echo esc_html(dom_c('winkelwagen.teksten.titel', 'Winkelwagen')); ?> (<span id="cartN2"><?php echo $cart_count; ?></span>)</b>
    <button class="ic js-cart-close" aria-label="Sluiten">
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 6l12 12M18 6L6 18"/></svg>
    </button>
  </div>

  <!-- Shipping ladder -->
  <div class="ship">
    <p id="shipMsg">
      <?php if ($hit350) : ?>
        <?php echo esc_html($FREE_LBL); ?> <b>+</b> <?php echo esc_html(mb_strtolower($GIFT_LBL)); ?> gehaald
      <?php elseif ($hit200) : ?>
        Nog <b><?php echo $eur($rem_gift); ?></b> tot je <?php echo esc_html(mb_strtolower($GIFT_LBL)); ?>
      <?php else : ?>
        Nog <b><?php echo $eur($rem_free); ?></b> tot <?php echo esc_html(mb_strtolower($FREE_LBL)); ?>
      <?php endif; ?>
    </p>
    <div class="ladder">
      <i id="shipBar" style="width:<?php echo $bar_pct; ?>%"></i>
      <?php
      // Eigen iconen per mijlpaal: een bezorgwagen voor de verzending en een
      // shirt voor het cadeau. Twee keer hetzelfde vinkje zei niets over wat
      // je bij dat bedrag krijgt.
      $ic_truck = '<svg viewBox="0 0 24 24"><path d="M3 7h11v9H3z"/><path d="M14 10h4l3 3v3h-7z"/><circle cx="7" cy="18" r="1.7"/><circle cx="17" cy="18" r="1.7"/></svg>';
      $ic_shirt = '<svg viewBox="0 0 24 24"><path d="M8.5 3.5 12 5.6l3.5-2.1 5 2.4-2 4.1-2-.8V20H7.5V9.2l-2 .8-2-4.1z"/></svg>';
      ?>
      <span class="mile<?php echo $hit200 ? ' hit' : ''; ?>" id="m200" style="left:<?php echo esc_attr(round($mile_pct, 2)); ?>%">
        <span class="dot"><?php echo $ic_truck; // phpcs:ignore ?></span>
      </span>
      <span class="mile<?php echo $hit350 ? ' hit' : ''; ?>" id="m350" style="left:100%">
        <span class="dot"><?php echo $ic_shirt; // phpcs:ignore ?></span>
      </span>
    </div>
    <div class="mile-lbls">
      <span>&euro;0</span>
      <span id="t200"<?php echo $hit200 ? ' class="hit"' : ''; ?>>&euro;<?php echo esc_html(number_format_i18n($FREE)); ?> verzending</span>
      <span id="t350"<?php echo $hit350 ? ' class="hit"' : ''; ?>>&euro;<?php echo esc_html(number_format_i18n($GIFT)); ?> cadeau</span>
    </div>
    <div class="gift<?php echo $hit350 ? ' on' : ''; ?>" id="gift">
      <span class="gi"><?php echo $ic_shirt; // phpcs:ignore ?></span>
      <div>
        <b><?php echo esc_html($GIFT_LBL); ?></b>
        <?php if ($hit350) : ?>
          <span class="sub"><?php esc_html_e('Toegevoegd aan je bestelling', 'dom-theme'); ?></span>
        <?php endif; ?>
      </div>
      <span class="st" id="giftSt">
        <?php if ($hit350) : ?>
          <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 12l5 5L20 6"/></svg>
          <?php esc_html_e('Gratis', 'dom-theme'); ?>
        <?php else : ?>
          <?php echo 'Nog ' . $eur($rem_gift); ?>
        <?php endif; ?>
      </span>
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
          $regelprijs = dom_prices($product);
          $price      = $regelprijs['nu'];
          $was        = $regelprijs['van'];
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
            <?php
            // Het cadeau komt er vanzelf bij en gaat er vanzelf weer af; een
            // aantalkiezer en een verwijderknop zouden alleen verwarren.
            $is_cadeau = function_exists('dom_is_cadeau_regel') && dom_is_cadeau_regel($cart_item);
            ?>
            <?php if ($is_cadeau) : ?>
              <div class="cadeau-regel"><?php esc_html_e('Cadeau van ons', 'dom-theme'); ?></div>
            <?php else : ?>
              <div class="qty">
                <button data-a="-" data-key="<?php echo esc_attr($cart_item_key); ?>" aria-label="Minder">&minus;</button>
                <span><?php echo $quantity; ?></span>
                <button data-a="+" data-key="<?php echo esc_attr($cart_item_key); ?>" aria-label="Meer">+</button>
              </div>
            <?php endif; ?>
          </div>
          <div>
            <div class="p"><?php echo $eur($line_total); ?><?php if ($line_was > 0) : ?><s><?php echo $eur($line_was); ?></s><?php endif; ?></div>
            <?php if (!$is_cadeau) : ?>
              <button class="rm" data-rm="<?php echo esc_attr($cart_item_key); ?>">Verwijder</button>
            <?php endif; ?>
          </div>
        </div>
      <?php endforeach;
      else : ?>
        <div style="padding:40px 0;text-align:center">
          <p class="cap" style="margin-bottom:16px"><?php echo esc_html(dom_c('winkelwagen.teksten.leeg', 'Je winkelwagen is nog leeg.')); ?></p>
          <a href="<?php echo esc_url(dom_shop_url()); ?>" class="btn btn-chrome js-cart-close" style="display:inline-flex">
            <span>Bekijk shop</span>
            <span class="arw">&rarr;</span>
          </a>
        </div>
      <?php endif; ?>
    </div>

  </div>

  <!-- Footer -->
  <?php if ($cart_count > 0) : ?>
  <div class="drw-f">
    <?php
    // Het totaal stond links onder de lijst met de besparing eronder, waardoor
    // niet duidelijk was wat nu het bedrag is. Label links, bedrag rechts.
    ?>
    <div class="totaal">
      <span class="lb"><?php esc_html_e('Totaal', 'dom-theme'); ?></span>
      <span class="vv" id="cartTotal"><?php echo $eur($cart_total); ?></span>
      <?php if ($saved > 0) : ?>
        <span class="bespaard"><?php printf(esc_html__('Je bespaart %s', 'dom-theme'), $eur($saved)); ?></span>
      <?php endif; ?>
      <span class="btw"><?php esc_html_e('incl. btw', 'dom-theme'); ?></span>
    </div>
    <a href="<?php echo esc_url(function_exists('wc_get_checkout_url') ? wc_get_checkout_url() : dom_shop_url()); ?>" class="btn btn-chrome">
      <span><?php echo esc_html(dom_c('winkelwagen.teksten.knop', 'Afrekenen')); ?></span>
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
      <span><?php echo esc_html(dom_c('winkelwagen.teksten.trust', '1.200+ tevreden klanten')); ?></span>
    </div>
  </div>
  <?php endif; ?>

</aside>
