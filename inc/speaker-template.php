                    <div class="gdlr-item gdlr-blog-grid">
                        <div class="gdlr-ux gdlr-blog-grid-ux">
                            <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                                <div class="gdlr-standard-style">
                                    <?php if ( has_post_thumbnail() ) { ?>
                                    <div class="gdlr-blog-thumbnail">
                                        <a href="<?php the_permalink(); ?>"><?php the_post_thumbnail( 'small-grid-size' ); ?></a>
                                    </div>
                                    <?php } ?>

                                    <header class="post-header">
                                        <div class="gdlr-blog-info gdlr-info gdlr-info-font"><span class="gdlr-separator">/</span>
                                            <div class="conventions-attending">
                                                <?php echo output_convention_icons( get_the_terms( get_the_ID(), 'ghc_conventions_taxonomy' ) ); ?>
                                            </div>
                                            <div class="clear"></div>
                                        </div>
                                        <h3 class="gdlr-blog-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
                                        <div class="clear"></div>
                                    </header>
                                    <!-- entry-header -->

                                    <div class="gdlr-blog-content"><?php the_excerpt(); ?>
                                        <div class="clear"></div>
                                    </div>
                                </div>
                            </article>
                            <!-- #post -->
                        </div>
                    </div>
