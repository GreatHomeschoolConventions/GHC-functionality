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

			// Get current price point.
			if ( empty( $price_point['begin_date'] ) && empty( $price_point['end_date'] ) ) {
				$current_price_point = $price_point['title'];
			} else {
				// Test date range.
				$date       = new DateTime();
				$begin_date = date_create_from_format( 'Ymd', $price_point['begin_date'] );
				$end_date   = date_create_from_format( 'Ymd', $price_point['end_date'] );

				if ( $date >= $begin_date && $date <= $end_date ) {
					$current_price_point = $price_point['title'];
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

		$info = $this->get_single_convention_info( $this->get_this_convention() );
		?>
		<p class="text-center"><a href="<?php echo esc_url( get_home_url( null, 'pricing/' . $info['slug'] ) ); ?>" class="button">Detailed Pricing</a></p>
</div>
</div>
