<?php
defined('ABSPATH') || exit;

global $product;

if (!$product || !$product->is_type('variable')) return;

$size_attribute = $product->get_attribute('pa_maat') ?: $product->get_attribute('pa_size');
if (!$size_attribute) return;

$sizes = array_map('trim', explode(', ', $size_attribute));
$available = [];

foreach ($product->get_available_variations() as $variation) {
    $attrs = $variation['attributes'];
    $size_val = $attrs['attribute_pa_maat'] ?? $attrs['attribute_pa_size'] ?? '';
    if ($size_val && $variation['is_in_stock']) {
        $available[] = $size_val;
    }
}
?>
<div class="mb-4">
    <label class="block text-xs font-bold uppercase tracking-label text-muted mb-2">Maat</label>
    <div class="flex flex-wrap gap-1.5">
        <?php foreach ($sizes as $size) :
            $in_stock = in_array(sanitize_title($size), $available);
        ?>
            <button type="button"
                class="dom-size-btn min-w-[48px] px-3.5 py-2.5 text-center border-[1.5px] rounded-md text-sm font-semibold transition-all
                    <?php echo $in_stock
                        ? 'border-border hover:border-fg'
                        : 'border-border opacity-25 line-through pointer-events-none'; ?>"
                data-size="<?php echo esc_attr(sanitize_title($size)); ?>"
                <?php echo $in_stock ? '' : 'disabled'; ?>>
                <?php echo esc_html($size); ?>
            </button>
        <?php endforeach; ?>
    </div>
</div>
