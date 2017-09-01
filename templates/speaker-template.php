<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
    <?php if ( has_post_thumbnail() ) { ?>
    <div class="speaker-thumbnail">
        <a href="<?php the_permalink(); ?>"><?php
            if ( 'speaker' == get_post_type() && ! isset( $thumbnail_size ) ) {
                $thumbnail_size = 'medium';
            } elseif ( 'special_event' == get_post_type() && ! isset( $thumbnail_size ) ) {
                $thumbnail_size = 'large';
            }
            the_post_thumbnail( $thumbnail_size );
            ?></a>
    </div>
    <?php } ?>

    <?php if ( ! $shortcode_attributes['image_only'] ) { ?>
    <header class="post-header">
        <div class="conventions-attending">
            <?php echo output_convention_icons( get_the_terms( get_the_ID(), 'ghc_conventions_taxonomy' ) ); ?>
        </div>
        <h3 class="post-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
        <?php echo ghc_get_speaker_short_bio( get_the_ID() ); ?>
    </header>
    <!-- entry-header -->

    <div class="excerpt"><?php the_excerpt(); ?></div>
    <?php } ?>
</article><!-- .speaker -->
