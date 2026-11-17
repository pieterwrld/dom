<?php
/**
 * Plugin Name: DOM Checkout Redirect
 * Description: Stuurt de verouderde /checkout/-pagina door naar de echte afrekenpagina.
 * Version: 1.0.0
 *
 * Deploy: kopieer naar /wp-content/mu-plugins/dom-checkout-redirect.php
 *
 * Waarom dit nodig is: bij de installatie van WooCommerce is een pagina
 * "Checkout" (ID 10) aangemaakt, maar de winkel gebruikt /shop-checkout/
 * (ID 6324). Omdat pagina 10 niet de ingestelde afrekenpagina is, sluit
 * LiteSpeed hem niet uit van caching. Er staat een WooCommerce Blocks-
 * checkout op met een nonce die mee gecachet wordt, dus wie daar belandt
 * krijgt "Nonce is ongeldig" en kan niet bestellen.
 *
 * De site linkt er nergens naartoe en de pagina staat op noindex, dus dit
 * raakt alleen wie hem bewaard heeft of intypt. Dat is precies het geval
 * dat je niet wilt verliezen.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'template_redirect', function () {
	if ( is_admin() || ! function_exists( 'wc_get_checkout_url' ) ) {
		return;
	}

	$checkout_id = (int) wc_get_page_id( 'checkout' );
	if ( $checkout_id <= 0 || ! is_page() ) {
		return;
	}

	$current_id = (int) get_queried_object_id();

	// Alleen een pagina die zich als afrekenpagina voordoet maar het niet is.
	if ( $current_id === $checkout_id || get_post_field( 'post_name', $current_id ) !== 'checkout' ) {
		return;
	}

	nocache_headers();
	wp_safe_redirect( wc_get_checkout_url(), 301 );
	exit;
}, 1 );
