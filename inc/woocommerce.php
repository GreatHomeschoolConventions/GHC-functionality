<?php

defined( 'ABSPATH' ) or die( 'No access allowed' );

/**
 * Show product short description on product archive
 */
add_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_single_excerpt', 5);

/**
 * Replace related products with cross-sells
 */
function ghc_show_special_event_tickets() {
    global $post;

    // get terms and filter out the “Registration” item
    $terms = get_the_terms( $post->ID, 'product_cat' );
    if ( $terms ) {
        foreach ( $terms as $term ) {
            $convention_term_array = array( 'Texas', 'Southeast', 'Midwest', 'California', 'Missouri' );
            if ( in_array( $term->name, $convention_term_array ) ) {
                $convention_category = $term->term_id;
            }
        }

        // set up query args
        $special_events_query_args = array (
            'post__not_in'      => array( $post->ID ),
            'posts_per_page'    => -1,
            'post_status'       => 'publish',
            'post_type'         => 'product',
            'orderby'           => 'menu_order',
            'order'             => 'ASC',
            'tax_query'         => array(
                array(
                    'taxonomy'  => 'product_cat',
                    'field'     => 'id',
                    'terms'     => $convention_category
                ),
                array(
                    'taxonomy'  => 'product_cat',
                    'field'     => 'slug',
                    'terms'     => 'special-events'
                )
            )
        );

        $special_events = new WP_Query( $special_events_query_args );

        // loop through results
        if ( $special_events->have_posts() ) {
            ?>
            <div class="cross-sells">
                <h2>Special Events</h2>
                <?php woocommerce_product_loop_start(); ?>

                    <?php while ( $special_events->have_posts() ) : $special_events->the_post(); ?>

                        <?php wc_get_template_part( 'content', 'product' ); ?>

                    <?php endwhile; // end of the loop. ?>

                <?php woocommerce_product_loop_end(); ?>
            </div>
        <?php }

        // reset global query
        wp_reset_query();
    }
}
add_action( 'woocommerce_after_single_product_summary', 'ghc_show_special_event_tickets', 5 );

/**
 * Show lots of columns
 * @param  integer $columns number of columns to display
 * @return integer modified number of columns to display
 */
function woocommerce_remove_cross_sells_columns( $columns ) {
    return 10;
}
add_filter( 'woocommerce_cross_sells_columns', 'woocommerce_remove_cross_sells_columns', 10, 1 );

/**
 * Disable the add to cart button if trying to add another registration
 */
function woocommerce_single_variation_add_to_cart_button() {
    global $product;

    // check this product’s categories to determine if it’s a registration product
    $this_product_terms = get_the_terms( $product->ID, 'product_cat' );
    $check_cart_for_registration = false;
    if ( $this_product_terms ) {
        foreach ( $this_product_terms as $this_term ) {
            if ( 'Registration' == $this_term->name ) {
                $check_cart_for_registration = true;
            }
        }
    }

    if ( $check_cart_for_registration ) {
        $all_convention_IDs = ghc_get_convention_IDs();

        // check cart products against registration items
        foreach( WC()->cart->get_cart() as $cart_item_key => $values ) {
            $in_cart_product = $values['data'];

            if ( in_array( $in_cart_product->id, $all_convention_IDs ) ) {
                $disable_purchase = true;
                add_filter( 'woocommerce_product_single_add_to_cart_text', function() {
                    return 'Sorry, you can only purchase tickets for one convention at a time. Please check out before purchasing more tickets.';
                } );
            }
        }
    }

    // output buttons
    echo '<div class="variations_button">';
        woocommerce_quantity_input( array( 'input_value' => isset( $_POST['quantity'] ) ? wc_stock_amount( $_POST['quantity'] ) : 1 ) );
        echo '<button type="submit" class="single_add_to_cart_button button alt';
        if ( $disable_purchase ) {
            echo ' disabled" disabled="true';
        }
        echo '">' . esc_html( $product->single_add_to_cart_text() ) . '</button>
        <input type="hidden" name="add-to-cart" value="' . absint( $product->id )  .'" />
        <input type="hidden" name="product_id" value="' . absint( $product->id ) . '" />
        <input type="hidden" name="variation_id" class="variation_id" value="" />
    </div>';
}

/**
 * Add custom data fields to cart item metadata
 *
 * @link https://wordpress.stackexchange.com/a/138596 Adapted from this sample code
 *
 * @param  array   $cart_item_meta WC cart item metadata
 * @param  integer $product_id     WC product ID
 * @return array   WC cart itme metadata
 */
function ghc_add_cart_item_family_members( $cart_item_meta, $product_id ) {
    global $woocommerce;
    $cart_item_meta['family_members'] = $_POST['familyMembers'];
    return $cart_item_meta;
}
add_filter( 'woocommerce_add_cart_item_data', 'ghc_add_cart_item_family_members', 10, 2 );

/**
 * Add family member count to cart data
 *
 * @link https://wordpress.stackexchange.com/a/138596 Adapted from this sample code
 *
 * @param  array  $session_data session data
 * @param  array  $values       WC_Cart_Product data
 * @param  string $key          WC_Cart_Product key
 * @return array  session data
 */
function ghc_get_cart_items_from_session( $session_data, $values, $key ) {
    if ( array_key_exists( 'family_members', $values ) ) {
        $session_data['family_members'] = $values['family_members'];
    }
    return $session_data;
}
add_filter( 'woocommerce_get_cart_item_from_session', 'ghc_get_cart_items_from_session', 1, 3 );

/**
 * Add “family members” to cart if greater than 1
 * @param  string $formatted_name HTML formatted name
 * @param  array  $cart_item      WC_Cart_Product data
 * @param  string $cart_item_key  WC_Cart_Product key
 * @return string HTML formatted name
 */
function ghc_cart_item_name_add_family_members( $formatted_name, $cart_item, $cart_item_key ) {
    if ( array_key_exists( 'family_members', $cart_item ) && ! is_null( $cart_item['family_members'] )  && ( $cart_item['family_members'] > 1 ) ) {
        $formatted_name .= '<br/>Family members: ' . esc_attr( $cart_item['family_members'] );
    }
    return $formatted_name;
}
add_filter( 'woocommerce_cart_item_name', 'ghc_cart_item_name_add_family_members', 10, 3 );

/**
 * Set the max special event ticket quantities to number of purchased tickets
 * @return integer max allowed quantity
 */
function ghc_get_max_ticket_quantity() {
    // set default
    $max_quantity = 1;

    // loop over products in cart searching for a product with an attendee-type attribute
    foreach( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {

        $attributes = $cart_item['data']->get_attributes();

        if ( array_key_exists( 'attendee-type', $attributes ) && $attributes['attendee-type'] === 'Family' ) {
            // get family member quantity
            if ( array_key_exists( 'family_members', $cart_item ) ) {
                $max_quantity = (int) $cart_item['family_members'];
            }
        }
    }

    return $max_quantity;
}

/**
 * Set max ticket quantities for simple products
 * @return integer max ticket quantity
 */
function ghc_get_max_ticket_quantity_simple() {
    return ghc_get_max_ticket_quantity();
}
add_filter( 'woocommerce_quantity_input_max', 'ghc_get_max_ticket_quantity_simple' );

/**
 * Set max ticket quantities for simple products
 * @param  array   $variation_data variation data
 * @return integer max ticket quantity
 */
function ghc_get_max_ticket_quantity_variable( $variation_data ) {
    $variation_data['max_qty'] = ghc_get_max_ticket_quantity();
    return $variation_data;
}
add_filter( 'woocommerce_available_variation', 'ghc_get_max_ticket_quantity_variable' );

/**
 * Set max ticket quantities for simple products
 * @param  string  $product_quantity string output from woocommerce_quantity_input
 * @param  string  $cart_item_key    WC_Cart_Product key
 * @param  array   $cart_item        WC_Cart_Product data
 * @return integer max ticket quantity
 */
function ghc_get_max_ticket_quantity_cart( $product_quantity, $cart_item_key, $cart_item ) {
    $product_quantity = str_replace( 'max=""', 'max="' . ghc_get_max_ticket_quantity() . '"', $product_quantity );
    return $product_quantity;
}
add_filter( 'woocommerce_cart_item_quantity', 'ghc_get_max_ticket_quantity_cart', 10, 3 );

/**
 * Get all convention variation IDs
 * @return array all product variation IDs
 */
function ghc_get_convention_IDs() {
    if ( false === ( $all_convention_IDs = get_transient( 'ghc-all-convention-variation-ids' ) ) ) {
        $all_convention_IDs = ghc_set_convention_variation_IDs_transient();
    }

    return $all_convention_IDs;
}

/**
 * Save all convention variation IDs to transient to improve performance
 * @return array all conventian variation product IDs
 */
function ghc_set_convention_variation_IDs_transient() {
    $all_conventions_query_args = array (
        'posts_per_page'    => -1,
        'post_status'       => 'publish',
        'post_type'         => 'product',
        'fields'            => 'ids',
        'tax_query'         => array(
            array(
                'taxonomy'  => 'product_cat',
                'field'     => 'slug',
                'terms'     => 'registration'
            )
        )
    );

    $all_conventions = new WP_Query( $all_conventions_query_args );

    // loop through results and add IDs to an array
    $all_convention_IDs = array();
    if ( $all_conventions->have_posts() ) {
        while( $all_conventions->have_posts() ) {
            $all_conventions->the_post();
            $all_convention_IDs[] = get_the_ID();
        }
    }

    // reset global query
    wp_reset_query();

    set_transient( 'ghc-all-convention-variation-ids', $all_convention_IDs );
    return $all_convention_IDs;
}
add_action( 'save_post_product', 'ghc_set_convention_variation_IDs_transient' );
add_action( 'save_post_product_variation', 'ghc_set_convention_variation_IDs_transient' );

/**
 * Add custom cart total location
 * @param  array $fragments array of cart total areas
 * @return array modified array of cart total areas
 */
function ghc_woocommerce_header_add_to_cart_fragment( $fragments ) {
    global $woocommerce;

    ob_start();

    ?>
    <span class="custom-cart-total"><?php echo $woocommerce->cart->get_cart_total(); ?></span>
    <?php

    $fragments['.custom-cart-total'] = ob_get_clean();

    return $fragments;

}
add_filter('woocommerce_add_to_cart_fragments', 'ghc_woocommerce_header_add_to_cart_fragment');

/**
 * Add placeholders to all checkout fields
 * @param  array $fields all checkout fields
 * @return array modified checkout fields
 */
function ghc_woocommerce_checkout_fields_placeholders( $fields ) {
    $fields['billing']['billing_first_name']['placeholder'] = 'John';
    $fields['billing']['billing_last_name']['placeholder'] = 'Doe';
    $fields['billing']['billing_company']['placeholder'] = 'ACME Inc.';
    $fields['billing']['billing_address_1']['placeholder'] = '123 Anystreet';
    $fields['billing']['billing_address_2']['placeholder'] = 'Suite 1001';
    $fields['billing']['billing_city']['placeholder'] = 'Anytown';
    $fields['billing']['billing_postcode']['placeholder'] = '12345';
    $fields['billing']['billing_phone']['label'] = 'Cell Phone';
    $fields['billing']['billing_phone']['placeholder'] = '234-567-8901';
    $fields['billing']['billing_email']['placeholder'] = 'john.doe@example.com';

    return $fields;
}
add_filter( 'woocommerce_checkout_fields', 'ghc_woocommerce_checkout_fields_placeholders' );

/**
 * Tweak return customer login message
 * @param string $message returning customer string
 * @param string modified message
 */
add_filter( 'woocommerce_checkout_login_message', function() {return 'Been to a GHC convention before?';} );

/**
 * Auto-complete all orders
 */
function ghc_auto_complete_order( $order_id ) {
    if ( ! $order_id ) {
        return;
    }

    $order = wc_get_order( $order_id );
    $order->update_status( 'completed' );
}
add_action( 'woocommerce_thankyou', 'ghc_auto_complete_order' );
