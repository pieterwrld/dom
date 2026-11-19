<?php
defined('ABSPATH') || exit;

/**
 * Hero.
 *
 * Staat er een Black Friday-banner, dan is dat de hero: de banner draagt
 * zijn eigen tekst, dus er komt geen kop of badge overheen. Alleen een knop,
 * onderin gecentreerd op een donkere verloopband zodat hij leesbaar blijft
 * waar de banner ook licht is.
 *
 * Mobiel en desktop zijn verschillende uitsneden, geen geschaalde versie van
 * elkaar, dus ze wisselen via <picture> op breedte.
 *
 * Zonder banner valt alles terug op de oorspronkelijke hero met video of foto.
 *
 * Alle teksten en beelden komen uit het beheerscherm (De Outfitman > Homepage).
 * De waarden die hier als terugval staan zijn wat de site toont zolang daar
 * niets is ingevuld.
 */

$banner_m = dom_c_img('home.hero.banner_mobile');
$banner_d = dom_c_img('home.hero.banner_desktop');

// Wat eerder via de Customizer was ingesteld blijft gelden tot het in het
// nieuwe scherm wordt overschreven.
if (!$banner_m && function_exists('dom_setting_image_url')) {
    $banner_m = dom_setting_image_url('bf_banner_mobile');
}
if (!$banner_d && function_exists('dom_setting_image_url')) {
    $banner_d = dom_setting_image_url('bf_banner_desktop');
}

$shop_url = dom_shop_url();
$cta_link = dom_c('home.hero.cta_link', '');
$cta_link = $cta_link ?: $shop_url;
$cta_tekst = dom_c('home.hero.cta', __('Shop alle collecties', 'dom-theme'));

/** De beloftes in de doorlopende balk onder de hero. */
$trust_items = dom_c_rows('home.trust.items', [
    ['icoon' => 'truck',    'tekst' => 'Voor 17:00 besteld, morgen in huis'],
    ['icoon' => 'shield',   'tekst' => 'Nr. 1 in kwaliteit'],
    ['icoon' => 'check',    'tekst' => 'Voor verzending nagekeken'],
    ['icoon' => 'whatsapp', 'tekst' => 'Persoonlijk advies, 24/7 via WhatsApp'],
    ['icoon' => 'truck',    'tekst' => 'Gratis verzending vanaf €220'],
]);

if ($banner_m || $banner_d) :
    $primary = $banner_m ?: $banner_d;
?>
<section class="hero-banner-sec" id="hero">
  <a class="hero-banner" href="<?php echo esc_url($cta_link); ?>">
    <picture>
      <?php if ($banner_d) : ?>
        <source media="(min-width:761px)" srcset="<?php echo esc_url($banner_d); ?>">
      <?php endif; ?>
      <img src="<?php echo esc_url($primary); ?>"
           alt="<?php esc_attr_e('Black Friday bij De Outfitman', 'dom-theme'); ?>"
           loading="eager" fetchpriority="high" decoding="async">
    </picture>
    <span class="hero-banner__cta">
      <?php echo esc_html($cta_tekst); ?>
      <span class="arw">&rarr;</span>
    </span>
  </a>
</section>

<?php if ($trust_items) : ?>
<!-- Doorlopende trustbalk: naast elkaar in plaats van onder elkaar.
     De rij staat twee keer in de DOM, zodat de animatie naadloos rondloopt. -->
<div class="htm" aria-label="<?php esc_attr_e('Onze beloftes', 'dom-theme'); ?>">
  <div class="htm-t">
    <?php for ($kopie = 0; $kopie < 2; $kopie++) : ?>
      <div class="htm-g"<?php echo $kopie ? ' aria-hidden="true"' : ''; ?>>
        <?php foreach ($trust_items as $item) : ?>
          <span class="htm-i">
            <?php echo dom_icon($item['icoon'] ?? 'check'); // phpcs:ignore WordPress.Security.EscapeOutput ?>
            <b><?php echo esc_html($item['tekst'] ?? ''); ?></b>
          </span>
        <?php endforeach; ?>
      </div>
    <?php endfor; ?>
  </div>
</div>
<?php endif; ?>

<?php
    return;
endif;

$video_url   = dom_c_img('home.hero.video');
$hero_poster = dom_c_img('home.hero.image');

if (!$video_url && function_exists('dom_setting_image_url')) {
    $video_url = dom_setting_image_url('hero_video');
}
if (!$hero_poster && function_exists('dom_setting_image_url')) {
    $hero_poster = dom_setting_image_url('hero_image');
}
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
      <span class="l1"><?php echo esc_html(dom_c('home.hero.titel_1', __('Nr. 1 in merkkleding', 'dom-theme'))); ?></span>
      <span class="l2"><?php echo esc_html(dom_c('home.hero.titel_2', __('Voor elke man', 'dom-theme'))); ?></span>
    </h1>
    <div class="hero-cta" data-rv="up" style="--d:520ms">
      <a href="<?php echo esc_url($cta_link); ?>" class="btn btn-bf"><?php echo esc_html($cta_tekst); ?> <span class="arw">&rarr;</span></a>
    </div>
    <?php if ($trust_items) : ?>
      <div class="hero-trust" data-rv="up" style="--d:640ms">
        <?php foreach (array_slice($trust_items, 0, 3) as $item) : ?>
          <div class="ht">
            <?php echo dom_icon($item['icoon'] ?? 'check'); // phpcs:ignore WordPress.Security.EscapeOutput ?>
            <div><b><?php echo esc_html($item['tekst'] ?? ''); ?></b></div>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>
</section>
