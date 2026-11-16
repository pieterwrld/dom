<?php
/**
 * Homepage — De Outfitman (Black Friday Chrome)
 *
 * @package DomTheme
 */
defined('ABSPATH') || exit;
get_header();

$shop_url = get_permalink(wc_get_page_id('shop'));
?>

<!-- ======================= HERO ======================= -->
<section class="hero" id="hero">
  <div class="hero-media">
    <?php
    $hero_video = function_exists('get_field') ? get_field('hero_video', 'option') : '';
    $hero_poster = function_exists('get_field') ? get_field('hero_poster', 'option') : '';
    $poster_url = $hero_poster ? (is_array($hero_poster) ? $hero_poster['url'] : wp_get_attachment_url($hero_poster)) : '';
    $video_url = $hero_video ? (is_array($hero_video) ? $hero_video['url'] : wp_get_attachment_url($hero_video)) : '';
    ?>
    <?php if ($video_url) : ?>
      <video class="hv" id="heroVideo" muted loop playsinline preload="none"
             <?php if ($poster_url) echo 'poster="' . esc_url($poster_url) . '"'; ?>
             src="<?php echo esc_url($video_url); ?>"></video>
    <?php elseif ($poster_url) : ?>
      <img src="<?php echo esc_url($poster_url); ?>" alt="De Outfitman">
    <?php endif; ?>
  </div>
  <div class="hero-gl"><canvas id="glHero"></canvas></div>

  <div class="wrap hero-in">
    <div data-rv="fade">
      <div class="hero-ban">
        <span class="l"><i class="dot"></i><b>Black Friday live</b></span>
        <span class="r"><span>Tot -50% op de hele collectie</span></span>
      </div>
    </div>
    <h1 class="d1 split">
      <span class="l1">Nr. 1 in merkkleding</span>
      <span class="l2">Voor elke <span class="acc">man</span></span>
    </h1>
    <div class="hero-cta" data-rv="up" style="--d:520ms">
      <a href="<?php echo esc_url($shop_url); ?>?sale=1" class="btn btn-bf">Shop de Black Friday deals <span class="arw">&rarr;</span></a>
      <a href="<?php echo esc_url($shop_url); ?>" class="btn btn-o">Alle collecties</a>
    </div>
    <div class="hero-trust" data-rv="up" style="--d:640ms">
      <div class="ht">
        <svg viewBox="0 0 24 24"><path d="M3 7h11v9H3z"/><path d="M14 10h4l3 3v3h-7z"/><circle cx="7" cy="18" r="1.6"/><circle cx="17" cy="18" r="1.6"/></svg>
        <div><b>Voor 17:00</b><span>Morgen in huis</span></div>
      </div>
      <div class="ht">
        <svg viewBox="0 0 24 24"><path d="M12 3l7 3v6c0 4-3 7-7 9-4-2-7-5-7-9V6z"/><path d="M9 12l2 2 4-4"/></svg>
        <div><b>100% origineel</b><span>Gecontroleerd voor verzending</span></div>
      </div>
      <div class="ht">
        <svg viewBox="0 0 24 24"><path d="M12 2a10 10 0 0 0-8.6 15.1L2 22l5-1.3A10 10 0 1 0 12 2z"/></svg>
        <div><b><span data-count="800">0</span>+ klanten</b><span>24/7 via WhatsApp</span></div>
      </div>
    </div>
  </div>
  <div class="scrollind"><span>Scroll</span><i></i></div>
</section>

<!-- ======================= COLLECTIES 3x3 ======================= -->
<section class="sec" id="collecties">
  <div class="wrap">
    <div class="sec-t" data-rv="up">
      <div class="eyebrow"><i></i><span class="lbl">Collecties</span></div>
      <div class="sec-h">
        <h2 class="d2 split">Shop per categorie</h2>
        <a href="<?php echo esc_url($shop_url); ?>" class="lk">Alle producten <span class="arw">&rarr;</span></a>
      </div>
    </div>

    <div class="cgrid">
      <?php
      $categories = [
          'sneakers'         => ['label' => 'Sneakers',      'badge' => 'Bestseller', 'bf' => false],
          'jassen'           => ['label' => 'Jassen',         'badge' => '-25%',       'bf' => true],
          'jeans'            => ['label' => 'Jeans',          'badge' => '',           'bf' => false],
          'trainingspakken'  => ['label' => 'Trainings&shy;pakken', 'badge' => '',     'bf' => false],
          'sweaters-hoodies' => ['label' => 'Sweaters',       'badge' => '',           'bf' => false],
          't-shirts'         => ['label' => 'T&#8209;Shirts', 'badge' => '3 v. &euro;130', 'bf' => true],
          'zwembroeken'      => ['label' => 'Zwem&shy;broeken', 'badge' => '',         'bf' => false],
          'slippers'         => ['label' => 'Slippers',       'badge' => '',           'bf' => false],
          'sale'             => ['label' => 'Sale',            'badge' => 'Tot -50%',  'bf' => true],
      ];
      $delay = 0;
      foreach ($categories as $slug => $cat) :
          $term = get_term_by('slug', $slug, 'product_cat');
          if (!$term) continue;

          $thumb_id = get_term_meta($term->term_id, 'thumbnail_id', true);
          $thumb_url = $thumb_id ? wp_get_attachment_image_url($thumb_id, 'medium_large') : '';
          $cat_url = get_term_link($term);
          $is_sale = ($slug === 'sale');
      ?>
        <a href="<?php echo esc_url($cat_url); ?>" class="ctile<?php echo $is_sale ? ' saletile' : ''; ?>" data-rv="scale"<?php echo $delay > 0 ? ' style="--d:' . $delay . 'ms"' : ''; ?>>
          <?php if ($thumb_url) : ?>
            <img class="cim" data-up src="<?php echo esc_url($thumb_url); ?>" alt="<?php echo esc_attr($term->name); ?>" loading="lazy">
          <?php endif; ?>
          <span class="shade"></span>
          <?php if ($cat['badge']) : ?>
            <span class="cbadge<?php echo $cat['bf'] ? ' bf' : ''; ?>"><?php echo $cat['badge']; ?></span>
          <?php endif; ?>
          <span class="cline"></span>
          <div class="cbody">
            <div class="cname"><?php echo $cat['label']; ?></div>
            <div class="cmeta"><?php echo esc_html($term->count); ?> producten</div>
            <div class="cgo">Shop nu <span class="arw">&rarr;</span></div>
          </div>
        </a>
      <?php
          $delay += 50;
      endforeach;
      ?>
    </div>

    <a href="<?php echo esc_url($shop_url); ?>?deal=3voor130" class="dealbar" data-rv="up">
      <div>
        <h3>3 voor &euro;130</h3>
        <p>Combineer 3 T&#8209;shirts of zwembroeken. Korting automatisch bij het afrekenen.</p>
      </div>
      <span class="btn btn-ow">Stel je deal samen <span class="arw">&rarr;</span></span>
    </a>
  </div>
</section>

<!-- ======================= ACCESSOIRES ======================= -->
<section class="sec" style="padding-top:0">
  <div class="wrap">
    <div class="accblk" data-rv="up">
      <div class="acc-ims">
        <?php
        $acc_cats = ['brillen', 'petten', 'tassen'];
        foreach ($acc_cats as $acc_slug) :
            $acc_term = get_term_by('slug', $acc_slug, 'product_cat');
            $acc_thumb_id = $acc_term ? get_term_meta($acc_term->term_id, 'thumbnail_id', true) : 0;
            $acc_thumb = $acc_thumb_id ? wp_get_attachment_image_url($acc_thumb_id, 'medium_large') : '';
        ?>
          <figure>
            <?php if ($acc_thumb) : ?>
              <img data-up src="<?php echo esc_url($acc_thumb); ?>" alt="<?php echo esc_attr($acc_term ? $acc_term->name : ucfirst($acc_slug)); ?>" loading="lazy">
            <?php endif; ?>
          </figure>
        <?php endforeach; ?>
      </div>
      <div class="acc-in">
        <div>
          <span class="tagred">Accessoires</span>
          <h2 style="margin-top:10px" class="split">Maak je look compleet</h2>
          <p class="tx" style="--d:300ms">Het verschil tussen een outfit en een look. Bril, pet en tas uit dezelfde collectie.</p>
        </div>
        <div class="acc-btns">
          <?php
          $acc_links = [
              'brillen' => ['label' => 'Brillen', 'brands' => 'Dita &middot; Cartier'],
              'petten'  => ['label' => 'Petten',  'brands' => 'Loro Piana'],
              'tassen'  => ['label' => 'Tassen',  'brands' => 'Hermes &middot; Goyard'],
          ];
          foreach ($acc_links as $a_slug => $a_data) :
              $a_term = get_term_by('slug', $a_slug, 'product_cat');
              $a_url = $a_term ? get_term_link($a_term) : $shop_url;
          ?>
            <a href="<?php echo esc_url($a_url); ?>" class="accb">
              <svg class="ar" width="12" height="12" viewBox="0 0 24 24" stroke="#fff" stroke-width="2" fill="none"><path d="M7 17L17 7M9 7h8v8"/></svg>
              <b><?php echo $a_data['label']; ?></b><span><?php echo $a_data['brands']; ?></span>
            </a>
          <?php endforeach; ?>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ======================= DE KLOKJESMAN ======================= -->
<section class="sec" style="padding-top:0">
  <div class="wrap">
    <div class="kk" data-rv="up">
      <div class="kk-im">
        <?php
        $kk_img = function_exists('get_field') ? get_field('klokjesman_image', 'option') : '';
        $kk_url = $kk_img ? (is_array($kk_img) ? $kk_img['url'] : wp_get_attachment_url($kk_img)) : '';
        ?>
        <?php if ($kk_url) : ?>
          <img src="<?php echo esc_url($kk_url); ?>" alt="De Klokjesman" loading="lazy">
        <?php endif; ?>
      </div>
      <div class="kk-c">
        <h3 class="split">De <span class="silver">Klokjes</span>man</h3>
        <p class="tx" style="--d:260ms">De beste kwaliteit horloges voor de scherpste prijzen. Zelfde service, zelfde garantie, zelfde WhatsApp.</p>
        <a href="https://deklokjesman.com/" class="btn btn-bf" style="width:max-content" target="_blank" rel="noopener">Bekijk horloges <span class="arw">&rarr;</span></a>
        <div class="kk-stats">
          <div><b>24/7</b><span>WhatsApp</span></div>
          <div><b>1&ndash;2</b><span>Dagen levering</span></div>
          <div><b>14</b><span>Dagen retour</span></div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ======================= GEDRAGEN DOOR ======================= -->
<section class="sec cover-sec" id="wornby">
  <div class="wrap">
    <div class="cover-head">
      <h2 class="split">Gedragen door</h2>
      <p class="tx" style="--d:320ms">Ontdek De Outfitman door de ogen van onze creators.</p>
      <div class="cover-soc" data-rv="up" style="--d:420ms">
        <a href="https://www.tiktok.com/@deoutfitman" aria-label="TikTok" target="_blank" rel="noopener">
          <svg viewBox="0 0 24 24"><path d="M16 3c.4 2.3 1.9 3.9 4 4.2v3c-1.5.1-2.9-.3-4.1-1.1v6.2c0 3.6-2.9 6.2-6.2 5.6-2.5-.5-4.4-2.7-4.4-5.3 0-3.3 3-5.9 6.3-5.3v3.1c-1.4-.5-2.8.5-2.8 2 0 1.2 1 2.2 2.2 2.2s2.2-1 2.2-2.2V3z"/></svg>
        </a>
        <a href="https://www.instagram.com/deoutfitman/" aria-label="Instagram" target="_blank" rel="noopener">
          <svg viewBox="0 0 24 24"><path d="M12 7.5A4.5 4.5 0 1 0 16.5 12 4.5 4.5 0 0 0 12 7.5zm0 7.4A2.9 2.9 0 1 1 14.9 12 2.9 2.9 0 0 1 12 14.9zM17 3H7a4 4 0 0 0-4 4v10a4 4 0 0 0 4 4h10a4 4 0 0 0 4-4V7a4 4 0 0 0-4-4zm2.4 14a2.4 2.4 0 0 1-2.4 2.4H7A2.4 2.4 0 0 1 4.6 17V7A2.4 2.4 0 0 1 7 4.6h10A2.4 2.4 0 0 1 19.4 7zm-2-10.3a1 1 0 1 1-1-1 1 1 0 0 1 1 1z"/></svg>
        </a>
      </div>
    </div>
  </div>
  <div class="cover" id="cover"></div>
  <div class="cover-f">
    <button class="cvnav" id="cvPrev" aria-label="Vorige"><svg viewBox="0 0 24 24"><path d="M15 5l-7 7 7 7"/></svg></button>
    <div class="nm"><b id="cvName">Djezja</b><span id="cvSub">Shop de look</span></div>
    <button class="cvnav" id="cvNext" aria-label="Volgende"><svg viewBox="0 0 24 24"><path d="M9 5l7 7-7 7"/></svg></button>
  </div>
  <div class="cvdots" id="cvDots"></div>
</section>

<!-- ======================= REVIEWS ======================= -->
<section class="sec" id="reviews" style="background:var(--off);border-bottom:1px solid var(--line)">
  <div class="wrap">
    <div class="sec-t" data-rv="up">
      <div class="eyebrow"><i></i><span class="lbl">Reviews</span></div>
      <div class="sec-h">
        <div>
          <h2 class="d2 split">Wat onze klanten zeggen</h2>
          <div class="rev-stat" style="margin-top:14px">
            <div><div class="bigno"><span data-count="800">0</span>+</div><div class="lbl">Klanten geholpen</div></div>
            <div><div class="bigno">4.9</div><div class="lbl">Gemiddelde score</div></div>
          </div>
        </div>
        <div class="rvctl">
          <div class="rvdots" id="rvDots"></div>
          <button class="rnav" id="rvPrev" aria-label="Vorige"><svg viewBox="0 0 24 24"><path d="M15 5l-7 7 7 7"/></svg></button>
          <button class="rnav" id="rvNext" aria-label="Volgende"><svg viewBox="0 0 24 24"><path d="M9 5l7 7-7 7"/></svg></button>
        </div>
      </div>
    </div>
    <div class="rvwrap"><div class="rvtrack" id="rvTrack"></div></div>
    <div style="display:flex;align-items:center;gap:14px;margin-top:22px;flex-wrap:wrap" data-rv="up">
      <a href="https://wa.link/a36gtn" class="btn btn-dk" target="_blank" rel="noopener">Stel je vraag via WhatsApp <span class="arw">&rarr;</span></a>
      <span class="cap">Gemiddelde reactietijd onder 5 minuten</span>
    </div>
  </div>
</section>

<!-- ======================= USP ======================= -->
<section class="sec">
  <div class="wrap">
    <div class="usp" data-rv="up">
      <div class="ucard">
        <div class="uic"><svg viewBox="0 0 24 24"><path d="M12 3l7 3v6c0 4-3 7-7 9-4-2-7-5-7-9V6z"/><path d="M9 12l2 2 4-4"/></svg></div>
        <h4>Kwaliteitsgarantie</h4>
        <p>Elk product wordt voor verzending gecontroleerd. Topkwaliteit, zonder compromis.</p>
      </div>
      <div class="ucard">
        <div class="uic"><svg viewBox="0 0 24 24"><path d="M3 7h11v9H3z"/><path d="M14 10h4l3 3v3h-7z"/><circle cx="7" cy="18" r="1.6"/><circle cx="17" cy="18" r="1.6"/></svg></div>
        <h4>Morgen in huis</h4>
        <p>Voor 17:00 besteld. Altijd met trackingcode via een betrouwbare partner.</p>
      </div>
      <div class="ucard">
        <div class="uic"><svg viewBox="0 0 24 24"><rect x="4" y="11" width="16" height="9"/><path d="M8 11V8a4 4 0 0 1 8 0v3"/></svg></div>
        <h4>Veilig betalen</h4>
        <p>iDEAL, Klarna, Apple Pay en meer. Altijd via een beveiligde omgeving.</p>
      </div>
      <div class="ucard">
        <div class="uic"><svg viewBox="0 0 24 24"><path d="M12 2a10 10 0 0 0-8.6 15.1L2 22l5-1.3A10 10 0 1 0 12 2z"/></svg></div>
        <h4>24/7 WhatsApp</h4>
        <p>Vragen over maat of voorraad? Je hebt binnen 5 minuten antwoord.</p>
      </div>
    </div>
  </div>
</section>

<?php get_footer(); ?>
