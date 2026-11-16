<?php defined('ABSPATH') || exit; ?>
<form role="search" method="get" action="<?php echo esc_url(home_url('/')); ?>" class="flex gap-2">
    <input type="search" name="s" value="<?php echo get_search_query(); ?>" placeholder="<?php esc_attr_e('Zoeken...', 'dom-theme'); ?>" class="flex-1 px-4 py-3 bg-surface border border-border rounded-md text-sm placeholder:text-muted focus:outline-none focus:border-accent transition-colors" aria-label="<?php esc_attr_e('Zoeken', 'dom-theme'); ?>">
    <input type="hidden" name="post_type" value="product">
    <button type="submit" class="px-4 py-3 silver-gradient text-fg rounded-md" aria-label="<?php esc_attr_e('Zoeken', 'dom-theme'); ?>">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><circle cx="11" cy="11" r="7"/><line x1="16.5" y1="16.5" x2="21" y2="21"/></svg>
    </button>
</form>
