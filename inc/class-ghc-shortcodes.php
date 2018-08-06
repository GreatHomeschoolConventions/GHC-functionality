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
	 * Subclass instance.
	 *
	 * @var null
	 */
	private static $instance = null;

	/**
	 * Return only one instance of this class.
	 *
	 * @return GHC_Shortcodes class.
	 */
	public static function get_instance() : GHC_Shortcodes {
		if ( null === self::$instance ) {
			self::$instance = new GHC_Shortcodes();
		}

		return self::$instance;
	}

	/**
	 * Kick things off
	 *
	 * @access  private
	 */
	private function __construct() {
		$this->init_shortcodes();
		add_action( 'wpcf7_init', array( $this, 'convention_form_list_setup' ) );
	}

	/**
	 * Initialize all shortcodes.
	 *
	 * @return void Registers shortcodes.
	 */
	public function init_shortcodes() {
		$shortcodes = array(
			'carousel',
			'container',
			'convention_address',
			'convention_cta',
			'convention_icon',
			'convention_features',
			'convention_pricing',
			'exhibitor_list',
			'exhibit_hall_hours',
			'hotel_grid',
			'locations_map',
			'price_sheet',
			'product_price',
			'register',
			'speaker_archive',
			'speaker_grid',
			'speaker_info',
			'speaker_list',
			'speaker_tags',
			'special_event_grid',
			'special_event_list',
			'special_track_speakers',
			'sponsors',
			'workshop_list',
		);

		foreach ( $shortcodes as $shortcode ) {
			add_shortcode( $shortcode, array( $this, $shortcode ) );
		}
	}

	/**
	 * Custom post type grid helper.
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
	private function cpt_grid( $attributes = array() ) : string {
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

		// Set default content.
		if ( is_null( $shortcode_attributes['show'] ) ) {
			$shortcode_attributes['show'] = 'image,title';
		}

		// Set WP_Query args.
		$cpt_grid_args = array(
			'post_type'      => $shortcode_attributes['post_type'],
			'posts_per_page' => $shortcode_attributes['posts_per_page'],
			'offset'         => $shortcode_attributes['offset'],
			'orderby'        => 'menu_order',
			'order'          => 'ASC',
		);

		if ( 'speaker' === $shortcode_attributes['post_type'] ) {
			$cpt_grid_args['tax_query'] = array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query -- depend on frontend caching to help performance
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
					'terms'    => $this->get_single_convention_abbreviation( $this_convention ),
				),
			);

			if ( array_key_exists( 'tax_query', $cpt_grid_args ) ) {
				$cpt_grid_args['tax_query'] = array_merge( $cpt_grid_args['tax_query'], array( 'relation' => 'AND' ), $convention_tax_query ); // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query -- depend on frontend caching to help performance
			} else {
				$cpt_grid_args['tax_query'] = $convention_tax_query; // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query -- depend on frontend caching to help performance
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
			echo '<div class="ghc-grid shortcode ' . esc_attr( $shortcode_attributes['post_type'] ) . '-container ghc-cpt container">';
			while ( $cpt_grid_query->have_posts() ) {
				$cpt_grid_query->the_post();
				require $this->plugin_dir_path( 'templates/speaker-template.php' );
			}
			echo '</div>';
		}

		// Restore original post data.
		wp_reset_postdata();

		return ob_get_clean();
	}

	/**
	 * Get buttons to filter conventions.
	 *
	 * @since  4.0.0
	 *
	 * @param  string $style Whether to display links or filter buttons.
	 *
	 * @return string Sanitized and escaped HTML content.
	 */
	private function get_locations_buttons( $style = 'link' ) : string {
		$content = '';
		foreach ( $this->get_conventions_info() as $convention ) {
			if ( 'link' === $style ) {
				$content .= '<a class="button hollow" href="' . esc_url( $convention['permalink'] ) . '"">' . wp_kses_post( $convention['convention_short_name'] ) . '</a> ';
			} elseif ( in_array( $style, array( 'radio', 'checkbox' ), true ) ) {
				$content .= '<input type="' . esc_attr( $style ) . '" class="convention filter" name="convention-filter" id="convention-filter-' . esc_attr( $convention['slug'] ) . '" required>
				<label for="convention-filter-' . esc_attr( $convention['slug'] ) . '" class="convention filter button hollow">' . wp_kses_post( $convention['convention_short_name'] ) . '</label> ';
			}
		}
		return $content;
	}

	/** Get radio button filters for all available attributes.
	 *
	 * @since  4.0.0
	 *
	 * @param  array  $attributes WC_Product_Variable attributes array.
	 * @param  string $slug       Optional slug to add as class name.
	 *
	 * @return string             Sanitized and escaped HTML content.
	 */
	private function get_variation_attribute_filters( array $attributes, string $slug = '' ) : string {
		ob_start();

		if ( ! empty( $attributes ) ) {
			foreach ( $attributes as $key => $values ) {
				echo '<h3 class="filter-target ' . esc_attr( $slug ) . '">' . esc_attr( wc_attribute_label( $key ) ) . '</h3>';

				foreach ( $values as $value ) {
					echo '<input type="radio" class="filter" name="' . esc_attr( $key ) . '" id="' . esc_attr( strtolower( $slug . '-' . $key . '-' . $value ) ) . '" class="' . esc_attr( $slug ) . '" required>
					<label for="' . esc_attr( strtolower( $slug . '-' . $key . '-' . $value ) ) . '" class="filter filter-target button hollow ' . esc_attr( $slug ) . '">' . esc_attr( $this->get_attribute_nicename( $key, $value ) ) . '</label> ';
				}
			}
		}

		return ob_get_clean();
	}

	/**
	 * Get global attribute nice name.
	 *
	 * @since  4.0.0
	 *
	 * @param  string $key   Attribute taxonomy name.
	 * @param  string $value Attribute name.
	 *
	 * @return string        Attribute nice name.
	 */
	private function get_attribute_nicename( string $key, string $value ) : string {
		$taxonomy = wc_attribute_taxonomy_name( str_replace( 'pa_', '', urldecode( $key ) ) );

		if ( taxonomy_exists( $taxonomy ) ) {

			// If this is a term slug, get the term’s nice name.
			$term = get_term_by( 'slug', $value, $taxonomy );
			if ( ! is_wp_error( $term ) && $term && $term->name ) {
				$value = $term->name;
			}
		}

		return $value;
	}

	/**
	 * Display the specified posts in a carousel layout.
	 *
	 * @uses https://kenwheeler.github.io/slick/ Slick Carousel
	 *
	 * @since  4.0.0
	 *
	 * @param  array $attributes Shortcode attributes.
	 *
	 * @return string            HTML content.
	 */
	public function carousel( $attributes = array() ) : string {
		$shortcode_attributes = shortcode_atts(
			array(
				'post_type'      => 'speaker',
				'convention'     => null,
				'posts_per_page' => 12,
				'offset'         => null,
				'show'           => null,
				'image_size'     => 'medium',
				'slick_args'     => null, // Note: must use single quotes to wrap the parameter and double quotes in the JSON.
			), $attributes
		);

		// Set defaults.
		$carousel_args = array(
			'post_type'      => $shortcode_attributes['post_type'],
			'posts_per_page' => $shortcode_attributes['posts_per_page'],
			'offset'         => $shortcode_attributes['offset'],
			'orderby'        => 'menu_order',
			'order'          => 'ASC',
			'tax_query'      => [], // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query -- depend on frontend caching to help performance
		);

		// Set default items to show.
		if ( is_null( $shortcode_attributes['show'] ) ) {
			$shortcode_attributes['show'] = 'image,title';
		}

		// Set default convention if on a single convention.
		if ( is_singular( 'location' ) && ! isset( $shortcode_attributes['convention'] ) ) {
			$shortcode_attributes['convention'] = strtolower( get_field( 'convention_abbreviated_name' ) );
		}

		// Get featured speakers only.
		if ( 'speaker' === $shortcode_attributes['post_type'] ) {
			$carousel_args['tax_query'][] = array(
				'taxonomy' => 'ghc_speaker_category_taxonomy',
				'field'    => 'slug',
				'terms'    => 'featured',
			);
		}

		// Get items for the specified convention only.
		if ( ! empty( $shortcode_attributes['convention'] ) ) {
			$this_convention = strtolower( esc_attr( $shortcode_attributes['convention'] ) );

			$carousel_args['tax_query']['relation'] = 'AND';
			$carousel_args['tax_query'][]           = array(
				'taxonomy' => 'ghc_conventions_taxonomy',
				'field'    => 'slug',
				'terms'    => $this->get_single_convention_abbreviation( $this_convention ),
			);
		}

		// Set image size.
		if ( strpos( $shortcode_attributes['image_size'], ',' ) !== false ) {
			$shortcode_attributes['image_size'] = str_replace( ' ', '', $shortcode_attributes['image_size'] );
			$thumbnail_size                     = explode( ',', $shortcode_attributes['image_size'] );
			array_walk( $thumbnail_size, 'intval' );
		} else {
			$thumbnail_size = $shortcode_attributes['image_size'];
		}

		$carousel_query = new WP_Query( $carousel_args );

		ob_start();

		// Get posts.
		if ( $carousel_query->have_posts() ) {
			$custom_args = json_decode( $shortcode_attributes['slick_args'] );
			$slider_id   = 'carousel-' . md5( json_encode( $carousel_args ) ); // phpcs:ignore WordPress.WP.AlternativeFunctions
			$slick_data  = $this->get_slick_options( $custom_args );

			wp_enqueue_script( 'slick' );
			wp_enqueue_style( 'slick' );

			wp_add_inline_script( 'slick', 'jQuery(document).ready(function(){jQuery("#' . $slider_id . '").slick(' . wp_json_encode( $slick_data ) . ');});', 'after' );

			echo '<div class="container">';
			printf(
				'<section id="%1$s" class="shortcode carousel %2$s">',
				esc_attr( $slider_id ),
				esc_attr( $shortcode_attributes['post_type'] )
			);
			while ( $carousel_query->have_posts() ) {
				$carousel_query->the_post();
				include $this->plugin_dir_path( 'templates/carousel-single.php' );
			}
			echo '</section>
			</div>';
		}

		wp_reset_postdata();

		return ob_get_clean();
	}

	/**
	 * Get Slick carousel settings.
	 *
	 * @since  4.0.0
	 *
	 * @param  array $custom_args Custom arguments to modify the defaults.
	 *
	 * @return array              Merged arguments.
	 */
	private function get_slick_options( $custom_args = array() ) : array {
		return wp_parse_args(
			$custom_args,
			[
				'dots'           => true,
				'slidesToShow'   => 4,
				'slidesToScroll' => 1,
				'infinite'       => true,
				'autoplay'       => true,
				'autoplaySpeed'  => 5000,
				'adaptiveHeight' => true,
				'prevArrow'      => '<a class="slick-arrow prev dashicons dashicons-arrow-left-alt2">',
				'nextArrow'      => '<a class="slick-arrow next dashicons dashicons-arrow-right-alt2">',
				'swipeToSlide'   => true,
				'responsive'     => [
					[
						'breakpoint' => '700',
						'settings'   => [
							'slidesToShow' => 3,
						],
					],
					[
						'breakpoint' => '500',
						'settings'   => [
							'slidesToShow' => 2,
						],
					],
					[
						'breakpoint' => '400',
						'settings'   => [
							'slidesToShow' => 1,
							'dots'         => false,
						],
					],
				],
			]
		);
	}

	/**
	 * Add .container wrapper to post content.
	 *
	 * Necessary because all the other shortcodes include their own .container; can’t add .container to entry because other shortcodes need to be full-width.
	 *
	 * @since  4.0.0
	 *
	 * @param  array  $attributes Shortcode attributes.
	 * @param  string $content    Shortcode content.
	 *
	 * @return string             HTML content with div.container surrounding it.
	 */
	public function container( $attributes = array(), string $content = '' ) : string {
		ob_start();
		echo '<div class="container shortcode wrapper">' . wp_kses_post( $content ) . '</div>';
		return ob_get_clean();
	}

	/**
	 * Display a convention’s CTA.
	 *
	 * @param  array $attributes Shortcode parameters including convention.
	 *
	 * @return string HTML content.
	 */
	public function convention_cta( $attributes = array() ) : string {
		$shortcode_attributes = shortcode_atts(
			array(
				'convention' => null,
			), $attributes
		);
		$this_convention      = strtolower( esc_attr( $shortcode_attributes['convention'] ) );

		$all_ctas    = get_field( 'cta', $this->get_single_convention_info( $this_convention )['ID'] );
		$current_cta = array_filter( $all_ctas, array( $this, 'get_current_cta' ) );
		$current_cta = array_pop( $current_cta )['cta_content'];

		// CF7 is not scanning form tags for some reason. This fixes it.
		$current_cta = wpcf7_replace_all_form_tags( $current_cta );

		return apply_filters( 'the_content', $current_cta );
	}

	/**
	 * Display convention address for the current location.
	 *
	 * @since  4.0.0
	 *
	 * @return string HTML content.
	 */
	public function convention_address() : string {
		$key      = strtolower( get_field( 'convention_abbreviated_name' ) );
		$location = $this->get_single_convention_info( $key );
		$address  = $location['address']['street_address'] . ', ' . $location['address']['city'] . ', ' . $location['address']['state'] . ' ' . $location['address']['zip'];

		ob_start();
		?>
			<div class="address-stripe" style="background-image: url(<?php the_post_thumbnail_url(); ?>)">
				<div class="container">
					<span class="dashicons dashicons-location"></span>
					<h2><?php echo esc_attr( $location['address']['convention_center_name'] ); ?></h2>
					<address class="meta"><?php echo wp_kses_post( $address ); ?></address>
					<p><a class="button" target="_blank" href="<?php echo esc_url( 'https://www.google.com/maps/dir/?api=1&destination=' . rawurlencode( $address ) ); ?>">Get Directions</a></p>
				</div>
			</div>
		<?php
		return ob_get_clean();
	}

	/**
	 * Register WPCF7 shortcode.
	 *
	 * @return void Register WPCF7 shortcode.
	 */
	public function convention_form_list_setup() {
		wpcf7_add_form_tag( 'convention_form_list', array( $this, 'convention_form_list' ), array( 'name-attr' => true ) );
	}

	/**
	 * Add callback handler for WPCF7 convention list checkboxes.
	 *
	 * @param  WPCF7_FormTag $tag Form tag.
	 *
	 * @return string HTML content.
	 */
	public function convention_form_list( WPCF7_FormTag $tag ) : string {
		$form_options = array();
		foreach ( $tag['options'] as $key => $value ) {
			$form_options[ $value ] = $tag['values'][ $key ];
		}

		wp_enqueue_script( 'ghc-robly-lists' );

		ob_start();
		?>
		<span class="wpcf7-form-control-wrap conventions">
			<span class="wpcf7-form-control wpcf7-checkbox">

			<?php
			foreach ( $this->get_conventions_info() as $convention ) {
				?>
				<span class="wpcf7-list-item"><label><input type="checkbox" name="conventions[]" value="<?php echo esc_attr( $convention['convention_abbreviated_name'] ); ?>">&nbsp;<span class="wpcf7-list-item-label"><?php echo wp_kses_post( $this->format_form_convention_label( $form_options, $convention ) ); ?></span></label></span>
				<?php
			}
			?>
			</span>
		</span>

		<?php
		return ob_get_clean();
	}

	/**
	 * Format convention info for WPCF7 shortcode.
	 *
	 * @param  array $form_options Form options.
	 * @param  array $convention   Convention info.
	 *
	 * @return string               Formatted info.
	 */
	private function format_form_convention_label( array $form_options, array $convention ) : string {
		if ( ! array_key_exists( 'format', $form_options ) ) {
			$form_options['format'] = 'long';
		}

		if ( 'long' === $form_options['format'] ) {
			return '<strong>' . $convention['convention_short_name'] . '</strong>: ' . $this->get_single_convention_date( $convention['convention_abbreviated_name'] ) . ' at the ' . $convention['address']['convention_center_name'] . ' in ' . $convention['address']['city'] . ', ' . $convention['address']['state'];
		}
		if ( 'short' === $form_options['format'] ) {
			return '<strong>' . $convention['convention_abbreviated_name'] . '</strong>: ' . $this->get_single_convention_date( $convention['convention_abbreviated_name'] );
		}
	}

	/**
	 * Helper function to get current CTA.
	 *
	 * @param  array $value List of CTAs defined for this convention.
	 *
	 * @return bool Whether or not this is the correct CTA.
	 */
	private function get_current_cta( array $value ) : bool {
		$conventions = GHC_Conventions::get_instance();
		return $conventions->get_current_cta( $value );
	}

	/**
	 * Show all convention feature icons.
	 *
	 * @since  4.0.0
	 *
	 * @param  array $attributes Shortcode attributes.
	 *
	 * @return string            HTML output.
	 */
	public function convention_features( $attributes = array() ) : string {
		$shortcode_attributes = shortcode_atts(
			array(
				'convention' => null,
			), $attributes
		);
		$this_convention      = strtolower( esc_attr( $shortcode_attributes['convention'] ) );

		ob_start();
		include $this->plugin_dir_path( 'templates/location-features.php' );
		return ob_get_clean();
	}

	/**
	 * Display a single convention icon.
	 *
	 * @param  array $attributes Shortcode parameters, including `convention` as a two-letter abbreviation or full name.
	 *
	 * @return string HTML output.
	 */
	public function convention_icon( $attributes = array() ) : string {
		$shortcode_attributes = shortcode_atts(
			array(
				'convention' => null,
			), $attributes
		);
		$this_convention      = strtolower( esc_attr( $shortcode_attributes['convention'] ) );

		$conventions = GHC_Conventions::get_instance();
		return $conventions->get_icons( $this_convention );
	}

	/**
	 * Display convention pricing stripe for the current location.
	 *
	 * @since  4.0.0
	 *
	 * @return string HTML content.
	 */
	public function convention_pricing() : string {
		ob_start();

		$price_comparison = get_field( 'price_comparison_points' );
		$scheduled_prices = get_field( 'pricing' );

		// Get current price point.
		foreach ( $scheduled_prices as $schedule ) {
			// Test date range.
			$date       = new DateTime();
			$begin_date = date_create_from_format( 'Ymd', $schedule['begin_date'] );
			$end_date   = date_create_from_format( 'Ymd', $schedule['end_date'] );

			if ( $date >= $begin_date && $date <= $end_date ) {
				$price_comparison[] = array(
					'price'       => array(
						'title'      => '$' . $schedule['family_price'],
						'begin_date' => $schedule['begin_date'],
						'end_date'   => $schedule['end_date'],
					),
					'denominator' => get_field( 'pricing_details_family_denominator' ),
					'description' => get_field( 'pricing_details_family_description' ),
				);
			}
		}

		if ( ! empty( $price_comparison ) ) {
			include $this->plugin_dir_path( 'templates/pricing-stripe.php' );
		}

		return ob_get_clean();
	}

	/**
	 * Display all exhibitors for a given convention.
	 *
	 * @param  array $attributes Shortcode parameters.
	 *                           ['convention']    Two-letter convention abbreviation.
	 *                           ['style']         Type of list to display; allowed values include “large,” “small,” and “list”.
	 *
	 * @return string  HTML output.
	 */
	public function exhibitor_list( $attributes = array() ) : string {
		$shortcode_attributes = shortcode_atts(
			array(
				'convention' => null,
				'style'      => 'large',
			), $attributes
		);

		$this_convention = strtolower( esc_attr( $shortcode_attributes['convention'] ) );

		echo '';

		$exhibitor_args = array(
			'posts_per_page' => -1,
			'post_type'      => 'exhibitor',
			'order'          => 'ASC',
			'orderby'        => 'post_title',
		);

		if ( $shortcode_attributes['convention'] ) {
			$exhibitor_args['tax_query'] = array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query -- depend on frontend caching to help performance
				array(
					'taxonomy' => 'ghc_conventions_taxonomy',
					'field'    => 'slug',
					'terms'    => $this->get_conventions_abbreviations( $this_convention ),
				),
			);
		}

		$exhibitor_query = new WP_Query( $exhibitor_args );

		ob_start();
		if ( $exhibitor_query->have_posts() ) {
			if ( 'list' === $shortcode_attributes['style'] ) {
				echo '<ul class="exhibitor-container ghc-cpt container ' . esc_attr( $shortcode_attributes['style'] ) . '">';
			} else {
				echo '<div class="exhibitor-container ghc-cpt container ' . esc_attr( $shortcode_attributes['style'] ) . '">';
			}

			while ( $exhibitor_query->have_posts() ) {
				$exhibitor_query->the_post();
				if ( 'large' === $shortcode_attributes['style'] ) {
					require $this->plugin_dir_path( 'templates/exhibitor-template.php' );
				} else {
					if ( 'list' === $shortcode_attributes['style'] ) {
						echo '<li id="post-' . get_the_ID() . '" class="' . esc_attr( implode( ' ', get_post_class() ) ) . '">';
					}
					echo '<a href="' . esc_url( get_permalink() ) . '">' . get_the_title() . '</a>';
					if ( 'list' === $shortcode_attributes['style'] ) {
						echo '</li>';
					}
				}
			}

			if ( 'list' === $shortcode_attributes['style'] ) {
				echo '</ul>';
			} else {
				echo '</div>';
			}
		}
		wp_reset_postdata();

		return ob_get_clean();
	}

	/**
	 * Display exhibit hall hours.
	 *
	 * @return string HTML output.
	 */
	public function exhibit_hall_hours() : string {
		return get_field( 'exhibit_hall_hours', 'option' );
	}

	/**
	 * Display hotels.
	 *
	 * @param  array $attributes Shortcode parameters, including `convention` as a two-letter abbreviation or full name.
	 *
	 * @return string HTML output.
	 */
	public function hotel_grid( $attributes = array() ) : string {
		$shortcode_attributes = shortcode_atts(
			array(
				'convention'   => null,
				'show_content' => false,
			), $attributes
		);
		$this_convention      = strtolower( esc_attr( $shortcode_attributes['convention'] ) );

		ob_start();
		$is_shortcode = true;
		require $this->plugin_dir_path( 'templates/loop-hotel.php' );
		return ob_get_clean();
	}

	/**
	 * Display locations map.
	 *
	 * @since  2.0.0
	 *
	 * @param array $attributes Shortcode attributes.
	 *                          ['convention']      Two-letter lowercase convention code.
	 *                          ['display']         Accepts one of 'side' or 'infoWindow' to determine where to show content.
	 *
	 * @return string HTML content.
	 */
	public function locations_map( $attributes = array() ) : string {
		$shortcode_attributes = shortcode_atts(
			array(
				'convention' => null,
				'display'    => 'side',
			), $attributes
		);

		// Get single or all conventions.
		if ( isset( $shortcode_attributes['convention'] ) ) {
			$conventions = array( $shortcode_attributes['convention'] => $this->get_single_convention_info( $shortcode_attributes['convention'] ) );
		} else {
			$conventions = $this->get_conventions_info();
		}

		// Get map data.
		$map_data     = [ 'style' => 'plain' ];
		$display_data = '';
		$i            = 0;
		foreach ( $conventions as $key => $convention ) {
			$map_data['points'][ $key ] = array(
				'title'   => $convention['title'],
				'icon'    => $convention['icon'],
				'address' => $convention['address'],
			);

			$display_data .= '<div class="map-info key-' . esc_attr( $key ) . '" style="background-image: url(' . esc_url( $convention['icon'] ) . ');' . ( 0 === $i ? '' : 'display: none;' ) . '">
				<h1>' . esc_attr( $convention['title'] ) . '</h1>
				<p class="meta">' . esc_attr( $this->get_single_convention_date( $key ) ) . '</p>
				<address>
					<strong>' . esc_attr( $convention['address']['convention_center_name'] ) . '</strong><br />
					' . esc_attr( $convention['address']['street_address'] ) . '<br />
					' . esc_attr( $convention['address']['city'] ) . ', ' . esc_attr( $convention['address']['state'] ) . ' ' . esc_attr( $convention['address']['zip'] ) . '
				</address>
				' . $this->convention_cta( array( 'convention' => $key ) ) . '
			</div>
			';
			$i++;
		}

		$map_json       = wp_json_encode( $map_data );
		$map_identifier = md5( $map_json );

		wp_enqueue_script( 'ghc-maps' );
		wp_add_inline_script( 'ghc-maps', 'var ghcMap_' . esc_attr( $map_identifier ) . ' = ' . $map_json . ';', 'before' );

		ob_start();
		echo '<div class="ghc-map-container shortcode display-' . esc_attr( $shortcode_attributes['display'] ) . '">
			<div class="container">
				<div class="ghc-map" id="ghcMap_' . esc_attr( $map_identifier ) . '"></div>
				<div class="map-locations-info">' . $display_data . '</div>
			</div>
		</div>'; // WPCS: XSS ok since it’s all escaped above.
		return ob_get_clean();
	}

	/**
	 * Display price sheet.
	 *
	 * @param  array $attributes Shortcode parameters, including `convention` as a two-letter abbreviation or full name.
	 *
	 * @return string HTML output.
	 */
	public function price_sheet( $attributes = array() ) : string {
		$shortcode_attributes = shortcode_atts(
			array(
				'convention' => null,
			), $attributes
		);
		$this_convention      = strtolower( esc_attr( $shortcode_attributes['convention'] ) );

		wp_enqueue_script( 'ghc-price-sheets' );

		ob_start();
		include $this->plugin_dir_path( 'price-sheets/price-sheet-' . $this_convention . '.html' );
		return ob_get_clean();
	}

	/**
	 * Show product sale/regular prices.
	 *
	 * @return string Formatted price string.
	 */
	public function product_price() : string {
		$registration_product = new WC_Product_Variable( get_field( 'registration_product' ) );
		return $registration_product->get_price_html();
	}

	/**
	 * Single or all convention registration form.
	 *
	 * If on a single location page or if `convention` parameter is specified anywhere, includes products only for the given convention.
	 *
	 * @since  4.0.0
	 *
	 * @param  array $attributes Shortcode attributes; accepts two-letter convention parameter.
	 *
	 * @return string             HTML output.
	 */
	public function register( $attributes = array() ) : string {
		$shortcode_attributes = shortcode_atts(
			array(
				'convention' => null,
			), $attributes
		);

		// Set default convention if on a single convention.
		if ( is_singular( 'location' ) && ! isset( $shortcode_attributes['convention'] ) ) {
			$shortcode_attributes['convention'] = strtolower( get_field( 'convention_abbreviated_name' ) );
		}

		// Get convention categories.
		if ( ! empty( $shortcode_attributes['convention'] ) ) {
			$convention_categories = array( $this->get_single_convention_info( $shortcode_attributes['convention'] )['convention_short_name'] );
		} else {
			$convention_categories = wp_list_pluck( $this->get_conventions_info(), 'convention_short_name' );
		}

		// Get main products.
		$registration_args = array(
			'orderby'        => 'menu_order',
			'order'          => 'ASC',
			'posts_per_page' => -1,
			'tax_query'      => array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query -- depend on frontend caching to help performance
				'relation' => 'AND',
				array(
					'taxonomy' => 'product_cat',
					'field'    => 'slug',
					'terms'    => $convention_categories,
				),
				array(
					'taxonomy' => 'product_cat',
					'field'    => 'slug',
					'terms'    => 'registration',
				),
			),
		);

		$registration_products = wc_get_products( $registration_args );

		// Get special events.
		$special_events_args = array(
			'orderby'        => 'menu_order',
			'order'          => 'ASC',
			'posts_per_page' => -1,
			'tax_query'      => array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query -- depend on frontend caching to help performance
				'relation' => 'AND',
				array(
					'taxonomy' => 'product_cat',
					'field'    => 'slug',
					'terms'    => $convention_categories,
				),
				array(
					'taxonomy' => 'product_cat',
					'field'    => 'slug',
					'terms'    => 'special-events',
				),
			),
		);

		$special_events_products = wc_get_products( $special_events_args );

		ob_start();
		?>

		<div class="container register shortcode" id="register">
			<h2>Register</h2>

			<?php

			wp_enqueue_script( 'ghc-woocommerce' );

			// Get locations filters if convention is not specified.
			if ( empty( $shortcode_attributes['convention'] ) ) {
				echo $this->get_locations_buttons( 'radio' ); // phpcs:ignore WordPress.Security.EscapeOutput -- it’s all sanitized and escaped in the function
			}

			// Get attribute filters.
			foreach ( $registration_products as $product ) {
				if ( $product->is_type( 'variable' ) ) {
					$attributes = $product->get_variation_attributes();
					echo $this->get_variation_attribute_filters( $attributes, $product->get_slug() ); // phpcs:ignore WordPress.Security.EscapeOutput -- it’s all sanitized and escaped in the function
				}
			}

			?>
			<form method="post" action="<?php echo esc_url( wc_get_cart_url() ); ?>" class="products">
			<table class="products">
				<tbody>
					<?php

					// Display registration products.
					foreach ( $registration_products as $product ) {
						if ( $product->is_type( 'variable' ) ) {
							$variations = $product->get_available_variations();

							foreach ( $variations as $variation_array ) {
								$variation = new WC_Product_Variation( $variation_array['variation_id'] );
								require $this->plugin_dir_path( 'templates/registration-table-row-variation.php' );
							}
						} else {
							require $this->plugin_dir_path( 'templates/registration-table-row.php' );
						}
					}

					// Display special event products.
					if ( count( $special_events_products ) > 0 ) {
						foreach ( $special_events_products as $product ) {
							if ( $product->is_type( 'variable' ) ) {
								$variations = $product->get_available_variations();

								foreach ( $variations as $variation_array ) {
									$variation = new WC_Product_Variation( $variation_array['variation_id'] );
									require $this->plugin_dir_path( 'templates/registration-table-row-variation.php' );
								}
							} else {
								require $this->plugin_dir_path( 'templates/registration-table-row.php' );
							}
						}
					}
					?>
				</tbody>
				<tfoot>
					<tr class="cart-totals">
						<td colspan="2" class="header">Total</td>
						<td class="total">
							<span class="custom-cart-total"><?php echo wp_kses_post( wc_cart_totals_subtotal_html() ); ?></span>
						</td>
						<td class="actions add-to-cart">
							<input type="submit" value="Add All to Cart&rarr;" />
						</td>
					</tr>
				</tfoot>
			</table>
			</form>
		</div>

		<?php
		return ob_get_clean();
	}

	/**
	 * Display custom speaker/special event archive.
	 *
	 * @return string HTML of entire archive.
	 */
	public function speaker_archive() : string {
		ob_start();
		echo '<div class="speaker-archive">';
			require $this->plugin_dir_path( 'templates/loop-speaker.php' );
		echo '</div>';
		return ob_get_clean();
	}

	/**
	 * Display speaker grid.
	 *
	 * @param  array $attributes Shortcode parameters, including `convention` as a two-letter abbreviation or full name.
	 *                          ['convention']     string      two-letter abbreviation or short convention name.
	 *                          ['posts_per_page'] integer     number of posts to display; defaults to -1 (all).
	 *                          ['offset']         integer     number of posts to skip.
	 *                          ['show']           string      comma-separated list of elements to show; allowed values include any combination of the following: image, conventions, name, bio, excerpt.
	 *                          ['image_size']     string      named image size or two comma-separated integers creating an image size array.
	 *
	 * @return string HTML output.
	 */
	public function speaker_grid( $attributes = array() ) : string {
		$attributes = wp_parse_args( $attributes, array( 'post_type' => 'speaker' ) );
		return $this->cpt_grid( $attributes );
	}

	/**
	 * Display speaker(s) info.
	 *
	 * @param  array $attributes Shortcode parameters (see array above).
	 *                          ['postid']          integer post ID for a specific speaker.
	 *                          ['pagename']        string  post slug for a specific speaker.
	 *                          ['align']           string  align right, left, or center.
	 *                          ['no_conventions']  boolean whether or not to show convention icons beneath speaker’s name.
	 *                          ['extra_classes']   string  extra classes to add to the output.
	 *
	 * @return string HTML output.
	 */
	public function speaker_info( $attributes = array() ) : string {
		$shortcode_attributes = shortcode_atts(
			array(
				'postid'         => null,
				'post_id'        => null,
				'pagename'       => null,
				'align'          => null,
				'no_conventions' => null,
				'photo_only'     => null,
				'extra_classes'  => null,
			), $attributes
		);

		if ( is_null( $shortcode_attributes['postid'] ) && ! is_null( $shortcode_attributes['post_id'] ) ) {
			$this_postid = esc_attr( $shortcode_attributes['post_id'] );
		} else {
			$this_postid = esc_attr( $shortcode_attributes['postid'] );
		}

		$this_pagename  = esc_attr( $shortcode_attributes['pagename'] );
		$this_alignment = esc_attr( $shortcode_attributes['align'] );
		$no_conventions = esc_attr( $shortcode_attributes['no_conventions'] );
		$photo_only     = esc_attr( $shortcode_attributes['photo_only'] );
		$extra_classes  = esc_attr( $shortcode_attributes['extra_classes'] );

		$args = array(
			'post_type'      => array( 'speaker' ),
			'posts_per_page' => '-1',
		);

		if ( ! empty( $this_postid ) ) {
			if ( strpos( $this_postid, ',' ) !== false ) {
				$args['post__in'] = explode( ',', $this_postid );
			} else {
				$args['p'] = $this_postid;
			}
		}

		if ( $this_pagename ) {
			$args['pagename'] = $this_pagename; }
		if ( ( $this_alignment ) && ( strpos( $this_alignment, 'align' ) === false ) ) {
			$this_alignment = 'align' . $this_alignment; }

		$speaker_query = new WP_Query( $args );

		ob_start();
		if ( $speaker_query->have_posts() ) {
			echo '<div class="speaker-container ghc-cpt container shortcode';
			if ( $this_alignment ) {
				echo ' ' . esc_attr( $this_alignment );
			}
			if ( $extra_classes ) {
				echo ' ' . esc_attr( $extra_classes );
			}
			echo '">';

			while ( $speaker_query->have_posts() ) {
				$speaker_query->the_post();
				?>

				<div class="speaker ghc-cpt item">
					<div class="thumbnail">
						<a href="<?php the_permalink(); ?>"><?php the_post_thumbnail( 'medium', array( 'class' => 'speaker-thumb' ) ); ?></a>
					</div>
					<?php if ( ! $photo_only ) { ?>
						<div class="info">
							<h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
							<?php if ( ! $no_conventions ) { ?>
								<div class="conventions-attending">
									<?php
									$conventions = GHC_Conventions::get_instance();
									echo wp_kses_post( $conventions->get_icons( get_the_ID() ) );
									?>
								</div>
							<?php } ?>
						</div>
					<?php } ?>
				</div>
				<?php
			}
			echo '</div><!-- .speaker-container.ghc-cpt.container -->';
		}

		// Restore original post data.
		wp_reset_postdata();

		return ob_get_clean();
	}

	/**
	 * Display a list of speakers.
	 *
	 * @param  array $attributes Shortcode parameters (see array above).
	 *                          ['convention']      string  two-letter abbreviation or full name.
	 *                          ['posts_per_page']  integer number of posts to display.
	 *                          ['offset']          integer how many posts to skip.
	 *                          ['ul_class']        string  class(es) to add to the wrapping <ul>.
	 *                          ['li_class']        string  class(es) to add to each speaker <li>.
	 *                          ['a_class']         string  class(es) to add to each speaker <a>.
	 *
	 * @return string HTML output.
	 */
	public function speaker_list( $attributes = array() ) : string {
		$shortcode_attributes = shortcode_atts(
			array(
				'convention'     => null,
				'posts_per_page' => -1,
				'offset'         => null,
				'ul_class'       => null,
				'li_class'       => null,
				'a_class'        => null,
			), $attributes
		);

		// Workaround for posts_per_page overriding offset.
		if ( ! is_null( $shortcode_attributes['offset'] ) && -1 === $shortcode_attributes['posts_per_page'] ) {
			$shortcode_attributes['posts_per_page'] = 500; // phpcs:ignore WordPress.WP.PostsPerPage.posts_per_page_posts_per_page
		}
		$this_convention = strtolower( esc_attr( $shortcode_attributes['convention'] ) );

		$speaker_list_args = array(
			'post_type'      => 'speaker',
			'posts_per_page' => esc_attr( $shortcode_attributes['posts_per_page'] ),
			'offset'         => esc_attr( $shortcode_attributes['offset'] ),
			'orderby'        => 'menu_order',
			'order'          => 'ASC',
			'tax_query'      => array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query -- depend on frontend caching to help performance
				array(
					'taxonomy' => 'ghc_speaker_category_taxonomy',
					'field'    => 'slug',
					'terms'    => 'featured',
				),
			),
		);

		// If single convention is specified, add to the WP_Query.
		if ( $this_convention ) {
			$speaker_list_args['tax_query'] = array_merge( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query -- depend on frontend caching to help performance
				$speaker_list_args['tax_query'], array(
					'relation' => 'AND',
					array(
						'taxonomy' => 'ghc_conventions_taxonomy',
						'field'    => 'slug',
						'terms'    => $this->get_single_convention_abbreviation( $this_convention ),
					),
				)
			);
		}

		$speaker_list_query = new WP_Query( $speaker_list_args );

		ob_start();
		if ( $speaker_list_query->have_posts() ) {
			echo '<ul class="speaker-list ' . esc_attr( $shortcode_attributes['ul_class'] ) . '">';
			while ( $speaker_list_query->have_posts() ) {
				$speaker_list_query->the_post();
				echo '<li class="' . esc_attr( $shortcode_attributes['li_class'] ) . '"><a class="' . esc_attr( $shortcode_attributes['a_class'] ) . '" href="' . esc_url( get_permalink() ) . '">' . esc_attr( get_the_title() ) . '</a></li>';
			}
			echo '</ul>';
		}

		// Reset the post data.
		wp_reset_postdata();

		return ob_get_clean();
	}

	/**
	 * Display stripe with content type tags and associated CPTs.
	 *
	 * @since  4.0.0
	 *
	 * @param  array $attributes Shortcode parameters.
	 *
	 * @return string            HTML content.
	 */
	public function speaker_tags( $attributes = array() ) : string {
		ob_start();
		?>
		<div class="speaker-tags shortcode" style="background-image: url(<?php echo esc_url( get_field( 'speaker_tag_background', 'option' ) ); ?>);">
			<div class="container overlay">
				<h2>What Interests You?</h2>
				<?php
				// Get categories.
				$category_args = array(
					'taxonomy' => 'ghc_content_tags_taxonomy',
				);
				$categories    = get_categories( $category_args );

				// Display categories.
				echo '<p class="filter">';
				foreach ( $categories as $category ) {
					echo '<a class="button hollow" href="' . esc_url( get_category_link( $category ) ) . '" data-content-tag-id="' . esc_attr( $category->term_id ) . '">' . wp_kses_post( $category->name ) . '</a> ';
				}
				echo '</p>';

				// Script.
				wp_add_inline_script( 'ghc-content-types-filter', 'var speakerAjaxUrl = "' . esc_url( admin_url( 'admin-ajax.php' ) ) . '", speakerTagSlickArgs = ' . wp_json_encode( $this->get_slick_options() ) . ';', 'before' );
				wp_enqueue_script( 'ghc-content-types-filter' );

				// Container.
				echo '<div class="speakers-container carousel shortcode speaker">';
				$speakers_query = new WP_Query( array( 'post_type' => 'speaker' ) );
				if ( $speakers_query->have_posts() ) {
					while ( $speakers_query->have_posts() ) {
						$speakers_query->the_post();
						include $this->plugin_dir_path( 'templates/carousel-single.php' );
					}
				}
				wp_reset_postdata();
				echo '</div>';

				?>
			</div>
		</div>
		<?php
		return ob_get_clean();
	}

	/**
	 * Display special event grid.
	 *
	 * @param  array $attributes Shortcode parameters, including `convention` as a two-letter abbreviation or full name.
	 *                          ['post_type']      string      post type; defaults to 'special_event'.
	 *                          ['convention']     string      two-letter abbreviation or short convention name.
	 *                          ['posts_per_page'] integer     number of posts to display; defaults to -1 (all).
	 *                          ['offset']         integer     number of posts to skip.
	 *                          ['show']           string      comma-separated list of elements to show; allowed values include any combination of the following: image, conventions, name, bio, excerpt.
	 *                          ['image_size']     string      named image size or two comma-separated integers creating an image size array.
	 *
	 * @return string HTML output.
	 */
	public function special_event_grid( $attributes = array() ) : string {
		$attributes = wp_parse_args( $attributes, array( 'post_type' => 'special_event' ) );
		return $this->cpt_grid( $attributes );
	}

	/**
	 * Show a list of special events.
	 *
	 * @return string HTML output.
	 */
	public function special_event_list() {
		$special_event_list_args = array(
			'taxonomy' => 'ghc_special_tracks_taxonomy',
			'title_li' => '',
			'echo'     => false,
		);

		return '<ul>' . wp_list_categories( $special_event_list_args ) . '</ul>';
	}

	/**
	 * Display sponsors for a particular track.
	 *
	 * @param  array $attributes Shortcode parameters, including the `track` slug.
	 *
	 * @return string HTML output.
	 */
	public function special_track_speakers( $attributes = array() ) : string {
		$shortcode_attributes = shortcode_atts(
			array(
				'track' => null,
			), $attributes
		);

		$special_track_speakers_args = array(
			'post_type'      => 'speaker',
			'posts_per_page' => -1,
			'orderby'        => 'menu_order',
			'order'          => 'ASC',
			'tax_query'      => array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query -- depend on frontend caching to help performance
				array(
					'taxonomy' => 'ghc_special_tracks_taxonomy',
					'field'    => 'slug',
					'terms'    => $shortcode_attributes['track'],
				),
			),
		);

		$special_track_speakers_query = new WP_Query( $special_track_speakers_args );

		ob_start();
		if ( $special_track_speakers_query->have_posts() ) {
			echo '<div class="speaker-container ghc-cpt container">';
			while ( $special_track_speakers_query->have_posts() ) {
				$special_track_speakers_query->the_post();
				require $this->plugin_dir_path( 'templates/speaker-template.php' );
			}
			echo '</div>';
		}

		// Restore original post data.
		wp_reset_postdata();

		return ob_get_clean();
	}

	/**
	 * Display all sponsors.
	 *
	 * Example: array[]
	 *                 ['gray']    bool Whether to show the featured image or the gray version.
	 *                 ['width']   int  Width in pixels for the output image.
	 *
	 * @param  array $attributes Shortcode parameters (see above array).
	 *
	 * @return string HTML output.
	 */
	public function sponsors( $attributes = array() ) : string {
		$shortcode_attributes = shortcode_atts(
			array(
				'gray'  => null,
				'width' => null,
			), $attributes
		);

		$sponsors_args = array(
			'post_type'      => 'sponsor',
			'posts_per_page' => -1,
			'orderby'        => 'menu_order',
			'order'          => 'ASC',
		);

		$sponsors_query = new WP_Query( $sponsors_args );

		ob_start();
		if ( $sponsors_query->have_posts() ) {
			echo '<div class="sponsor-container ghc-cpt container">';
			while ( $sponsors_query->have_posts() ) {
				$sponsors_query->the_post();
				echo '<article id="post-' . esc_attr( get_the_ID() ) . '" class="ghc-cpt item ' . esc_attr( implode( ' ', get_post_class() ) ) . '">';
				echo '<a href="' . esc_attr( get_permalink() ) . '">
					<div class="sponsor-thumbnail">';
				if ( $shortcode_attributes['gray'] ) {
					if ( $shortcode_attributes['width'] ) {
						echo wp_get_attachment_image( get_field( 'grayscale_logo' )['id'], array( $shortcode_attributes['width'], -1 ) );
					} else {
						echo wp_get_attachment_image( get_field( 'grayscale_logo' )['id'] );
					}
				} else {
					if ( $shortcode_attributes['width'] ) {
						echo get_the_post_thumbnail( get_the_ID(), array( $shortcode_attributes['width'], -1 ) );
					} else {
						echo get_the_post_thumbnail();
					}
				}
					echo '</div></a>
				</article>';
			}
			echo '</div>';
		}
		wp_reset_postdata();

		return ob_get_clean();
	}

	/**
	 * Display list of workshops
	 *
	 * @param  array $attributes Shortcode attributes.
	 *                          ['convention']     Two-letter convention abbreviation.
	 *                          ['posts_per_page'] Number of posts to show (defaults to all; if offset is specified, then is set to 500).
	 *                          ['offset']         Number of posts to skip (useful mainly in conjunction with posts_per_page).
	 *                          ['speaker']        Include only workshops from this speaker, specified by post ID.
	 *
	 * @return string HTML output.
	 */
	public function workshop_list( $attributes = array() ) : string {
		$shortcode_attributes = shortcode_atts(
			array(
				'convention'     => null,
				'posts_per_page' => -1,
				'offset'         => null,
				'speaker'        => null,
			), $attributes
		);

		// Add workaround for posts_per_page overriding offset.
		if ( ! is_null( $shortcode_attributes['offset'] ) && -1 === $shortcode_attributes['posts_per_page'] ) {
			$shortcode_attributes['posts_per_page'] = 500; // phpcs:ignore WordPress.WP.PostsPerPage.posts_per_page_posts_per_page
		}
		$this_convention = strtolower( esc_attr( $shortcode_attributes['convention'] ) );

		$workshop_list_args = array(
			'post_type'      => 'workshop',
			'posts_per_page' => esc_attr( $shortcode_attributes['posts_per_page'] ),
			'offset'         => esc_attr( $shortcode_attributes['offset'] ),
			'orderby'        => array( 'menu_order', 'title' ),
			'order'          => 'ASC',
		);

		if ( $this_convention ) {
			$this_convention_speaker_args = array(
				'post_type'      => 'speaker',
				'posts_per_page' => -1,
				'tax_query'      => array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query -- depend on frontend caching to help performance
					array(
						'taxonomy' => 'ghc_conventions_taxonomy',
						'field'    => 'slug',
						'terms'    => $this->get_single_convention_abbreviation( $this_convention ),
					),
				),
			);

			$this_convention_speaker = new WP_Query( $this_convention_speaker_args );

			$workshop_ids_array = array();

			if ( $this_convention_speaker->have_posts() ) {
				while ( $this_convention_speaker->have_posts() ) {
					$this_convention_speaker->the_post();

					$related_workshops = get_field( 'related_workshops' );
					if ( is_array( $related_workshops ) ) {
						$workshop_ids_array = array_merge( $workshop_ids_array, $related_workshops );
					}
				}
			}
			wp_reset_postdata();

			$workshop_list_args['post__in'] = $workshop_ids_array;
		}

		// If speaker is specified, add to meta query.
		if ( $shortcode_attributes['speaker'] ) {
			$workshop_list_args['meta_key']     = 'speaker';  // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key -- depend on frontend caching to help performance
			$workshop_list_args['meta_value']   = $shortcode_attributes['speaker'];  // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value -- depend on frontend caching to help performance
			$workshop_list_args['meta_compare'] = 'LIKE';
		}

		$workshop_list_query = new WP_Query( $workshop_list_args );

		ob_start();
		if ( $workshop_list_query->have_posts() ) {
			echo '<ul class="workshop-list">';
			while ( $workshop_list_query->have_posts() ) {
				$workshop_list_query->the_post();
				$speaker = get_field( 'speaker' );

				if ( $speaker && is_null( $shortcode_attributes['speaker'] ) ) {
					$speaker_string = ' <span class="entry-meta"> | ';
					foreach ( $speaker as $this_speaker ) {
						$speaker_string .= apply_filters( 'the_title', $this_speaker->post_title ) . ', ';
					}
					$speaker_string = rtrim( $speaker_string, ', ' ) . '</span>';
				} else {
					$speaker_string = '';
				}

				echo '<li><a href="' . esc_attr( get_permalink() ) . '">' . get_the_title() . '</a>' . esc_attr( $speaker_string ) . '</li>';
			}

			if ( -1 !== $shortcode_attributes['posts_per_page'] && ! is_null( $shortcode_attributes['convention'] ) ) {
				echo '<li><a href="' . esc_url( home_url() ) . '/workshops/">And <strong>many</strong> more!</a></li>';
			}

			echo '</ul>';
		}
		wp_reset_postdata();

		return ob_get_clean();
	}

}

GHC_Shortcodes::get_instance();
