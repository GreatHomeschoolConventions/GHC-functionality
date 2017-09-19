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
        <input name="qty-<?php echo $variation_array['variation_id'] ?>" type="number" value="1" min="1" max="20" />
        <?php echo WC_Shortcodes::product_add_to_cart( array( 'id' => $variation_array['variation_id'], 'style' => '', 'show_price' => false, ) ); ?>
    </td>
</tr>
