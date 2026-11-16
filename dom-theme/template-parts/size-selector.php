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
<div class="mb-5">
    <div class="flex items-center justify-between mb-3">
        <label class="text-xs font-bold uppercase tracking-label">Maat</label>
        <button type="button" class="text-xs text-muted underline underline-offset-2 hover:text-fg transition-colors">Maatadvies</button>
    </div>
    <div class="flex flex-wrap gap-2">
        <?php foreach ($sizes as $size) :
            $in_stock = in_array(sanitize_title($size), $available);
        ?>
            <button type="button"
                class="dom-size-btn relative min-w-[52px] px-4 py-3 text-center border-[1.5px] rounded-lg text-sm font-semibold transition-all duration-200
                    <?php echo $in_stock
                        ? 'border-border hover:border-fg hover:bg-fg hover:text-white active:scale-95'
                        : 'border-border/50 text-muted/40 line-through cursor-not-allowed'; ?>"
                data-size="<?php echo esc_attr(sanitize_title($size)); ?>"
                <?php echo $in_stock ? '' : 'disabled'; ?>>
                <?php echo esc_html($size); ?>
                <?php if (!$in_stock) : ?>
                    <span class="absolute inset-0 flex items-center justify-center">
                        <span class="w-[1px] h-[70%] bg-muted/20 rotate-45"></span>
                    </span>
                <?php endif; ?>
            </button>
        <?php endforeach; ?>
    </div>
</div>
