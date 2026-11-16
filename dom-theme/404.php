<?php
defined('ABSPATH') || exit;
get_header();
?>

<section class="sec" style="padding:clamp(80px,12vw,160px) 0">
    <div class="wrap" style="max-width:560px;text-align:center">
        <h1 class="d1 silver" style="margin-bottom:12px">404</h1>
        <p class="tx" style="color:var(--muted);margin-bottom:32px">Deze pagina bestaat niet meer of is verplaatst.</p>
        <a href="<?php echo esc_url(home_url('/')); ?>" class="btn btn-bf">
            Terug naar home
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12,5 19,12 12,19"/></svg>
        </a>
    </div>
</section>

<?php get_footer(); ?>
