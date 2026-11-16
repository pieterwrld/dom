<?php
/**
 * Announcement Marquee Bar
 * Uses .ann classes from core.css
 */

$messages = [];

if ( function_exists( 'have_rows' ) && have_rows( 'marquee_messages', 'option' ) ) {
	while ( have_rows( 'marquee_messages', 'option' ) ) {
		the_row();
		$messages[] = [
			'text'   => get_sub_field( 'message' ),
			'is_hot' => get_sub_field( 'is_hot' ),
		];
	}
}

if ( empty( $messages ) ) {
	$messages = [
		[ 'text' => 'Gratis verzending vanaf €200', 'is_hot' => false ],
		[ 'text' => 'BLACK FRIDAY DEALS', 'is_hot' => true ],
		[ 'text' => '14 dagen retourrecht', 'is_hot' => false ],
		[ 'text' => '24/7 WhatsApp support', 'is_hot' => false ],
		[ 'text' => '100% authentieke producten', 'is_hot' => false ],
		[ 'text' => 'TOT 50% KORTING', 'is_hot' => true ],
	];
}
?>
<div class="ann" role="marquee" aria-label="Aankondigingen">
	<div class="ann-t">
		<?php for ( $g = 0; $g < 2; $g++ ) : ?>
		<div class="ann-g">
			<?php foreach ( $messages as $msg ) :
				$hot = ! empty( $msg['is_hot'] );
			?>
			<span class="ann-i<?php echo $hot ? ' hot' : ''; ?>">
				<span class="st"></span>
				<?php if ( $hot ) : ?>
					<b><?php echo esc_html( $msg['text'] ); ?></b>
				<?php else : ?>
					<?php echo esc_html( $msg['text'] ); ?>
				<?php endif; ?>
			</span>
			<?php endforeach; ?>
		</div>
		<?php endfor; ?>
	</div>
</div>
