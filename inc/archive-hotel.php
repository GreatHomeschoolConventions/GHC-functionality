<?php
global $convention_abbreviations;

// arguments
$hotel_args = array(
    'post_type'              => 'hotel',
    'posts_per_page'         => -1,
    'orderby'                => 'menu_order',
    'order'                  => 'ASC',
    'tax_query' => array(
        array(
            'taxonomy'  => 'ghc_conventions_taxonomy',
            'field'     => 'slug',
            'terms'     => $convention_abbreviations[$this_convention],
        )
    ),
);
// the query
$hotel_query = new WP_Query( $hotel_args );

// the loop
if ( $hotel_query->have_posts() ) {
    $counter = 1;
    while ( $hotel_query->have_posts() ) {
        $hotel_query->the_post();
        global $conventions;
        if ( 1 == $counter % 3 ) {
            echo '<div class="hotels flexbox-wrapper">';
        }
        include( 'hotel-template.php' );
        if ( 0 == $counter % 3 ) {
            echo '<div class="clear"></div>
            </div><!-- .hotels.flexbox-wrapper -->';
        }
        $counter++;
    }
}

// Restore original Post Data
wp_reset_postdata(); ?>
