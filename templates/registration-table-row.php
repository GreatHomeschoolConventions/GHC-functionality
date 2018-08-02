<?php
/**
 * Register shortcode single row template for simple products.
 *
 * @package GHC_Functionality_Plugin
 */

$ghc_wc = GHC_Woocommerce::get_instance();

if ( $product->is_in_stock() ) { ?>
	<tr id="post-<?php the_ID(); ?>" class="<?php echo esc_attr( $ghc_wc->product_post_class( $product, array( $product->get_slug() ) ) ); ?>">
		<td class="thumb">
			<?php
			$image = $product->get_image( array( 50, 50 ) );
			if ( $image ) {
				echo wp_kses_post( $image );
			}
			?>
		</td>
		<td class="title">
			<?php echo wp_kses_post( $product->get_title() ); ?>
			<?php
			if ( get_field( 'subtitle' ) ) {
				echo '<p class="meta">' . wp_kses_post( get_field( 'subtitle' ) ) . '</p>';
			}
			?>
			<?php
			if ( strpos( $product->get_title(), 'teen track' ) !== false ) {
				echo '<p class="meta small">Parents are welcome to attend without tickets.</p>'; }
			?>
		</td>
		<td class="price"><?php echo wp_kses_post( $product->get_price_html() ); ?></td>
		<td class="actions">
			<?php
			$product_terms        = get_the_terms( get_the_ID(), 'product_cat' );
			$registration_product = false;

			foreach ( $product_terms as $term ) {
				if ( 'registration' === $term->slug ) {
					$registration_product = true;
				}
			}

			if ( $registration_product ) {
				echo '<input name="qty-' . get_the_ID() . '" type="hidden" value="1" min="1" max="1" />
				<label for="family-members">Family members: <br/>
					<button type="button" class="decrement btn">-</button>
					<input name="family-members-display" type="number" value="1" disabled />
					<button type="button" class="increment btn">+</button>
				</label>';
			} else {
				echo '<label class="qty" for="qty-' . esc_attr( $variation_array['variation_id'] ) . '"><span class="tickets-qty">Tickets</span><span class="tickets-separator">:</span><br/>
					<button type="button" class="decrement btn">-</button>
					<input class="qty" name="qty-' . esc_attr( $variation_array['variation_id'] ) . '" type="number" value="0" min="0" max="6" />
					<button type="button" class="increment btn">+</button>
				</label>';
			}
			?>
			<p class="product woocommerce add_to_cart_inline">
				<a rel="nofollow" href="<?php echo esc_url( $variation->add_to_cart_url() ); ?>" data-quantity="1" data-product_id="<?php echo esc_attr( get_the_ID() ); ?>" class="button product_type_variation add_to_cart_button ajax_add_to_cart">Add to my order</a>
			</p>
		</td>
	</tr>
<?php } ?>
