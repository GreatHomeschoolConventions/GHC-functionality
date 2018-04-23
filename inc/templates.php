<?php
/**
 * Custom content templates
 *
 * @package GHC Functionality Plugin
 */

defined( 'ABSPATH' ) or die( 'No access allowed' );

/**
 * Use custom hotel archive template
 *
 * @param  string $archive_template name of template to use.
 * @return string modified name of template to use
 */
function ghc_hotel_archive( $archive_template ) {
	global $post;
	if ( is_post_type_archive( 'hotel' ) ) {
		$archive_template = plugin_dir_path( __FILE__ ) . '/../templates/archive-hotel.php';
	}
	return $archive_template;
}
add_filter( 'archive_template', 'ghc_hotel_archive' );

/**
 * Add hotel info to single views
 *
 * @param  string $content HTML content.
 * @return string modified HTML content
 */
function ghc_hotel_single( $content ) {
	if ( is_singular( 'hotel' ) ) {
		// get convention info
		global $conventions, $convention_abbreviations;
		$conventions_taxonomy = get_the_terms( get_the_ID(), 'ghc_conventions_taxonomy' );
		$this_convention      = array_flip( $convention_abbreviations )[ $conventions_taxonomy[0]->slug ];

		// get hotel details
		ob_start();
		include( '../templates/hotel-details.php' );
		$content .= ob_get_clean();

		if ( get_field( 'hotel_URL' ) && ! get_field( 'sold_out' ) ) {
			$content .= '<a class="button book-hotel" target="_blank" rel="noopener noreferrer" href="' . get_field( 'hotel_URL' ) . '">Book Online Now</a>';
		}
	}

	return $content;
}
add_filter( 'the_content', 'ghc_hotel_single' );
