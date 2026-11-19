<?php
/**
 * Black Friday-balk met aftelklok.
 *
 * Tekst, einddatum en of de balk überhaupt verschijnt staan in het
 * beheerscherm onder De Outfitman > Homepage.
 */

defined('ABSPATH') || exit;

if (!dom_c_bool('home.bf.aan', true)) {
	return;
}

$dom_bf_label = dom_c('home.bf.label', 'BLACK FRIDAY');
$dom_bf_einde = dom_c('home.bf.einde', '2026-11-27T23:59:59');
$dom_bf_knop  = dom_c('home.bf.knop', 'SHOP NU');

/**
 * De korting die hier staat moet kloppen met de prijzen in de winkel. Een vast
 * "tot 50%" is een prijsclaim die niemand nakijkt en die zomaar onjuist kan
 * zijn. Vult de winkel zelf niets in, dan rekenen we de hoogste werkelijke
 * korting uit en laten we de zin weg als er niets in de aanbieding is.
 */
$dom_bf_claim = (string) dom_c('home.bf.claim', '');
if ($dom_bf_claim === '') {
	$dom_bf_pct   = function_exists('dom_max_sale_percentage') ? dom_max_sale_percentage() : 0;
	$dom_bf_claim = $dom_bf_pct > 0 ? sprintf('DEALS TOT %d%% KORTING', $dom_bf_pct) : '';
}
?>
<div class="bfs" id="bf-strip" data-end="<?php echo esc_attr($dom_bf_einde); ?>">
	<div class="bfs-in wrap">
		<div class="bfs-t">
			<b><?php echo esc_html($dom_bf_label); ?></b>
			<?php if ($dom_bf_claim) : ?><s><?php echo esc_html($dom_bf_claim); ?></s><?php endif; ?>
		</div>
		<?php // Zonder deze regel staan er alleen vier getallen en is niet te zien waar ze over gaan. ?>
		<span class="bfs-eind"><?php esc_html_e('eindigt over', 'dom-theme'); ?></span>
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
			<?php echo esc_html( $dom_bf_knop ); ?> <span class="arw">&rarr;</span>
		</a>
	</div>
	<button class="bfs-close" aria-label="Sluiten">
		<svg viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
	</button>
</div>
