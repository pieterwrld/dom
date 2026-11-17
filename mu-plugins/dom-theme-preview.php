<?php
/**
 * Plugin Name: DOM Theme Preview
 * Description: Laat dom-theme alleen zien aan ingelogde beheerders. Klanten blijven het actieve thema zien.
 * Version: 1.0.0
 *
 * Deploy: kopieer naar /wp-content/mu-plugins/dom-theme-preview.php
 *
 * Aanzetten:  https://deoutfitman.com/?dom_preview=dom-theme
 * Uitzetten:  https://deoutfitman.com/?dom_preview=off
 *
 * De keuze blijft in een cookie staan, dus daarna klik je gewoon door de site.
 * Het actieve thema verandert niet: dit raakt alleen jouw eigen sessie.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const DOM_PREVIEW_COOKIE = 'dom_preview_theme';

/** Alleen beheerders mogen voorvertonen. */
function dom_preview_user_may() {
	return is_user_logged_in() && current_user_can( 'manage_options' );
}

/** Welk thema wil deze beheerder zien? Lege string betekent: het echte thema. */
function dom_preview_requested_theme() {
	static $theme = null;
	if ( null !== $theme ) {
		return $theme;
	}

	$theme = '';

	if ( ! dom_preview_user_may() ) {
		return $theme;
	}

	if ( isset( $_GET['dom_preview'] ) ) {
		$requested = sanitize_key( wp_unslash( $_GET['dom_preview'] ) );
		if ( 'off' === $requested || '' === $requested ) {
			$theme = '';
		} elseif ( wp_get_theme( $requested )->exists() ) {
			$theme = $requested;
		}
	} elseif ( isset( $_COOKIE[ DOM_PREVIEW_COOKIE ] ) ) {
		$stored = sanitize_key( wp_unslash( $_COOKIE[ DOM_PREVIEW_COOKIE ] ) );
		if ( $stored && wp_get_theme( $stored )->exists() ) {
			$theme = $stored;
		}
	}

	return $theme;
}

/** De keuze onthouden, zodat doorklikken werkt. */
add_action( 'init', function () {
	if ( ! isset( $_GET['dom_preview'] ) || ! dom_preview_user_may() || headers_sent() ) {
		return;
	}
	$theme = dom_preview_requested_theme();
	setcookie(
		DOM_PREVIEW_COOKIE,
		$theme,
		[
			'expires'  => $theme ? time() + DAY_IN_SECONDS : time() - 3600,
			'path'     => COOKIEPATH ? COOKIEPATH : '/',
			'domain'   => COOKIE_DOMAIN,
			'secure'   => is_ssl(),
			'httponly' => true,
			'samesite' => 'Lax',
		]
	);
} );

/** Het thema omzetten, uitsluitend in de frontend van deze beheerder. */
foreach ( [ 'template', 'stylesheet' ] as $dom_preview_filter ) {
	add_filter( $dom_preview_filter, function ( $value ) {
		if ( is_admin() || wp_doing_ajax() ) {
			return $value;
		}
		$theme = dom_preview_requested_theme();
		if ( ! $theme ) {
			return $value;
		}
		$preview = wp_get_theme( $theme );
		return current_filter() === 'template'
			? $preview->get_template()
			: $preview->get_stylesheet();
	}, 99 );
}

/** Niet cachen, anders serveert LiteSpeed jouw preview aan klanten. */
add_action( 'template_redirect', function () {
	if ( ! dom_preview_requested_theme() ) {
		return;
	}
	if ( ! defined( 'DONOTCACHEPAGE' ) ) {
		define( 'DONOTCACHEPAGE', true );
	}
	do_action( 'litespeed_control_set_nocache', 'DOM theme preview' );
	nocache_headers();
} );

/** Zichtbare markering, zodat je nooit denkt dat dit al live staat. */
add_action( 'wp_footer', function () {
	$theme = dom_preview_requested_theme();
	if ( ! $theme ) {
		return;
	}
	printf(
		'<div style="position:fixed;left:0;right:0;bottom:0;z-index:2147483647;background:#D0142C;color:#fff;'
		. 'font:700 12px/1.4 system-ui,sans-serif;letter-spacing:.06em;text-transform:uppercase;'
		. 'padding:8px 14px;display:flex;gap:14px;align-items:center;justify-content:center">'
		. '<span>Preview: %s &mdash; alleen jij ziet dit</span>'
		. '<a href="%s" style="color:#fff;text-decoration:underline">Terug naar de live site</a>'
		. '</div>',
		esc_html( $theme ),
		esc_url( add_query_arg( 'dom_preview', 'off', home_url( '/' ) ) )
	);
}, 999 );
