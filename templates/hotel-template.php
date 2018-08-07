<?php
/**
 * Single hotel template
 *
 * @package GHC_Functionality_Plugin
 */

if ( empty( $this_convention ) ) {
	$this_convention = $this->get_this_convention();
}
	$post_classes = array(
		'ghc-cpt item',
		$this_convention,
		get_field( 'sold_out' ) ? 'sold-out' : '',
	);
	?>
<div class="<?php echo esc_attr( implode( ' ', get_post_class( $post_classes ) ) ); ?>">
	<?php
	if ( ( is_post_type_archive( 'hotel' ) || $is_shortcode ) && ! isset( $this_convention ) ) {
		echo do_shortcode( '[convention_icon convention="' . $this_convention . '"]' );
	}
	?>
	<?php if ( has_post_thumbnail() ) { ?>
	<div class="accommodation-thumbnail"><?php the_post_thumbnail( 'small-grid-size' ); ?></div>
	<?php } ?>
	<div class="accommodation-content-wrapper">
		<h3 class="accommodation-title"><?php the_title(); ?></h3>
		<?php require 'hotel-details.php'; ?>
	</div>
	<?php
	if ( ! get_field( 'sold_out' ) ) {
		if ( get_field( 'hotel_URL' ) ) {
			?>
			<a class="button" target="_blank" rel="noopener noreferrer" href="<?php the_field( 'hotel_URL' ); ?>">Book Online Now&rarr;</a>
		<?php } elseif ( get_field( 'hotel_phone' ) ) { ?>
			<a class="button" target="_blank" rel="noopener noreferrer" href="tel:+1<?php echo esc_attr( str_replace( '-', '', get_field( 'hotel_phone' ) ) ); ?>">Call to Book Now&rarr;</a>
			<?php
		} // phpcs:ignore Generic.WhiteSpace.ScopeIndent
	}
	?>
</div>
