<?php
defined('ABSPATH') || exit;

/**
 * Hero.
 *
 * Staat er een Black Friday-banner klaar, dan is dat de hero: de banner draagt
 * zijn eigen tekst, dus er komt geen kop of badge overheen. Alleen een knop,
 * onderin gecentreerd op een donkere verloopband zodat hij leesbaar blijft
 * waar de banner ook licht is.
 *
 * Mobiel en desktop zijn verschillende uitsneden, geen geschaalde versie van
 * elkaar, dus ze wisselen via <picture> op breedte.
 *
 * Zonder banner valt alles terug op de oorspronkelijke hero met video of foto.
 */

$banner_m = function_exists('dom_setting_image_url') ? dom_setting_image_url('bf_banner_mobile') : '';
$banner_d = function_exists('dom_setting_image_url') ? dom_setting_image_url('bf_banner_desktop') : '';
$shop_url = dom_shop_url();

if ($banner_m || $banner_d) :
    $primary = $banner_m ?: $banner_d;
?>
<section class="hero-banner-sec" id="hero">
  <a class="hero-banner" href="<?php echo esc_url($shop_url); ?>">
    <picture>
      <?php if ($banner_d) : ?>
        <source media="(min-width:761px)" srcset="<?php echo esc_url($banner_d); ?>">
      <?php endif; ?>
      <img src="<?php echo esc_url($primary); ?>"
           alt="<?php esc_attr_e('Black Friday bij De Outfitman', 'dom-theme'); ?>"
           loading="eager" fetchpriority="high" decoding="async">
    </picture>
    <span class="hero-banner__cta">
      <?php esc_html_e('Shop alle collecties', 'dom-theme'); ?>
      <span class="arw">&rarr;</span>
    </span>
  </a>
</section>

<div class="wrap">
  <div class="hero-trust hero-trust--under" data-rv="up">
    <div class="ht">
      <svg viewBox="0 0 24 24" fill="none"><path d="M3 7h11v9H3z"/><path d="M14 10h4l3 3v3h-7z"/><circle cx="7" cy="18" r="1.6"/><circle cx="17" cy="18" r="1.6"/></svg>
      <div><b><?php esc_html_e('Voor 17:00', 'dom-theme'); ?></b><span><?php esc_html_e('Morgen in huis', 'dom-theme'); ?></span></div>
    </div>
    <div class="ht">
      <svg viewBox="0 0 24 24" fill="none"><path d="M12 3l7 3v6c0 4-3 7-7 9-4-2-7-5-7-9V6z"/><path d="M9 12l2 2 4-4"/></svg>
      <div><b><?php esc_html_e('Gecontroleerd', 'dom-theme'); ?></b><span><?php esc_html_e('Voor verzending nagekeken', 'dom-theme'); ?></span></div>
    </div>
    <div class="ht">
      <svg viewBox="0 0 24 24" fill="none"><path d="M12 2a10 10 0 0 0-8.6 15.1L2 22l5-1.3A10 10 0 1 0 12 2z"/></svg>
      <div><b><?php esc_html_e('Persoonlijk advies', 'dom-theme'); ?></b><span><?php esc_html_e('24/7 via WhatsApp', 'dom-theme'); ?></span></div>
    </div>
  </div>
</div>

<?php
    return;
endif;

$video_url   = function_exists('dom_setting_image_url') ? dom_setting_image_url('hero_video') : '';
$hero_poster = function_exists('dom_setting_image_url') ? dom_setting_image_url('hero_image') : '';
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
    <h1 class="d1 split">
      <span class="l1"><?php esc_html_e('Nr. 1 in merkkleding', 'dom-theme'); ?></span>
      <span class="l2"><?php esc_html_e('Voor elke', 'dom-theme'); ?> <span class="acc"><?php esc_html_e('man', 'dom-theme'); ?></span></span>
    </h1>
    <div class="hero-cta" data-rv="up" style="--d:520ms">
      <a href="<?php echo esc_url($shop_url); ?>" class="btn btn-bf"><?php esc_html_e('Shop alle collecties', 'dom-theme'); ?> <span class="arw">&rarr;</span></a>
    </div>
    <div class="hero-trust" data-rv="up" style="--d:640ms">
      <div class="ht">
        <svg viewBox="0 0 24 24" fill="none"><path d="M3 7h11v9H3z"/><path d="M14 10h4l3 3v3h-7z"/><circle cx="7" cy="18" r="1.6"/><circle cx="17" cy="18" r="1.6"/></svg>
        <div><b><?php esc_html_e('Voor 17:00', 'dom-theme'); ?></b><span><?php esc_html_e('Morgen in huis', 'dom-theme'); ?></span></div>
      </div>
      <div class="ht">
        <svg viewBox="0 0 24 24" fill="none"><path d="M12 3l7 3v6c0 4-3 7-7 9-4-2-7-5-7-9V6z"/><path d="M9 12l2 2 4-4"/></svg>
        <div><b><?php esc_html_e('Gecontroleerd', 'dom-theme'); ?></b><span><?php esc_html_e('Voor verzending nagekeken', 'dom-theme'); ?></span></div>
      </div>
      <div class="ht">
        <svg viewBox="0 0 24 24" fill="none"><path d="M12 2a10 10 0 0 0-8.6 15.1L2 22l5-1.3A10 10 0 1 0 12 2z"/></svg>
        <div><b><?php esc_html_e('Persoonlijk advies', 'dom-theme'); ?></b><span><?php esc_html_e('24/7 via WhatsApp', 'dom-theme'); ?></span></div>
      </div>
    </div>
  </div>
</section>
