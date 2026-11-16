<?php
defined('ABSPATH') || exit;

$messages = [
    '24/7 KLANTENSERVICE VIA WHATSAPP',
    'VOOR 17:00 BESTELD = MORGEN IN HUIS',
    '14 DAGEN GRATIS RETOURNEREN',
    '100% ORIGINELE MERKKLEDING',
    'GRATIS VERZENDING VANAF €200',
    '3 VOOR €130 OP T-SHIRTS & ZWEMBROEKEN',
];

if (function_exists('get_field')) {
    $acf_messages = get_field('marquee_messages', 'option');
    if ($acf_messages && is_array($acf_messages)) {
        $messages = array_column($acf_messages, 'message');
    }
}
?>
<div class="bg-fg overflow-hidden" role="marquee" aria-label="<?php esc_attr_e('Aanbiedingen', 'dom-theme'); ?>">
    <div class="marquee-track py-2.5 text-bg text-2xs font-bold uppercase tracking-label">
        <?php for ($i = 0; $i < 2; $i++) : ?>
            <?php foreach ($messages as $msg) : ?>
                <span class="whitespace-nowrap flex items-center gap-2.5">
                    <span class="w-1 h-1 rounded-full bg-white/30 shrink-0"></span>
                    <?php echo esc_html($msg); ?>
                </span>
            <?php endforeach; ?>
        <?php endfor; ?>
    </div>
</div>
