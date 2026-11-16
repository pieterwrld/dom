<?php
defined('ABSPATH') || exit;
get_header();
?>

<section class="sec" style="padding:clamp(48px,8vw,96px) 0">
    <div class="wrap" style="max-width:760px">
        <?php while (have_posts()) : the_post(); ?>
            <h1 class="d2 split" style="margin-bottom:24px"><?php the_title(); ?></h1>
            <div class="page-content" data-rv>
                <?php the_content(); ?>
            </div>
        <?php endwhile; ?>
    </div>
</section>

<?php get_footer(); ?>
