<?php
/**
 * Register shortcode single row template for variable products.
 *
 * @package GHC_Functionality_Plugin
 */

$ghc_wc = GHC_Woocommerce::get_instance();
?>
<tr id="post-<?php echo esc_attr( $variation->get_ID() ); ?>" class="<?php echo esc_attr( $ghc_wc->product_post_class( $variation, array( $product->get_slug() ) ) ); ?>">
	<td class="thumb">
		<?php
		$image = $product->get_image( array( 50, 50 ) );
		if ( $image ) {
			echo wp_kses_post( $image );
		}
		?>
	</td>
	<td class="title">
		<h4><?php echo wp_kses_post( $variation->get_title() ); ?></h4>
		<?php
		if ( get_field( 'subtitle' ) ) {
			echo '<p class="meta">' . wp_kses_post( get_field( 'subtitle' ) ) . '</p>';
		}
		?>
		<?php
		$attributes = array();
		foreach ( $variation->get_variation_attributes() as $key => $value ) {
			$name = $this->get_attribute_nicename( str_replace( 'attribute_', '', $key ), $value );

			if ( 'Family' === $name ) {
				$name .= ' <span class="lowercase">(parents, children/teens, and grandparents)</span>';
			}
			$attributes[] = $name;
		}
		echo '<p class="meta">' . wp_kses_post( implode( ' | ', $attributes ) ) . '</p>';
		?>
	</td>
	<?php if ( $variation->is_in_stock() ) { ?>
		<td class="price"><?php echo wp_kses_post( $variation->get_price_html() ); ?></td>
		<td class="actions">
			<?php
			$product_terms        = get_the_terms( $product->get_ID(), 'product_cat' );
			$registration_product = false;

			foreach ( $product_terms as $term ) {
				if ( 'registration' === $term->slug ) {
					$registration_product = true;
				}
			}

			if ( $registration_product ) {
				echo '<input class="qty" name="qty-' . esc_attr( $variation->get_ID() ) . '" type="hidden" value="1" min="1" max="1" />
				<label for="family-members">Family members:<br/>
					<button type="button" class="decrement btn">-</button>
					<input name="family-members" type="number" value="2" min="2" max="' . esc_attr( get_field( 'max_family_members', 'option' ) ) . '" />
					<button type="button" class="increment btn">+</button>
				</label>';
			} else {
				echo '<label class="qty" for="qty-' . esc_attr( $variation->get_ID() ) . '"><span class="tickets-qty">Tickets</span><span class="tickets-separator">:</span><br/>
					<button type="button" class="decrement btn">-</button>
					<input class="qty" name="qty-' . esc_attr( $variation->get_ID() ) . '" type="number" value="0" min="2" max="' . esc_attr( get_field( 'max_family_members', 'option' ) ) . '" />
					<button type="button" class="increment btn">+</button>
				</label>';
			}
			?>
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
