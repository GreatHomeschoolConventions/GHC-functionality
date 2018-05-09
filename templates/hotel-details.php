<?php
/**
 * Hotel details template
 *
 * @package GHC_Functionality_Plugin
 */

?>
<div class="accommodation-caption">
<?php
	echo get_field( 'sold_out' ) ? '<h4 class="sold-out">Sold Out</h4>' : '';

	echo get_field( 'discount_rate' ) ? '<p>From $' . esc_attr( get_field( 'discount_rate' ) ) . ' per night' : '';
	echo get_field( 'discount_rate_details' ) ? '<br/>' . esc_attr( get_field( 'discount_rate_details' ) ) . '</p>' : '</p>';
	echo get_field( 'discount_rate2' ) ? '<p>From $' . esc_attr( get_field( 'discount_rate2' ) ) . ' per night' : '';
	echo get_field( 'discount_rate2_details' ) ? '<br/>' . esc_attr( get_field( 'discount_rate2_details' ) ) . '</p>' : '</p>';
	echo get_field( 'discount_rate3' ) ? '<p>From $' . esc_attr( get_field( 'discount_rate3' ) ) . ' per night' : '';
	echo get_field( 'discount_rate3_details' ) ? '<br/>' . esc_attr( get_field( 'discount_rate3_details' ) ) . '</p>' : '</p>';

if ( ! is_singular() ) {
	echo '<div class="content">' . wp_kses_post( get_the_content() ) . '</div>';
}

	echo get_field( 'discount_valid_date' ) ? '<p>Discount valid through: ' . esc_attr( get_field( 'discount_valid_date' ) ) . '</p>' : '';

	echo get_field( 'discount_group_code' ) ? '<p>Group code: ' . esc_attr( get_field( 'discount_group_code' ) ) . '</p>' : '';
	echo get_field( 'discount_rate_details' ) ? '<p>Details: ' . esc_attr( get_field( 'discount_rate_details' ) ) . '</p>' : '';

	echo get_field( 'hotel_phone' ) ? '<p>Phone: ' . esc_attr( get_field( 'hotel_phone' ) ) . '</p>' : '';

if ( $is_shortcode && $shortcode_attributes['show_content'] && ! empty( get_the_content() ) ) {
	// Use get_the_content() to bypass the_content filters.
	echo '<div class="small">
		<h4 class="perks">Perks</h4>
		' . wp_kses_post( get_the_content() ) . '</div>';
}

if ( get_field( 'location' ) ) {
	$location        = get_field( 'location' );
	$convention_info = $this->get_single_convention_info( $this_convention );

	$convention_address = $convention_info['address']['street_address'] . ' ' . $convention_info['address']['city'] . ', ' . $convention_info['address']['state'] . ' ' . $convention_info['address']['zip'];

	echo '<p><a target="_blank" rel="noopener noreferrer" href="https://www.google.com/maps/dir/' . esc_url( str_replace( ' ', '+', $location['address'] ) ) . '/' . esc_url( str_replace( ' ', '+', $convention_address_url ) ) . '/">Directions to ' . esc_attr( $convention_info['address']['convention_center_name'] ) . '</a></p>';

	if ( is_singular() ) {
		wp_enqueue_script( 'ghc-hotel-map' );
		wp_enqueue_style( 'leaflet' );
		echo '<div class="hotel map" data-source-address="' . esc_attr( $location['address'] ) . '" data-destination-address="' . esc_attr( $convention_address ) . '"></div>';
	}
}
?>
</div>
