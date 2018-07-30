<?php
/**
 * GHC WooCommerce
 *
 * @author AndrewRMinion Design
 * @package GHC_Functionality
 */

if ( ! function_exists( 'add_filter' ) ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit();
}

/**
 * GHC WooCommerce
 */
class GHC_Woocommerce extends GHC_Base {

	/**
	 * Subclass instance.
	 *
	 * @var null
	 */
	private static $instance = null;

	/**
	 * Return only one instance of this class.
	 *
	 * @return GHC_Woocommerce class.
	 */
	public static function get_instance() : GHC_Woocommerce {
		if ( null === self::$instance ) {
			self::$instance = new GHC_Woocommerce();
		}

		return self::$instance;
	}

	/**
	 * Kick things off
	 */
	private function __construct() {
		add_action( 'wp_enqueue_scripts', array( $this, 'register_frontend_resources' ) );

		// Add custom cart locations.
		add_filter( 'woocommerce_add_to_cart_fragments', array( $this, 'woocommerce_header_add_to_cart_fragment' ) );

		// Show product short description on product archive.
		add_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_single_excerpt', 5 );

		// Show special events on each registration product-single.
		remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20 );
		add_action( 'woocommerce_after_single_product_summary', array( $this, 'show_special_event_tickets' ), 5 );
		add_filter( 'woocommerce_cross_sells_columns', array( $this, 'woocommerce_cross_sells_columns' ), 10, 1 );

		// Save registration product IDs to a transient.
		add_action( 'save_post_product', array( $this, 'set_convention_variation_ids_transient' ) );
		add_action( 'save_post_product_variation', array( $this, 'set_convention_variation_ids_transient' ) );

		// Save family member metadata.
		add_filter( 'woocommerce_add_cart_item_data', array( $this, 'add_cart_item_family_members_metadata' ), 10, 3 );
		add_filter( 'woocommerce_get_item_data', array( $this, 'add_cart_family_members_metadata' ), 10, 2 );
		add_action( 'woocommerce_checkout_create_order_line_item', array( $this, 'add_order_meta_family_members' ), 10, 4 );

		// Handle and enforce max quantities.
		add_filter( 'woocommerce_quantity_input_args', array( $this, 'get_max_ticket_quantity_simple' ), 10, 2 );
		add_filter( 'woocommerce_available_variation', array( $this, 'get_max_ticket_quantity_variable' ) );
		add_filter( 'woocommerce_cart_item_quantity', array( $this, 'get_max_ticket_quantity_cart' ), 10, 3 );
		add_filter( 'woocommerce_add_to_cart_quantity', array( $this, 'enforce_max_ticket_quantity' ), 10, 2 );

		// Tweak checkout experience.
		add_filter( 'woocommerce_cart_item_class', array( $this, 'checkout_cart_item_class' ), 10, 3 );
		add_filter( 'woocommerce_checkout_fields', array( $this, 'woocommerce_checkout_fields_placeholders' ) );
		add_filter( 'woocommerce_checkout_login_message', array( $this, 'woocommerce_checkout_login_message' ) );
		add_filter( 'woocommerce_variable_sale_price_html', array( $this, 'variable_price_html' ), 10, 2 );
		add_filter( 'woocommerce_variable_price_html', array( $this, 'variable_price_html' ), 10, 2 );
		remove_action( 'woocommerce_single_variation', 'woocommerce_single_variation_add_to_cart_button', 20 );
		add_action( 'woocommerce_single_variation', array( $this, 'woocommerce_single_variation_add_to_cart_button' ), 20 );

		// Autocomplete orders.
		add_action( 'woocommerce_thankyou', array( $this, 'auto_complete_order' ) );

		// Send failed/cancelled order notifications to customer as well.
		add_filter( 'woocommerce_email_recipient_cancelled_order', array( $this, 'add_customer_to_cancellation_email' ), 10, 2 );
		add_filter( 'woocommerce_email_recipient_failed_order', array( $this, 'add_customer_to_cancellation_email' ), 10, 2 );

		// Modify emails.
		add_filter( 'woocommerce_email_subject_new_order', array( $this, 'woocommerce_email_subject_new_order' ), 10, 2 );
		add_action( 'woocommerce_email_order_details', array( $this, 'add_coupon_code_admin_email' ), 8, 4 );
		add_action( 'woocommerce_email_order_details', array( $this, 'add_customer_email_note' ), 5, 4 );

		// Tweak downloads.
		add_filter( 'woocommerce_order_get_downloadable_items', array( $this, 'download_url_redirect' ) );
	}

	/**
	 * Auto-complete all orders.
	 *
	 * @param integer $order_id WC_Order ID.
	 *
	 * @return  void Updates order status.
	 */
	public function auto_complete_order( $order_id ) {
		if ( ! $order_id ) {
			return;
		}

		$order = wc_get_order( $order_id );
		$order->update_status( 'completed' );
	}

	/**
	 * ////////////////////
	 * // Family Members //
	 * ////////////////////
	 */

	/**
	 * Add custom data fields to cart item metadata.
	 *
	 * @link https://wisdmlabs.com/blog/add-custom-data-woocommerce-order-2/ Adapted from this sample code.
	 *
	 * @param  array $cart_item_data WC cart item data.
	 * @param  int   $product_id     WC product ID.
	 * @param  int   $variation_id   WC variation ID.
	 *
	 * @return array WC cart item metadata.
	 */
	public function add_cart_item_family_members_metadata( array $cart_item_data, int $product_id, int $variation_id ) : array {
		if ( isset( $_REQUEST['familyMembers'] ) ) { // WPCS: CSRF ok.
			$cart_item_data['family_members'] = sanitize_text_field( wp_unslash( $_REQUEST['familyMembers'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.NoNonceVerification
		}

		return $cart_item_data;
	}

	/**
	 * Add family members to cart metadata
	 *
	 * @param  array $item_data WC cart item data.
	 * @param  array $cart_item WC cart item.
	 * @return array WC cart item data
	 */
	public function add_cart_family_members_metadata( array $item_data, array $cart_item ) : array {
		if ( array_key_exists( 'family_members', $cart_item ) ) {
			$item_data[] = array(
				'key'   => 'Family Members',
				'value' => $cart_item['family_members'],
			);
		}

		return $item_data;
	}

	/**
	 * Add family members to order item meta
	 *
	 * @param WC_Order_Item_Product $item          Order item product object.
	 * @param string                $cart_item_key Cart item key.
	 * @param array                 $values        Line item details.
	 * @param WC_Order              $order         Entire order object.
	 *
	 * @return  void Adds metadata to the WC_Order_Item_Product.
	 */
	public function add_order_meta_family_members( WC_Order_Item_Product $item, string $cart_item_key, array $values, WC_Order $order ) {
		if ( array_key_exists( 'family_members', $values ) ) {
			$item->add_meta_data( 'Family Members', $values['family_members'], true );
		}
	}

	/**
	 * ///////////////////////////
	 * // Max Ticket Quantities //
	 * ///////////////////////////
	 */

	/**
	 * Enforce that only the max number of tickets are added in the cart.
	 *
	 * @param  int $quantity   Quantity.
	 * @param  int $product_id WC product ID.
	 *
	 * @return int Quantity.
	 */
	public function enforce_max_ticket_quantity( int $quantity, int $product_id = 0 ) : int {
		$product      = new WC_Product( $product_id );
		$max_quantity = $this->get_max_ticket_quantity();
		$category_id  = get_field( 'woocommerce_product_categories', 'option' );
		$cart_items   = WC()->cart->get_cart();

		// Handle special events.
		if ( ! in_array( $category_id, $product->get_category_ids(), true ) ) {

			// Check to see if this product is in the cart already and if so, deduct the cart quantity from $max_quantity.
			foreach ( $cart_items as $cart_item_key => $cart_item ) {
				if ( $product_id === $cart_item['product_id'] ) {
					$max_quantity = $max_quantity - $cart_item['quantity'];
				}
			}

			if ( $quantity > $max_quantity ) {
				$quantity = $max_quantity;
			}
		} else {
			// Handle registration tickets with different meta.
			foreach ( $cart_items as $cart_item_key => $cart_item ) {
				if ( $product_id === $cart_item['product_id'] ) {
					return 0;
				}
			}
		}

		return $quantity;
	}

	/**
	 * Set the max special event ticket quantities to number of purchased tickets.
	 *
	 * @return int Max allowed quantity.
	 */
	private function get_max_ticket_quantity() : int {
		// Set default.
		$max_quantity = 1;

		// Loop over products in cart searching for a product with an attendee-type attribute.
		if ( WC()->cart ) {
			foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {

				$attributes = $cart_item['data']->get_attributes();

				if ( array_key_exists( 'attendee-type', $attributes ) && 'Family' === $attributes['attendee-type'] ) {
					// Get family member quantity.
					if ( array_key_exists( 'family_members', $cart_item ) ) {
						$max_quantity = (int) $cart_item['family_members'];
					}
				}
			}
		}

		return $max_quantity;
	}

	/**
	 * Set max ticket quantities for simple products.
	 *
	 * @param  array      $args    Arguments for the input.
	 * @param  WC_Product $product Product object.
	 *
	 * @return array Modified argument array.
	 */
	public function get_max_ticket_quantity_simple( array $args, WC_Product $product ) : array {
		if ( strpos( $product->get_categories(), 'Public Tickets' ) === false ) {
			$args['max_value'] = min( $this->get_max_ticket_quantity(), $product->get_stock_quantity() );
		}
		return $args;
	}

	/**
	 * Set max ticket quantities for variable products.
	 *
	 * @param  array $variation_data Variation data.
	 *
	 * @return array Variation data.
	 */
	public function get_max_ticket_quantity_variable( array $variation_data ) : array {
		$variation_data['max_qty'] = $this->get_max_ticket_quantity();
		return $variation_data;
	}

	/**
	 * Set max ticket quantities for cart quantity inputs.
	 *
	 * @param  string $product_quantity String output from woocommerce_quantity_input.
	 * @param  string $cart_item_key    Product key.
	 * @param  array  $cart_item        Product data.
	 * @return integer max ticket quantity
	 */
	public function get_max_ticket_quantity_cart( string $product_quantity, string $cart_item_key, array $cart_item ) : string {
		$product_quantity = str_replace( 'max=""', 'max="' . $this->get_max_ticket_quantity() . '"', $product_quantity );
		return $product_quantity;
	}

	/**
	 * ///////////////////
	 * // Miscellaneous //
	 * ///////////////////
	 */

	/**
	 * Get all convention variation IDs
	 *
	 * @return array All product variation IDs.
	 */
	private function get_convention_product_ids() : array {
		$all_convention_ids = get_transient( 'ghc-all-convention-variation-ids' );
		if ( false === $all_convention_ids ) {
			$all_convention_ids = $this->set_convention_variation_ids_transient();
		}

		return $all_convention_ids;
	}

	/**
	 * Save all convention variation IDs to transient to improve performance.
	 *
	 * @return array All convention variation product IDs.
	 */
	private function set_convention_variation_ids_transient() : array {
		$all_conventions_query_args = array(
			'posts_per_page' => -1,
			'post_status'    => 'publish',
			'post_type'      => 'product',
			'fields'         => 'ids',
			'tax_query'      => array( // phpcs:ignore WordPress.DB.SlowDBQuery -- since this isn’t run on any frontend page load.
				array(
					'taxonomy' => 'product_cat',
					'field'    => 'slug',
					'terms'    => 'registration',
				),
			),
		);

		$all_conventions = new WP_Query( $all_conventions_query_args );

		// Loop through results and add IDs to an array.
		$all_convention_ids = array();
		if ( $all_conventions->have_posts() ) {
			while ( $all_conventions->have_posts() ) {
				$all_conventions->the_post();
				$all_convention_ids[] = get_the_ID();
			}
		}
		wp_reset_postdata();

		set_transient( 'ghc-all-convention-variation-ids', $all_convention_ids );
		return $all_convention_ids;
	}

	/**
	 * Add product attribute classes
	 *
	 * @param  int $variation_id Product variation ID.
	 *
	 * @return void Prints `post_class` output.
	 */
	public function product_post_class( int $variation_id ) {
		$variation_classes = array();

		if ( $variation_id ) {
			if ( 'product_variation' === get_post_type( $variation_id ) ) {
				$variation = new WC_Product_Variation( $variation_id );
				foreach ( $variation->get_attributes() as $key => $value ) {
					$variation_classes[] = 'attribute_' . $key . '-' . strtolower( str_replace( ' ', '-', $value ) );
				}
			}
		}

		post_class( $variation_classes );
	}

	/**
	 * Register and enqueue WooCommerce scripts.
	 *
	 * @return void Enqueues WooCommerce assets.
	 */
	public function register_frontend_resources() {
		wp_register_script( 'ghc-woocommerce', $this->plugin_dir_url( 'dist/js/woocommerce.min.js' ), array( 'jquery', 'woocommerce' ), $this->version, true );
		wp_register_script( 'ghc-price-sheets', $this->plugin_dir_url( 'dist/js/price-sheets.min.js' ), array( 'jquery' ), $this->version, true );

		// Load WooCommerce script only on WC pages.
		if ( function_exists( 'is_product' ) && function_exists( 'is_cart' ) ) {
			if ( is_product() || is_cart() ) {
				wp_enqueue_script( 'ghc-woocommerce' );
			}
		}
	}

	/**
	 * Add custom cart total location.
	 *
	 * @param  array $fragments Array of cart total areas.
	 *
	 * @return array Modified array of cart total areas.
	 */
	public function woocommerce_header_add_to_cart_fragment( array $fragments ) : array {
		global $woocommerce;

		ob_start();

		?>
		<span class="custom-cart-total"><?php echo wp_kses_post( $woocommerce->cart->get_cart_total() ); ?></span>
		<?php

		$fragments['.custom-cart-total'] = ob_get_clean();

		return $fragments;
	}

	/**
	 * Disable the add to cart button if trying to add another registration.
	 *
	 * @return void Prints content.
	 */
	public function woocommerce_single_variation_add_to_cart_button() {
		global $product;

		// Check this product’s categories to determine if it’s a registration product.
		$this_product_terms          = get_the_terms( $product->ID, 'product_cat' );
		$check_cart_for_registration = false;
		if ( $this_product_terms ) {
			foreach ( $this_product_terms as $this_term ) {
				if ( 'Registration' === $this_term->name ) {
					$check_cart_for_registration = true;
				}
			}
		}

		if ( $check_cart_for_registration ) {
			$all_convention_ids = $this->get_convention_product_ids();

			// Check cart products against registration items.
			foreach ( WC()->cart->get_cart() as $cart_item_key => $values ) {
				$in_cart_product = $values['data'];

				if ( in_array( $in_cart_product->id, $all_convention_ids, true ) ) {
					$disable_purchase = true;
					add_filter(
						'woocommerce_product_single_add_to_cart_text', function() {
							return 'Sorry, you can only purchase tickets for one convention at a time. Please check out before purchasing more tickets.';
						}
					);
				}
			}
		}

		// Output buttons.
		echo '<div class="variations_button">';
			woocommerce_quantity_input( array( 'input_value' => isset( $_POST['quantity'] ) ? wc_stock_amount( wp_unslash( $_POST['quantity'] ) ) : 1 ) ); // phpcs:ignore WordPress.Security.NonceVerification.NoNonceVerification, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- since we’re just checking to see if the first key is set.
			echo '<button type="submit" class="single_add_to_cart_button button alt';
		if ( $disable_purchase ) {
			echo ' disabled" disabled="true';
		}
			echo '">' . esc_html( $product->single_add_to_cart_text() ) . '</button>
			<input type="hidden" name="add-to-cart" value="' . absint( $product->id ) . '" />
			<input type="hidden" name="product_id" value="' . absint( $product->id ) . '" />
			<input type="hidden" name="variation_id" class="variation_id" value="" />
		</div>';
	}

	/**
	 * Add show regular price alongside sale price.
	 *
	 * @param  string     $price   Price string.
	 * @param  WC_Product $product Product object.
	 *
	 * @return string Reformatted price string.
	 */
	public function variable_price_html( string $price, WC_Product $product ) : string {
		$variation_min_price         = $product->get_variation_price( 'min', true );
		$variation_max_price         = $product->get_variation_price( 'max', true );
		$variation_min_regular_price = $product->get_variation_regular_price( 'min', true );
		$variation_max_regular_price = $product->get_variation_regular_price( 'max', true );

		if ( ( $variation_min_price === $variation_min_regular_price ) && ( $variation_max_price === $variation_max_regular_price ) ) {
			$html_min_max_price = $price;
		} else {
			$html_price         = '<p class="price">';
			$html_price        .= '<del>' . wc_price( $variation_min_regular_price ) . '–' . wc_price( $variation_max_regular_price ) . '</del> ';
			$html_price        .= '<ins>' . wc_price( $variation_min_price ) . '–' . wc_price( $variation_max_price ) . '</ins>';
			$html_min_max_price = $html_price;
		}

		return $html_min_max_price;
	}

	/**
	 * Add simple redirect for specific downloads.
	 *
	 * @param  array $downloads All downloads available to this customer.
	 *
	 * @return array Modified array of available downloads.
	 */
	public function download_url_redirect( array $downloads ) : array {
		// Array of file names to redirect instead of protect.
		$downloads_to_redirect = array( 'Chart', 'Bible Timeline with World History' );

		foreach ( $downloads as $key => $download ) {
			if ( in_array( $download['download_name'], $downloads_to_redirect, true ) ) {
				$downloads[ $key ]['download_url'] = $download['file']['file'];
			}
		}

		return $downloads;
	}

	/**
	 * ////////////////////
	 * // Special Events //
	 * ////////////////////
	 */

	/**
	 * Replace related products with cross-sells
	 *
	 * @return  void Prints content.
	 */
	public function show_special_event_tickets() {
		global $post;

		// Get terms and filter out the “Registration” item.
		$terms = get_the_terms( $post->ID, 'product_cat' );
		if ( $terms ) {
			foreach ( $terms as $term ) {
				if ( in_array( $term->name, $this->get_conventions_abbreviations(), true ) ) {
					$convention_category = $term->term_id;
				}
			}

			$special_events_query_args = array(
				'post__not_in'   => array( $post->ID ),
				'posts_per_page' => -1,
				'post_status'    => 'publish',
				'post_type'      => 'product',
				'orderby'        => 'menu_order',
				'order'          => 'ASC',
				'tax_query'      => array( // FUTURE: cache related tickets to post_meta?
					array(
						'taxonomy' => 'product_cat',
						'field'    => 'id',
						'terms'    => $convention_category,
					),
					array(
						'taxonomy' => 'product_cat',
						'field'    => 'slug',
						'terms'    => 'special-events',
					),
				),
			);

			$special_events = new WP_Query( $special_events_query_args );

			if ( $special_events->have_posts() ) {
				?>
				<div class="cross-sells">
					<h2>Special Events</h2>
					<?php
					woocommerce_product_loop_start();
					while ( $special_events->have_posts() ) {
						$special_events->the_post();
						wc_get_template_part( 'content', 'product' );
					}
					woocommerce_product_loop_end();
					?>
				</div>
				<?php
			}
			wp_reset_postdata();
		}
	}

	/**
	 * Show more columns in cross-sells section.
	 *
	 * @param  int $columns Number of columns to display.
	 *
	 * @return int Modified number of columns to display.
	 */
	public function woocommerce_cross_sells_columns( int $columns ) : int {
		return 10;
	}

	/**
	 * //////////////
	 * // Checkout //
	 * //////////////
	 */

	/**
	 * Add product categories to checkout review table for styling.
	 *
	 * @param  string $class         Default class.
	 * @param  array  $cart_item     WC_Cart_Product array.
	 * @param  string $cart_item_key Cart item key.
	 *
	 * @return string Space-separated classes.
	 */
	public function checkout_cart_item_class( string $class, array $cart_item, string $cart_item_key ) : string {
		$post_categories = wp_get_post_terms( $cart_item['product_id'], 'product_cat' );
		foreach ( $post_categories as $category ) {
			$class .= ' ' . $category->slug;
		}

		return $class;
	}

	/**
	 * Add placeholders to all checkout fields.
	 *
	 * @param  array $fields All checkout fields.
	 *
	 * @return array Modified checkout fields.
	 */
	public function woocommerce_checkout_fields_placeholders( array $fields ) : array {
		$fields['billing']['billing_first_name']['placeholder'] = 'John';
		$fields['billing']['billing_last_name']['placeholder']  = 'Doe';
		$fields['billing']['billing_company']['placeholder']    = 'ACME Inc.';
		$fields['billing']['billing_address_1']['placeholder']  = '123 Anystreet';
		$fields['billing']['billing_address_2']['placeholder']  = 'Suite 1001';
		$fields['billing']['billing_city']['placeholder']       = 'Anytown';
		$fields['billing']['billing_postcode']['placeholder']   = '12345';
		$fields['billing']['billing_phone']['label']            = 'Cell Phone';
		$fields['billing']['billing_phone']['placeholder']      = '234-567-8901';
		$fields['billing']['billing_email']['placeholder']      = 'john.doe@example.com';

		return $fields;
	}

	/**
	 * Tweak return customer login message
	 *
	 * @param string $message Returning customer string.
	 *
	 * @return string modified message.
	 */
	public function woocommerce_checkout_login_message( string $message ) : string {
		return 'Been to a GHC convention before?';
	}

	/**
	 * ////////////
	 * // Emails //
	 * ////////////
	 */

	/**
	 * Add customer first/last name and convention to admin order email.
	 *
	 * @param  string   $subject Default email subject.
	 * @param  WC_Order $order   Order object.
	 *
	 * @return string Modified email subject.
	 */
	public function woocommerce_email_subject_new_order( string $subject, WC_Order $order ) : string {
		$subject = sprintf(
			'New Customer Order (#%s) from %s %s (Convention: %s)',
			$order->get_order_number(),
			$order->get_billing_first_name(),
			$order->get_billing_last_name(),
			$this->get_registration_product( $order )
		);

		return $subject;
	}

	/**
	 * Get name for the registration product in the order
	 *
	 * @param  WC_Order $order Order object.
	 *
	 * @return string Registration product name or empty.
	 */
	private function get_registration_product( WC_Order $order ) : string {
		$registration_product_name = '';

		if ( is_object( $order ) && $order->get_items() ) {
			foreach ( $order->get_items() as $item ) {
				$product_id = $item->get_data()['product_id'];

				// Check to see if this is a registration product.
				if ( has_term( 'registration', 'product_cat', $product_id ) ) {
					$registration_product_name = $item->get_data()['name'];
				}
			}
		}

		return $registration_product_name;
	}

	/**
	 * Add coupons to admin order email.
	 *
	 * @param WC_Order $order          Order object.
	 * @param bool     $sent_to_admin  Whether this goes to admin or not.
	 * @param string   $plain_text     Plain-text email.
	 * @param string   $email          HTML email.
	 *
	 * @return  void Prints content.
	 */
	public function add_coupon_code_admin_email( WC_Order $order, bool $sent_to_admin, string $plain_text, string $email ) {
		if ( $sent_to_admin && $order->get_used_coupons() ) {
			echo '<p>Coupon(s) used: <span class="highlighted">' . esc_attr( implode( ', ', $order->get_used_coupons() ) ) . '</span></p>';
		}
	}

	/**
	 * Add customer to failed/cancelled order notification email.
	 *
	 * @param string   $recipient Comma-separated list of email recipient(s).
	 * @param WC_Order $order     Order object.
	 *
	 * @return  string Comma-separated list of email receipients.
	 */
	public function add_customer_to_cancellation_email( string $recipient, WC_Order $order ) : string {
		return $recipient . ',' . $order->billing_email;
	}


	/**
	 * Add intro content to customer email
	 *
	 * @param WC_Order                  $order         Order object.
	 * @param bool                      $sent_to_admin Whether or not this email is sent to the admin.
	 * @param bool                      $plain_text    Whether this email is HTML formatted or plain-text.
	 * @param WC_Email_Customer_Invoice $email         Email object.
	 *
	 * @return  void Prints content.
	 */
	public function add_customer_email_note( WC_Order $order, bool $sent_to_admin, bool $plain_text, WC_Email_Customer_Invoice $email ) {
		if ( $sent_to_admin ) {
			return;
		}

		if ( $plain_text ) {
			?>
			Order Confirmation

			Your recent order on %s has been completed and your order details are shown below. Please print a copy of this email and bring it to the convention with you.

			To keep registration costs low, nothing will be mailed to you; your packet and any special event tickets will be available when you arrive at the convention.

			Special Events and More

			Watch your email in the coming weeks for FAQs, deals on hotels, and more.

			If you would like to add anything to your order, log in and place another order online (https://www.greathomeschoolconventions.com/product-category/special-events/) or email us at addtomyorder@greathomeschoolconventions.com.
		<?php } else { ?>
			<h2>Order Confirmation</h2>

			<p>Your order from Great Homeschool Conventions has been completed and your order details are shown below. Please print a copy of this email and bring it to the convention with you.</p>

			<p>To keep registration costs low, <strong>nothing</strong> will be mailed to you; your packet and any special event tickets will be available when you arrive at the convention.</p>

			<h2>Special Events and More</h2>

			<p>Watch your email in the coming weeks for FAQs, deals on hotels, and more.</p>

			<p>If you would like to add anything to your order, log in and <a href="https://www.greathomeschoolconventions.com/product-category/special-events/?utm_source=woocommerce&utm_medium=email-receipt&utm_campaign=registration-receipt&utm_content=add-to-order">place another order online</a> or email us at <a href="mailto:addtomyorder@greathomeschoolconventions.com/" target="_blank" >addtomyorder@greathomeschoolconventions.com</a>.</p>
			<?php
		} // phpcs:ignore Generic.WhiteSpace.ScopeIndent.IncorrectExact
	}



}

GHC_Woocommerce::get_instance();
