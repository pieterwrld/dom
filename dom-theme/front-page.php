<?php
/**
 * Homepage template
 *
 * @package DomTheme
 */
defined('ABSPATH') || exit;
get_header();
?>

<?php get_template_part('template-parts/hero-video'); ?>

<section class="max-w-7xl mx-auto px-4 mb-14">
    <?php get_template_part('template-parts/collection-grid'); ?>
</section>

<section class="mb-14">
    <?php get_template_part('template-parts/accessories-banner'); ?>
</section>

<section class="max-w-7xl mx-auto px-4 mb-14">
    <?php get_template_part('template-parts/klokjesman'); ?>
</section>

<section class="max-w-7xl mx-auto px-4 mb-14">
    <?php get_template_part('template-parts/parfum4car'); ?>
</section>

<section class="max-w-7xl mx-auto px-4 mb-14">
    <?php get_template_part('template-parts/reviews-grid'); ?>
</section>

<section class="max-w-7xl mx-auto px-4 mb-14">
    <?php get_template_part('template-parts/influencer-scroll'); ?>
</section>

<?php get_footer(); ?>
