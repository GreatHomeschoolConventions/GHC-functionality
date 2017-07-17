<?php
#TODO: remove GDLR
?>
<?php
    if ( is_post_type_archive( 'hotel' ) ) {
        $conventions_taxonomy = get_the_terms( get_the_ID(), 'ghc_conventions_taxonomy' );
        $this_convention = array_flip( $convention_abbreviations )[$conventions_taxonomy[0]->slug];
    }
?>
<div class="four columns <?php echo $this_convention; ?>">
    <div class="gdlr-item gdlr-accommodation-item hotel <?php echo get_field( 'sold_out' ) ? 'sold-out' : ''; ?>">
        <?php if ( has_post_thumbnail() ) { ?>
        <div class="accommodation-thumbnail"><?php the_post_thumbnail( 'small-grid-size' ); ?></div>
        <?php } ?>
        <div class="accommodation-content-outer-wrapper">
            <div class="accommodation-content-wrapper">
                <?php
                if ( is_post_type_archive( 'hotel' ) ) {
                    echo do_shortcode( '[convention_icon convention="' . $this_convention . '"]' );
                }
                ?>
                <h3 class="accommodation-title"><?php the_title(); ?></h3>
                <?php include('hotel-details.php'); ?>
            </div>
            <?php if ( get_field( 'hotel_URL' ) && ! get_field( 'sold_out' ) ) { ?><a class="accommodation-button-text gdlr-button" target="_blank" href="<?php the_field( 'hotel_URL' ); ?>">BOOK ONLINE NOW</a><?php } ?>
        </div>
    </div>
</div>
