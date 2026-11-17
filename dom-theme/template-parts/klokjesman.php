<?php
defined('ABSPATH') || exit;

$kk_image = function_exists('dom_setting_image_url') ? dom_setting_image_url('klokjesman_image') : '';
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
    <h3 class="split">De <span class="silver">Klokjes</span>man</h3>
    <p class="tx" style="--d:260ms">De beste kwaliteit horloges voor de scherpste prijzen. Zelfde service, zelfde garantie, zelfde WhatsApp.</p>
    <a href="https://deklokjesman.com/" class="btn btn-bf" style="width:max-content" target="_blank" rel="noopener">Bekijk horloges <span class="arw">&rarr;</span></a>
    <div class="kk-stats">
      <div><b>24/7</b><span>WhatsApp</span></div>
      <div><b>1&ndash;2</b><span>Dagen levering</span></div>
      <div><b>14</b><span>Dagen retour</span></div>
    </div>
  </div>
</div>
