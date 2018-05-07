<?php
/**
 * GHC Conventions
 *
 * @author AndrewRMinion Design
 *
 * @package WordPress
 * @subpackage GHC_Functionality
 */

if ( ! function_exists( 'add_filter' ) ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit();
}

/**
 * GHC Conventions
 */
class GHC_Conventions extends GHC_Base {

	/**
	 * Kick things off
	 *
	 * @private
	 */
	public function __construct() {
		// Update transients when locations are updated.
		add_action( 'save_post_location', array( $this, 'load_conventions_info' ) );
		add_action( 'save_post_location', array( $this, 'load_conventions_abbreviations' ) );
		add_action( 'save_post_location', array( $this, 'load_conventions_dates' ) );
	}

	/**
	 * Get convention info
	 *
	 * @return array All convention info
	 */
	public function get_conventions_info() {
		$transient = get_transient( 'ghc_conventions' );
		if ( $transient ) {
			return $transient;
		} else {
			return $this->load_conventions_info();
		}
	}

	/**
	 * Get convention info
	 *
	 * @return array Convention locations abbreviations
	 */
	public function get_conventions_abbreviations() {
		$transient = get_transient( 'ghc_conventions_abbreviations' );
		if ( $transient ) {
			return $transient;
		} else {
			return $this->load_conventions_abbreviations();
		}
	}

	/**
	 * Get convention info
	 *
	 * @return array Conventions dates
	 */
	public function get_conventions_dates() {
		$transient = get_transient( 'ghc_conventions_dates' );
		if ( $transient ) {
			return $transient;
		} else {
			return $this->load_conventions_dates();
		}
	}

	/**
	 * Read convention info into global array
	 *
	 * Each key is the two-letter convention abbreviation.
	 *
	 * @return array associative array with convention info
	 */
	public function load_conventions_info() {
		$conventions = array();

		$args = array(
			'post_type'      => array( 'location' ),
			'posts_per_page' => -1,
			'post_status'    => 'publish',
			'order'          => 'ASC',
			'orderby'        => 'meta_value',
			'meta_key'       => 'begin_date',
			'no_found_rows'  => true,
		);

		$locations_query = new WP_Query( $args );

		if ( $locations_query->have_posts() ) {
			while ( $locations_query->have_posts() ) {
				$locations_query->the_post();

				$convention_info                = array(
					'ID'        => get_the_ID(),
					'title'     => get_the_title(),
					'permalink' => get_the_permalink(),
					// 'cta_list'  => get_field( 'cta' ),
					// FIXME: get_base_country() on null when cta_list is enabled.
				);
				$convention_key                 = strtolower( get_field( 'convention_abbreviated_name' ) );
				$conventions[ $convention_key ] = array_merge( $convention_info, get_post_meta( get_the_ID() ) );
			}
		}
		wp_reset_postdata();

		set_transient( 'ghc_conventions', $conventions );

		return $conventions;
	}

	/**
	 * Set up convention abbreviations array
	 *
	 * @return array associative array with convention abbreviation => convention short name
	 */
	public function load_conventions_abbreviations() {
		$conventions_abbreviations = array();

		foreach ( $this->get_conventions_info() as $key => $values ) {
			$convention_abbreviations[ $key ] = strtolower( implode( '', $values['convention_short_name'] ) );
		}

		set_transient( 'ghc_conventions_abbreviations', $convention_abbreviations );

		return $convention_abbreviations;
	}

	/**
	 * Set up convention dates array
	 *
	 * @return array associative array with convention abbreviation => Unix time
	 */
	public function load_conventions_dates() {
		$conventions_dates = array();
		foreach ( $this->get_conventions_info() as $key => $values ) {
			$convention_dates[ $key ] = mktime( get_field( 'end_date', $values['ID'] ) );
		}

		set_transient( 'ghc_conventions_dates', $convention_dates );

		return $convention_dates;
	}

	/**
	 * Return convention icons
	 *
	 * @param  array         $input_conventions      Conventions to display.
	 * @param  array  [array $args = array()] Extra arguments.
	 * @return string $convention_icons HTML string with content
	 */
	public function get_icons( $input_conventions, array $args = array() ) {
		$convention_icons      = '';
		$conventions_to_output = array();

		// Check whether input is a ID number, array, or array of objects.
		if ( is_numeric( $input_conventions ) ) {
			$this_post_terms       = get_the_terms( get_the_ID(), 'ghc_conventions_taxonomy' );
			$conventions_to_output = array();
			if ( $this_post_terms ) {
				foreach ( $this_post_terms as $term ) {
					$conventions_to_output[] = $term->slug;
				}
				usort( $conventions_to_output, $this->sort_conventions() );
			}
		} elseif ( is_string( $input_conventions ) ) {
			// Handle two-letter abbreviations.
			if ( strlen( $input_conventions ) > 2 ) {
				$input_conventions = str_replace( $this->get_convention_abbreviations(), array_keys( $this->get_convention_abbreviations() ), $input_conventions );
			}
			$conventions_to_output[] = $input_conventions;
		} elseif ( is_array( $input_conventions ) ) {
			if ( ! is_object( $input_conventions[0] ) ) {
				// If not an object, then it's an array of abbreviations.
				$conventions_to_output = array();
				foreach ( $input_conventions as $convention ) {
					if ( strlen( $convention ) > 2 ) {
						$convention = str_replace( $this->get_convention_abbreviations(), array_keys( $this->get_convention_abbreviations() ), $convention );
					}
					$conventions_to_output[] = trim( $convention );
				}
			} else {
				// If an object, then it’s a WP_Term object and we can pass directly to the output section.
				$conventions_to_output = $input_conventions;
			}

			// Sort by date (original WP_Query sorted by begin_date).
			usort( $conventions_to_output, $this->sort_conventions() );
		}

		// Add icons to $convention_icons.
		if ( is_array( $this->get_convention_abbreviations() ) ) {
			foreach ( $conventions_to_output as $convention ) {
				// Get short convention name.
				if ( is_object( $convention ) ) {
					$convention_key = array_search( $convention->slug, $this->get_convention_abbreviations(), true );
				} elseif ( 2 === strlen( $convention ) ) {
					$convention_key = $convention;
				} else {
					$convention_key = array_flip( $this->get_convention_abbreviations() )[ $convention ];
				}

				$convention_icons .= '<a class="convention-link" href="' . esc_url( $conventions[ $convention_key ]['permalink'] ) . '">
					<img src="' . esc_url( plugins_url( 'dist/images/svg/' . strtoupper( $convention_key ), GHC_PLUGIN_FULE ) ) . '.svg" alt="' . esc_attr( $conventions[ $convention_key ]['title'] ) . '" class="convention-icon" />
				</a>';
			}
		}

		return apply_filters( 'ghc_convention_icons', $convention_icons );
	}

	/**
	 * Sort locations in correct order
	 *
	 * @param  string $a Array member 1.
	 * @param  string $b Array member 2.
	 * @return array  sorted array
	 */
	public function sort_conventions( $a, $b ) {
		$sort_order = null;

		// Convert objects.
		if ( is_object( $a ) && is_object( $b ) ) {
			$a = $a->slug;
			$b = $b->slug;
		}

		// Strip spaces.
		$a = trim( $a );
		$b = trim( $b );

		// Convert two-letter abbreviations to names.
		if ( strlen( $a ) === 2 && strlen( $b ) === 2 ) {
			$a = str_replace( array_flip( $this->get_convention_abbreviations() ), $this->get_convention_abbreviations(), $a );
			$b = str_replace( array_flip( $this->get_convention_abbreviations() ), $this->get_convention_abbreviations(), $b );
		}

		// Strip key names from conventions.
		if ( is_array( $this->get_convention_abbreviations() ) ) {
			$convention_names = array_values( $this->get_convention_abbreviations() );

			// Get array key numbers.
			$a_position = array_search( $a, $convention_names, true );
			$b_position = array_search( $b, $convention_names, true );

			// Compare and return sort order.
			if ( $a_position > $b_position ) {
				$sort_order = 1;
			} else {
				$sort_order = -1;
			}
		}

		return $sort_order;
	}

}

new GHC_Conventions();
