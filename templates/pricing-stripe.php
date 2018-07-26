<?php
/**
 * Pricing stripe template
 *
 * @author AndrewRMinion Design
 * @package GHC_Functionality_Plugin
 */

?>
<div id="pricing" class="pricing-stripe" style="background-image: url(<?php echo esc_url( get_field( 'price_background_image' ) ); ?>);">
	<div class="container overlay">
		<?php
		// Loop over price points.
		foreach ( $price_comparison as $price_point ) {

			// Loop over scheduled prices.
			foreach ( $price_point['price'] as $key => $level ) {

				// Get current price point.
				if ( empty( $level['begin_date'] ) && empty( $level['end_date'] ) ) {
					$current_price_point = $level['title'];
				} else {
					// test date range.
					$date       = new DateTime();
					$begin_date = date_create_from_format( 'U', $level['begin_date'] );
					$end_date   = date_create_from_format( 'U', $level['end_date'] );

					if ( $date >= $begin_date && $date <= $end_date ) {
						$current_price_point = $level['title'];
					}
				}
			}

			?>
			<div class="price-point">
				<h3 class="header">
					<?php
					echo wp_kses_post( $current_price_point );

					if ( isset( $price_point['denominator'] ) ) {
						echo '<span class="denominator meta">' . esc_attr( $price_point['denominator'] ) . '</span>';
					}
					?>
				</h3>
				<?php if ( isset( $price_point['description'] ) ) : ?>
				<div class="content">
					<?php echo wp_kses_post( $price_point['description'] ); ?>
				</div>
				<?php endif; ?>
			</div>
			<?php
		}
		?>
</div>
</div>
