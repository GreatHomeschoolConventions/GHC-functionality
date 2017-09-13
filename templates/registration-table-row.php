<tr id="post-<?php the_ID() ?>" <?php ghc_product_post_class(); ?>>
    <td class="title"><?php echo $product->get_title(); ?></td>
    <td class="price"><?php echo $product->get_price_html(); ?></td>
    <td class="actions">
        <input name="qty-<?php echo $variation_array['variation_id'] ?>" type="number" value="1" min="1" max="20" />
        <a class="button cart-add" href="<?php echo $variation->add_to_cart_url() ?>">Add</a>
        <a class="button cart-remove" href="<?php echo $variation->add_to_cart_url() ?>">Remove</a>
    </td>
</tr>
