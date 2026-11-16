<?php
defined('ABSPATH') || exit;

$reviews = [];
if (function_exists('get_field')) {
    $acf_reviews = get_field('homepage_reviews', 'option');
    if (is_array($acf_reviews)) {
        $reviews = $acf_reviews;
    }
}

$review_count = count($reviews) ?: 12;
$avg_rating   = 4.9;
?>
<section class="sec" id="reviews" style="background:var(--off);border-bottom:1px solid var(--line)">
  <div class="wrap">
    <div class="sec-t" data-rv="up">
      <div class="eyebrow"><i></i><span class="lbl">Reviews</span></div>
      <div class="sec-h">
        <h2 class="d2 split">Wat onze klanten zeggen</h2>
      </div>
      <div class="rev-stat">
        <div class="bigno"><span data-count="<?php echo (int) $review_count; ?>">0</span><small>reviews</small></div>
        <div class="bigno"><span data-count="<?php echo number_format($avg_rating, 1); ?>">0</span><small>gemiddeld</small></div>
        <div class="bigno"><span data-count="98">0</span><small>% aanraden</small></div>
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
        <?php if ($reviews) : ?>
          <?php foreach ($reviews as $r) :
            $name   = $r['reviewer_name'] ?? 'Klant';
            $stars  = (int) ($r['rating'] ?? 5);
            $date   = $r['date'] ?? '';
            $img    = '';
            if (!empty($r['screenshot'])) {
                $img = is_array($r['screenshot']) ? ($r['screenshot']['url'] ?? '') : $r['screenshot'];
            }
          ?>
            <div class="rev">
              <div class="rev-top">
                <div class="rev-av"><?php echo esc_html(mb_substr($name, 0, 1)); ?></div>
                <div>
                  <b><?php echo esc_html($name); ?></b>
                  <div class="stars"><?php echo str_repeat('&#9733;', $stars); ?></div>
                </div>
                <?php if ($date) : ?><time><?php echo esc_html($date); ?></time><?php endif; ?>
              </div>
              <?php if ($img) : ?>
                <div class="rev-shot"><img src="<?php echo esc_url($img); ?>" alt="Review van <?php echo esc_attr($name); ?>" loading="lazy"></div>
              <?php endif; ?>
              <div class="rev-foot">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="#25D366"><path d="M12 2a10 10 0 0 0-8.6 15.1L2 22l5-1.3A10 10 0 1 0 12 2z"/></svg>
                <span>via WhatsApp</span>
              </div>
            </div>
          <?php endforeach; ?>
        <?php else : ?>
          <?php
          $names = ['Mohammed', 'Dylan', 'Youssef', 'Jayden', 'Achraf', 'Bilal'];
          for ($i = 0; $i < 6; $i++) : ?>
            <div class="rev">
              <div class="rev-top">
                <div class="rev-av"><?php echo mb_substr($names[$i], 0, 1); ?></div>
                <div>
                  <b><?php echo $names[$i]; ?></b>
                  <div class="stars">&#9733;&#9733;&#9733;&#9733;&#9733;</div>
                </div>
              </div>
              <div class="rev-shot"><div class="ph" style="height:200px"><span class="ph-l">Review screenshot</span></div></div>
              <div class="rev-foot">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="#25D366"><path d="M12 2a10 10 0 0 0-8.6 15.1L2 22l5-1.3A10 10 0 1 0 12 2z"/></svg>
                <span>via WhatsApp</span>
              </div>
            </div>
          <?php endfor; ?>
        <?php endif; ?>
      </div>
    </div>

    <div style="text-align:center;margin-top:40px" data-rv="up">
      <a href="https://wa.me/31612345678" class="btn btn-bf" target="_blank" rel="noopener">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor" style="margin-right:8px"><path d="M12 2a10 10 0 0 0-8.6 15.1L2 22l5-1.3A10 10 0 1 0 12 2z"/></svg>
        Stuur ons een bericht
      </a>
    </div>
  </div>
</section>
<script>document.addEventListener('DOMContentLoaded',function(){if(window.DOM&&DOM.buildReviews)DOM.buildReviews('#rvTrack','#rvDots','#rvPrev','#rvNext')});</script>
