<?php
defined('ABSPATH') || exit;

/**
 * "Gedragen door": de coverflow uit het ontwerp.
 *
 * Namen van echte personen horen bij hun eigen foto, dus de koppeling komt
 * letterlijk uit het ontwerpprototype en wordt niet door het thema verzonnen.
 * Staat er beheerde data klaar, dan gaat die voor.
 */
$influencers = [];

if (function_exists('get_field')) {
    $acf = get_field('influencers', 'option');
    if (is_array($acf)) {
        foreach ($acf as $row) {
            $img = '';
            if (!empty($row['image'])) {
                $img = is_array($row['image']) ? ($row['image']['url'] ?? '') : $row['image'];
            }
            if (!$img) {
                continue;
            }
            $influencers[] = [
                'name' => $row['name'] ?? '',
                'sub'  => $row['handle'] ?? '',
                'img'  => $img,
            ];
        }
    }
}

if (!$influencers) {
    $uploads = wp_get_upload_dir();
    $base    = $uploads['baseurl'] ?? '';

    foreach ([
        ['Djezja',         'Shop de look', '2025/09/11.jpg'],
        ['Furkan',         'Shop de look', '2025/09/12.jpg'],
        ['Kubi de Barber', 'Shop de look', '2025/09/13.jpg'],
        ['Fato YT',        'Shop de look', '2025/09/14.jpg'],
        ['De Outfitman',   'Winter drop',  '2025/09/17.jpg'],
        ['De Outfitman',   'Black Friday', '2025/09/19.jpg'],
    ] as [$name, $sub, $file]) {
        $path = ($uploads['basedir'] ?? '') . '/' . $file;
        if (!$base || !file_exists($path)) {
            continue;
        }
        $influencers[] = ['name' => $name, 'sub' => $sub, 'img' => $base . '/' . $file];
    }
}

if (!$influencers) {
    return;
}

$dom_cover_shop = dom_shop_url();
?>
<section class="sec cover-sec" id="wornby">
  <div class="wrap">
    <div class="cover-head" data-rv="up">
      <div class="eyebrow"><i></i><span class="lbl">Community</span></div>
      <h2 class="split">Gedragen door</h2>
      <p class="tx">Influencers, atleten en creatives die De Outfitman dragen.</p>
      <div class="cover-soc">
        <a href="https://www.tiktok.com/@deoutfitman" target="_blank" rel="noopener" aria-label="TikTok">
          <svg viewBox="0 0 24 24"><path d="M16 3c.4 2.3 1.9 3.9 4 4.2v3c-1.5.1-2.9-.3-4.1-1.1v6.2c0 3.6-2.9 6.2-6.2 5.6-2.5-.5-4.4-2.7-4.4-5.3 0-3.3 3-5.9 6.3-5.3v3.1c-1.4-.5-2.8.5-2.8 2 0 1.2 1 2.2 2.2 2.2s2.2-1 2.2-2.2V3z"/></svg>
        </a>
        <a href="https://www.instagram.com/deoutfitman/" target="_blank" rel="noopener" aria-label="Instagram">
          <svg viewBox="0 0 24 24"><path d="M12 7.5A4.5 4.5 0 1 0 16.5 12 4.5 4.5 0 0 0 12 7.5zm0 7.4A2.9 2.9 0 1 1 14.9 12 2.9 2.9 0 0 1 12 14.9zM17 3H7a4 4 0 0 0-4 4v10a4 4 0 0 0 4 4h10a4 4 0 0 0 4-4V7a4 4 0 0 0-4-4zm2.4 14a2.4 2.4 0 0 1-2.4 2.4H7A2.4 2.4 0 0 1 4.6 17V7A2.4 2.4 0 0 1 7 4.6h10A2.4 2.4 0 0 1 19.4 7zm-2-10.3a1 1 0 1 1-1-1 1 1 0 0 1 1 1z"/></svg>
        </a>
      </div>
    </div>
  </div>

  <div class="cover" id="cover">
    <?php foreach ($influencers as $inf) : ?>
      <div class="cv" data-name="<?php echo esc_attr($inf['name']); ?>" data-sub="<?php echo esc_attr($inf['sub']); ?>">
        <img src="<?php echo esc_url($inf['img']); ?>" alt="<?php echo esc_attr($inf['name']); ?>" loading="lazy">
        <span class="rim"></span>
        <div class="cvb">
          <b><?php echo esc_html($inf['name']); ?></b>
          <a class="shop" href="<?php echo esc_url($dom_cover_shop); ?>">Shop nu <span class="arw">&rarr;</span></a>
        </div>
      </div>
    <?php endforeach; ?>
  </div>

  <div class="cover-f">
    <button class="cvnav" id="cvPrev" aria-label="Vorige">
      <svg viewBox="0 0 24 24"><path d="M15 5l-7 7 7 7"/></svg>
    </button>
    <div class="nm">
      <b id="cvName"></b>
      <span id="cvSub"></span>
    </div>
    <button class="cvnav" id="cvNext" aria-label="Volgende">
      <svg viewBox="0 0 24 24"><path d="M9 5l7 7-7 7"/></svg>
    </button>
  </div>
  <div class="cvdots" id="cvDots"></div>
</section>
<script>document.addEventListener('DOMContentLoaded',function(){if(window.DOM&&DOM.buildCover)DOM.buildCover('#cover')});</script>
