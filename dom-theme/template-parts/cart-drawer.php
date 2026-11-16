<?php
defined('ABSPATH') || exit;

$cart_count = 0;
$cart_total = 0;
$cart_items = [];
$free_shipping_threshold = 200;
$mystery_shirt_threshold = 350;

if (function_exists('WC') && WC()->cart) {
    $cart_count = WC()->cart->get_cart_contents_count();
    $cart_total = (float) WC()->cart->get_subtotal();
    $cart_items = WC()->cart->get_cart();
}

$progress_pct = min(100, ($cart_total / $free_shipping_threshold) * 100);
$shipping_unlocked = $cart_total >= $free_shipping_threshold;
$mystery_unlocked = $cart_total >= $mystery_shirt_threshold;
$remaining = max(0, $free_shipping_threshold - $cart_total);
?>

<div class="cart-overlay" id="cartOverlay"></div>

<div class="cart-drawer dom-cart-drawer" id="cartDrawer" aria-label="<?php esc_attr_e('Winkelwagen', 'dom-theme'); ?>" role="dialog">
    <!-- Header -->
    <div class="flex items-center justify-between px-5 py-4 border-b border-border">
        <h2 class="text-sm font-extrabold uppercase tracking-label">Winkelwagen (<?php echo $cart_count; ?>)</h2>
        <button class="dom-cart-close w-8 h-8 flex items-center justify-center rounded-full border border-border hover:border-accent transition-colors text-base" aria-label="Sluiten">&times;</button>
    </div>

    <!-- Progress bar -->
    <div class="px-5 py-3.5 bg-bg-off">
        <div class="text-xs font-bold mb-2 <?php echo $shipping_unlocked ? 'text-success' : 'silver-text'; ?>">
            <?php if ($shipping_unlocked) : ?>
                &#10003; Gratis verzending!
            <?php else : ?>
                Nog &euro;<?php echo number_format($remaining, 0, ',', '.'); ?> voor gratis verzending
            <?php endif; ?>
        </div>
        <div class="h-0.5 bg-border rounded-full overflow-hidden mb-1.5">
            <div class="h-full silver-gradient rounded-full transition-all duration-500" style="width:<?php echo $progress_pct; ?>%"></div>
        </div>
        <div class="flex justify-between text-[10px] text-muted">
            <span class="<?php echo $shipping_unlocked ? 'text-success font-semibold' : ''; ?>">&euro;200 gratis verzending</span>
            <span class="<?php echo $mystery_unlocked ? 'text-success font-semibold' : ''; ?>">&euro;350 mystery shirt</span>
        </div>
    </div>

    <!-- Items -->
    <div class="flex-1 overflow-y-auto px-5 py-3">
        <?php if (!empty($cart_items)) :
            foreach ($cart_items as $cart_item_key => $cart_item) :
                $product = $cart_item['data'];
                $product_id = $cart_item['product_id'];
                $quantity = $cart_item['quantity'];
                $thumbnail = get_the_post_thumbnail_url($product_id, 'thumbnail');
                $name = $product->get_name();
                $price = $product->get_price();
                $variation = '';
                if (!empty($cart_item['variation'])) {
                    $parts = [];
                    foreach ($cart_item['variation'] as $attr => $val) {
                        $parts[] = ucfirst(str_replace('attribute_pa_', '', $attr)) . ': ' . $val;
                    }
                    $variation = implode(' · ', $parts);
                }
            ?>
                <div class="flex gap-3 py-3 border-b border-border" data-cart-item="<?php echo esc_attr($cart_item_key); ?>">
                    <div class="w-[72px] h-[90px] bg-surface border border-border rounded-md shrink-0 overflow-hidden">
                        <?php if ($thumbnail) : ?>
                            <img src="<?php echo esc_url($thumbnail); ?>" alt="<?php echo esc_attr($name); ?>" class="w-full h-full object-cover">
                        <?php endif; ?>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="text-sm font-bold truncate"><?php echo esc_html($name); ?></div>
                        <?php if ($variation) : ?>
                            <div class="text-xs text-muted mt-0.5"><?php echo esc_html($variation); ?></div>
                        <?php endif; ?>
                        <div class="flex justify-between items-center mt-2">
                            <span class="font-extrabold text-sm tabular-nums">&euro;<?php echo number_format($price, 2, ',', '.'); ?></span>
                            <div class="flex border border-border rounded-md">
                                <button class="dom-qty-btn w-8 h-8 flex items-center justify-center text-sm" data-action="decrease" data-key="<?php echo esc_attr($cart_item_key); ?>">&minus;</button>
                                <span class="w-7 text-center text-xs font-bold leading-8"><?php echo $quantity; ?></span>
                                <button class="dom-qty-btn w-8 h-8 flex items-center justify-center text-sm" data-action="increase" data-key="<?php echo esc_attr($cart_item_key); ?>">+</button>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach;
        else : ?>
            <div class="flex flex-col items-center justify-center py-12 text-center">
                <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="text-muted mb-3"><path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 01-8 0"/></svg>
                <span class="text-sm text-muted">Je winkelwagen is leeg</span>
            </div>
        <?php endif; ?>
    </div>

    <?php if ($cart_count > 0) : ?>
        <!-- Footer -->
        <div class="border-t border-border px-5 py-3.5">
            <div class="flex justify-between items-center mb-3">
                <span class="text-sm text-muted">Subtotaal</span>
                <span class="text-2xl font-black tabular-nums">&euro;<?php echo number_format($cart_total, 2, ',', '.'); ?></span>
            </div>
            <a href="<?php echo esc_url(wc_get_checkout_url()); ?>" class="block w-full py-4 silver-gradient text-fg text-center text-sm font-extrabold uppercase tracking-label rounded-md hover:shadow-lg transition-shadow">
                Afrekenen
            </a>
            <div class="flex justify-center gap-4 mt-2.5 text-2xs text-muted">
                <span>&#128274; Veilig</span>
                <span>&#9889; Snel</span>
                <span>&#8634; 14d retour</span>
            </div>
        </div>
    <?php endif; ?>
</div>
