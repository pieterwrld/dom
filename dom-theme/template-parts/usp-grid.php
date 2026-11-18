<?php
/**
 * Vier beloftes in blokken.
 * Beheer: De Outfitman > Homepage > Vier beloftes.
 */

defined('ABSPATH') || exit;

$usp_items = dom_c_rows('home.usp.items', [
    ['icoon' => 'shield',   'titel' => 'Kwaliteitsgarantie', 'tekst' => 'Elk product wordt voor verzending gecontroleerd. Topkwaliteit, zonder compromis.'],
    ['icoon' => 'truck',    'titel' => 'Morgen in huis',     'tekst' => 'Voor 17:00 besteld, morgen bezorgd. Gratis verzending vanaf €200.'],
    ['icoon' => 'card',     'titel' => 'Veilig betalen',     'tekst' => 'iDEAL, Bancontact, creditcard en Apple Pay — altijd via een beveiligde omgeving.'],
    ['icoon' => 'whatsapp', 'titel' => '24/7 WhatsApp',      'tekst' => 'Vragen over maten, bestellingen of advies? We staan altijd voor je klaar.'],
]);

if (!$usp_items) {
    return;
}
?>
<div class="usp" data-rv="up">
  <?php foreach ($usp_items as $item) : ?>
    <div class="ucard">
      <div class="uic"><?php echo dom_icon($item['icoon'] ?? 'check'); // phpcs:ignore WordPress.Security.EscapeOutput ?></div>
      <h4><?php echo esc_html($item['titel'] ?? ''); ?></h4>
      <p><?php echo esc_html($item['tekst'] ?? ''); ?></p>
    </div>
  <?php endforeach; ?>
</div>
