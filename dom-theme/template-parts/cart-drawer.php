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
$mystery_pct = min(100, ($cart_total / $mystery_shirt_threshold) * 100);
$shipping_unlocked = $cart_total >= $free_shipping_threshold;
$mystery_unlocked = $cart_total >= $mystery_shirt_threshold;
$remaining = max(0, $free_shipping_threshold - $cart_total);
$mystery_remaining = max(0, $mystery_shirt_threshold - $cart_total);
?>

<div class="cart-overlay" id="cartOverlay"></div>

<div class="cart-drawer dom-cart-drawer" id="cartDrawer" aria-label="<?php esc_attr_e('Winkelwagen', 'dom-theme'); ?>" role="dialog">
    <!-- Header -->
    <div class="flex items-center justify-between px-5 py-4 border-b border-border">
        <div class="flex items-center gap-2.5">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 01-8 0"/></svg>
            <h2 class="text-sm font-extrabold uppercase tracking-label">Winkelwagen</h2>
            <?php if ($cart_count > 0) : ?>
                <span class="w-5 h-5 silver-gradient text-fg text-[10px] font-bold rounded-full flex items-center justify-center"><?php echo $cart_count; ?></span>
            <?php endif; ?>
        </div>
        <button class="dom-cart-close w-9 h-9 flex items-center justify-center rounded-full hover:bg-surface transition-colors" aria-label="Sluiten">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
        </button>
    </div>

    <!-- Progress milestones -->
    <div class="px-5 py-4 bg-bg-off">
        <div class="relative">
            <!-- Track -->
            <div class="h-1.5 bg-border/50 rounded-full overflow-hidden">
                <div class="h-full silver-gradient rounded-full transition-all duration-700 ease-out" style="width:<?php echo $mystery_pct; ?>%"></div>
            </div>
            <!-- Milestone markers -->
            <div class="relative mt-3 flex justify-between text-[10px]">
                <div class="flex flex-col items-start gap-1">
                    <?php if ($shipping_unlocked) : ?>
                        <span class="flex items-center gap-1 text-success font-bold">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round"><polyline points="20,6 9,17 4,12"/></svg>
                            Gratis verzending
                        </span>
                    <?php else : ?>
                        <span class="text-muted font-medium">Nog &euro;<?php echo number_format($remaining, 0, ',', '.'); ?></span>
                        <span class="text-muted/60">Gratis verzending</span>
                    <?php endif; ?>
                </div>
                <div class="flex flex-col items-end gap-1">
                    <?php if ($mystery_unlocked) : ?>
                        <span class="flex items-center gap-1 text-success font-bold">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round"><polyline points="20,6 9,17 4,12"/></svg>
                            Mystery shirt!
                        </span>
                    <?php else : ?>
                        <span class="text-muted font-medium">Nog &euro;<?php echo number_format($mystery_remaining, 0, ',', '.'); ?></span>
                        <span class="text-muted/60">Gratis mystery shirt</span>
                    <?php endif; ?>
                </div>
            </div>
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
                $line_total = $price * $quantity;
                $variation = '';
                if (!empty($cart_item['variation'])) {
                    $parts = [];
                    foreach ($cart_item['variation'] as $attr => $val) {
                        $parts[] = ucfirst(str_replace('attribute_pa_', '', $attr)) . ': ' . $val;
                    }
                    $variation = implode(' / ', $parts);
                }
            ?>
                <div class="flex gap-3.5 py-4 border-b border-border last:border-0" data-cart-item="<?php echo esc_attr($cart_item_key); ?>">
                    <a href="<?php echo esc_url(get_permalink($product_id)); ?>" class="w-[76px] h-[95px] bg-surface rounded-lg shrink-0 overflow-hidden">
                        <?php if ($thumbnail) : ?>
                            <img src="<?php echo esc_url($thumbnail); ?>" alt="<?php echo esc_attr($name); ?>" class="w-full h-full object-cover">
                        <?php endif; ?>
                    </a>
                    <div class="flex-1 min-w-0">
                        <div class="flex justify-between items-start gap-2">
                            <div class="min-w-0">
                                <a href="<?php echo esc_url(get_permalink($product_id)); ?>" class="text-sm font-bold truncate block hover:text-muted transition-colors"><?php echo esc_html($name); ?></a>
                                <?php if ($variation) : ?>
                                    <div class="text-xs text-muted mt-0.5"><?php echo esc_html($variation); ?></div>
                                <?php endif; ?>
                            </div>
                            <button class="dom-remove-item shrink-0 w-6 h-6 flex items-center justify-center text-muted hover:text-urgency transition-colors" data-key="<?php echo esc_attr($cart_item_key); ?>" aria-label="Verwijderen">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                            </button>
                        </div>
                        <div class="flex justify-between items-center mt-3">
                            <div class="inline-flex items-center border border-border rounded-lg overflow-hidden">
                                <button class="dom-qty-btn w-8 h-8 flex items-center justify-center text-sm hover:bg-surface transition-colors" data-action="decrease" data-key="<?php echo esc_attr($cart_item_key); ?>">&minus;</button>
                                <span class="w-8 text-center text-xs font-bold"><?php echo $quantity; ?></span>
                                <button class="dom-qty-btn w-8 h-8 flex items-center justify-center text-sm hover:bg-surface transition-colors" data-action="increase" data-key="<?php echo esc_attr($cart_item_key); ?>">+</button>
                            </div>
                            <span class="font-extrabold text-sm tabular-nums">&euro;<?php echo number_format($line_total, 2, ',', '.'); ?></span>
                        </div>
                    </div>
                </div>
            <?php endforeach;
        else : ?>
            <div class="flex flex-col items-center justify-center py-16 text-center">
                <div class="w-16 h-16 rounded-full bg-surface flex items-center justify-center mb-4">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="text-muted"><path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 01-8 0"/></svg>
                </div>
                <span class="text-sm font-semibold mb-1">Je winkelwagen is leeg</span>
                <span class="text-xs text-muted">Voeg items toe om te beginnen</span>
                <a href="<?php echo esc_url(get_permalink(wc_get_page_id('shop'))); ?>" class="dom-cart-close mt-4 px-5 py-2.5 bg-fg text-white text-xs font-bold uppercase tracking-label rounded-lg hover:bg-fg/90 transition-colors">
                    Bekijk shop
                </a>
            </div>
        <?php endif; ?>
    </div>

    <?php if ($cart_count > 0) : ?>
        <!-- Footer -->
        <div class="border-t border-border px-5 py-4 bg-white">
            <div class="flex justify-between items-center mb-4">
                <span class="text-sm text-muted">Subtotaal</span>
                <span class="text-2xl font-black tabular-nums">&euro;<?php echo number_format($cart_total, 2, ',', '.'); ?></span>
            </div>
            <a href="<?php echo esc_url(wc_get_checkout_url()); ?>" class="block w-full py-4 bg-fg text-white text-center text-sm font-extrabold uppercase tracking-label rounded-lg hover:bg-fg/90 hover:shadow-lg transition-all active:scale-[0.98]">
                Afrekenen
            </a>
            <div class="flex items-center justify-center gap-5 mt-3 text-[10px] text-muted">
                <span class="flex items-center gap-1">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                    Veilig betalen
                </span>
                <span class="flex items-center gap-1">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><rect x="1" y="3" width="15" height="13"/><polygon points="16,8 20,8 23,11 23,16 16,16"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg>
                    Snelle levering
                </span>
                <span class="flex items-center gap-1">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><polyline points="1,4 1,10 7,10"/><path d="M3.51 15a9 9 0 1 0 2.13-9.36L1 10"/></svg>
                    14d retour
                </span>
            </div>
        </div>
    <?php endif; ?>
</div>
