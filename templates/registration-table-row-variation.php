<tr id="post-<?php echo $variation_array['variation_id']; ?>" <?php ghc_product_post_class( $variation_array['variation_id'] ); ?>>
    <td class="title">
        <?php echo $variation->get_title(); ?><br/>
        <?php
        $attribute_string = '';
        foreach ( $variation->get_variation_attributes() as $key => $value ) {
            $attribute_string .= $value . ', ';
        }
        echo rtrim( $attribute_string, ', ' );
        ?>
    </td>
    <td class="price"><?php echo $variation->get_price_html(); ?></td>
    <td class="actions">
        <?php
        $product_terms = get_the_terms( get_the_ID(), 'product_cat' );
        $registration_product = false;

        foreach ( $product_terms as $term ) {
            if ( 'registration' == $term->slug ) {
                $registration_product = true;
            }
        }

        if ( $registration_product ) {
            echo '<input class="qty" name="qty-' . $variation_array['variation_id']  . '" type="hidden" value="1" min="1" max="1" />
            <label for="family-members"><input name="family-members-display" type="number" value="1" disabled /> family members</label>';
        } else {
            echo '<label for="qty-' . $variation_array['variation_id'] . '"><input class="qty" name="qty-' . $variation_array['variation_id']  . '" type="number" value="1" min="1" max="20" /> <span class="tickets-qty">tickets</span></label>';
        }
        ?>
        <?php echo WC_Shortcodes::product_add_to_cart( array( 'id' => $variation_array['variation_id'], 'style' => '', 'show_price' => false, ) ); ?>
    </td>
</tr>
