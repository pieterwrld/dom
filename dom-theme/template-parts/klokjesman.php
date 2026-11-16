<?php
defined('ABSPATH') || exit;

$kk_image = '';
if (function_exists('get_field')) {
    $img = get_field('klokjesman_image', 'option');
    if (is_array($img) && !empty($img['url'])) {
        $kk_image = $img['url'];
    } elseif (is_string($img) && $img) {
        $kk_image = $img;
    }
}
?>
<div class="kk" data-rv="up">
  <div class="kk-im">
    <?php if ($kk_image) : ?>
      <img src="<?php echo esc_url($kk_image); ?>" alt="De Klokjesman" loading="lazy">
    <?php else : ?>
      <div class="ph" style="position:absolute;inset:0"><span class="ph-l">De Klokjesman</span></div>
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
