<?php defined('ABSPATH') || exit; ?>
<form role="search" method="get" action="<?php echo esc_url(home_url('/')); ?>" class="dom-search">
	<label class="scr" for="dom-search-field-<?php echo esc_attr(wp_unique_id()); ?>"><?php esc_html_e('Zoeken', 'dom-theme'); ?></label>
	<input
		type="search"
		class="dom-search__input"
		name="s"
		value="<?php echo esc_attr(get_search_query()); ?>"
		placeholder="<?php esc_attr_e('Waar ben je naar op zoek?', 'dom-theme'); ?>"
		aria-label="<?php esc_attr_e('Zoeken', 'dom-theme'); ?>">
	<input type="hidden" name="post_type" value="product">
	<button type="submit" class="dom-search__btn" aria-label="<?php esc_attr_e('Zoeken', 'dom-theme'); ?>">
		<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><circle cx="11" cy="11" r="7"/><line x1="16.5" y1="16.5" x2="21" y2="21"/></svg>
	</button>
</form>
