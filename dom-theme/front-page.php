<?php
/**
 * Homepage — De Outfitman (Black Friday Chrome)
 *
 * @package DomTheme
 */
defined('ABSPATH') || exit;
get_header();
?>

<?php get_template_part('template-parts/hero-video'); ?>

<section class="sec" id="collecties">
  <div class="wrap">
    <?php get_template_part('template-parts/collection-grid'); ?>
  </div>
</section>

<section class="sec" style="padding-top:0">
  <div class="wrap">
    <?php get_template_part('template-parts/accessories-banner'); ?>
  </div>
</section>

<section class="sec" style="padding-top:0">
  <div class="wrap">
    <?php get_template_part('template-parts/klokjesman'); ?>
  </div>
</section>

<section class="sec" style="padding-top:0">
  <div class="wrap">
    <?php get_template_part('template-parts/parfum4car'); ?>
  </div>
</section>

<?php get_template_part('template-parts/influencer-scroll'); ?>

<?php get_template_part('template-parts/reviews-grid'); ?>

<section class="sec">
  <div class="wrap">
    <?php get_template_part('template-parts/usp-grid'); ?>
  </div>
</section>

<?php get_footer(); ?>
