<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
    <header class="post-header">
        <h3><a href="<?php the_field( 'exhibitor_URL' ); ?>" target="_blank" rel="noopener"><?php the_title(); ?></a></h3>
    </header><!-- entry-header -->

    <p><a href="<?php the_field( 'exhibitor_URL' ) ?>" target="_blank" rel="noopener">Visit website&rarr;</a></p>
</article><!-- #post -->
