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
  <?php
  // Beheer: De Outfitman > Footer.
  $ft_trust = dom_c_rows('footer.trust.items', [
      ['icoon' => 'shield', 'tekst' => 'Veilig betalen'],
      ['icoon' => 'return', 'tekst' => 'Gratis retour'],
      ['icoon' => 'truck',  'tekst' => 'Snelle levering'],
  ]);
  $ft_ster = '<svg width="13" height="13" viewBox="0 0 20 20"><polygon points="10,1 12.6,7.4 19.5,7.6 14,11.8 15.8,18.8 10,14.8 4.2,18.8 6,11.8 0.5,7.6 7.4,7.4" fill="currentColor"/></svg>';
  ?>
  <div class="wrap">
    <div class="ft-trust">
      <div class="ftt">
        <div class="stars"><?php echo str_repeat($ft_ster, 5); // phpcs:ignore ?></div>
        <b><?php echo esc_html(dom_c('footer.trust.klanten', '1.200+')); ?></b>
        <span><?php echo esc_html(dom_c('footer.trust.klanten_label', 'Tevreden klanten')); ?></span>
      </div>
      <?php foreach ($ft_trust as $item) : ?>
        <div class="ftt">
          <?php echo dom_icon($item['icoon'] ?? 'check'); // phpcs:ignore ?>
          <b><?php echo esc_html($item['tekst'] ?? ''); ?></b>
        </div>
      <?php endforeach; ?>
    </div>
  </div>

  <!-- 4-column grid -->
  <div class="ft-top wrap">
    <?php
    foreach (['kolom_1' => 'Klantenservice', 'kolom_2' => 'Over ons'] as $kolom => $standaard_kop) :
        $links = dom_c_rows('footer.' . $kolom . '.links', []);
    ?>
      <div class="ft-col">
        <h5><?php echo esc_html(dom_c('footer.' . $kolom . '.kop', $standaard_kop)); ?></h5>
        <?php if ($links) : ?>
          <ul>
            <?php foreach ($links as $link) : ?>
              <li><a href="<?php echo esc_url($link['url'] ?? '#'); ?>"><?php echo esc_html($link['tekst'] ?? ''); ?></a></li>
            <?php endforeach; ?>
          </ul>
        <?php endif; ?>

        <?php if ($kolom === 'kolom_2') :
          $insta  = dom_c('footer.kolom_2.instagram', 'https://instagram.com/deoutfitman');
          $tiktok = dom_c('footer.kolom_2.tiktok', 'https://tiktok.com/@deoutfitman');
          if ($insta || $tiktok) : ?>
          <div class="socs">
            <?php if ($insta) : ?>
              <a href="<?php echo esc_url($insta); ?>" target="_blank" rel="noopener" class="soc" aria-label="Instagram">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><rect x="2" y="2" width="20" height="20" rx="5"/><circle cx="12" cy="12" r="4.5"/><circle cx="17.5" cy="6.5" r="1.2" fill="currentColor" stroke="none"/></svg>
              </a>
            <?php endif; ?>
            <?php if ($tiktok) : ?>
              <a href="<?php echo esc_url($tiktok); ?>" target="_blank" rel="noopener" class="soc" aria-label="TikTok">
                <svg width="15" height="15" viewBox="0 0 24 24"><path d="M19.59 6.69a4.83 4.83 0 01-3.77-4.25V2h-3.45v13.67a2.89 2.89 0 01-2.88 2.5 2.89 2.89 0 01-2.89-2.89 2.89 2.89 0 012.89-2.89c.28 0 .54.04.79.1v-3.5a6.37 6.37 0 00-.79-.05A6.34 6.34 0 003.15 15.2a6.34 6.34 0 006.34 6.34 6.34 6.34 0 006.34-6.34V9.14a8.16 8.16 0 004.76 1.52v-3.4a4.85 4.85 0 01-1-.57z" fill="currentColor"/></svg>
              </a>
            <?php endif; ?>
          </div>
        <?php endif; endif; ?>
      </div>
    <?php endforeach; ?>
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
