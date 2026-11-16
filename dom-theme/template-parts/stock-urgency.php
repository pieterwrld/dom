<?php
defined('ABSPATH') || exit;

$stock_qty = $args['stock_qty'] ?? null;
$stock_status = $args['stock_status'] ?? 'instock';

if ($stock_status !== 'instock' || $stock_qty === null || $stock_qty > 6) return;

$message = '';
if ($stock_qty <= 1) {
    $message = 'Laatste op voorraad — wees er snel bij!';
} elseif ($stock_qty <= 2) {
    $message = 'Nog ' . $stock_qty . ' op voorraad — twijfel niet!';
} else {
    $message = 'Bijna uitverkocht';
}
?>
<div class="flex items-center gap-2 px-3.5 py-2.5 my-3 bg-urgency/5 border border-urgency/15 rounded-md text-sm font-semibold text-urgency">
    <span class="w-2 h-2 rounded-full bg-urgency animate-pulse shrink-0"></span>
    <?php echo esc_html($message); ?>
</div>
