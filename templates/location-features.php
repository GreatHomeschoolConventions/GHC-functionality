<?php
/**
 * Single location features template
 *
 * @package GHC_Functionality_Plugin
 */

?>

<?php if ( get_field( 'feature_icons' ) ) : ?>
<div class="features">
	<div class="container">
		<?php

		foreach ( get_field( 'feature_icons' ) as $icon ) {
			/**
			 * Workshop schedule
			 *
			 * #FIXME: needs image field on backend and here.
			 * #FIXME: needs workshop description link.
			 */
			$workshop = get_field( 'workshop_schedule' );
			if ( strpos( $icon['title'], 'Workshop' ) !== false && ! empty( $workshop['link'] ) ) {
				echo '<div class="feature" style="background-image: url(' . esc_url( $icon['image'] ) . ')">
					<a class="icon" href="' . esc_attr( $workshop['link'] ) . '">
						<h3>Workshop Schedule</h3>
					</a>
				</div>';
			}

			/**
			 * Everything else
			 *
			 * #FIXME: workshop descriptions needs image field on backend and here.
			 */
			$workshop_descriptions = get_field( 'workshop_descriptions' );
			if ( strpos( $icon['title'], 'Workshop' ) !== false && ! empty( $workshop_descriptions['url'] ) ) {
				echo '<div class="feature" style="background-image: url(' . esc_url( $icon['image'] ) . ')">
					<a class="icon" href="' . esc_url( $workshop_descriptions['url'] ) . '" target="' . esc_attr( $workshop_descriptions['target'] ) . '">
						<h3>Workshop Descriptions</h3>
					</a>
				</div>';
			} else {
				echo '<div class="feature" style="background-image: url(' . esc_url( $icon['image'] ) . ');">';
				if ( $icon['url'] ) {
					echo '<a class="icon" href="' . esc_url( $icon['url'] ) . '">';
				}

				if ( $icon['title'] ) {
					echo '<h3>' . esc_attr( $icon['title'] ) . '</h3>';
				}

				if ( $icon['url'] ) {
					echo '</a>';
				}
				echo '</div>';
			}
		}
		?>
	</div>
</div>
<?php endif; ?>
