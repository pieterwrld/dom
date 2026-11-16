<?php
defined('ABSPATH') || exit;

$accessories = [
    ['slug' => 'brillen', 'label' => 'Brillen', 'desc' => 'Designer zonnebrillen'],
    ['slug' => 'petten', 'label' => 'Petten', 'desc' => 'Caps & snapbacks'],
    ['slug' => 'tassen', 'label' => 'Tassen', 'desc' => 'Crossbody & rugzakken'],
];
?>
<div class="reveal bg-bg-off">
    <div class="max-w-7xl mx-auto px-4 py-12 md:py-16">
        <h2 class="text-xl md:text-2xl font-bold tracking-tight mb-2">Accessoires</h2>
        <p class="text-sm text-muted mb-8">Maak je outfit compleet</p>

        <div class="grid grid-cols-3 gap-3 md:gap-4">
            <?php foreach ($accessories as $acc) :
                $term = get_term_by('slug', $acc['slug'], 'product_cat');
                $link = $term ? get_term_link($term) : '#';
                $thumb_id = $term ? get_term_meta($term->term_id, 'thumbnail_id', true) : 0;
                $image = $thumb_id ? wp_get_attachment_image_url($thumb_id, 'collection-card') : '';
            ?>
                <a href="<?php echo esc_url($link); ?>" class="group relative aspect-[3/5] md:aspect-[2/3] rounded-lg overflow-hidden bg-surface">
                    <?php if ($image) : ?>
                        <img src="<?php echo esc_url($image); ?>" alt="<?php echo esc_attr($acc['label']); ?>" class="absolute inset-0 w-full h-full object-cover transition-transform duration-500 group-hover:scale-105" loading="lazy">
                    <?php else : ?>
                        <div class="absolute inset-0 bg-gradient-to-br from-surface to-surface-2"></div>
                    <?php endif; ?>
                    <div class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent"></div>
                    <div class="absolute bottom-0 left-0 right-0 p-4">
                        <span class="text-base md:text-lg font-bold uppercase text-white"><?php echo esc_html($acc['label']); ?></span>
                        <span class="block text-xs text-white/60 mt-0.5"><?php echo esc_html($acc['desc']); ?></span>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</div>
