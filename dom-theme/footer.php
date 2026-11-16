</main><!-- #content -->

<!-- Back to top -->
<button id="backToTop" class="fixed bottom-5 left-5 z-[90] w-10 h-10 bg-fg text-white rounded-full flex items-center justify-center shadow-lg opacity-0 pointer-events-none translate-y-4 transition-all duration-300" aria-label="Terug naar boven">
    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><polyline points="18,15 12,9 6,15"/></svg>
</button>

<footer class="bg-bg-off border-t border-border mt-16 md:mt-24" role="contentinfo">
    <div class="max-w-7xl mx-auto px-4">
        <!-- Trust badges -->
        <div class="grid grid-cols-3 gap-3 md:gap-4 py-8 md:py-10 border-b border-border">
            <div class="flex flex-col items-center text-center gap-2">
                <span class="w-10 h-10 md:w-12 md:h-12 silver-gradient rounded-full flex items-center justify-center shrink-0">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#0A0A0A" stroke-width="2" stroke-linecap="round"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87"/><path d="M16 3.13a4 4 0 010 7.75"/></svg>
                </span>
                <div>
                    <span class="block text-sm md:text-base font-extrabold">14.000+</span>
                    <span class="text-2xs text-muted">tevreden klanten</span>
                </div>
            </div>
            <div class="flex flex-col items-center text-center gap-2">
                <span class="w-10 h-10 md:w-12 md:h-12 silver-gradient rounded-full flex items-center justify-center shrink-0">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#0A0A0A" stroke-width="2" stroke-linecap="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                </span>
                <div>
                    <span class="block text-sm md:text-base font-extrabold">100%</span>
                    <span class="text-2xs text-muted">originele merkkleding</span>
                </div>
            </div>
            <div class="flex flex-col items-center text-center gap-2">
                <span class="w-10 h-10 md:w-12 md:h-12 silver-gradient rounded-full flex items-center justify-center shrink-0">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#0A0A0A" stroke-width="2" stroke-linecap="round"><polyline points="1,4 1,10 7,10"/><path d="M3.51 15a9 9 0 102.13-9.36L1 10"/></svg>
                </span>
                <div>
                    <span class="block text-sm md:text-base font-extrabold">14 dagen</span>
                    <span class="text-2xs text-muted">gratis retourneren</span>
                </div>
            </div>
        </div>

        <!-- Newsletter -->
        <div class="py-10 md:py-14 border-b border-border text-center">
            <span class="block text-2xs font-bold uppercase tracking-label text-muted mb-2">Newsletter</span>
            <h3 class="text-2xl md:text-3xl font-black tracking-tight mb-2">Mis geen enkele drop</h3>
            <p class="text-sm text-muted mb-6 max-w-md mx-auto">Ontvang exclusieve aanbiedingen, early access op nieuwe collecties en style tips — direct in je inbox.</p>
            <form class="flex gap-2 max-w-md mx-auto" action="#" method="post">
                <input type="email" name="email" required placeholder="E-mailadres" class="flex-1 px-4 py-3.5 bg-white border border-border rounded-lg text-sm placeholder:text-muted focus:outline-none focus:border-fg transition-colors">
                <button type="submit" class="px-6 py-3.5 silver-gradient text-fg rounded-lg text-xs font-bold uppercase tracking-label hover:shadow-lg transition-shadow">Aanmelden</button>
            </form>
        </div>

        <!-- Main footer grid -->
        <div class="py-10 md:py-12">
            <!-- Logo + tagline -->
            <div class="mb-8 md:mb-10">
                <span class="text-xl font-extrabold tracking-tight uppercase">De Outf<span class="silver-text">i</span>tman</span>
                <p class="text-sm text-muted mt-1">Premium streetwear voor de moderne man.</p>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-8">
                <div>
                    <h4 class="text-2xs font-bold uppercase tracking-label text-muted mb-4">Shop</h4>
                    <nav class="flex flex-col gap-2">
                        <a href="<?php echo esc_url(get_permalink(wc_get_page_id('shop'))); ?>" class="text-sm text-fg/70 hover:text-fg transition-colors">Alle producten</a>
                        <?php
                        $footer_cats = get_terms(['taxonomy' => 'product_cat', 'hide_empty' => true, 'number' => 7, 'exclude' => [get_option('default_product_cat')]]);
                        if (!is_wp_error($footer_cats)) :
                            foreach ($footer_cats as $cat) : ?>
                                <a href="<?php echo esc_url(get_term_link($cat)); ?>" class="text-sm text-fg/70 hover:text-fg transition-colors"><?php echo esc_html($cat->name); ?></a>
                            <?php endforeach;
                        endif; ?>
                    </nav>
                </div>
                <div>
                    <h4 class="text-2xs font-bold uppercase tracking-label text-muted mb-4">Klantenservice</h4>
                    <nav class="flex flex-col gap-2">
                        <a href="/contact" class="text-sm text-fg/70 hover:text-fg transition-colors">Contact</a>
                        <a href="/verzending" class="text-sm text-fg/70 hover:text-fg transition-colors">Verzending</a>
                        <a href="/retourneren" class="text-sm text-fg/70 hover:text-fg transition-colors">Retourneren</a>
                        <a href="/faq" class="text-sm text-fg/70 hover:text-fg transition-colors">FAQ</a>
                        <a href="/maatadvies" class="text-sm text-fg/70 hover:text-fg transition-colors">Maatadvies</a>
                    </nav>
                </div>
                <div>
                    <h4 class="text-2xs font-bold uppercase tracking-label text-muted mb-4">Over ons</h4>
                    <nav class="flex flex-col gap-2">
                        <a href="/ons-verhaal" class="text-sm text-fg/70 hover:text-fg transition-colors">Ons verhaal</a>
                        <a href="https://deklokjesman.com" target="_blank" rel="noopener" class="text-sm text-fg/70 hover:text-fg transition-colors">De Klokjesman</a>
                        <a href="https://parfum4car.nl" target="_blank" rel="noopener" class="text-sm text-fg/70 hover:text-fg transition-colors">Parfum4Car</a>
                    </nav>
                </div>
                <div>
                    <h4 class="text-2xs font-bold uppercase tracking-label text-muted mb-4">Volg ons</h4>
                    <div class="flex gap-2.5 mb-6">
                        <!-- Instagram -->
                        <a href="#" target="_blank" rel="noopener" class="w-10 h-10 flex items-center justify-center rounded-full border border-border hover:border-fg hover:bg-fg hover:text-white transition-all" aria-label="Instagram">
                            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="2" width="20" height="20" rx="5"/><path d="M16 11.37A4 4 0 1112.63 8 4 4 0 0116 11.37z"/><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"/></svg>
                        </a>
                        <!-- TikTok -->
                        <a href="#" target="_blank" rel="noopener" class="w-10 h-10 flex items-center justify-center rounded-full border border-border hover:border-fg hover:bg-fg hover:text-white transition-all" aria-label="TikTok">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="currentColor"><path d="M19.59 6.69a4.83 4.83 0 01-3.77-4.25V2h-3.45v13.67a2.89 2.89 0 01-2.88 2.5 2.89 2.89 0 01-2.89-2.89 2.89 2.89 0 012.89-2.89c.28 0 .54.04.79.1v-3.5a6.37 6.37 0 00-.79-.05A6.34 6.34 0 003.15 15.2a6.34 6.34 0 006.34 6.34 6.34 6.34 0 006.34-6.34V9.14a8.16 8.16 0 004.76 1.52v-3.4a4.85 4.85 0 01-1-.57z"/></svg>
                        </a>
                        <!-- YouTube -->
                        <a href="#" target="_blank" rel="noopener" class="w-10 h-10 flex items-center justify-center rounded-full border border-border hover:border-fg hover:bg-fg hover:text-white transition-all" aria-label="YouTube">
                            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22.54 6.42a2.78 2.78 0 00-1.94-2C18.88 4 12 4 12 4s-6.88 0-8.6.46a2.78 2.78 0 00-1.94 2A29 29 0 001 12a29 29 0 00.46 5.58A2.78 2.78 0 003.4 19.6C5.12 20 12 20 12 20s6.88 0 8.6-.46a2.78 2.78 0 001.94-2A29 29 0 0023 12a29 29 0 00-.46-5.58z"/><polygon points="9.75,15.02 15.5,12 9.75,8.98"/></svg>
                        </a>
                        <!-- WhatsApp -->
                        <a href="https://wa.me/31612345678" target="_blank" rel="noopener" class="w-10 h-10 flex items-center justify-center rounded-full border border-border hover:border-[#25D366] hover:bg-[#25D366] hover:text-white transition-all" aria-label="WhatsApp">
                            <svg width="17" height="17" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Payment methods -->
        <div class="flex flex-wrap items-center justify-center gap-2 py-6 border-t border-border">
            <!-- iDEAL -->
            <span class="h-8 px-3 bg-white border border-border rounded flex items-center gap-1.5 text-[10px] font-bold text-fg/80">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="10" stroke="#CC0066" stroke-width="2"/><path d="M12 6v8M8 14h8" stroke="#CC0066" stroke-width="2" stroke-linecap="round"/></svg>
                iDEAL
            </span>
            <!-- Visa -->
            <span class="h-8 px-3 bg-white border border-border rounded flex items-center text-[10px] font-bold text-[#1A1F71]">VISA</span>
            <!-- Mastercard -->
            <span class="h-8 px-3 bg-white border border-border rounded flex items-center gap-0.5">
                <svg width="20" height="12" viewBox="0 0 32 20"><circle cx="11" cy="10" r="8" fill="#EB001B" opacity="0.9"/><circle cx="21" cy="10" r="8" fill="#F79E1B" opacity="0.9"/><path d="M16 4a8 8 0 010 12 8 8 0 010-12z" fill="#FF5F00"/></svg>
            </span>
            <!-- Apple Pay -->
            <span class="h-8 px-3 bg-white border border-border rounded flex items-center text-[10px] font-bold text-fg">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor" class="mr-0.5"><path d="M18.71 19.5c-.83 1.24-1.71 2.45-3.05 2.47-1.34.03-1.77-.79-3.29-.79-1.53 0-2 .77-3.27.82-1.31.05-2.3-1.32-3.14-2.53C4.25 17 2.94 12.45 4.7 9.39c.87-1.52 2.43-2.48 4.12-2.51 1.28-.02 2.5.87 3.29.87.78 0 2.26-1.07 3.8-.91.65.03 2.47.26 3.64 1.98-.09.06-2.17 1.28-2.15 3.81.03 3.02 2.65 4.03 2.68 4.04-.03.07-.42 1.44-1.38 2.83"/><path d="M13 3.5c.73-.83 1.94-1.46 2.94-1.5.13 1.17-.34 2.35-1.04 3.19-.69.85-1.83 1.51-2.95 1.42-.15-1.15.41-2.35 1.05-3.11z"/></svg>
                Pay
            </span>
            <!-- Klarna -->
            <span class="h-8 px-3 bg-white border border-border rounded flex items-center text-[10px] font-bold" style="color:#FFB3C7">
                <svg width="40" height="10" viewBox="0 0 60 16" fill="#FFB3C7"><path d="M0 0h6v16H0zM9 0h5.5a8 8 0 01-3.5 6.6L15 16h-6L5.5 6.6A8 8 0 009 0zM18 0h6v16h-6zM28 4a3 3 0 110 6 3 3 0 010-6zm0 10a3 3 0 110-6 3 3 0 010 6zM36 0h6v16h-6zM46 4a3 3 0 110 6 3 3 0 010-6zm0 10a3 3 0 110-6 3 3 0 010 6zM54 4a3 3 0 110 6 3 3 0 010-6z"/></svg>
            </span>
            <!-- PayPal -->
            <span class="h-8 px-3 bg-white border border-border rounded flex items-center text-[10px] font-bold text-[#003087]">PayPal</span>
        </div>

        <!-- Copyright -->
        <div class="flex flex-col md:flex-row items-center justify-between gap-3 py-6 border-t border-border text-xs text-muted">
            <span>&copy; <?php echo date('Y'); ?> De Outfitman. Alle rechten voorbehouden.</span>
            <div class="flex gap-4">
                <a href="/privacy" class="hover:text-fg transition-colors">Privacy</a>
                <a href="/algemene-voorwaarden" class="hover:text-fg transition-colors">Voorwaarden</a>
                <a href="/cookies" class="hover:text-fg transition-colors">Cookies</a>
            </div>
        </div>
    </div>
</footer>

<?php get_template_part('template-parts/cart-drawer'); ?>

<script>
(function(){
    // Search overlay
    var so=document.getElementById('searchOverlay');
    document.querySelectorAll('.dom-search-toggle').forEach(function(b){
        b.addEventListener('click',function(){
            so.classList.toggle('open');
            if(so.classList.contains('open')){
                document.body.style.overflow='hidden';
                setTimeout(function(){so.querySelector('input[type="search"]').focus()},100);
            }else{
                document.body.style.overflow='';
            }
        });
    });
    document.querySelectorAll('.dom-search-close').forEach(function(b){
        b.addEventListener('click',function(){so.classList.remove('open');document.body.style.overflow='';});
    });
    document.addEventListener('keydown',function(e){
        if(e.key==='Escape'&&so.classList.contains('open')){so.classList.remove('open');document.body.style.overflow='';}
    });

    // Back to top
    var btt=document.getElementById('backToTop');
    if(btt){
        window.addEventListener('scroll',function(){
            if(window.scrollY>600){btt.style.opacity='1';btt.style.pointerEvents='auto';btt.style.transform='translateY(0)';}
            else{btt.style.opacity='0';btt.style.pointerEvents='none';btt.style.transform='translateY(1rem)';}
        },{passive:true});
        btt.addEventListener('click',function(){window.scrollTo({top:0,behavior:'smooth'});});
    }

    // Header border on scroll
    var hdr=document.getElementById('siteHeader');
    if(hdr){
        window.addEventListener('scroll',function(){
            hdr.style.borderColor=window.scrollY>20?'#E5E5E4':'transparent';
            hdr.style.boxShadow=window.scrollY>20?'0 1px 3px rgba(0,0,0,0.04)':'none';
        },{passive:true});
    }
})();
</script>

<?php wp_footer(); ?>
</body>
</html>
