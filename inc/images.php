<?php

defined( 'ABSPATH' ) or die( 'No access allowed' );

/**
 * Add custom image sizes
 */
function ghc_custom_image_sizes() {
	// update ghc_resp_image_sizes() function in functions.php as well
	add_image_size( 'thumbnail-no-crop', 140, 140, false );
	add_image_size( 'pinterest-thumb', 173, 345, true );
	add_image_size( 'pinterest-medium', 346, 690, true );
	add_image_size( 'square-tiny', 150, 150, true );
	add_image_size( 'square-thumb', 250, 250, true );
	add_image_size( 'square-small', 400, 400, true );
	add_image_size( 'square-medium', 600, 600, true );
	add_image_size( 'square-large', 900, 900, true );
	add_image_size( 'small-grid-size', 400, 300, true );
	add_image_size( 'small-grid-size-medium', 600, 450, true );
	add_image_size( 'small-grid-size-large', 800, 600, true );
	add_image_size( 'special-event-small', 300, 150, true );
	add_image_size( 'special-event-medium', 450, 225, true );
	add_image_size( 'special-event-large', 600, 300, true );
	// update ghc_resp_image_sizes() function in functions.php as well
}
add_action( 'after_setup_theme', 'ghc_custom_image_sizes' );

/**
 * Add custom image size names
 *
 * @param  array $sizes array of named image sizes
 * @return array modified array of named image sizes
 */
function ghc_custom_image_sizes_names( $sizes ) {
	return array_merge(
		$sizes, array(
			'thumbnail-no-crop'      => 'Thumbnail (no crop)',
			'square-thumb'           => 'Square',
			'square-small'           => 'Square',
			'square-medium'          => 'Square',
			'square-large'           => 'Square',
			'small-grid'             => 'Grid',
			'small-grid-size-medium' => 'Grid',
			'small-grid-size-large'  => 'Grid',
			'special-event-small'    => 'Special Event',
			'special-event-medium'   => 'Special Event',
			'special-event-large'    => 'Special Event',
		)
	);
}
add_filter( 'image_size_names_choose', 'ghc_custom_image_sizes_names' );
