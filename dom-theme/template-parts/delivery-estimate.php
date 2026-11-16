<?php
defined('ABSPATH') || exit;

$cutoff_hour = 18;
$now = new DateTime('now', new DateTimeZone('Europe/Amsterdam'));
$delivery = clone $now;

$is_before_cutoff = (int) $now->format('H') < $cutoff_hour && !in_array($now->format('N'), ['6', '7']);

if ($is_before_cutoff) {
    $delivery->modify('+1 weekday');
} else {
    $delivery->modify('+2 weekday');
}

$day_names_nl = [
    1 => 'maandag', 2 => 'dinsdag', 3 => 'woensdag',
    4 => 'donderdag', 5 => 'vrijdag', 6 => 'zaterdag', 7 => 'zondag',
];
$month_names_nl = [
    1 => 'januari', 2 => 'februari', 3 => 'maart', 4 => 'april',
    5 => 'mei', 6 => 'juni', 7 => 'juli', 8 => 'augustus',
    9 => 'september', 10 => 'oktober', 11 => 'november', 12 => 'december',
];

$day_name = $day_names_nl[(int) $delivery->format('N')];
$day_num = $delivery->format('j');
$month_name = $month_names_nl[(int) $delivery->format('n')];
?>
<div class="flex items-start gap-3 px-4 py-3.5 bg-success/5 border border-success/15 rounded-lg text-sm mb-5">
    <div class="w-9 h-9 rounded-full bg-success/10 flex items-center justify-center shrink-0 mt-0.5">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-success">
            <rect x="1" y="3" width="15" height="13"/><polygon points="16,8 20,8 23,11 23,16 16,16"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/>
        </svg>
    </div>
    <div>
        <p class="font-semibold text-success">
            <?php if ($is_before_cutoff) : ?>
                Voor <?php echo $cutoff_hour; ?>:00 besteld, morgen in huis
            <?php else : ?>
                Besteld, <?php echo esc_html($day_name); ?> in huis
            <?php endif; ?>
        </p>
        <p class="text-xs text-muted mt-0.5"><?php echo esc_html(ucfirst($day_name) . ' ' . $day_num . ' ' . $month_name); ?></p>
    </div>
</div>
