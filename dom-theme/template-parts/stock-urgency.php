<?php
defined('ABSPATH') || exit;

$stock_qty = $args['stock_qty'] ?? null;
$stock_status = $args['stock_status'] ?? 'instock';

if ($stock_status !== 'instock' || $stock_qty === null || $stock_qty > 6) return;

$message = '';
$intensity = '';
if ($stock_qty <= 1) {
    $message = 'Laatste op voorraad — wees er snel bij!';
    $intensity = 'high';
} elseif ($stock_qty <= 2) {
    $message = 'Nog ' . $stock_qty . ' op voorraad — twijfel niet!';
    $intensity = 'medium';
} else {
    $message = 'Bijna uitverkocht';
    $intensity = 'low';
}
?>
<div class="flex items-center gap-2.5 px-4 py-3 my-3 rounded-lg text-sm font-semibold
    <?php echo $intensity === 'high' ? 'bg-urgency/8 border border-urgency/20 text-urgency' : 'bg-amber-50 border border-amber-200/50 text-amber-700'; ?>">
    <span class="relative flex shrink-0">
        <span class="w-2 h-2 rounded-full <?php echo $intensity === 'high' ? 'bg-urgency' : 'bg-amber-500'; ?>"></span>
        <?php if ($intensity === 'high') : ?>
            <span class="absolute inset-0 w-2 h-2 rounded-full bg-urgency animate-ping"></span>
        <?php endif; ?>
    </span>
    <?php echo esc_html($message); ?>
    <?php if ($intensity === 'high') : ?>
        <svg class="w-4 h-4 ml-auto shrink-0 animate-pulse" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z"/></svg>
    <?php endif; ?>
</div>
