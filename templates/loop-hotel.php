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
    <h3>Hotel Scam Alert</h3>

    <p>We have received word that a third-party company is contacting Great Homeschool Conventions attendees. They do this each year, and it is a scam. Their rates are $45&ndash;70 higher than what GHC has negotiated with area hotels.</p>

    <p>We have nothing to do with this company and they do not represent us in any way.</p>

    <p>Please book your accommodations through one of the options below to take advantage of our negotiated rates.</p>

    <div class="hotel-container">
    <?php
    while ( $hotel_query->have_posts() ) {
        $hotel_query->the_post();
        global $conventions;
        include( 'hotel-template.php' );
    }
    echo '</div>';
}

// Restore original Post Data
wp_reset_postdata(); ?>
