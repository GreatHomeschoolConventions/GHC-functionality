<?php
/**
 * GHC Shortcodes
 *
 * @author AndrewRMinion Design
 * @package GHC_Functionality_Plugin
 */

if ( ! function_exists( 'add_filter' ) ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit();
}

/**
 * Content-related functions
 */
class GHC_Shortcodes extends GHC_Base {

	/**
	 * Kick things off
	 *
	 * @private
	 */
	public function __construct() {}

	/**
	 * Custom post type grid helper
	 *
	 * @param  array $attributes Shortcode parameters, including `convention` as a two-letter abbreviation or full name.
	 *                          ['post_type']      string      post type.
	 *                          ['convention']     string      two-letter abbreviation or short convention name.
	 *                          ['posts_per_page'] integer     number of posts to display; defaults to -1 (all).
	 *                          ['offset']         integer     number of posts to skip.
	 *                          ['show']           string      comma-separated list of elements to show; allowed values include any combination of the following: image, conventions, name, bio, excerpt.
	 *                          ['image_size']     string      named image size or two comma-separated integers creating an image size array.
	 *
	 * @return string HTML output.
	 */
	private function ghc_cpt_grid( array $attributes = array() ) : string {
		global $convention_abbreviations;
		$shortcode_attributes = shortcode_atts(
			array(
				'post_type'      => 'speaker',
				'convention'     => null,
				'posts_per_page' => -1,
				'offset'         => null,
				'show'           => null,
				'image_size'     => 'medium',
			), $attributes
		);

		$this_convention = strtolower( esc_attr( $shortcode_attributes['convention'] ) );

		$cpt_grid_args = array(
			'post_type'      => $shortcode_attributes['post_type'],
			'posts_per_page' => $shortcode_attributes['posts_per_page'],
			'offset'         => $shortcode_attributes['offset'],
			'orderby'        => 'menu_order',
			'order'          => 'ASC',
		);

		if ( 'speaker' === $shortcode_attributes['post_type'] ) {
			$cpt_grid_args['tax_query'] = array(
				array(
					'taxonomy' => 'ghc_speaker_category_taxonomy',
					'field'    => 'slug',
					'terms'    => 'featured',
				),
			);
		}

		// If convention is specified, include only it.
		if ( ! is_null( $shortcode_attributes['convention'] ) ) {
			$convention_tax_query = array(
				array(
					'taxonomy' => 'ghc_conventions_taxonomy',
					'field'    => 'slug',
					'terms'    => $convention_abbreviations[ $this_convention ],
				),
			);

			if ( array_key_exists( 'tax_query', $cpt_grid_args ) ) {
				$cpt_grid_args['tax_query'] = array_merge( $cpt_grid_args['tax_query'], array( 'relation' => 'AND' ), $convention_tax_query );
			} else {
				$cpt_grid_args['tax_query'] = $convention_tax_query;
			}
		}

		// Set image size.
		if ( strpos( $shortcode_attributes['image_size'], ',' ) !== false ) {
			$shortcode_attributes['image_size'] = str_replace( ' ', '', $shortcode_attributes['image_size'] );
			$thumbnail_size                     = explode( ',', $shortcode_attributes['image_size'] );
			array_walk( $thumbnail_size, 'intval' );
		} else {
			$thumbnail_size = $shortcode_attributes['image_size'];
		}

		$cpt_grid_query = new WP_Query( $cpt_grid_args );

		ob_start();
		if ( $cpt_grid_query->have_posts() ) {
			echo '<div class="' . esc_attr( $shortcode_attributes['post_type'] ) . '-container ghc-cpt container">';
			while ( $cpt_grid_query->have_posts() ) {
				$cpt_grid_query->the_post();
				require( plugin_dir_path( __FILE__ ) . '../templates/speaker-template.php' );
			}
			echo '</div>';
		}

		// Restore original post data.
		wp_reset_postdata();

		return ob_get_clean();
	}


}

new GHC_Shortcodes();
