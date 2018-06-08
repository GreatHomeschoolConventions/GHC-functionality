<?php
/**
 * Hotel loop template
 *
 * @package GHC_Functionality_Plugin
 */

$hotel_args = array(
	'post_type'      => 'hotel',
	'posts_per_page' => -1,
	'orderby'        => 'menu_order',
	'order'          => 'ASC',
	'meta_query'     => array(
		array(
			'meta_key' => 'discount_valid_date',
			'value'    => date( 'Ymd' ),
			'compare'  => '<=',
		),
	),
);
if ( $this_convention ) {
	$hotel_args['tax_query'] = array(
		array(
			'taxonomy' => 'ghc_conventions_taxonomy',
			'field'    => 'slug',
			'terms'    => $this->get_single_convention_abbreviation( $this_convention ),
		),
	);
}

$hotel_query = new WP_Query( $hotel_args );
?>

<h3>Hotel Scam Alert</h3>

<p>Each year, a third-party company claims to be booking rooms on behalf of Great Homeschool Conventions. This is a scam; their rates are $45&ndash;70 higher than what GHC has negotiated with area hotels.</p>

<p>We have nothing to do with this company and they do not represent us in any way.</p>

<p>Please book your accommodations through one of the options below to take advantage of our negotiated rates.</p>

<?php if ( $hotel_query->have_posts() ) { ?>
	<div class="hotel-container ghc-cpt container">
	<?php
	while ( $hotel_query->have_posts() ) {
		$hotel_query->the_post();
		global $conventions;
		include( 'hotel-template.php' );
	}
	echo '</div>';
} else {
	echo '<h3>Note</h3>
	<p>We&rsquo;re still working on the hotel discount codes. Please check back later for a list of participating hotels.</p>';
}

// Restore original post data.
wp_reset_postdata(); ?>
