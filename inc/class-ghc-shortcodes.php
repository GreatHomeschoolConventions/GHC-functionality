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
			'convention_cta',
			'convention_icon',
			'exhibitor_list',
			'exhibit_hall_hours',
			'hotel_grid',
			'price_sheet',
			'product_price',
			'registration_page',
			'speaker_archive',
			'speaker_grid',
			'speaker_info',
			'speaker_list',
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
	private function cpt_grid( array $attributes = array() ) : string {
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
					'terms'    => $this->get_single_convention_abbreviation( $this_convention ),
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
				require $this->plugin_dir_path( 'templates/speaker-template.php' );
			}
			echo '</div>';
		}

		// Restore original post data.
		wp_reset_postdata();

		return ob_get_clean();
	}

	/**
	 * Display a convention’s CTA.
	 *
	 * @param  array $attributes Shortcode parameters including convention.
	 *
	 * @return string HTML content.
	 */
	public function convention_cta( array $attributes = array() ) : string {
		$shortcode_attributes = shortcode_atts(
			array(
				'convention' => null,
			), $attributes
		);
		$this_convention      = strtolower( esc_attr( $shortcode_attributes['convention'] ) );

		$cta_array   = array_filter( $this->get_conventions_info()[ $this_convention ]['cta_list'], array( $this, 'get_current_cta' ) );
		$current_cta = array_pop( $cta_array )['cta_content'];

		// CF7 is not scanning form tags for some reason. This fixes it.
		$current_cta = wpcf7_replace_all_form_tags( $current_cta );

		return apply_filters( 'the_content', $current_cta );
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
		if ( 'long' === $form_options['format'] ) {
			return '<strong>' . $convention['convention'] . '</strong>: ' . $this->get_single_convention_date( $convention['convention_abbreviated_name'] ) . ' at the ' . $convention['address']['convention_center_name'] . ' in ' . $convention['address']['city'] . ', ' . $convention['address']['state'];
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
	 * Display a single convention icon.
	 *
	 * @param  array $attributes Shortcode parameters, including `convention` as a two-letter abbreviation or full name.
	 *
	 * @return string HTML output.
	 */
	public function convention_icon( array $attributes = array() ) : string {
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
	 * Display all exhibitors for a given convention.
	 *
	 * @param  array $attributes Shortcode parameters.
	 *                           ['convention']    Two-letter convention abbreviation.
	 *                           ['style']         Type of list to display; allowed values include “large,” “small,” and “list”.
	 *
	 * @return string  HTML output.
	 */
	public function exhibitor_list( array $attributes = array() ) : string {
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
			$exhibitor_args['tax_query'] = array(
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
	public function hotel_grid( array $attributes = array() ) : string {
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
	 * Display price sheet.
	 *
	 * @param  array $attributes Shortcode parameters, including `convention` as a two-letter abbreviation or full name.
	 *
	 * @return string HTML output.
	 */
	public function price_sheet( array $attributes = array() ) : string {
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
	 * Display custom registration.
	 *
	 * @return string HTML output.
	 */
	public function registration_page() : string {
		ob_start();

		echo '<div class="register">';

		$registration_args = array(
			'category'       => 'registration',
			'orderby'        => 'menu_order',
			'order'          => 'ASC',
			'posts_per_page' => -1,
		);

		$registration_query = wc_get_products( $registration_args );

		$special_events_args = array(
			'category'       => array( 'special-events', 'program-guide' ),
			'orderby'        => 'menu_order',
			'order'          => 'ASC',
			'posts_per_page' => -1,
		);

		$special_events_query = wc_get_products( $special_events_args );

		if ( count( $registration_query ) > 0 ) {
		?>

			<h3 id="convention">Convention</h3>
			<p>Choose one:</p>
			<?php
			$next_convention = '';
			foreach ( $this->get_conventions_info() as $convention ) {
				$convention_abbreviation = strtolower( $convention['convention_abbreviated_name'] );
				if ( empty( $next_convention ) && date( 'Ymd' ) < $convention['begin_date'] ) {
					$next_convention = $convention_abbreviation;
				}
				?>
				<input class="registration-choice convention" type="radio" name="convention" value="<?php echo esc_attr( $convention_abbreviation ); ?>" id="convention-<?php echo esc_attr( $convention_abbreviation ); ?>" <?php checked( $next_convention, $convention_abbreviation ); ?> />
					<label class="registration-choice convention theme bg <?php echo esc_attr( $convention_abbreviation ); ?>" for="convention-<?php echo esc_attr( $convention_abbreviation ); ?>">
						<h4><?php echo esc_attr( $convention['convention_short_name'] ); ?></h4>
						<p class="info"><?php echo esc_attr( $this->format_date_range( $convention['begin_date'], $convention['end_date'], 'Ymd' ) ); ?></p>
					</label>
			<?php } ?>

			<h3 id="attendee-type">Attendee Type</h3>
			<p>Choose one:</p>
			<input class="registration-choice attendee-type" type="radio" name="attendee-type" value="individual" id="attendee-individual" />
				<label class="registration-choice attendee-type theme bg se dashicons-before dashicons-admin-users" for="attendee-individual"><h4>Individual</h4></label>
			<input class="registration-choice attendee-type" type="radio" name="attendee-type" value="family" id="attendee-family" checked="checked" />
				<label class="registration-choice attendee-type theme bg se dashicons-before dashicons-groups" for="attendee-family"><h4>Family</h4></label>

			<table class="products">
			<tbody>
			<?php
			wp_enqueue_script( 'ghc-woocommerce' );

			foreach ( $registration_query as $this_product ) {
				$product_object  = get_post( $this_product->get_id() );
				$GLOBALS['post'] = &$product_object; // phpcs:ignore WordPress.Variables.GlobalVariables.OverrideProhibited
				setup_postdata( $product_object );
				global $product;

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

			if ( count( $special_events_query ) > 0 ) {
				foreach ( $special_events_query as $this_product ) {
					$product_object  = get_post( $this_product->get_id() );
					$GLOBALS['post'] = &$product_object; // phpcs:ignore WordPress.Variables.GlobalVariables.OverrideProhibited
					setup_postdata( $product_object );
					global $product;

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
						<span class="custom-cart-total"><?php echo esc_attr( WC()->cart->get_cart_total() ); ?></span>
					</td>
					<td class="actions">
						<a class="button" href="<?php echo esc_url( wc_get_cart_url() ); ?>" title="<?php esc_attr_e( 'Review your shopping cart', 'woocommerce' ); ?>">Check Out&rarr;</a>
						<!-- TODO: after allowing dynamic updates, change to checkout URL, basically making this shortcode replace the cart -->
					</td>
				</tr>
			</tfoot>

			<?php
			echo '</table>';
		}

		echo '</div><!-- .register -->';

		wp_reset_postdata();

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
	public function speaker_grid( array $attributes = array() ) : string {
		$attributes['post_type'] = 'speaker';
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
	public function speaker_info( array $attributes = array() ) : string {
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
					<div class="post-thumbnail">
						<a href="<?php the_permalink(); ?>"><?php the_post_thumbnail( 'medium', array( 'class' => 'speaker-thumb' ) ); ?></a>
					</div>
					<?php if ( ! $photo_only ) { ?>
						<div class="info">
							<h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
							<?php if ( ! $no_conventions ) { ?>
								<div class="conventions-attending">
									<?php echo wp_kses_post( $conventions->get_icons( get_the_ID() ) ); ?>
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
	public function speaker_list( array $attributes = array() ) : string {
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
			$shortcode_attributes['posts_per_page'] = 500;
		}
		$this_convention = strtolower( esc_attr( $shortcode_attributes['convention'] ) );

		$speaker_list_args = array(
			'post_type'      => 'speaker',
			'posts_per_page' => esc_attr( $shortcode_attributes['posts_per_page'] ),
			'offset'         => esc_attr( $shortcode_attributes['offset'] ),
			'orderby'        => 'menu_order',
			'order'          => 'ASC',
			'tax_query'      => array(
				array(
					'taxonomy' => 'ghc_speaker_category_taxonomy',
					'field'    => 'slug',
					'terms'    => 'featured',
				),
			),
		);

		// If single convention is specified, add to the WP_Query.
		if ( $this_convention ) {
			$speaker_list_args['tax_query'] = array_merge(
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
	public function special_event_grid( array $attributes = array() ) : string {
		$attributes['post_type'] = 'special_event';
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
	public function special_track_speakers( array $attributes = array() ) : string {
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
			'tax_query'      => array(
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
	public function sponsors( array $attributes = array() ) : string {
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
	public function workshop_list( array $attributes = array() ) : string {
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
			$shortcode_attributes['posts_per_page'] = 500;
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
				'tax_query'      => array(
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
			$workshop_list_args['meta_key']     = 'speaker';
			$workshop_list_args['meta_value']   = $shortcode_attributes['speaker'];
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
