<?php
defined('ABSPATH') || exit;

$usps = [
    [
        'icon' => '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><rect x="1" y="3" width="15" height="13"/><polygon points="16,8 20,8 23,11 23,16 16,16"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg>',
        'label' => 'Snelle levering',
        'sub' => 'Gratis vanaf €200',
    ],
    [
        'icon' => '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><polyline points="1,4 1,10 7,10"/><path d="M3.51 15a9 9 0 1 0 2.13-9.36L1 10"/></svg>',
        'label' => '14 dagen retour',
        'sub' => 'Gratis retourneren',
    ],
    [
        'icon' => '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>',
        'label' => 'Veilig betalen',
        'sub' => 'iDEAL, Visa, Klarna',
    ],
    [
        'icon' => '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"/></svg>',
        'label' => '24/7 WhatsApp',
        'sub' => 'Direct antwoord',
    ],
];
?>
<div class="grid grid-cols-2 gap-2.5 mb-5">
    <?php foreach ($usps as $usp) : ?>
        <div class="flex items-start gap-3 p-4 bg-surface rounded-lg">
            <div class="w-10 h-10 rounded-full silver-gradient flex items-center justify-center shrink-0 text-fg">
                <?php echo $usp['icon']; ?>
            </div>
            <div>
                <span class="block text-xs font-bold"><?php echo esc_html($usp['label']); ?></span>
                <span class="block text-[11px] text-muted mt-0.5"><?php echo esc_html($usp['sub']); ?></span>
            </div>
        </div>
    <?php endforeach; ?>
</div>
