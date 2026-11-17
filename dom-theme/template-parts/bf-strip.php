<?php
/**
 * Black Friday Countdown Strip
 * Uses .bfs classes from core.css
 * Countdown target: November 27, 2026 23:59:59
 */
?>
<div class="bfs" id="bf-strip" data-end="2026-11-27T23:59:59">
	<div class="bfs-in wrap">
		<div class="bfs-t">
			<b>BLACK FRIDAY</b>
			<s>DEALS TOT 50% KORTING</s>
		</div>
		<div class="cd" id="bf-cd">
			<div class="u" data-unit="d">
				<b>00</b>
				<span>DAGEN</span>
			</div>
			<div class="u" data-unit="h">
				<b>00</b>
				<span>UREN</span>
			</div>
			<div class="u" data-unit="m">
				<b>00</b>
				<span>MIN</span>
			</div>
			<div class="u" data-unit="s">
				<b>00</b>
				<span>SEC</span>
			</div>
		</div>
		<?php
		// get_term_link() geeft een WP_Error als de term niet bestaat, en een
		// WP_Error is truthy: die glipt dus door een ?:-fallback heen.
		$dom_sale_link = dom_term_url( 'sale', 'product_cat' );
		if ( '' === $dom_sale_link ) {
			$dom_sale_link = dom_shop_url();
		}
		?>
		<a href="<?php echo esc_url( $dom_sale_link ); ?>" class="go">
			SHOP NU <span class="arw">&rarr;</span>
		</a>
	</div>
	<button class="bfs-close" aria-label="Sluiten">
		<svg viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
	</button>
</div>
