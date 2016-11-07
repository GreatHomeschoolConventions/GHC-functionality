<div class="accommodation-caption gdlr-info-font"><?php
    echo get_field( 'sold_out' ) ? '<h4 class="sold-out">Sold Out</h4>' : '';

    echo get_field( 'discount_rate' ) ? '<p>From $' . get_field( 'discount_rate' ) . ' per night' : '';
    echo get_field( 'discount_rate_details' ) ? '<br/>' . get_field( 'discount_rate_details' ) . '</p>' : '</p>';
    echo get_field( 'discount_rate2' ) ? '<p>From $' . get_field( 'discount_rate2' ) . ' per night' : '';
    echo get_field( 'discount_rate2_details' ) ? '<br/>' . get_field( 'discount_rate2_details' ) . '</p>' : '</p>';
    echo get_field( 'discount_rate3' ) ? '<p>From $' . get_field( 'discount_rate3' ) . ' per night' : '';
    echo get_field( 'discount_rate3_details' ) ? '<br/>' . get_field( 'discount_rate3_details' ) . '</p>' : '</p>';

    echo '<div class="content">' . get_the_content() . '</div>';;

    echo get_field( 'discount_valid_date' ) ? '<p>Discount valid through: ' . get_field( 'discount_valid_date' ) . '</p>' : '';

    echo get_field( 'discount_group_code' ) ? '<p>Group code: ' . get_field( 'discount_group_code' ) . '</p>' : '';
    echo get_field( 'discount_rate_details' ) ? '<p>Details: ' . get_field( 'discount_rate_details' ) . '</p>' : '';

    echo get_field( 'hotel_phone' ) ? '<p>Phone: ' . get_field( 'hotel_phone' ) . '</p>' : '';

    if ( get_field( 'location' ) ) {
        $location = get_field( 'location' );

        $convention_address = $conventions[$this_convention]['address'][0] . ' ' . $conventions[$this_convention]['city'][0] . ', ' . $conventions[$this_convention]['state'][0] . ' ' . $conventions[$this_convention]['zip'][0];
        echo '<p><a target="_blank" href="https://www.google.com/maps/dir/' . str_replace( ' ', '+', $location['address'] ) . '/' . str_replace( ' ', '+', $convention_address ) . '/">Directions to ' . $conventions[$this_convention]['convention_center_name'][0] . ' <span class="fa fa-map"></span></a></p>
        <div class="map" data-source-address="' . $location['address'] . '" data-destination-address="' . $convention_address . '"></div>';
    }
?></div>