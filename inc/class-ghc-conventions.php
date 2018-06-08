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
	 * All conventions with full info.
	 *
	 * @var array
	 */
	protected $conventions = array();

	/**
	 * All conventions abbreviations.
	 *
	 * @var array
	 */
	protected $conventions_abbreviations = array();

	/**
	 * All convention dates.
	 *
	 * @var array
	 */
	protected $conventions_dates = array();

	/**
	 * Subclass instance.
	 *
	 * @var null
	 */
	private static $instance = null;

	/**
	 * Kick things off.
	 *
	 * @access private
	 */
	private function __construct() {
		// Update transients when locations are updated.
		add_action( 'save_post_location', array( $this, 'load_conventions_info' ) );
		add_action( 'save_post_location', array( $this, 'load_conventions_abbreviations' ) );
		add_action( 'save_post_location', array( $this, 'load_conventions_dates' ) );

		// Add microdata.
		add_action( 'wp_footer', array( $this, 'add_schema_org_microdata' ), 50 );
	}

	/**
	 * Return only one instance of this class.
	 *
	 * @return GHC_Conventions class.
	 */
	public function get_instance() : GHC_Conventions {
		if ( null === self::$instance ) {
			self::$instance = new GHC_Conventions();
		}

		return self::$instance;
	}

	/**
	 * Get convention info.
	 *
	 * @return array All convention info.
	 */
	public function get_conventions_info() : array {
		if ( empty( $this->conventions ) ) {
			$transient = get_transient( 'ghc_conventions' );
			if ( $transient ) {
				$this->conventions = $transient;
				return $transient;
			} else {
				$this->conventions = $this->load_conventions_info();
			}
		}

		return $this->conventions;
	}

	/**
	 * Get convention abbreviations.
	 *
	 * @return array Convention locations abbreviations.
	 */
	public function get_conventions_abbreviations() : array {
		if ( empty( $this->conventions_abbreviations ) ) {
			$transient = get_transient( 'ghc_conventions_abbreviations' );
			if ( $transient ) {
				$this->conventions_abbreviations = $transient;
			} else {
				$this->conventions_abbreviations = $this->load_conventions_abbreviations();
			}
		}

		return $this->conventions_abbreviations;
	}

	/**
	 * Get convention dates.
	 *
	 * @return array Conventions dates.
	 */
	public function get_conventions_dates() : array {
		if ( empty( $this->conventions_dates ) ) {
			$transient = get_transient( 'ghc_conventions_dates' );
			if ( $transient ) {
				$this->convention_dates = $transient;
			} else {
				$this->conventions_dates = $this->load_conventions_dates();
			}
		}
		return $this->conventions_dates;
	}

	/**
	 * Read convention info into global array.
	 *
	 * Each key is the two-letter convention abbreviation.
	 *
	 * @return array Associative array with convention info.
	 */
	public function load_conventions_info() : array {
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

				$convention_key                 = strtolower( get_field( 'convention_abbreviated_name' ) );
				$conventions[ $convention_key ] = array(
					'ID'                          => get_the_ID(),
					'title'                       => get_the_title(),
					'permalink'                   => get_the_permalink(),
					'convention'                  => get_field( 'convention' ),
					'convention_short_name'       => get_field( 'convention_short_name' ),
					'convention_abbreviated_name' => get_field( 'convention_abbreviated_name' ),
					'icon'                        => $this->plugin_dir_url( 'dist/images/svg/' . strtoupper( $convention_key ) . '.svg' ),
					'begin_date'                  => get_field( 'begin_date' ),
					'end_date'                    => get_field( 'end_date' ),
					'address'                     => array(
						'convention_center_name' => get_field( 'convention_center_name' ),
						'street_address'         => get_field( 'address' ),
						'city'                   => get_field( 'city' ),
						'state'                  => get_field( 'state' ),
						'zip'                    => get_field( 'zip' ),
					),
					'registration'                => get_field( 'registration' ),
					'cta_list'                    => get_field( 'cta' ),
				);
			}
		}
		wp_reset_postdata();

		set_transient( 'ghc_conventions', $conventions );

		return $conventions;
	}

	/**
	 * Set up convention abbreviations array.
	 *
	 * @return array Associative array with convention abbreviation => convention short name
	 */
	public function load_conventions_abbreviations() : array {
		$conventions_abbreviations = array();

		foreach ( $this->get_conventions_info() as $key => $values ) {
			$convention_abbreviations[ $key ] = strtolower( $values['convention_short_name'] );
		}

		set_transient( 'ghc_conventions_abbreviations', $convention_abbreviations );

		return $convention_abbreviations;
	}

	/**
	 * Set up convention dates array.
	 *
	 * @return array Associative array with convention abbreviation => Unix time.
	 */
	public function load_conventions_dates() : array {
		$conventions_dates = array();
		foreach ( $this->get_conventions_info() as $key => $values ) {
			$convention_dates[ $key ] = mktime( get_field( 'end_date', $values['ID'] ) );
		}

		set_transient( 'ghc_conventions_dates', $convention_dates );

		return $convention_dates;
	}

	/**
	 * Return convention icons.
	 *
	 * @param  mixed         $input_conventions      Conventions to display.
	 * @param  array  [array $args = array()] Extra arguments.
	 *
	 * @return string $convention_icons HTML string with content.
	 */
	public function get_icons( $input_conventions, array $args = array() ) : string {
		$convention_icons      = '';
		$conventions_to_output = array();

		// Check whether input is a post ID, a string, or an array of strings or WP_Term objects.
		if ( is_int( $input_conventions ) ) {
			$this_post_terms = get_the_terms( $input_conventions, 'ghc_conventions_taxonomy' );
			if ( $this_post_terms ) {
				foreach ( $this_post_terms as $term ) {
					$conventions_to_output[] = $term->slug;
				}
			}
		} elseif ( is_string( $input_conventions ) ) {
			// Handle two-letter abbreviations.
			if ( strlen( $input_conventions ) > 2 ) {
				$input_conventions = $this->get_abbreviation( $input_conventions );
			}
			$conventions_to_output[] = $input_conventions;
		} elseif ( is_array( $input_conventions ) ) {
			if ( ! is_object( $input_conventions[0] ) ) {
				// If not an object, then it’s an array of abbreviations.
				foreach ( $input_conventions as $convention ) {
					if ( strlen( $convention ) > 2 ) {
						$convention = $this->get_abbreviation( $convention );
					}
					$conventions_to_output[] = trim( $convention );
				}
			} else {
				// If an object, then it should be a WP_Term object and we can pass directly to the output section.
				$conventions_to_output = $input_conventions;
			}

			// Sort by date (original WP_Query sorted by begin_date).
			usort( $conventions_to_output, array( $this, 'sort_conventions' ) );
		}

		// Add icons to $convention_icons.
		if ( is_array( $this->get_conventions_abbreviations() ) ) {
			foreach ( $conventions_to_output as $convention ) {
				// Get short convention name.
				if ( is_object( $convention ) ) {
					$convention_key = array_search( $convention->slug, $this->get_conventions_abbreviations(), true );
				} elseif ( 2 === strlen( $convention ) ) {
					$convention_key = $convention;
				} else {
					$convention_key = array_flip( $this->get_conventions_abbreviations() )[ $convention ];
				}

				$convention_icons .= '<a class="convention-link" href="' . esc_url( $this->get_conventions_info()[ $convention_key ]['permalink'] ) . '">
					<img src="' . esc_url( $this->plugin_dir_url( 'dist/images/svg/' . strtoupper( $convention_key ) . '.svg' ) ) . '" alt="' . esc_attr( $this->get_conventions_info()[ $convention_key ]['title'] ) . '" class="convention-icon" />
				</a>';
			}
		}

		return apply_filters( 'ghc_convention_icons', $convention_icons, $input_conventions );
	}

	/**
	 * Get convention abbreviation from full name.
	 *
	 * @access private
	 * @param  string $convention Convention long name.
	 *
	 * @return string Two-letter convention abbreviation.
	 */
	private function get_abbreviation( string $convention ) : string {
		return str_replace( $this->get_conventions_abbreviations(), array_keys( $this->get_conventions_abbreviations() ), $convention );
	}

	/**
	 * Sort locations in correct order.
	 *
	 * @param  mixed $a Array member 1.
	 * @param  mixed $b Array member 2.
	 *
	 * @return int Whether key should be moved forward or backward in array.
	 */
	public function sort_conventions( $a, $b ) : int {
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
			$a = str_replace( array_flip( $this->get_conventions_abbreviations() ), $this->get_conventions_abbreviations(), $a );
			$b = str_replace( array_flip( $this->get_conventions_abbreviations() ), $this->get_conventions_abbreviations(), $b );
		}

		// Strip key names from conventions.
		if ( is_array( $this->get_conventions_abbreviations() ) ) {
			$convention_names = array_values( $this->get_conventions_abbreviations() );

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

	/**
	 * Add JSON-LD microdata to each location single view.
	 *
	 * @return  void Prints output.
	 */
	public function add_schema_org_microdata() {
		if ( 'location' === get_post_type() ) {
			$content    = '';
			$product_id = get_post_meta( get_the_ID(), 'registration', true );
			$product    = new WC_Product( $product_id );

			if ( $product->is_type( 'variable' ) ) {
				$prices       = $product->get_variation_prices();
				$lowest       = reset( $prices['price'] );
				$highest      = end( $prices['price'] );
				$price_string = '
					"@type": "AggregateOffer",
					"lowPrice": ' . wc_format_decimal( $lowest, wc_get_price_decimals() ) . ',
					"highPrice": ' . wc_format_decimal( $highest, wc_get_price_decimals() ) . ',
				';
			} else {
				$price_string = '
					"@type": "Offer",
					"price": ' . wc_format_decimal( $product->get_price(), wc_get_price_decimals() ) . ',
				';
			}

			// Fix protocol-agnostic URLs.
			$registration_url  = $this->format_schema_url( get_field( 'registration' ) );
			$product_image_url = $this->format_schema_url( get_the_post_thumbnail_url( $product_id ) );

			ob_start(); ?>
			<script type='application/ld+json'>
			{
				"@context": "http://schema.org/",
				"@type": "Event",
				"startDate": "<?php echo esc_attr( $this->format_schema_org_date( get_field( 'begin_date' ) ) ); ?>",
				"endDate": "<?php echo esc_attr( $this->format_schema_org_date( get_field( 'begin_date' ) ) ); ?>",
				"name": "<?php the_title(); ?>",
				"location": {
					"@type": "Place",
					"name": "<?php the_field( 'convention_center_name' ); ?>",
					"address": {
						"@type": "PostalAddress",
						"addressCountry": "United States",
						"addressLocality": "<?php the_field( 'state' ); ?>",
						"addressRegion": "<?php the_field( 'city' ); ?>",
						"postalCode": "<?php the_field( 'zip' ); ?>",
						"streetAddress": "<?php the_field( 'address' ); ?>"
					}
				},
				"isAccessibleForFree": "false",
				"offers": {
					<?php echo esc_attr( $price_string ); ?>
					"availability": "available",
					"url": "<?php echo esc_url( $registration_url ); ?>",
					"priceCurrency": "USD"
				},
				"image": "<?php echo esc_url( $product_image_url ); ?>",
				"description": "The Homeschool Event of the Year",
				"performer": "Dozens of outstanding featured speakers"
			}
			</script>
			<?php
			echo ob_get_clean(); // WPCS: XSS ok because it’s all escaped above.
		}
	}

	/**
	 * Format date as Y-m-d for schema.org use.
	 *
	 * @param  string $date Ymd-formatted date.
	 *
	 * @return string Y-m-d-formatted date.
	 */
	private function format_schema_org_date( string $date ) : string {
		$date = date_create_from_format( 'Ymd', $date );
		return $date->format( 'Y-m-d' );
	}

	/**
	 * Fix protocol-agnostic URLs.
	 *
	 * @param  string $url Original URL.
	 *
	 * @return string URL with https:// prepended.
	 */
	private function format_schema_url( string $url ) : string {
		if ( strpos( $url, 'http' ) === false || strpos( $url, 'http' ) === 0 ) {
			$url = 'https:' . $url;
		}

		return $url;
	}

}

GHC_Conventions::get_instance();
