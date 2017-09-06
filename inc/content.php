<?php

defined( 'ABSPATH' ) or die( 'No access allowed' );

/**
 * Add author info to posts
 * @param  string $content HTML content
 * @return string modified HTML content
 */
function ghc_meet_the_author( $content ) {
    if ( is_singular() && get_the_author_meta( 'speaker_match' ) ) {
        ob_start(); ?>
        <div class="author-info">
            <p><?php echo get_avatar( get_the_author_meta( 'ID' ), 120 ) . get_the_author_meta( 'description' ); ?></p>
            <p>Meet <a href="<?php the_permalink( get_the_author_meta( 'speaker_match' ) ); ?>"><?php the_author(); ?></a> at these conventions:</p>
            <p><?php echo output_convention_icons( get_the_terms( get_the_author_meta( 'speaker_match' ), 'ghc_conventions_taxonomy' ) ); ?></p>
        </div>
        <?php
        $author_info = ob_get_clean();
    }
    return $content . $author_info;
}
add_filter( 'the_content', 'ghc_meet_the_author', 50 );

/**
 * Add related sponsor(s) to posts/pages
 * @param  string $content HTML content
 * @return string modified HTML content
 */
function ghc_related_sponsors( $content ) {
    if ( is_singular() ) {
        // get related sponsors
        $related_sponsors = get_field( 'related_sponsors' );

        if ( $related_sponsors ) {
            // set up query args
            $related_sponsors_query_args = array(
                'post_type'         => 'sponsor',
                'orderby'           => 'menu_order',
                'order'             => 'ASC',
                'posts_per_page'    => -1,
                'post__in'          => $related_sponsors,
            );

            $related_sponsors_query = new WP_Query( $related_sponsors_query_args );

            if ( $related_sponsors_query->have_posts() ) {
                $content .= '<div id="sponsor-stripe">
                <h3 class="related-sponsors">Sponsors</h3>
                <div class="sponsors">';

                while ( $related_sponsors_query->have_posts() ) {
                    $related_sponsors_query->the_post();
                    $content .= '<div class="sponsor">';
                    $grayscale_logo = get_field( 'grayscale_logo' );
                    $permalink = get_permalink();

                    if ( $grayscale_logo ) {
                        $content .= '<a href="' . $permalink . '"><img class="wp-post-image sponsor wp-image-' . $grayscale_logo['id'] . '" src="' . $grayscale_logo['url'] . '" alt="' . $grayscale_logo['alt'] . '" title="' . $grayscale_logo['title'] . '" /></a>';
                    } else {
                        $content .= '<a href="' . $permalink . '">' . get_the_post_thumbnail() . '</a>';
                    }
                    $content .= '</div><!-- .sponsor -->';
                }
                $content .= '</div><!-- .sponsors -->
                </div><!-- #sponsor-stripe -->';
            }

            // reset post data
            wp_reset_postdata();
        }
    }
    return $content;
}
add_filter( 'the_content', 'ghc_related_sponsors', 15 );

/**
 * Add Pinterest image to singular content
 * @param  string $content HTML content string
 * @return string modified HTML content string
 */
function ghc_pinterest_image( $content ) {
    $pinterest_image = get_field( 'pinterest_image' );
    if ( is_singular() && $pinterest_image ) {
        // get description
        $description = get_post_meta( get_the_ID(), '_yoast_wpseo_metadesc', true );
        if ( ! $description ) {
            $description = get_the_title();
        }

        $content = '<figure class="pinterest-image"><a target="_blank" href="http://www.pinterest.com/pin/create/button/?url=' . get_permalink() . '&media=' . $pinterest_image['url'] . '&description=' . $description . '">' . wp_get_attachment_image( $pinterest_image['ID'], 'pinterest-thumb' ) . '</a></figure>' . $content;
    }
    return $content;
}
add_filter( 'the_content', 'ghc_pinterest_image' );

/**
 * Add video OpenGraph data if featured_video is specified
 */
function ghc_opengraph_video() {
    $featured_video = get_field( 'featured_video' );
    if ( $featured_video ) {
        $video_ID = get_video_ID( $featured_video );
        $featured_video_meta = get_post_meta( get_the_ID(), 'featured_video_meta', true );

        // video
        echo '<meta property="og:video" content="' . $featured_video . '" />';
        echo strpos( $featured_video, 'https' ) !== false ? '<meta property="og:video:secure_url" content="' . $featured_video . '" />' : '' ;
        echo '<meta property="og:video:width" content="' . $featured_video_meta->snippet->thumbnails->maxres->width . '" />';
        echo '<meta property="og:video:height" content="' . $featured_video_meta->snippet->thumbnails->maxres->height . '" />';

        // placeholder image
        echo '<meta property="og:image" content="' . $featured_video_meta->snippet->thumbnails->maxres->url . '" />';
    }
}
add_action( 'wp_head', 'ghc_opengraph_video', 8 );

/**
 * Add special track info to speakers/workshops
 * @param  string $content HTML content
 * @return string modified HTML content
 */
function ghc_list_special_tracks( $content ) {
    $intro_content = '';

    if ( is_singular( array( 'speaker', 'workshop' ) ) ) {
        $special_tracks = wp_get_post_terms( get_the_ID(), 'ghc_special_tracks_taxonomy' );
        $special_tracks_count = count( $special_tracks );

        if ( $special_tracks_count > 0 ) {
            // set up content
            $track_output = '';
            $track_index = 1;
            foreach ( $special_tracks as $special_track ) {
                $track_output .= '<a href="' . get_term_link( $special_track->term_id, 'ghc_special_tracks_taxonomy' ) . '">' . $special_track->name . '</a> track';

                // check for sponsors
                $sponsors = get_field( 'related_sponsors', 'ghc_special_tracks_taxonomy_' . $special_track->term_id );
                if ( $sponsors ) {
                    $sponsor_index = 1;
                    $track_output .= ' (sponsored by ';
                    foreach( $sponsors as $sponsor ) {
                        $track_output .= '<a href="' . get_permalink( $sponsor ) . '">' . get_the_title( $sponsor ) . '</a>';
                        if ( count( $sponsors ) > 2 ) {
                            $track_output .= ', ';
                            if ( count( $sponsors ) == $index ) {
                                $track_output .= ' and ';
                            }
                        } elseif ( count( $sponsors ) == 2 && $sponsor_index != 2 ) {
                            $track_output .= ' and ';
                        }
                        $sponsor_index++;
                    }
                    $track_output .= ')';
                }

                if ( $special_tracks_count > 2 ) {
                    $track_output .= ', ';
                    if ( $track_index == $special_tracks_count ) {
                        $track_output .= ' and ';
                    }
                } elseif ( $special_tracks_count == 2 && $track_index != 2 ) {
                    $track_output .= ' and ';
                }
                $track_index++;
            }

            // output content
            $intro_content = '<h4 class="related-special-tracks">Special Tracks</h2>';

            if ( 'speaker' == get_post_type() ) {
                $intro_content =  sprintf( '
                <p>We are honored to have %1$s participating in this year&rsquo;s %2$s.</p>',
                               get_the_title(),
                               $track_output
                               );
            } elseif ( 'workshop' == get_post_type() ) {
                $intro_content =  sprintf( '
                <p>%1$s is part of this year&rsquo;s %2$s.</p>',
                               get_the_title(),
                               $track_output
                               );
            }
        }
    }

    return $intro_content . $content;
}
add_filter( 'the_content', 'ghc_list_special_tracks', 8 );

/**
 * Add speaker location info to each speaker/workshop
 * @param  string $content HTML content
 * @return string modified HTML content
 */
function ghc_speaker_show_locations( $content ) {
    if ( is_singular( array( 'speaker', 'workshop' ) ) ) {
        $content = '<div class="conventions">' . output_convention_icons( get_the_terms( get_the_ID(), 'ghc_conventions_taxonomy' ) ) . '</div>' . $content;
    }

    return $content;
}
add_action( 'the_content', 'ghc_speaker_show_locations', 11 );

/**
 * Add workshops list to each speaker and related workshops to each workshop
 * @param  string $content HTML content
 * @return string modified HTML content
 */
function ghc_list_related_workshops( $content ) {
    $workshop_content = '';

    if ( is_singular( array( 'speaker', 'workshop' ) ) ) {
        if ( 'speaker' == get_post_type() ) {
            $speaker_id = get_the_ID();
        } elseif ( 'workshop' == get_post_type() ) {
            $related_speakers = get_field( 'speaker' );
            if ( count( $related_speakers ) == 1 ) {
                $speaker_id = $related_speakers[0]->ID;
            }
        }

        if ( is_int( $speaker_id ) ) {
            $workshop_content .= '<div class="related-workshops"><h2>' . ( 'workshop' == get_post_type() ? 'Other ' : '' ) . 'Workshops by ' . ( 'speaker' == get_post_type() ? get_the_title() : $related_speakers[0]->post_title ) . '</h2>';

            $related_workshops_args = array(
                'post_type'         => 'workshop',
                'posts_per_page'    => -1,
                'orderby'           => 'title',
                'order'             => 'ASC',
                'meta_query'        => array(
                    'relation'      => 'AND',
                    array(
                        'key'       => 'speaker',
                        'compare'   => 'EXISTS',
                    ),
                    array(
                        'key'       => 'speaker',
                        'value'     => '"' . $speaker_id . '"',
                        'compare'   => 'LIKE',
                    ),
                ),
            );

            $related_workshops = new WP_Query( $related_workshops_args );

            if ( $related_workshops->have_posts() ) {
                $workshop_content .= '<ul class="related-workshops">';
                while ( $related_workshops->have_posts() ) {
                    $related_workshops->the_post();
                    $workshop_content .= '<li><a href="' . get_permalink() . '">' . get_the_title() . '</a></li>';
                }
                $workshop_content .= '</ul>';
            }

            wp_reset_query();

            $workshop_content .= '</div>';
        }
    }

    return $content . $workshop_content;
}
add_action( 'the_content', 'ghc_list_related_workshops', 11 );