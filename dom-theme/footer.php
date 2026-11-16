</main><!-- #content -->

<footer class="bg-bg-off border-t border-border mt-14" role="contentinfo">
    <div class="max-w-7xl mx-auto px-4 pt-12 pb-6">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-8 mb-10">
            <div>
                <h4 class="text-2xs font-bold uppercase tracking-label text-muted mb-4">Shop</h4>
                <nav class="flex flex-col gap-1.5">
                    <a href="<?php echo esc_url(get_permalink(wc_get_page_id('shop'))); ?>" class="text-sm text-muted hover:text-fg transition-colors">Alle producten</a>
                    <?php
                    $categories = get_terms(['taxonomy' => 'product_cat', 'hide_empty' => true, 'number' => 6, 'exclude' => [get_option('default_product_cat')]]);
                    if (!is_wp_error($categories)) :
                        foreach ($categories as $cat) : ?>
                            <a href="<?php echo esc_url(get_term_link($cat)); ?>" class="text-sm text-muted hover:text-fg transition-colors"><?php echo esc_html($cat->name); ?></a>
                        <?php endforeach;
                    endif; ?>
                </nav>
            </div>
            <div>
                <h4 class="text-2xs font-bold uppercase tracking-label text-muted mb-4">Klantenservice</h4>
                <nav class="flex flex-col gap-1.5">
                    <a href="/contact" class="text-sm text-muted hover:text-fg transition-colors">Contact</a>
                    <a href="/verzending" class="text-sm text-muted hover:text-fg transition-colors">Verzending</a>
                    <a href="/retourneren" class="text-sm text-muted hover:text-fg transition-colors">Retourneren</a>
                    <a href="/faq" class="text-sm text-muted hover:text-fg transition-colors">FAQ</a>
                    <a href="/maatadvies" class="text-sm text-muted hover:text-fg transition-colors">Maatadvies</a>
                </nav>
            </div>
            <div>
                <h4 class="text-2xs font-bold uppercase tracking-label text-muted mb-4">Over ons</h4>
                <nav class="flex flex-col gap-1.5">
                    <a href="/ons-verhaal" class="text-sm text-muted hover:text-fg transition-colors">Ons verhaal</a>
                    <a href="https://deklokjesman.com" target="_blank" rel="noopener" class="text-sm text-muted hover:text-fg transition-colors">De Klokjesman</a>
                    <a href="https://parfum4car.nl" target="_blank" rel="noopener" class="text-sm text-muted hover:text-fg transition-colors">Parfum4car</a>
                </nav>
            </div>
            <div>
                <h4 class="text-2xs font-bold uppercase tracking-label text-muted mb-4">Volg ons</h4>
                <nav class="flex flex-col gap-1.5">
                    <a href="#" class="text-sm text-muted hover:text-fg transition-colors">Instagram</a>
                    <a href="#" class="text-sm text-muted hover:text-fg transition-colors">TikTok</a>
                    <a href="#" class="text-sm text-muted hover:text-fg transition-colors">WhatsApp</a>
                </nav>
            </div>
        </div>

        <!-- Newsletter -->
        <div class="border-t border-border pt-8 mb-8">
            <h4 class="text-base font-bold mb-1">Mis geen deals</h4>
            <p class="text-sm text-muted mb-4">Exclusieve aanbiedingen en early access op nieuwe drops</p>
            <form class="flex gap-2 max-w-md" action="#" method="post">
                <input type="email" name="email" required placeholder="E-mailadres" class="flex-1 px-4 py-3 bg-surface border border-border rounded-md text-sm placeholder:text-muted focus:outline-none focus:border-accent transition-colors">
                <button type="submit" class="px-5 py-3 silver-gradient text-fg rounded-md text-xs font-bold uppercase tracking-label">Go</button>
            </form>
        </div>

        <!-- Bottom -->
        <div class="flex flex-wrap items-center justify-between gap-4 border-t border-border pt-6 text-xs text-muted">
            <span>&copy; <?php echo date('Y'); ?> De Outfitman</span>
            <div class="flex gap-1.5">
                <span class="px-2.5 py-1 bg-surface rounded text-[10px] font-semibold">iDEAL</span>
                <span class="px-2.5 py-1 bg-surface rounded text-[10px] font-semibold">Visa</span>
                <span class="px-2.5 py-1 bg-surface rounded text-[10px] font-semibold">MC</span>
                <span class="px-2.5 py-1 bg-surface rounded text-[10px] font-semibold">Klarna</span>
                <span class="px-2.5 py-1 bg-surface rounded text-[10px] font-semibold">PayPal</span>
            </div>
        </div>
    </div>
</footer>

<?php get_template_part('template-parts/cart-drawer'); ?>

<?php wp_footer(); ?>
</body>
</html>
