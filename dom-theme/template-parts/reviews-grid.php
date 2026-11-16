<?php
defined('ABSPATH') || exit;

$reviews = [];
if (function_exists('get_field')) {
    $reviews = get_field('homepage_reviews', 'option') ?: [];
}
?>
<div class="reveal">
    <div class="flex items-baseline justify-between gap-4 mb-5">
        <h2 class="text-xl md:text-2xl font-bold tracking-tight">800+ tevreden klanten</h2>
    </div>

    <div class="grid grid-cols-2 md:grid-cols-3 gap-2.5 md:gap-3.5">
        <?php if (!empty($reviews)) :
            foreach (array_slice($reviews, 0, 6) as $review) : ?>
                <div class="bg-surface border border-border rounded-md overflow-hidden">
                    <?php if (!empty($review['screenshot'])) : ?>
                        <div class="relative aspect-[4/5]">
                            <img src="<?php echo esc_url($review['screenshot']['url']); ?>" alt="Review" class="w-full h-full object-cover" loading="lazy">
                            <span class="absolute top-2 left-2 px-2.5 py-0.5 bg-[#25D366] text-white text-[10px] font-bold rounded-full">WhatsApp</span>
                        </div>
                    <?php endif; ?>
                    <div class="p-3">
                        <div class="text-accent text-xs tracking-wider">&#9733;&#9733;&#9733;&#9733;&#9733;</div>
                        <div class="text-sm font-semibold mt-1"><?php echo esc_html($review['name'] ?? ''); ?></div>
                        <?php if (!empty($review['text'])) : ?>
                            <div class="text-xs text-muted mt-0.5"><?php echo esc_html($review['text']); ?></div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach;
        else : ?>
            <?php
            $placeholders = [
                ['name' => 'Mohammed A.', 'text' => 'Snelle levering, top kwaliteit!'],
                ['name' => 'Younes B.', 'text' => 'Past perfect, precies op maat'],
                ['name' => 'Bilal K.', 'text' => 'Beste prijs-kwaliteit online'],
                ['name' => 'Youssef M.', 'text' => 'Aanrader, zeker voor sneakers'],
            ];
            foreach ($placeholders as $ph) : ?>
                <div class="bg-surface border border-border rounded-md overflow-hidden">
                    <div class="relative aspect-[4/5] bg-surface-2 flex items-center justify-center">
                        <span class="text-xs text-muted">Review screenshot</span>
                        <span class="absolute top-2 left-2 px-2.5 py-0.5 bg-[#25D366] text-white text-[10px] font-bold rounded-full">WhatsApp</span>
                    </div>
                    <div class="p-3">
                        <div class="text-accent text-xs tracking-wider">&#9733;&#9733;&#9733;&#9733;&#9733;</div>
                        <div class="text-sm font-semibold mt-1"><?php echo esc_html($ph['name']); ?></div>
                        <div class="text-xs text-muted mt-0.5"><?php echo esc_html($ph['text']); ?></div>
                    </div>
                </div>
            <?php endforeach;
        endif; ?>
    </div>
</div>
