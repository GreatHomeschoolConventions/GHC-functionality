<?php
/**
 * Hotel loop template
 *
 * @package GHC_Functionality_Plugin
 */

$hotel_args = array(
	'post_type'  => 'hotel',
	'orderby'    => 'menu_order',
	'order'      => 'ASC',
	'meta_query' => array( // phpcs:ignore WordPress.DB.SlowDBQuery -- since we need to order this way.
		array(
			'meta_key' => 'discount_valid_date', // phpcs:ignore WordPress.DB.SlowDBQuery -- since we need to order this way.
			'value'    => date( 'Ymd' ),
			'compare'  => '<=',
		),
	),
);
if ( $this_convention ) {
	$hotel_args['tax_query'] = array( // phpcs:ignore WordPress.DB.SlowDBQuery -- since we need to order this way.
		array(
			'taxonomy' => 'ghc_conventions_taxonomy',
			'field'    => 'slug',
			'terms'    => $this->get_single_convention_slug( $this_convention ),
		),
	);
}

$hotel_query = new WP_Query( $hotel_args );

foreach ( get_field( 'archive_descriptions', 'option' ) as $description ) {
	if ( 'hotel' === $description['post_type'] ) {
		echo '<div class="container">' . wp_kses_post( apply_filters( 'the_content', $description['message'] ) ) . '</div>';
	}
}

if ( $hotel_query->have_posts() ) {
	?>
	<div class="container shortcode hotel">
	<?php
	while ( $hotel_query->have_posts() ) {
		$hotel_query->the_post();
		include 'hotel-template.php';
	}
	echo '</div>';
} else {
	echo '<h3>Note</h3>
	<p>We&rsquo;re still working on the hotel discount codes. Please check back later for a list of participating hotels.</p>';
}

echo '<p class="pages">' . wp_kses_post( paginate_links( array( 'show_all' => true ) ) ) . '</p>';

// Restore original post data.
wp_reset_postdata(); ?>
