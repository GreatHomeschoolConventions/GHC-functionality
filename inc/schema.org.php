<?php
/**
 * Microdata functions
 *
 * @package GHC_Functionality_Plugin
 */

defined( 'ABSPATH' ) or die( 'No access allowed' );

/**
 * Add JSON-LD microdata to each location single view
 */
function ghc_schema_org_locations() {
	$content = '';
	if ( 'location' === get_post_type() ) {
		$product_id = get_post_meta( get_the_ID(), 'registration', true );
		$product    = new WC_Product( $product_id );

		if ( $product->is_type( 'variable' ) ) {
			$prices       = $product->get_variation_prices();
			$lowest       = reset( $prices['price'] );
			$highest      = end( $prices['price'] );
			$price_string = '
				"@type": "AggregateOffer",
				"lowPrice": ' . wc_format_decimal( $lowest, wc_get_price_decimals() ) . ',
				"highPrice": ' . wc_format_decimal( $highest, wc_get_price_decimals() ) . ',
			';
		} else {
			$price_string = '
				"@type": "Offer",
				"price": ' . wc_format_decimal( $product->get_price(), wc_get_price_decimals() ) . ',
			';
		}

		// fix protocol-agnostic URLs
		$registration_url  = ghc_format_schema_url( get_field( 'registration' ) );
		$product_image_url = ghc_format_schema_url( get_the_post_thumbnail_url( $product_id ) );

		ob_start(); ?>
		<script type='application/ld+json'>
		{
			"@context": "http://schema.org/",
			"@type": "Event",
			"startDate": "<?php echo ghc_format_schema_org_date( get_field( 'begin_date' ) ); ?>",
			"endDate": "<?php echo ghc_format_schema_org_date( get_field( 'begin_date' ) ); ?>",
			"name": "<?php the_title(); ?>",
			"location": {
				"@type": "Place",
				"name": "<?php the_field( 'convention_center_name' ); ?>",
				"address": {
					"@type": "PostalAddress",
					"addressCountry": "United States",
					"addressLocality": "<?php the_field( 'state' ); ?>",
					"addressRegion": "<?php the_field( 'city' ); ?>",
					"postalCode": "<?php the_field( 'zip' ); ?>",
					"streetAddress": "<?php the_field( 'address' ); ?>"
				}
			},
			"isAccessibleForFree": "false",
			"offers": {
				<?php echo $price_string; ?>
				"availability": "available",
				"url": "<?php echo $registration_url; ?>",
				"priceCurrency": "USD",
				"validFrom": "2017-10-01"
			},
			"image": "<?php echo $product_image_url; ?>",
			"description": "The Homeschool Event of the Year",
			"performer": "Dozens of outstanding featured speakers"
		}
		</script>
		<?php
		$content .= ob_get_clean();
	}
	echo $content;
}
add_action( 'wp_footer', 'ghc_schema_org_locations', 50 );

/**
 * Format date as Y-m-d for schema.org use
 *
 * @param  string $date Ymd-formatted date
 * @return string Y-m-d-formatted date
 */
function ghc_format_schema_org_date( $date ) {
	$date = date_create_from_format( 'Ymd', $date );
	return $date->format( 'Y-m-d' );
}

/**
 * Fix protocol-agnostic URLs
 *
 * @param  string $url original URL
 * @return string URL with https:// prepended
 */
function ghc_format_schema_url( $url ) {
	if ( strpos( $url, 'http' ) === false || strpos( $url, 'http' ) === 0 ) {
		$url = 'https:' . $url;
	}

	return $url;
}
