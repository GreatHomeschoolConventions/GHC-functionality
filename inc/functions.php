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
 * Save featured_video thumbnail to postmeta
 *
 * @param  integer $post_id wp post ID.
 * @return boolean Whether postmeta was succesfully updated
 */
function ghc_opengraph_video_get_meta( $post_id ) {
	if ( get_field( 'featured_video' ) ) {
		$video_id = get_video_id( sanitize_text_field( get_field( 'featured_video' ) ) );

		// Get video meta.
		$youtube_api_url = 'https://www.googleapis.com/youtube/v3/videos?part=snippet&id=' . $video_id . '&key=' . get_option( 'options_api_key' );
		$youtube_meta_ch = curl_init();
		curl_setopt( $youtube_meta_ch, CURLOPT_URL, $youtube_api_url );
		curl_setopt( $youtube_meta_ch, CURLOPT_REFERER, site_url() );
		curl_setopt( $youtube_meta_ch, CURLOPT_RETURNTRANSFER, 1 );
		$youtube_meta_json = curl_exec( $youtube_meta_ch );
		curl_close( $youtube_meta_ch );
		$youtube_meta      = json_decode( $youtube_meta_json );
		$youtube_thumbnail = $youtube_meta->items[0];

		// Save post meta.
		return update_post_meta( $post_id, 'featured_video_meta', $youtube_thumbnail );
	}
}
add_action( 'acf/save_post', 'ghc_opengraph_video_get_meta', 20 );

/**
 * Retrieve ID from video URL
 *
 * @param  string $video_url Public URL of video.
 * @return string video ID
 */
function get_video_id( $video_url ) {
	if ( strpos( $video_url, '//youtu.be' ) !== false ) {
		$video_id = basename( parse_url( $video_url, PHP_URL_PATH ) );
	} elseif ( strpos( $video_url, 'youtube.com' ) !== false ) {
		parse_str( parse_url( $video_url, PHP_URL_QUERY ), $video_array );
		$video_id = $video_array['v'];
	}

	return $video_id;
}

/**
 * Get speaker’s position and company name/link
 *
 * @param  integer $id WP post ID.
 * @return string  HTML content
 */
function ghc_get_speaker_short_bio( $id ) {
	$speaker_position    = get_field( 'position', $id );
	$speaker_company     = get_field( 'company', $id );
	$speaker_company_url = get_field( 'company_url', $id );

	ob_start();

	if ( $speaker_position || $speaker_company ) {
		echo '<p class="entry-meta speaker-info">';
		if ( $speaker_position ) {
			echo $speaker_position;
		}
		if ( $speaker_position && $speaker_company ) {
			echo ' <span class="separator">|</span> ';
		}
		if ( $speaker_company ) {
			echo ( $speaker_company_url && is_singular( 'speaker' ) ? '<a target="_blank" rel="noopener noreferrer" href="' . $speaker_company_url . '">' : '' ) . $speaker_company . ( $speaker_company_url ? '</a>' : '' );
		}
		echo '</p>';
	}

	return ob_get_clean();
}

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
 * Get special track related sponsor name(s) and link(s)
 *
 * @param  integer   $term_id              ghc_special_track term ID.
 * @param  string  [ $context             = 'inline'] “inline” or “standalone” context.
 * @return string  HTML output with sponsor name(s) and link(s)
 */
function ghc_get_special_track_related_sponsor_names( $term_id, $context = 'inline' ) {
	$track_output = '';
	$sponsors     = get_field( 'related_sponsors', 'ghc_special_tracks_taxonomy_' . $term_id );
	if ( $sponsors ) {
		$sponsor_index = 1;
		if ( 'inline' === $context ) {
			$track_output .= ' <small>(sponsored by ';
		} elseif ( 'standalone' === $context ) {
			$track_output .= '<p>This track is sponsored by ';
		}
		foreach ( $sponsors as $sponsor ) {
			$track_output .= '<a href="' . get_permalink( $sponsor ) . '">' . get_the_title( $sponsor ) . '</a>';
			if ( count( $sponsors ) > 2 ) {
				$track_output .= ', ';
				if ( count( $sponsors ) == $index ) {
					$track_output .= ' and ';
				}
			} elseif ( 2 === count( $sponsors ) && 2 !== $sponsor_index ) {
				$track_output .= ' and ';
			}
			$sponsor_index++;
		}
		if ( 'inline' === $context ) {
			$track_output .= ')</small>';
		} elseif ( 'standalone' === $context ) {
			$track_output .= '.</p>';
			$track_output .= '<div id="related-sponsors">
				<div class="sponsor-container ghc-cpt container">';
			foreach ( $sponsors as $sponsor ) {
				$track_output .= '<div class="sponsor">
					<div class="post-thumbnail">
					<a href="' . get_permalink( $sponsor ) . '">' . get_the_post_thumbnail( $sponsor, 'post-thumbnail', array( 'class' => 'sponsor' ) ) . '</a>
					</div>
					</div><!-- .sponsor -->';
			}
			$track_output .= '</div><!-- .sponsor-container.ghc-cpt.container -->
			</div><!-- #sponsor-container.ghc-cpt.container -->';
		}
	}

	return $track_output;
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
