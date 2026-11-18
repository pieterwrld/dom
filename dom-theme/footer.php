</main>

<footer class="ft" role="contentinfo">

  <!-- Logo marquee -->
  <div class="ftm" aria-hidden="true">
    <div class="ftm-t">
      <div class="ftm-g">
        <span>DE OUTFITMAN</span>
        <span>DE OUTFITMAN</span>
        <span>DE OUTFITMAN</span>
        <span>DE OUTFITMAN</span>
        <span>DE OUTFITMAN</span>
        <span>DE OUTFITMAN</span>
      </div>
      <div class="ftm-g">
        <span>DE OUTFITMAN</span>
        <span>DE OUTFITMAN</span>
        <span>DE OUTFITMAN</span>
        <span>DE OUTFITMAN</span>
        <span>DE OUTFITMAN</span>
        <span>DE OUTFITMAN</span>
      </div>
    </div>
  </div>

  <div class="wrap">
    <div class="hair"></div>
  </div>

  <!-- Trust bar -->
  <div class="wrap">
    <div class="ft-trust">
      <div class="ftt">
        <div class="stars">
          <svg width="13" height="13" viewBox="0 0 20 20"><polygon points="10,1 12.6,7.4 19.5,7.6 14,11.8 15.8,18.8 10,14.8 4.2,18.8 6,11.8 0.5,7.6 7.4,7.4" fill="currentColor"/></svg>
          <svg width="13" height="13" viewBox="0 0 20 20"><polygon points="10,1 12.6,7.4 19.5,7.6 14,11.8 15.8,18.8 10,14.8 4.2,18.8 6,11.8 0.5,7.6 7.4,7.4" fill="currentColor"/></svg>
          <svg width="13" height="13" viewBox="0 0 20 20"><polygon points="10,1 12.6,7.4 19.5,7.6 14,11.8 15.8,18.8 10,14.8 4.2,18.8 6,11.8 0.5,7.6 7.4,7.4" fill="currentColor"/></svg>
          <svg width="13" height="13" viewBox="0 0 20 20"><polygon points="10,1 12.6,7.4 19.5,7.6 14,11.8 15.8,18.8 10,14.8 4.2,18.8 6,11.8 0.5,7.6 7.4,7.4" fill="currentColor"/></svg>
          <svg width="13" height="13" viewBox="0 0 20 20"><polygon points="10,1 12.6,7.4 19.5,7.6 14,11.8 15.8,18.8 10,14.8 4.2,18.8 6,11.8 0.5,7.6 7.4,7.4" fill="currentColor"/></svg>
        </div>
        <b>1200+ klanten</b>
        <span>Geholpen via WhatsApp</span>
      </div>
      <div class="ftt">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
        <b>Veilig betalen</b>
      </div>
      <div class="ftt">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"><rect x="1" y="3" width="22" height="5" rx="0"/><path d="M21 8v13H3V8"/><path d="M10 12h4"/></svg>
        <b>Gratis retour</b>
      </div>
      <div class="ftt">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"><path d="M5 12h14"/><path d="M12 5l7 7-7 7"/></svg>
        <b>Snelle levering</b>
      </div>
    </div>
  </div>

  <!-- 4-column grid -->
  <div class="ft-top wrap">

    <!-- Newsletter -->
    <div class="ft-news">
      <h4>Blijf op de hoogte</h4>
      <p>Ontvang exclusieve aanbiedingen, early access op nieuwe collecties en style tips — direct in je inbox.</p>
      <?php
      // Alleen een werkend formulier tonen. MC4WP is de aanmeldlijst; zonder die
      // plugin zou het formulier inzendingen stilzwijgend weggooien.
      if (function_exists('mc4wp_show_form')) {
          mc4wp_show_form();
      } else {
          printf(
              '<p class="nform-alt"><a class="btn btn-bf" href="%s" target="_blank" rel="noopener">Aanmelden via WhatsApp</a></p>',
              esc_url('https://wa.me/31616264544')
          );
      }
      ?>
    </div>

    <!-- Shop -->
    <div class="ft-col">
      <h5>Shop</h5>
      <ul>
        <li><a href="<?php echo esc_url(dom_shop_url()); ?>">Alle producten</a></li>
        <?php
        $ft_cats = ['sneakers','jassen','jeans','trainingspakken','sweaters-hoodies','t-shirts','sale'];
        foreach ($ft_cats as $slug) :
          $term = get_term_by('slug', $slug, 'product_cat');
          if ($term) : ?>
            <li><a href="<?php echo esc_url(dom_term_url($term)); ?>"><?php echo esc_html($term->name); ?></a></li>
          <?php endif;
        endforeach; ?>
      </ul>
    </div>

    <!-- Klantenservice -->
    <div class="ft-col">
      <h5>Klantenservice</h5>
      <ul>
        <li><a href="/contact">Contact</a></li>
        <li><a href="/faq">FAQ</a></li>
        <li><a href="/verzending-retourneren">Verzending &amp; Retourneren</a></li>
        <li><a href="/ruilen-retourneren">Ruilen &amp; Retourneren</a></li>
        <li><a href="/betaalmethoden">Betaalmethoden</a></li>
      </ul>
    </div>

    <!-- Over Ons + Volg Ons -->
    <div class="ft-col">
      <h5>Over ons</h5>
      <ul>
        <li><a href="/ons-verhaal">Ons Verhaal</a></li>
        <li><a href="https://deklokjesman.com" target="_blank" rel="noopener">De Klokjesman</a></li>
      </ul>
      <div class="socs">
        <!-- Instagram -->
        <a href="https://instagram.com/deoutfitman" target="_blank" rel="noopener" class="soc" aria-label="Instagram">
          <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><rect x="2" y="2" width="20" height="20" rx="5"/><circle cx="12" cy="12" r="4.5"/><circle cx="17.5" cy="6.5" r="1.2" fill="currentColor" stroke="none"/></svg>
        </a>
        <!-- TikTok -->
        <a href="https://tiktok.com/@deoutfitman" target="_blank" rel="noopener" class="soc" aria-label="TikTok">
          <svg width="15" height="15" viewBox="0 0 24 24"><path d="M19.59 6.69a4.83 4.83 0 01-3.77-4.25V2h-3.45v13.67a2.89 2.89 0 01-2.88 2.5 2.89 2.89 0 01-2.89-2.89 2.89 2.89 0 012.89-2.89c.28 0 .54.04.79.1v-3.5a6.37 6.37 0 00-.79-.05A6.34 6.34 0 003.15 15.2a6.34 6.34 0 006.34 6.34 6.34 6.34 0 006.34-6.34V9.14a8.16 8.16 0 004.76 1.52v-3.4a4.85 4.85 0 01-1-.57z" fill="currentColor"/></svg>
        </a>
      </div>
    </div>
  </div>

  <!-- Bottom bar -->
  <div class="wrap">
    <div class="ft-bot">
      <span>&copy; <?php echo date('Y'); ?> De Outfitman. Alle rechten voorbehouden.</span>
    </div>
  </div>

</footer>

<?php
/**
 * Verduisterende laag achter de winkelwagen en het menu.
 *
 * De CSS en de JS gingen allebei uit van dit element, maar het stond nergens in
 * het thema: de drawer schoof open zonder achtergrond en sloot niet als je
 * ernaast klikte. De JS zoekt op .js-overlay, de CSS stijlt .ovl.
 */
?>
<div class="ovl js-overlay" id="ovl" aria-hidden="true"></div>

<?php get_template_part('template-parts/cart-drawer'); ?>

<?php wp_footer(); ?>
</body>
</html>
