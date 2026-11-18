<?php
defined('ABSPATH') || exit;

/**
 * Reviews: echte WhatsApp-screenshots.
 * ACF-veld 'homepage_reviews' heeft voorrang; anders de screenshots die in het
 * thema zitten (assets/img/reviews). Er worden nooit namen of teksten verzonnen:
 * de kaarten tonen de chat zoals hij is, met De Outfitman als afzender.
 */

$reviews = [];
if (function_exists('get_field')) {
    $acf_reviews = get_field('homepage_reviews', 'option');
    if (is_array($acf_reviews)) {
        $reviews = $acf_reviews;
    }
}

if (!$reviews) {
    $dir = get_stylesheet_directory() . '/assets/img/reviews';
    $uri = get_stylesheet_directory_uri() . '/assets/img/reviews';
    if (is_dir($dir)) {
        foreach (glob($dir . '/review-*.webp') ?: [] as $path) {
            $reviews[] = ['screenshot' => $uri . '/' . basename($path)];
        }
    }
}

$review_count = count($reviews);
if (!$review_count) {
    return;
}

$wa_number = '31616264544';
$star      = '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 2l2.9 6.2 6.6.9-4.8 4.6 1.2 6.6L12 17.2 6.1 20.3l1.2-6.6L2.5 9.1l6.6-.9z"/></svg>';
?>
<section class="sec" id="reviews" style="background:var(--off);border-bottom:1px solid var(--line)">
  <div class="wrap">
    <div class="sec-t" data-rv="up">
      <div class="eyebrow"><i></i><span class="lbl">Reviews</span></div>
      <div class="sec-h">
        <div>
          <h2 class="d2 split">Wat onze klanten zeggen</h2>
          <p class="rev-sub">
            <?php
            printf(
                esc_html(
                    /* translators: %s is het aantal getoonde gesprekken. */
                    _n('%s echt gesprek, rechtstreeks uit WhatsApp', '%s echte gesprekken, rechtstreeks uit WhatsApp', $review_count, 'dom-theme')
                ),
                '<b>' . esc_html(number_format_i18n($review_count)) . '</b>'
            );
            ?>
          </p>
        </div>
      </div>
      <div class="rvctl">
        <div class="rvdots" id="rvDots"></div>
        <div style="display:flex;gap:6px">
          <button class="cvnav" id="rvPrev" aria-label="Vorige reviews">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M15 18l-6-6 6-6"/></svg>
          </button>
          <button class="cvnav" id="rvNext" aria-label="Volgende reviews">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 18l6-6-6-6"/></svg>
          </button>
        </div>
      </div>
    </div>

    <div class="rvwrap">
      <div class="rvtrack" id="rvTrack">
        <?php foreach ($reviews as $r) :
            $img = '';
            if (!empty($r['screenshot'])) {
                $img = is_array($r['screenshot']) ? ($r['screenshot']['url'] ?? '') : $r['screenshot'];
            }
            if (!$img) {
                continue;
            }
        ?>
          <div class="rev">
            <div class="rev-top">
              <span class="rev-av">DO</span>
              <div>
                <div class="nm">De Outfitman</div>
                <div class="on">online</div>
              </div>
              <svg class="wa" viewBox="0 0 32 32" fill="#25D366" aria-hidden="true"><path d="M16 3C8.8 3 3 8.8 3 16c0 2.3.6 4.5 1.7 6.4L3 29l6.8-1.8A13 13 0 1 0 16 3zm0 23.7c-2 0-4-.5-5.7-1.6l-.4-.2-4 1 1.1-3.9-.3-.4A10.7 10.7 0 1 1 16 26.7zm6.1-7.9c-.3-.2-2-1-2.3-1.1-.3-.1-.5-.2-.8.2s-.9 1.1-1.1 1.3c-.2.2-.4.3-.8.1a8.7 8.7 0 0 1-4.3-3.8c-.3-.6.3-.5.9-1.7.1-.2 0-.4 0-.6l-1-2.5c-.3-.7-.6-.6-.8-.6h-.7c-.2 0-.6.1-.9.5-.3.4-1.2 1.2-1.2 2.9s1.2 3.3 1.4 3.6c.2.2 2.4 3.8 6 5.3 2.2.9 3.1 1 4.2.8.7-.1 2-.8 2.3-1.6.3-.8.3-1.5.2-1.6-.1-.2-.3-.3-.6-.4z"/></svg>
            </div>
            <div class="rev-phone">
              <span class="rev-island" aria-hidden="true"></span>
              <div class="rev-shot">
                <img src="<?php echo esc_url($img); ?>" alt="WhatsApp bericht van een klant" loading="lazy" decoding="async" width="620" height="1066">
              </div>
            </div>
            <div class="rev-foot">
              <div class="stars" aria-label="5 van de 5 sterren"><?php echo str_repeat($star, 5); ?></div>
              <span class="vf">
                <svg viewBox="0 0 24 24"><path d="M4 12l5 5L20 6"/></svg>
                Geverifieerd
              </span>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>

    <div style="text-align:center;margin-top:40px" data-rv="up">
      <a href="https://wa.me/<?php echo esc_attr($wa_number); ?>" class="btn btn-bf" target="_blank" rel="noopener">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor" style="margin-right:8px"><path d="M12 2a10 10 0 0 0-8.6 15.1L2 22l5-1.3A10 10 0 1 0 12 2z"/></svg>
        Stuur ons een bericht
      </a>
    </div>
  </div>
</section>
<script>document.addEventListener('DOMContentLoaded',function(){if(window.DOM&&DOM.buildReviews)DOM.buildReviews('#rvTrack','#rvDots','#rvPrev','#rvNext')});</script>
