<?php
/**
 * Homepage — De Outfitman
 *
 * @package DomTheme
 */
defined('ABSPATH') || exit;
get_header();
?>

<?php get_template_part('template-parts/hero-video'); ?>

<section class="py-16 md:py-24">
    <div class="max-w-7xl mx-auto px-4">
        <?php get_template_part('template-parts/collection-grid'); ?>
    </div>
</section>

<section class="bg-bg-off py-16 md:py-24">
    <?php get_template_part('template-parts/accessories-banner'); ?>
</section>

<section class="py-16 md:py-24">
    <div class="max-w-7xl mx-auto px-4">
        <?php get_template_part('template-parts/klokjesman'); ?>
    </div>
</section>

<section class="bg-bg-off py-16 md:py-24">
    <div class="max-w-7xl mx-auto px-4">
        <?php get_template_part('template-parts/parfum4car'); ?>
    </div>
</section>

<section class="py-16 md:py-24">
    <div class="max-w-7xl mx-auto px-4">
        <?php get_template_part('template-parts/reviews-grid'); ?>
    </div>
</section>

<section class="bg-fg py-16 md:py-24">
    <div class="max-w-7xl mx-auto px-4">
        <?php get_template_part('template-parts/influencer-scroll'); ?>
    </div>
</section>

<?php get_footer(); ?>
