<?php
/**
 * Register page single row template for variable products (addons)
 *
 * @package GHC_Functionality_Plugin
 */

$ghc_wc = GHC_Woocommerce::get_instance();
?>
<tr id="post-<?php echo esc_attr( $variation_array['variation_id'] ); ?>" <?php $ghc_wc->product_post_class( $variation_array['variation_id'] ); ?>>
	<td class="thumb">
		<?php
		if ( has_post_thumbnail() ) {
			the_post_thumbnail( array( 50, 50 ) );
		}
		?>
	</td>
	<td class="title">
		<?php echo esc_attr( $variation->get_title() ); ?><br/>
		<?php
		if ( get_field( 'subtitle' ) ) {
			echo '<div class="entry-meta">' . wp_kses_post( get_field( 'subtitle' ) ) . '</div>';
		}
		?>
		<?php
		$attribute_string = '';
		foreach ( $variation->get_variation_attributes() as $key => $value ) {
			if ( 'Family' === $value ) {
				$value .= ' <span class="lowercase">(parents, children/teens, and grandparents)</span>';
			}
			$attribute_string .= $value . ', ';
		}
		echo '<div class="entry-meta">' . wp_kses_post( rtrim( $attribute_string, ', ' ) ) . '</div>';
		?>
	</td>
	<?php if ( $variation->is_in_stock() ) { ?>
		<td class="price"><?php echo wp_kses_post( $variation->get_price_html() ); ?></td>
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
				echo '<input class="qty" name="qty-' . esc_attr( $variation_array['variation_id'] ) . '" type="hidden" value="1" min="1" max="1" />
				<label for="family-members">Family members:<br/>
					<button type="button" class="decrement btn">-</button>
					<input name="family-members" type="number" value="2" min="2" max="6" />
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
				<a rel="nofollow" href="<?php echo esc_url( $variation->add_to_cart_url() ); ?>" data-quantity="1" data-family-members="1" data-product_id="<?php echo esc_attr( $variation_array['variation_id'] ); ?>" class="button product_type_variation add_to_cart_button ajax_add_to_cart">Add to my order</a>
				<span class="spinner hidden"></span>
			</p>
		</td>
	<?php } else { ?>
		<td colspan="2">
			<?php
			$full_name         = strtolower( str_replace( ' Homeschool Convention', '', $variation->get_title() ) );
			$this_abbreviation = array_flip( $this->get_conventions_abbreviations() )[ $full_name ];

			echo do_shortcode( '[convention_cta convention="' . $this_abbreviation . '"]' );
			?>
		</td>
	<?php } ?>
</tr>
