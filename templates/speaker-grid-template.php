<?php
#TODO: remove GDLR
?>
<div class="three columns">
    <div class="gdlr-item gdlr-speaker-item">
        <div class="gdlr-ux gdlr-speaker-item-ux">
            <div class="gdlr-speaker-thumbnail"><?php the_post_thumbnail( 'medium' ) ?><a class="gdlr-speaker-thumbnail-overlay-link" href="<?php the_permalink(); ?>"><span class="gdlr-speaker-thumbnail-overlay"></span></a></div>
            <div class="gdlr-speaker-item-content">
                <h3 class="gdlr-speaker-item-title gdlr-skin-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3></div>
        </div>
    </div>
</div>
