<?php
defined('ABSPATH') || exit;

$hero_video = function_exists('get_field') ? get_field('hero_video', 'option') : '';
$hero_image = function_exists('get_field') ? get_field('hero_image', 'option') : '';
$hero_poster = '';
if (is_array($hero_image) && !empty($hero_image['url'])) {
    $hero_poster = $hero_image['url'];
} elseif (is_string($hero_image) && $hero_image) {
    $hero_poster = $hero_image;
}
$video_url = '';
if (is_array($hero_video) && !empty($hero_video['url'])) {
    $video_url = $hero_video['url'];
} elseif (is_string($hero_video) && $hero_video) {
    $video_url = $hero_video;
}

// Fallback: use recent product images as hero background collage
if (!$video_url && !$hero_poster) {
    $hero_products = get_posts([
        'post_type'      => 'product',
        'posts_per_page' => 1,
        'post_status'    => 'publish',
        'meta_key'       => 'total_sales',
        'orderby'        => 'meta_value_num',
        'order'          => 'DESC',
        'meta_query'     => [['key' => '_thumbnail_id', 'compare' => 'EXISTS']],
        'fields'         => 'ids',
    ]);
    if ($hero_products) {
        $hero_poster = get_the_post_thumbnail_url($hero_products[0], 'full');
    }
}

$shop_url = get_permalink(wc_get_page_id('shop'));
?>
<section class="hero" id="hero">
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
        <span class="r"><span>Tot -50% op de hele collectie</span></span>
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
        <div><b>100% origineel</b><span>Gecontroleerd voor verzending</span></div>
      </div>
      <div class="ht">
        <svg viewBox="0 0 24 24"><path d="M12 2a10 10 0 0 0-8.6 15.1L2 22l5-1.3A10 10 0 1 0 12 2z"/></svg>
        <div><b>Persoonlijk advies</b><span>24/7 via WhatsApp</span></div>
      </div>
    </div>
  </div>
  <div class="scrollind"><span>Scroll</span><i></i></div>
</section>
