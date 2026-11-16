<?php
defined('ABSPATH') || exit;

$cutoff_hour = 18;
$now = new DateTime('now', new DateTimeZone('Europe/Amsterdam'));
$delivery = clone $now;

if ((int) $now->format('H') < $cutoff_hour && !in_array($now->format('N'), ['6', '7'])) {
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

$delivery_text = sprintf('Voor %d:00 besteld, %s %s %s in huis', $cutoff_hour, $day_name, $day_num, $month_name);
?>
<div class="flex items-center gap-2 px-3.5 py-3 bg-success/5 border border-success/15 rounded-md text-sm text-success font-medium mb-4">
    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="shrink-0">
        <rect x="1" y="3" width="15" height="13"/><polygon points="16,8 20,8 23,11 23,16 16,16"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/>
    </svg>
    <?php echo esc_html($delivery_text); ?>
</div>
