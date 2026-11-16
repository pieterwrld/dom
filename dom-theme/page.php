<?php
defined('ABSPATH') || exit;
get_header();
?>

<div class="max-w-3xl mx-auto px-4 py-12">
    <?php while (have_posts()) : the_post(); ?>
        <h1 class="text-3xl font-bold tracking-tight mb-6"><?php the_title(); ?></h1>
        <div class="prose prose-sm max-w-none text-muted [&_h2]:text-fg [&_h2]:font-bold [&_h2]:text-xl [&_h2]:mt-8 [&_h2]:mb-3 [&_p]:mb-4 [&_a]:text-fg [&_a]:underline">
            <?php the_content(); ?>
        </div>
    <?php endwhile; ?>
</div>

<?php get_footer(); ?>
