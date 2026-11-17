<?php
/**
 * Plugin Name: DOM WooLentor Cleanup
 * Description: Removes unnecessary WooLentor CSS/JS from non-archive pages to prevent style conflicts with Ecomus theme
 * Version: 1.0.0
 * Author: De Outfitman
 *
 * Deploy: Copy this file to /wp-content/mu-plugins/dom-woolentor-cleanup.php
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'wp_enqueue_scripts', 'dom_cleanup_woolentor_assets', 999 );

function dom_cleanup_woolentor_assets() {
	if ( is_shop() || is_product_taxonomy() || is_post_type_archive( 'product' ) ) {
		return;
	}

	$styles_to_remove = array(
		'woolentor-product-grid-modern',
		'woolentor-product-grid-luxury',
		'woolentor-product-grid-editorial',
		'woolentor-product-grid-magazine',
		'woolentor-block-common',
		'woolentor-block-default',
		'simple-line-icons-wl',
		'htflexboxgrid',
		'slick',
		'woolentor-widgets',
		'woolentor-quickview',
	);

	$scripts_to_remove = array(
		'woolentor-block-main',
		'slick',
		'woolentor-accordion-min',
		'woolentor-quickview',
	);

	foreach ( $styles_to_remove as $handle ) {
		wp_dequeue_style( $handle );
		wp_deregister_style( $handle );
	}

	foreach ( $scripts_to_remove as $handle ) {
		wp_dequeue_script( $handle );
		wp_deregister_script( $handle );
	}
}
