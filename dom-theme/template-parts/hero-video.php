<?php
defined('ABSPATH') || exit;

$video_url   = function_exists('dom_setting_image_url') ? dom_setting_image_url('hero_video') : '';
$hero_poster = function_exists('dom_setting_image_url') ? dom_setting_image_url('hero_image') : '';

/**
 * Zonder eigen hero-beeld tonen we er geen.
 *
 * Hier stond een terugval op de bestverkochte productfoto. Dat levert een
 * willekeurige, lichte studiofoto op waar de hero-typografie in wegvalt: de
 * bovenste regel is met opzet een lichtgrijze schaduwtekst en die verdwijnt
 * tegen zo'n achtergrond volledig. Het prototype heeft hier een video staan
 * met een tijdelijke poster, geen productfoto.
 *
 * Zonder beeld valt de witte laag over een lege achtergrond en blijft er een
 * rustige typografische hero over. Beeld instelbaar via
 * Weergave > Customizer > De Outfitman content.
 */
$shop_url = dom_shop_url();
?>
<section class="hero<?php echo ($video_url || $hero_poster) ? '' : ' hero--nomedia'; ?>" id="hero">
  <div class="hero-media">
    <?php if ($video_url) : ?>
      <video class="hv" id="heroVideo" muted loop playsinline preload="none"
             <?php if ($hero_poster) : ?>poster="<?php echo esc_url($hero_poster); ?>"<?php endif; ?>
             src="<?php echo esc_url($video_url); ?>"></video>
    <?php elseif ($hero_poster) : ?>
      <img src="<?php echo esc_url($hero_poster); ?>" alt="De Outfitman" loading="eager" fetchpriority="high">
    <?php endif; ?>
  </div>
  <div class="hero-gl"><canvas id="glHero"></canvas></div>

  <div class="wrap hero-in">
    <div data-rv="fade">
      <div class="hero-ban">
        <span class="l"><i class="dot"></i><b>Black Friday live</b></span>
        <?php $dom_hero_pct = function_exists('dom_max_sale_percentage') ? dom_max_sale_percentage() : 0; ?>
        <?php if ($dom_hero_pct > 0) : ?>
          <span class="r"><span>Tot -<?php echo (int) $dom_hero_pct; ?>% op geselecteerde items</span></span>
        <?php endif; ?>
      </div>
    </div>
    <h1 class="d1 split">
      <span class="l1">Nr. 1 in merkkleding</span>
      <span class="l2">Voor elke <span class="acc">man</span></span>
    </h1>
    <div class="hero-cta" data-rv="up" style="--d:520ms">
      <a href="<?php echo esc_url($shop_url); ?>" class="btn btn-bf">Shop de Black Friday deals <span class="arw">&rarr;</span></a>
      <a href="<?php echo esc_url($shop_url); ?>" class="btn btn-o">Alle collecties</a>
    </div>
    <div class="hero-trust" data-rv="up" style="--d:640ms">
      <div class="ht">
        <svg viewBox="0 0 24 24"><path d="M3 7h11v9H3z"/><path d="M14 10h4l3 3v3h-7z"/><circle cx="7" cy="18" r="1.6"/><circle cx="17" cy="18" r="1.6"/></svg>
        <div><b>Voor 17:00</b><span>Morgen in huis</span></div>
      </div>
      <div class="ht">
        <svg viewBox="0 0 24 24"><path d="M12 3l7 3v6c0 4-3 7-7 9-4-2-7-5-7-9V6z"/><path d="M9 12l2 2 4-4"/></svg>
        <div><b>Gecontroleerd</b><span>Voor verzending nagekeken</span></div>
      </div>
      <div class="ht">
        <svg viewBox="0 0 24 24"><path d="M12 2a10 10 0 0 0-8.6 15.1L2 22l5-1.3A10 10 0 1 0 12 2z"/></svg>
        <div><b>Persoonlijk advies</b><span>24/7 via WhatsApp</span></div>
      </div>
    </div>
  </div>
  <div class="scrollind"><span>Scroll</span><i></i></div>
</section>
