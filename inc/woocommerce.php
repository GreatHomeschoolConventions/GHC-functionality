<?php

defined( 'ABSPATH' ) or die( 'No access allowed' );

/**
 * Show product short description on registration page
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
    if ( $this_product_terms ) {
        foreach ( $this_product_terms as $this_term ) {
            if ( 'Registration' == $this_term->name ) {
                $check_cart_for_registration = true;
            }
        }
    }

    if ( $check_cart_for_registration ) {
        // set up query args
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
        $all_convention_variation_IDs = array();
        if ( $all_conventions->have_posts() ) {
            while( $all_conventions->have_posts() ) {
                $all_conventions->the_post();
                $all_convention_variation_IDs[] = get_the_ID();
            }
        }

        // reset global query
        wp_reset_query();

        // check cart products against registration items
        foreach( WC()->cart->get_cart() as $cart_item_key => $values ) {
            $in_cart_product = $values['data'];

            if ( in_array( $in_cart_product->id, $all_convention_variation_IDs ) ) {
                $disable_purchase = true;
                add_filter( 'woocommerce_product_single_add_to_cart_text', function() {
                    return 'Please check out before adding another convention to your cart.';
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
 * Set the max special event ticket quantities to number of purchased tickets
 */
function ghc_check_for_individual_registration_in_cart() {
    // loop over products in cart searching for an individual product
    foreach( WC()->cart->get_cart() as $cart_item_key => $values ) {
        if ( isset( $values['variation']['attribute_attendee-type'] ) ) {
            // add filter for simple products
            add_filter( 'woocommerce_quantity_input_max', function() { return 0; } );
            // add filter for variable products
            add_filter( 'woocommerce_available_variation', function() { return 0; } );
        } elseif ( isset( $values['variation']['attribute_attendee-type'] ) && strpos( $values['variation']['attribute_attendee-type'], 'Individual' ) !== false ) {
            // add filter for simple products
            add_filter( 'woocommerce_quantity_input_max', function() { return 1; } );
            // add filter for variable products
            add_filter( 'woocommerce_available_variation', 'ghc_restrict_max_quantity_variable' );
        } else {
            // get the addons quantity and restrict to that number
            foreach( $values['addons'] as $value ) {
                if ( 'Family members' == $value['name'] ) {
                    global $max_special_event_tickets;
                    $max_special_event_tickets = esc_attr( $value['value'] );
                    // add filter for simple products
                    add_filter( 'woocommerce_quantity_input_max', 'ghc_restrict_max_quantity_simple' );
                    // add filter for variable products
                    add_filter( 'woocommerce_available_variation', 'ghc_restrict_max_quantity_variable' );
                }
            }
        }
    }
}
add_action( 'woocommerce_single_product_summary', 'ghc_check_for_individual_registration_in_cart' );

/**
 * Restrict product max quantity
 * @return integer max quantity to sell
 */
function ghc_restrict_max_quantity_simple() {
    global $max_special_event_tickets;
    return $max_special_event_tickets;
}

/**
 * Restrict variable product max quantity
 * @param  array $variations array of variations
 * @return array modified array
 */
function ghc_restrict_max_quantity_variable( $variations ) {
    global $max_special_event_tickets;

    if ( $max_special_event_tickets ) {
        $variations['max_qty'] = $max_special_event_tickets;
    } else {
        $variations['max_qty'] = '1';
    }
    return $variations;
}

/**
 * Restrict max quantity for products in cart
 * @param  array  $product_quantity array with input name, value, max, and min values
 * @param  string $cart_item_key    cart item key
 * @param  array  $cart_item        cart item
 * @return array  modified $product_quantity array
 */
function ghc_cart_item_quantity( $product_quantity, $cart_item_key, $cart_item ) {
    foreach( WC()->cart->get_cart() as $cart_item_key => $values ) {
        if ( isset( $values['variation']['attribute_attendee-type'] ) && strpos( $values['variation']['attribute_attendee-type'], 'Individual' ) !== false ) {
            // set max quantity to 1 if Individual is present for simple products
            $product_quantity = str_replace( 'min="0"', 'min="0" max="1"', $product_quantity );
        } else {
            foreach( $values['addons'] as $value ) {
                if ( 'Family members' == $value['name'] ) {
            // set max quantity to number of family members
            $product_quantity = str_replace( 'min="0"', 'min="0" max="' . $value['value'] . '"', $product_quantity );
                }
            }
        }
    }
    return $product_quantity;
}
add_filter( 'woocommerce_cart_item_quantity', 'ghc_cart_item_quantity', 10, 3 );
