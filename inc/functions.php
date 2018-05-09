<?php
/**
 * Generic functions
 *
 * @package GHC_Functionality_Plugin
 */

defined( 'ABSPATH' ) or die( 'No access allowed' );

/**
 * Include schema.org microdata
 */
include( 'schema.org.php' );

/**
 * Add slug to body class
 *
 * @param  array $classes Body classes.
 * @return array modified body classes
 */
function ghc_add_slug_body_class( $classes ) {
	global $post;
	if ( isset( $post ) ) {
		$classes[] = $post->post_type . '-' . $post->post_name;
	}
	return $classes;
}
add_filter( 'body_class', 'ghc_add_slug_body_class' );

/**
 * Custom post type grid
 *
 * @param  array $attributes Shortcode parameters, including `convention` as a two-letter abbreviation or full name.
 *                          ['post_type']      string      post type.
 *                          ['convention']     string      two-letter abbreviation or short convention name.
 *                          ['posts_per_page'] integer     number of posts to display; defaults to -1 (all).
 *                          ['offset']         integer     number of posts to skip.
 *                          ['show']           string      comma-separated list of elements to show; allowed values include any combination of the following: image, conventions, name, bio, excerpt.
 *                          ['image_size']     string      named image size or two comma-separated integers creating an image size array.
 * @return string HTML output
 */
function ghc_cpt_grid( $attributes ) {
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
	$this_convention      = strtolower( esc_attr( $shortcode_attributes['convention'] ) );

	$cpt_grid_args = array(
		'post_type'      => $shortcode_attributes['post_type'],
		'posts_per_page' => $shortcode_attributes['posts_per_page'],
		'offset'         => $shortcode_attributes['offset'],
		'orderby'        => 'menu_order',
		'order'          => 'ASC',
	);

	if ( 'speaker' == $shortcode_attributes['post_type'] ) {
		$cpt_grid_args['tax_query'] = array(
			array(
				'taxonomy' => 'ghc_speaker_category_taxonomy',
				'field'    => 'slug',
				'terms'    => 'featured',
			),
		);
	}

	// If specified, include only the specified convention.
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
		echo '<div class="' . $shortcode_attributes['post_type'] . '-container ghc-cpt container">';
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

/**
 * Filter to get the current convention for a CTA
 *
 * @param  array $value Input array.
 * @return boolean Whether or not this is the correct convention
 */
function get_current_cta( $value ) {
	if ( ( ! isset( $value['begin_date'] ) || strtotime( $value['begin_date'] ) <= time() ) && ( ! isset( $value['end_date'] ) || strtotime( $value['end_date'] ) >= time() ) ) {
		return true;
	} else {
		return false;
	}
}

/**
 * Add product attribute classes
 *
 * @param  integer $variation_id Product variation ID.
 * @return string  `post_class` output
 */
function ghc_product_post_class( $variation_id = '' ) {
	$variation_classes = array();

	if ( $variation_id ) {
		if ( 'product_variation' == get_post_type( $variation_id ) ) {
			$variation = new WC_Product_Variation( $variation_id );
			foreach ( $variation->get_attributes() as $key => $value ) {
				$variation_classes[] = 'attribute_' . $key . '-' . strtolower( str_replace( ' ', '-', $value ) );
			}
		}
	}

	return post_class( $variation_classes );
}

/**
 * Format date range
 *
 * @link https://codereview.stackexchange.com/a/78303 Adapted from this answer
 *
 * @param  object|string   $d1            start DateTime object or string.
 * @param  object|string   $d2            end DateTime object or string.
 * @param  string        [ $format       = ''] Input date format if passed as strings.
 * @return string        formatted date string
 */
function ghc_format_date_range( $d1, $d2, $format = '' ) {
	if ( is_string( $d1 ) && is_string( $d2 ) && $format ) {
		$d1 = date_create_from_format( $format, $d1 );
		$d2 = date_create_from_format( $format, $d2 );
	}

	if ( $d1->format( 'Y-m-d' ) === $d2->format( 'Y-m-d' ) ) {
		// Same day.
		return $d1->format( 'F j, Y' );
	} elseif ( $d1->format( 'Y-m' ) === $d2->format( 'Y-m' ) ) {
		// Same calendar month.
		return $d1->format( 'F j' ) . '&ndash;' . $d2->format( 'd, Y' );
	} elseif ( $d1->format( 'Y' ) === $d2->format( 'Y' ) ) {
		// Same calendar year.
		return $d1->format( 'F j' ) . '&ndash;' . $d2->format( 'F j, Y' );
	} else {
		// General case (spans calendar years).
		return $d1->format( 'F j, Y' ) . '&ndash;' . $d2->format( 'F j, Y' );
	}
}

/**
 * Allow ICS and SVG file uploads
 *
 * @param  array $mime_types Array of allowed mime types.
 * @return array modified array
 */
function ghc_mime_types( $mime_types ) {
	$mime_types['ics'] = 'text/calendar';
	$mime_types['svg'] = 'image/svg+xml';
	return $mime_types;
}
add_filter( 'upload_mimes', 'ghc_mime_types' );

/**
 * Set sizes atribute for responsive images and better performance
 *
 * @param  array        $attr       Markup attributes.
 * @param  object       $attachment WP_Post image attachment post.
 * @param  string|array $size       Named image size or array.
 * @return array        markup attributes
 */
function ghc_resp_img_sizes( $attr, $attachment, $size ) {
	if ( is_array( $size ) ) {
		$attr['sizes'] = $size[0] . 'px';
	} elseif ( 'thumbnail-no-crop' === $size ) {
		$attr['sizes'] = '140px';
	} elseif ( 'pinterest-thumb' === $size ) {
		$attr['sizes'] = '173px';
	} elseif ( 'pinterest-medium' === $size ) {
		$attr['sizes'] = '346px';
	} elseif ( 'square-tiny' === $size ) {
		$attr['sizes'] = '150px';
	} elseif ( 'square-thumb' === $size ) {
		$attr['sizes'] = '250px';
	} elseif ( 'square-small' === $size ) {
		$attr['sizes'] = '400px';
	} elseif ( 'square-medium' === $size ) {
		$attr['sizes'] = '600px';
	} elseif ( 'square-large' === $size ) {
		$attr['sizes'] = '900px';
	} elseif ( 'small-grid-size' === $size ) {
		$attr['sizes'] = '400px';
	} elseif ( 'small-grid-size-medium' === $size ) {
		$attr['sizes'] = '600px';
	} elseif ( 'small-grid-size-large' === $size ) {
		$attr['sizes'] = '800px';
	} elseif ( 'special-event-small' === $size ) {
		$attr['sizes'] = '300px';
	} elseif ( 'special-event-medium' === $size ) {
		$attr['sizes'] = '450px';
	} elseif ( 'special-event-large' === $size ) {
		$attr['sizes'] = '600px';
	}
	return $attr;
}
add_filter( 'wp_get_attachment_image_attributes', 'ghc_resp_img_sizes', 25, 3 );
