<?php
#TODO: remove GDLR
?>
<div class="four columns"><div class="gdlr-item gdlr-blog-grid"><div class="gdlr-ux gdlr-blog-grid-ux">
<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
    <div class="gdlr-standard-style">
        <header class="post-header">
            <h3 class="gdlr-blog-title"><a href="<?php the_field( 'exhibitor_URL' ); ?>"><?php the_title(); ?></a></h3>
            <div class="clear"></div>
        </header><!-- entry-header -->

        <p><a href="<?php the_field( 'exhibitor_URL' ) ?>" target="_blank">Visit website&rarr;</a></p>
    </div>
</article><!-- #post -->
</div></div></div>
