<?php
defined('ABSPATH') || exit;

$usps = [
    ['icon' => '&#9889;', 'label' => 'Snelle levering'],
    ['icon' => '&#8634;', 'label' => '14 dagen retour'],
    ['icon' => '&#128274;', 'label' => 'Veilig betalen'],
    ['icon' => '&#9742;', 'label' => 'WhatsApp support'],
];
?>
<div class="grid grid-cols-2 gap-2 mb-4">
    <?php foreach ($usps as $usp) : ?>
        <div class="flex items-center gap-2.5 p-3.5 bg-surface border border-border rounded-md text-xs font-medium">
            <span class="w-8 h-8 rounded-full silver-gradient flex items-center justify-center text-sm shrink-0 text-fg font-bold"><?php echo $usp['icon']; ?></span>
            <?php echo esc_html($usp['label']); ?>
        </div>
    <?php endforeach; ?>
</div>
