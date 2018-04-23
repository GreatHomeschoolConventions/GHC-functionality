<?php
/**
 * Single hotel template
 *
 * @package GHC_Functionality_Plugin
 */

if ( is_post_type_archive( 'hotel' ) || $is_shortcode ) {
	$conventions_taxonomy = get_the_terms( get_the_ID(), 'ghc_conventions_taxonomy' );
	$this_convention      = array_flip( $convention_abbreviations )[ $conventions_taxonomy[0]->slug ];
}
	$post_classes = array(
		'ghc-cpt item',
		$this_convention,
		get_field( 'sold_out' ) ? 'sold-out' : '',
	);
?>
<div class="<?php echo implode( ' ', get_post_class( $post_classes ) ); ?>">
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
		<?php include( 'hotel-details.php' ); ?>
	</div>
	<?php
	if ( ! get_field( 'sold_out' ) ) {
		if ( get_field( 'hotel_URL' ) ) {
		?>
			<a class="button" target="_blank" rel="noopener noreferrer" href="<?php the_field( 'hotel_URL' ); ?>">Book Online Now&rarr;</a>
		<?php } elseif ( get_field( 'hotel_phone' ) ) { ?>
			<a class="button" target="_blank" rel="noopener noreferrer" href="tel:+1<?php echo str_replace( '-', '', get_field( 'hotel_phone' ) ); ?>">Call to Book Now&rarr;</a>
		<?php
}
	}
	?>
</div>
