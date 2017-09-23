<?php

defined( 'ABSPATH' ) or die( 'No access allowed' );

/**
 * Add custom cart total location
 * @param  array $fragments array of cart total areas
 * @return array modified array of cart total areas
 */
function woocommerce_header_add_to_cart_fragment( $fragments ) {
    global $woocommerce;

    ob_start();

    ?>
    <span class="custom-cart-total"><?php echo $woocommerce->cart->get_cart_total(); ?></span>
    <?php

    $fragments['.custom-cart-total'] = ob_get_clean();

    return $fragments;

}
add_filter('woocommerce_add_to_cart_fragments', 'woocommerce_header_add_to_cart_fragment');

/**
 * Add placeholders to all checkout fields
 * @param  array $fields all checkout fields
 * @return array modified checkout fields
 */
function woocommerce_checkout_fields_placeholders( $fields ) {
    $fields['billing']['billing_first_name']['placeholder'] = 'John';
    $fields['billing']['billing_last_name']['placeholder'] = 'Doe';
    $fields['billing']['billing_company']['placeholder'] = 'ACME Inc.';
    $fields['billing']['billing_address_1']['placeholder'] = '123 Anystreet';
    $fields['billing']['billing_address_2']['placeholder'] = 'Suite 1001';
    $fields['billing']['billing_city']['placeholder'] = 'Anytown';
    $fields['billing']['billing_postcode']['placeholder'] = '12345';
    $fields['billing']['billing_phone']['placeholder'] = '234-567-8901';
    $fields['billing']['billing_email']['placeholder'] = 'john.doe@example.com';

    return $fields;
}
add_filter( 'woocommerce_checkout_fields', 'woocommerce_checkout_fields_placeholders' );

/**
 * Tweak return customer login message
 * @param string $message returning customer string
 * @param string modified message
 */
add_filter( 'woocommerce_checkout_login_message', function() {return 'Been to a GHC convention before?';} );
