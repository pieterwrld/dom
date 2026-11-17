<?php
/**
 * Announcement Marquee Bar
 * Uses .ann classes from core.css
 */

$messages = function_exists( 'dom_marquee_messages' ) ? dom_marquee_messages() : [];

if ( empty( $messages ) ) {
	return;
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
