<?php
/**
 * Hotel details template
 *
 * @package GHC_Functionality_Plugin
 */

?>
<div class="details">
<?php
echo get_field( 'sold_out' ) ? '<h4 class="sold-out">Sold Out</h4>' : '';

echo get_field( 'discount_rate' ) ? '<p class="dashicons-before dashicons-tag">From $' . esc_attr( get_field( 'discount_rate' ) ) . ' per night' : '';
echo get_field( 'discount_rate_details' ) ? '<br/>' . esc_attr( get_field( 'discount_rate_details' ) ) . '</p>' : '</p>';
echo get_field( 'discount_rate2' ) ? '<p class="dashicons-before dashicons-tag">From $' . esc_attr( get_field( 'discount_rate2' ) ) . ' per night' : '';
echo get_field( 'discount_rate2_details' ) ? '<br/>' . esc_attr( get_field( 'discount_rate2_details' ) ) . '</p>' : '</p>';
echo get_field( 'discount_rate3' ) ? '<p class="dashicons-before dashicons-tag">From $' . esc_attr( get_field( 'discount_rate3' ) ) . ' per night' : '';
echo get_field( 'discount_rate3_details' ) ? '<br/>' . esc_attr( get_field( 'discount_rate3_details' ) ) . '</p>' : '</p>';
echo get_field( 'discount_valid_date' ) ? '<p>Discount valid through: ' . esc_attr( get_field( 'discount_valid_date' ) ) . '</p>' : '';
echo get_field( 'discount_group_code' ) ? '<p>Group code: ' . esc_attr( get_field( 'discount_group_code' ) ) . '</p>' : '';

echo get_field( 'hotel_phone' ) ? '<p>Phone: ' . esc_attr( get_field( 'hotel_phone' ) ) . '</p>' : '';

echo get_field( 'hotel_URL' ) ? '<p><a class="button" href="' . esc_url( get_field( 'hotel_URL' ) ) . '" rel="noopener rofererrer">Book Online</a></p>' : '';

if ( get_field( 'location' ) ) {
	$location        = get_field( 'location' );
	$convention_info = $this->get_single_convention_info( $this_convention );
	$location_data   = array(
		'points' => array(
			'convention' => array(
				'title'   => $convention_info['title'],
				'icon'    => $convention_info['icon'],
				'address' => $convention_info['address'],
			),
			'hotel'      => array(
				'title'   => get_the_title(),
				'icon'    => $this->plugin_dir_url( 'dist/images/svg/hotel.svg' ),
				'address' => array( 'map' => $location ),
			),
		),
	);

	$convention_address = $convention_info['address']['street_address'] . ' ' . $convention_info['address']['city'] . ', ' . $convention_info['address']['state'] . ' ' . $convention_info['address']['zip'];

	$map_json       = wp_json_encode( $location_data );
	$map_identifier = md5( $map_json );

	wp_enqueue_script( 'ghc-maps' );
	wp_add_inline_script( 'ghc-maps', 'var ghcMap_' . esc_attr( $map_identifier ) . ' = ' . $map_json . ';', 'before' );

	ob_start();
	echo '<h4>Map</h4>
	<div class="ghc-map-container shortcode display-infoWindow">
		<div class="container">
			<div class="ghc-map" id="ghcMap_' . esc_attr( $map_identifier ) . '"></div>
		</div>
	</div>

	<p><a target="_blank" rel="noopener noreferrer" class="button dashicons-before dashicons-location-alt" href="https://www.google.com/maps/dir/' . esc_attr( str_replace( ' ', '+', $location['address'] ) ) . '/' . esc_attr( str_replace( ' ', '+', $convention_address ) ) . '/">Get directions</a></p>';
}
?>
</div>
