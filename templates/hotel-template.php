<?php
    if ( is_post_type_archive( 'hotel' ) ) {
        $conventions_taxonomy = get_the_terms( get_the_ID(), 'ghc_conventions_taxonomy' );
        $this_convention = array_flip( $convention_abbreviations )[$conventions_taxonomy[0]->slug];
    }
    $post_classes = array(
        $this_convention,
        get_field( 'sold_out' ) ? 'sold-out' : ''
    );
?>
<div class="<?php echo implode( ' ', get_post_class( $post_classes ) ); ?>">
    <?php if ( has_post_thumbnail() ) { ?>
    <div class="accommodation-thumbnail"><?php the_post_thumbnail( 'small-grid-size' ); ?></div>
    <?php } ?>
    <div class="accommodation-content-wrapper">
        <?php
        if ( is_post_type_archive( 'hotel' ) ) {
            echo do_shortcode( '[convention_icon convention="' . $this_convention . '"]' );
        }
        ?>
        <h3 class="accommodation-title"><?php the_title(); ?></h3>
        <?php include('hotel-details.php'); ?>
    </div>
    <?php if ( get_field( 'hotel_URL' ) && ! get_field( 'sold_out' ) ) { ?><a class="accommodation-button-text button" target="_blank" rel="noopener" href="<?php the_field( 'hotel_URL' ); ?>">BOOK ONLINE NOW</a><?php } ?>
</div>
