<?php

defined( 'ABSPATH' ) or die( 'No access allowed' );

/**
 * Include schema.org microdata
 */
include( 'schema.org.php' );

/**
 * Read all convention info into global arrays
 */
function convention_info() {
    global $conventions;
    $conventions = array();

    // WP_Query arguments
    $args = array (
        'post_type'              => array( 'location' ),
        'posts_per_page'         => -1,
        'post_status'            => 'publish',
        'order'                  => 'ASC',
        'orderby'                => 'meta_value',
        'meta_key'               => 'begin_date',
        'no_found_rows'          => true,
    );

    // The Query
    $locations_query = new WP_Query( $args );

    // The Loop
    if ( $locations_query->have_posts() ) {
        while ( $locations_query->have_posts()) {
            $locations_query->the_post();

            $convention_info = array(
                'ID'        => get_the_ID(),
                'title'     => get_the_title(),
                'permalink' => get_the_permalink(),
                'cta_list'  => get_field( 'cta' ),
            );
            $convention_key = strtolower( get_field( 'convention_abbreviated_name' ) );
            $conventions[$convention_key] = array_merge( $convention_info, get_post_meta( get_the_ID() ) );
        }
    }
    wp_reset_postdata();

    /* $conventions: each key is the two-letter abbreviation */

    // convention abbreviations
    global $convention_abbreviations;
    foreach ( $conventions as $key => $values ) {
        $convention_abbreviations[$key] = strtolower( implode( '', $values['convention_short_name'] ) );
    }
    /* $convention_abbreviations: each key is the two-letter abbreviation */

    // convention URLs
    global $convention_urls;
    foreach ( $conventions as $key => $values ) {
        $convention_urls[$key] = get_permalink( $values['ID'] );
    }
    /* $convention_urls: each key is the two-letter abbreviation */

    // convention dates (end dates)
    global $convention_dates;
    foreach ( $conventions as $key => $values ) {
        $convention_dates[$key] = mktime( get_field( 'end_date', $values['ID'] ) );
    }
    /* $convention_dates: each key is the two-letter abbreviation, and the value is the Unix time */

}
add_action( 'get_header', 'convention_info' );

/**
 * Output convention icons
 * @param  array  $input_conventions         conventions to display
 * @param  array  [$args             = NULL] options to use
 * @return string $convention_icons HTML string with content
 */
function output_convention_icons( $input_conventions, $args = NULL ) {
    global $conventions, $convention_abbreviations;
    $convention_icons = NULL;
    $conventions_to_output = array();

    // check whether input is a ID number, array, or array of objects
    if ( is_numeric( $input_conventions ) ) {
        $this_post_terms = get_the_terms( get_the_ID(), 'ghc_conventions_taxonomy' );
        $conventions_to_output = array();
        if ( $this_post_terms ) {
            foreach ( $this_post_terms as $term ) {
                $conventions_to_output[] = $term->slug;
            }
            usort( $conventions_to_output, 'array_sort_conventions' );
        }
    } elseif ( is_string( $input_conventions ) ) {
        // handle two-letter abbreviations
        if ( strlen( $input_conventions ) > 2 ) {
            $input_conventions = str_replace( $convention_abbreviations, array_keys( $convention_abbreviations ), $input_conventions );
        }
        $conventions_to_output[] = $input_conventions;
    } elseif ( is_array( $input_conventions ) ) {
        if ( ! is_object( $input_conventions[0] ) ) {
            // if not an object, then it's an array of abbreviations
            $conventions_to_output = array();
            foreach( $input_conventions as $convention ) {
                if ( strlen( $convention ) > 2 ) {
                    $convention = str_replace( $convention_abbreviations, array_keys( $convention_abbreviations ), $convention );
                }
                $conventions_to_output[] = trim( $convention );
            }
        } else {
            // if an object, then it's a WP_Term object and we can pass directly to the output section
            $conventions_to_output = $input_conventions;
        }
        // sort by date (original WP_Query sorted by begin_date)
        usort( $conventions_to_output, 'array_sort_conventions' );
    }

    // add icons to $convention_icons
    if ( is_array( $convention_abbreviations ) ) {
        foreach ( $conventions_to_output as $convention ) {
            // get short convention name
            if ( is_object( $convention ) ) {
                $convention_key = array_search( $convention->slug, $convention_abbreviations );
            } elseif ( 2 == strlen( $convention ) ) {
                $convention_key = $convention;
            } else {
                $convention_key = array_flip( $convention_abbreviations )[$convention];
            }

            $convention_icons .= '<a class="convention-link" href="' . $conventions[$convention_key]['permalink'] . '">';
                $convention_icons .= '<img src="' . plugins_url( '../dist/images/svg/' . strtoupper( $convention_key ), __FILE__ ) . '.svg" alt="' . $conventions[$convention_key]['title'] . '" class="convention-icon" />';
            $convention_icons .= '</a>';
        }
    }

    // add filter hook
    $convention_icons = apply_filters( 'ghc_convention_icons', $convention_icons );

    return $convention_icons;
}

/**
 * Sort locations in correct order
 * @param  string $a array member 1
 * @param  string $b array member 2
 * @return array  sorted array
 */
function array_sort_conventions( $a, $b ) {
    global $convention_abbreviations;
    $sort_order = NULL;

    // convert objects
    if ( is_object( $a ) && is_object( $b ) ) {
        $a = $a->slug;
        $b = $b->slug;
    }

    // strip spaces
    $a = trim( $a );
    $b = trim( $b );

    // convert two-letter abbreviations to names
    if ( strlen( $a ) == 2 && strlen( $b ) == 2 ) {
        $a = str_replace( array_flip( $convention_abbreviations ), $convention_abbreviations, $a );
        $b = str_replace( array_flip( $convention_abbreviations ), $convention_abbreviations, $b );
    }

    // strip key names from conventions
    if ( is_array( $convention_abbreviations ) ) {
        $convention_names = array_values( $convention_abbreviations );

        // get array key numbers
        $a_position = array_search( $a, $convention_names );
        $b_position = array_search( $b, $convention_names );

        // compare and return sort order
        if ( $a_position > $b_position ) {
            $sort_order = 1;
        } else {
            $sort_order = -1;
        }
    }

    return $sort_order;
}

/**
 * Save featured_video thumbnail to postmeta
 * @param integer $post_id wp post ID
 */
function ghc_opengraph_video_get_meta( $post_id ) {
    if ( get_field( 'featured_video') ) {
        $video_ID = get_video_ID( sanitize_text_field( get_field( 'featured_video' ) ) );

        // get video meta
        $youtube_api_url = 'https://www.googleapis.com/youtube/v3/videos?part=snippet&id=' . $video_ID . '&key=' . get_option( 'options_api_key' );
        $youtube_meta_ch = curl_init();
        curl_setopt( $youtube_meta_ch, CURLOPT_URL, $youtube_api_url );
        curl_setopt( $youtube_meta_ch, CURLOPT_REFERER, site_url() );
        curl_setopt( $youtube_meta_ch, CURLOPT_RETURNTRANSFER, 1 );
        $youtube_meta_json = curl_exec( $youtube_meta_ch );
        curl_close( $youtube_meta_ch );
        $youtube_meta = json_decode( $youtube_meta_json );
        $youtube_thumbnail = $youtube_meta->items[0];

        // save post meta
        update_post_meta( $post_id, 'featured_video_meta', $youtube_thumbnail );
    }
}
add_action( 'acf/save_post', 'ghc_opengraph_video_get_meta', 20 );

/**
 * Retrieve ID from video URL
 * @param  string $video_url public URL of video
 * @return string video ID
 */
function get_video_ID( $video_url ) {
    if ( strpos( $video_url, '//youtu.be' ) !== false ) {
        $video_ID = basename( parse_url( $video_url, PHP_URL_PATH ) );
    } elseif ( strpos( $video_url, 'youtube.com' ) !== false ) {
        parse_str( parse_url( $video_url, PHP_URL_QUERY ), $video_array );
        $video_ID = $video_array['v'];
    }

    return $video_ID;
}

/**
 * Get speaker’s position and company name/link
 * @param  integer $id WP post ID
 * @return string  HTML content
 */
function ghc_get_speaker_short_bio( $id ) {
    $speaker_position = get_field( 'position', $id );
    $speaker_company = get_field( 'company', $id );
    $speaker_company_URL = get_field( 'company_url', $id );

    ob_start();

    if ( $speaker_position || $speaker_company ) {
        echo '<p class="entry-meta speaker-info">';
        if ( $speaker_position ) {
            echo $speaker_position;
        }
        if ( $speaker_position && $speaker_company ) {
            echo ' <span class="separator">|</span> ';
        }
        if ( $speaker_company ) {
            echo ( $speaker_company_URL ? '<a target="_blank" rel="noopener noreferrer" href="' . $speaker_company_URL . '">' : '' ) . $speaker_company . ( $speaker_company_URL ? '</a>' : '' );
        }
        echo '</p>';
    }

    return ob_get_clean();
}

/**
 * Add slug to body class
 * @param  array $classes body classes
 * @return array modified body classes
 */
function ghc_add_slug_body_class( $classes ) {
    global $post;
    if ( isset( $post ) ) {
        $classes[] = $post->post_type . '-' . $post->post_name;
    }
    return $classes;
}
add_filter( 'body_class', 'ghc_add_slug_body_class' );

/**
 * Custom post type grid
 * @param  array  $attributes shortcode parameters, including `convention` as a two-letter abbreviation or full name
 *                           ['post_type']      string      post type
 *                           ['convention']     string      two-letter abbreviation or short convention name
 *                           ['posts_per_page'] integer     number of posts to display; defaults to -1 (all)
 *                           ['offset']         integer     number of posts to skip
 *                           ['show']           string      comma-separated list of elements to show; allowed values include any combination of the following: image, conventions, name, bio, excerpt
 *                           ['image_size']     string      named image size or two comma-separated integers creating an image size array
 * @return string HTML output
 */
function ghc_cpt_grid( $attributes ) {
    global $convention_abbreviations;
    $shortcode_attributes = shortcode_atts( array (
        'post_type'         => 'speaker',
        'convention'        => NULL,
        'posts_per_page'    => -1,
        'offset'            => NULL,
        'show'              => NULL,
        'image_size'        => 'medium',
    ), $attributes );
    $this_convention = strtolower( esc_attr( $shortcode_attributes['convention'] ) );

    // arguments
    $cpt_grid_args = array(
        'post_type'         => $shortcode_attributes['post_type'],
        'posts_per_page'    => $shortcode_attributes['posts_per_page'],
        'offset'            => $shortcode_attributes['offset'],
        'orderby'           => 'menu_order',
        'order'             => 'ASC',
    );

    if ( 'speaker' == $shortcode_attributes['post_type'] ) {
        $cpt_grid_args['tax_query'] = array(
            array(
                'taxonomy'  => 'ghc_speaker_category_taxonomy',
                'field'     => 'slug',
                'terms'     => 'featured',
            ),
        );
    }

    // include only the specified convention
    if ( $shortcode_attributes['convention'] ) {
        $convention_tax_query = array(
            array(
                'taxonomy'  => 'ghc_conventions_taxonomy',
                'field'     => 'slug',
                'terms'     => $convention_abbreviations[$this_convention],
            )
        );

        if ( array_key_exists( 'tax_query', $cpt_grid_args ) ) {
            $cpt_grid_args['tax_query'] = array_merge( $cpt_grid_args['tax_query'], array( 'relation'  => 'AND', ), $convention_tax_query );
        } else {
            $cpt_grid_args['tax_query'] = $convention_tax_query;
        }
    }

    // image size
    if ( strpos( $shortcode_attributes['image_size'], ',' ) !== false ) {
        $shortcode_attributes['image_size'] = str_replace( ' ', '', $shortcode_attributes['image_size'] );
        $thumbnail_size = explode( ',', $shortcode_attributes['image_size'] );
        array_walk( $thumbnail_size, 'intval' );
    } else {
        $thumbnail_size = $shortcode_attributes['image_size'];
    }

    // query
    $cpt_grid_query = new WP_Query( $cpt_grid_args );

    // loop
    ob_start();
    if ( $cpt_grid_query->have_posts() ) {
        echo '<div class="' . $shortcode_attributes['post_type'] . '-container ghc-cpt container">';
        while ( $cpt_grid_query->have_posts() ) {
            $cpt_grid_query->the_post();
            require( plugin_dir_path( __FILE__ ) . '../templates/speaker-template.php' );
        }
        echo '</div>';
    }

    // reset post data
    wp_reset_postdata();

    return ob_get_clean();
}

/**
 * Filter to get the current convention for a CTA
 * @param  array   $value input array
 * @return boolean whether or not this is the correct convention
 */
function get_current_CTA( $value ) {
    if ( ( ! isset( $value['begin_date'] ) || strtotime( $value['begin_date'] ) <= time() ) && ( ! isset( $value['end_date'] ) || strtotime( $value['end_date'] ) >= time() ) ) {
        return true;
    } else {
        return false;
    }
}

/**
 * Add product attribute classes
 * @param  integer $variation_id product variation ID
 * @return string  post_class output
 */
function ghc_product_post_class( $variation_id = '' ) {
    $variation_classes = array();

    if ( $variation_id ) {
        if ( 'product_variation' == get_post_type( $variation_id ) ) {
            $variation = new WC_Product_Variation( $variation_id );
            foreach ( $variation->get_attributes() as $key => $value ) {
                $variation_classes[] = 'attribute_' . $key . '-' . strtolower( str_replace( ' ', '-', $value ) );
            }
        }
    }

    post_class( $variation_classes );
}

/**
 * Format date range
 *
 * @link https://codereview.stackexchange.com/a/78303 Adapted from this answer
 *
 * @param  object|string $d1            start DateTime object or string
 * @param  object|string $d2            end DateTime object or string
 * @param  string        [$format       = ''] input date format if passed as strings
 * @return string        formatted date string
 */
function ghc_format_date_range( $d1, $d2, $format = '' ) {
    if ( is_string( $d1 ) && is_string( $d2 ) && $format ) {
        $d1 = date_create_from_format( $format, $d1 );
        $d2 = date_create_from_format( $format, $d2 );
    }

    if ( $d1->format( 'Y-m-d' ) === $d2->format( 'Y-m-d' ) ) {
        // Same day
        return $d1->format( 'F j, Y' );
    } elseif ( $d1->format( 'Y-m' ) === $d2->format( 'Y-m' ) ) {
        // Same calendar month
        return $d1->format( 'F j' ) . '&ndash;' . $d2->format( 'd, Y' );
    } elseif ( $d1->format( 'Y' ) === $d2->format( 'Y' ) ) {
        // Same calendar year
        return $d1->format( 'F j' ) . '&ndash;' . $d2->format( 'F j, Y' );
    } else {
        // General case (spans calendar years)
        return $d1->format( 'F j, Y' ) . '&ndash;' . $d2->format( 'F j, Y' );
    }
}

/**
 * Get special track related sponsor name(s) and link(s)
 * @param  integer $term_id ghc_special_track term ID
 * @return string  HTML output with sponsor name(s) and link(s)
 */
function ghc_get_special_track_related_sponsor_names( $term_id, $context = 'inline' ) {
    $track_output = '';
    $sponsors = get_field( 'related_sponsors', 'ghc_special_tracks_taxonomy_' . $term_id );
    if ( $sponsors ) {
        $sponsor_index = 1;
        if ( $context === 'inline' ) {
            $track_output .= ' <small>(sponsored by ';
        } elseif ( $context === 'standalone' ) {
            $track_output .= '<p>This track is sponsored by ';
        }
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
        if ( $context === 'inline' ) {
            $track_output .= ')</small>';
        } elseif ( $context === 'standalone' ) {
            $track_output .= '.</p>';
            $track_output .= '<div id="related-sponsors">
                <div class="sponsor-container ghc-cpt container">';
                foreach ( $sponsors as $sponsor ) {
                    $track_output .= '<div class="sponsor">
                    <div class="post-thumbnail">
                    <a href="' . get_permalink( $sponsor ) . '">' . get_the_post_thumbnail( $sponsor, 'post-thumbnail', array( 'class' => 'sponsor' ) ) . '</a>
                    </div>
                    </div><!-- .sponsor -->';
                }
                $track_output .= '</div><!-- .sponsor-container.ghc-cpt.container -->
                </div><!-- #sponsor-container.ghc-cpt.container -->';
        }
    }

    return $track_output;
}

/**
 * Allow ICS and SVG file uploads
 * @param  array $mime_types array of allowed mime types
 * @return array modified array
 */
function ghc_mime_types( $mime_types ) {
    $mime_types['ics'] = 'text/calendar';
    $mime_types['svg'] = 'image/svg+xml';
    return $mime_types;
}
add_filter( 'upload_mimes', 'ghc_mime_types' );

/**
 * Get author bio and convention locations
 * @return string HTML content
 */
function ghc_get_author_bio() {
    ob_start();
    $speaker_meta = get_the_author_meta( 'speaker_match' );

    if ( $speaker_meta ) {
        $this_post_terms = get_the_terms( $speaker_meta, 'ghc_conventions_taxonomy' );
        ?>
        <div class="author-info">
            <p><?php echo get_avatar( get_the_author_meta( 'ID' ), 120 ) . get_the_author_meta( 'description' ); ?></p>
            <?php if ( count( $this_post_terms ) > 0 ) { ?>
                <p>Meet <a href="<?php the_permalink( $speaker_meta ); ?>"><?php the_author(); ?></a> at these conventions:</p>
                <p><?php echo output_convention_icons( $this_post_terms ); ?></p>
            <?php } ?>
        </div>
        <?php
    }
    return ob_get_clean();
}

/**
 * Set sizes atribute for responsive images and better performance
 * @param  array        $attr       markup attributes
 * @param  object       $attachment WP_Post image attachment post
 * @param  string|array $size       named image size or array
 * @return array        markup attributes
 */
function ghc_resp_img_sizes( $attr, $attachment, $size ) {
    if ( is_array( $size ) ) {
        $attr['sizes'] = $size[0] . 'px';
    } elseif ( $size == 'thumbnail-no-crop') {
        $attr['sizes'] = '140px';
    } elseif ( $size == 'pinterest-thumb') {
        $attr['sizes'] = '173px';
    } elseif ( $size == 'pinterest-medium') {
        $attr['sizes'] = '346px';
    } elseif ( $size == 'square-tiny') {
        $attr['sizes'] = '150px';
    } elseif ( $size == 'square-thumb' ) {
        $attr['sizes'] = '250px';
    } elseif ( $size == 'square-small' ) {
        $attr['sizes'] = '400px';
    } elseif ( $size == 'square-medium' ) {
        $attr['sizes'] = '600px';
    } elseif ( $size == 'square-large' ) {
        $attr['sizes'] = '900px';
    } elseif ( $size == 'small-grid-size' ) {
        $attr['sizes'] = '400px';
    } elseif ( $size == 'small-grid-size-medium' ) {
        $attr['sizes'] = '600px';
    } elseif ( $size == 'small-grid-size-large' ) {
        $attr['sizes'] = '800px';
    } elseif ( $size == 'special-event-small' ) {
        $attr['sizes'] = '300px';
    } elseif ( $size == 'special-event-medium' ) {
        $attr['sizes'] = '450px';
    } elseif ( $size == 'special-event-large' ) {
        $attr['sizes'] = '600px';
    }
    return $attr;
}
add_filter( 'wp_get_attachment_image_attributes', 'ghc_resp_img_sizes', 25, 3 );
