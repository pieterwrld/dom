<?php
defined('ABSPATH') || exit;

$reviews = [];
if (function_exists('get_field')) {
    $reviews = get_field('homepage_reviews', 'option') ?: [];
}

$review_base = content_url('/plugins/nxs-whatsapp-reviews/assets/reviews/');
$fallback_reviews = [
    ['name' => 'Mohammed A.', 'text' => 'Snelle levering, top kwaliteit!', 'image' => $review_base . 'review-01.jpeg'],
    ['name' => 'Younes B.', 'text' => 'Past perfect, precies op maat', 'image' => $review_base . 'review-02.jpeg'],
    ['name' => 'Bilal K.', 'text' => 'Beste prijs-kwaliteit online', 'image' => $review_base . 'review-03.jpeg'],
    ['name' => 'Youssef M.', 'text' => 'Aanrader, zeker voor sneakers', 'image' => $review_base . 'review-04.jpeg'],
    ['name' => 'Ahmed R.', 'text' => 'Alles top geregeld!', 'image' => $review_base . 'review-05.jpeg'],
    ['name' => 'Omar H.', 'text' => 'Zeker opnieuw bestellen', 'image' => $review_base . 'review-06.jpeg'],
    ['name' => 'Khalid S.', 'text' => 'Super snelle verzending', 'image' => $review_base . 'review-07.jpeg'],
    ['name' => 'Ibrahim D.', 'text' => 'Mooier dan verwacht!', 'image' => $review_base . 'review-08.jpeg'],
    ['name' => 'Zakaria L.', 'text' => 'Top service via WhatsApp', 'image' => $review_base . 'review-09.jpeg'],
];
?>
<div>
    <div class="reveal flex items-end justify-between gap-4 mb-8 md:mb-10">
        <div>
            <span class="block text-2xs font-bold uppercase tracking-label text-muted mb-2">Echte reviews</span>
            <h2 class="text-3xl md:text-4xl font-black tracking-tight leading-none">800+ tevreden<br>klanten</h2>
        </div>
        <div class="hidden md:flex items-center gap-2 text-sm text-muted">
            <svg class="w-5 h-5 text-[#25D366]" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/><path d="M12 0C5.373 0 0 5.373 0 12c0 2.625.846 5.059 2.284 7.034L.789 23.492a.5.5 0 00.611.611l4.458-1.495A11.952 11.952 0 0012 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 22c-2.17 0-4.207-.672-5.883-1.84l-.42-.298-2.65.889.889-2.65-.298-.42A9.935 9.935 0 012 12C2 6.477 6.477 2 12 2s10 4.477 10 10-4.477 10-10 10z"/></svg>
            <span class="font-medium">Via WhatsApp</span>
        </div>
    </div>

    <div class="grid grid-cols-2 md:grid-cols-3 gap-2.5 md:gap-4">
        <?php if (!empty($reviews)) :
            foreach (array_slice($reviews, 0, 9) as $i => $review) : ?>
                <div class="reveal group bg-white border border-border rounded-lg overflow-hidden hover:shadow-lg hover:-translate-y-0.5 transition-all duration-300" style="transition-delay: <?php echo $i * 60; ?>ms">
                    <?php if (!empty($review['screenshot'])) : ?>
                        <div class="relative aspect-[3/4] overflow-hidden">
                            <img src="<?php echo esc_url($review['screenshot']['url']); ?>" alt="Review van <?php echo esc_attr($review['name'] ?? 'klant'); ?>" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105" loading="lazy">
                            <div class="absolute top-3 left-3 flex items-center gap-1.5 px-2.5 py-1 bg-[#25D366] text-white text-[10px] font-bold rounded-full">
                                <svg class="w-3 h-3" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/></svg>
                                WhatsApp
                            </div>
                        </div>
                    <?php endif; ?>
                    <div class="p-3.5">
                        <div class="flex gap-0.5 text-amber-400 mb-1.5">
                            <?php for ($s = 0; $s < 5; $s++) : ?>
                            <svg class="w-3.5 h-3.5 fill-current" viewBox="0 0 20 20"><path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/></svg>
                            <?php endfor; ?>
                        </div>
                        <p class="text-sm font-semibold"><?php echo esc_html($review['name'] ?? ''); ?></p>
                        <?php if (!empty($review['text'])) : ?>
                        <p class="text-xs text-muted mt-0.5 line-clamp-2"><?php echo esc_html($review['text']); ?></p>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach;
        else : ?>
            <?php foreach ($fallback_reviews as $i => $review) : ?>
                <div class="reveal group bg-white border border-border rounded-lg overflow-hidden hover:shadow-lg hover:-translate-y-0.5 transition-all duration-300 <?php echo $i >= 6 ? 'hidden md:block' : ''; ?>" style="transition-delay: <?php echo $i * 60; ?>ms">
                    <div class="relative aspect-[3/4] overflow-hidden">
                        <img src="<?php echo esc_url($review['image']); ?>" alt="Review van <?php echo esc_attr($review['name']); ?>" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105" loading="lazy">
                        <div class="absolute top-3 left-3 flex items-center gap-1.5 px-2.5 py-1 bg-[#25D366] text-white text-[10px] font-bold rounded-full">
                            <svg class="w-3 h-3" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/></svg>
                            WhatsApp
                        </div>
                    </div>
                    <div class="p-3.5">
                        <div class="flex gap-0.5 text-amber-400 mb-1.5">
                            <?php for ($s = 0; $s < 5; $s++) : ?>
                            <svg class="w-3.5 h-3.5 fill-current" viewBox="0 0 20 20"><path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/></svg>
                            <?php endfor; ?>
                        </div>
                        <p class="text-sm font-semibold"><?php echo esc_html($review['name']); ?></p>
                        <p class="text-xs text-muted mt-0.5"><?php echo esc_html($review['text']); ?></p>
                    </div>
                </div>
            <?php endforeach;
        endif; ?>
    </div>
</div>
