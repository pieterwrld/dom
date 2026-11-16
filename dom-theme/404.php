<?php
defined('ABSPATH') || exit;
get_header();
?>

<div class="max-w-lg mx-auto px-4 py-24 text-center">
    <h1 class="text-6xl font-black silver-text mb-4">404</h1>
    <p class="text-lg text-muted mb-8">Deze pagina bestaat niet meer of is verplaatst.</p>
    <a href="<?php echo esc_url(home_url('/')); ?>" class="inline-flex items-center gap-2 px-6 py-3 silver-gradient text-fg rounded-md text-sm font-bold uppercase tracking-label hover:shadow-lg transition-shadow">
        Terug naar home
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12,5 19,12 12,19"/></svg>
    </a>
</div>

<?php get_footer(); ?>
