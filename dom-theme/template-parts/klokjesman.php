<?php
/**
 * Kruisverwijzing naar De Klokjesman.
 * Beheer: De Outfitman > Homepage > De Klokjesman.
 */

defined('ABSPATH') || exit;

$kk_image = dom_c_img('home.klokjesman.afbeelding');
if (!$kk_image && function_exists('dom_setting_image_url')) {
    $kk_image = dom_setting_image_url('klokjesman_image');
}

$kk_stats = dom_c_rows('home.klokjesman.stats', [
    ['waarde' => '24/7', 'label' => 'WhatsApp'],
    ['waarde' => '1–2',  'label' => 'Dagen levering'],
    ['waarde' => '14',   'label' => 'Dagen retour'],
]);

$kk_link = dom_c('home.klokjesman.link', 'https://deklokjesman.com/');
?>
<div class="kk" data-rv="up">
  <div class="kk-im">
    <?php if ($kk_image) : ?>
      <img src="<?php echo esc_url($kk_image); ?>" alt="De Klokjesman" loading="lazy">
    <?php else : ?>
      <div style="position:absolute;inset:0;background:linear-gradient(135deg,#0A0A0A 0%,#1a1a2e 50%,#0A0A0A 100%);display:flex;align-items:center;justify-content:center">
        <span style="font-family:var(--disp);font-size:clamp(3rem,8vw,6rem);font-weight:900;text-transform:uppercase;background-image:linear-gradient(110deg,#6B7280,#9CA3AF,#F3F4F6,#9CA3AF);background-size:300% 300%;animation:shim 6s ease infinite;-webkit-background-clip:text;background-clip:text;-webkit-text-fill-color:transparent;opacity:.15">DKM</span>
      </div>
    <?php endif; ?>
  </div>
  <div class="kk-c">
    <h3 class="split">
      <?php echo esc_html(dom_c('home.klokjesman.titel_voor', 'De')); ?>
      <span class="silver"><?php echo esc_html(dom_c('home.klokjesman.titel_accent', 'Klokjes')); ?></span><?php echo esc_html(dom_c('home.klokjesman.titel_na', 'man')); ?>
    </h3>
    <p class="tx" style="--d:260ms"><?php echo esc_html(dom_c('home.klokjesman.tekst', 'De beste kwaliteit horloges voor de scherpste prijzen. Zelfde service, zelfde garantie, zelfde WhatsApp.')); ?></p>
    <a href="<?php echo esc_url($kk_link); ?>" class="btn btn-bf" style="width:max-content" target="_blank" rel="noopener"><?php echo esc_html(dom_c('home.klokjesman.knop', 'Bekijk horloges')); ?> <span class="arw">&rarr;</span></a>
    <?php if ($kk_stats) : ?>
      <div class="kk-stats">
        <?php foreach ($kk_stats as $stat) : ?>
          <div><b><?php echo esc_html($stat['waarde'] ?? ''); ?></b><span><?php echo esc_html($stat['label'] ?? ''); ?></span></div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>
</div>
