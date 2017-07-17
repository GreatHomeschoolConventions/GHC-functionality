<?php
global $convention_abbreviations;

// arguments
$hotel_args = array(
    'post_type'              => 'hotel',
    'posts_per_page'         => -1,
    'orderby'                => 'menu_order',
    'order'                  => 'ASC',
    'meta_query' => array(
        array(
            'meta_key'  => 'discount_valid_date',
            'value'     => date( 'Ymd' ),
            'compare'   => '<=',
        ),
    ),
);
if ( $this_convention ) {
    $hotel_args['tax_query'] = array(
        array(
            'taxonomy'  => 'ghc_conventions_taxonomy',
            'field'     => 'slug',
            'terms'     => $convention_abbreviations[$this_convention],
        )
    );
}

// the query
$hotel_query = new WP_Query( $hotel_args );

// the loop
if ( $hotel_query->have_posts() ) { ?>
    <h2>Hotel Scam Alert</h2>
    <p>We have received word that a company by the name of Expo Housing Services is making calls in regard to Great Homeschool Conventions&rsquo; hotel accommodations. They do this each year; it is a scam from a company trying to book hotel rooms on behalf of Great Homeschool Conventions.</p>
    <p>We have nothing to do with this company and they do not represent Great Homeschool Conventions in any way.</p>
    <p>Please book your accommodations through one of the options below.</p>
    <?php
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
