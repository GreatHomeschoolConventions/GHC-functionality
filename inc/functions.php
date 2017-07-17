<?php

defined( 'ABSPATH' ) or die( 'No access allowed' );

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
add_action( 'wp_head', 'convention_info' );

/**
 * Output convention icons
 * @param array  $conventions       array of WP_Term objects
 * @param array  $args              array of options arguments
 * @return string $convention_icons HTML string with content
 */
function output_convention_icons( $input_conventions, $args = NULL ) {
    global $conventions, $convention_abbreviations;
    $convention_icons = NULL;

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
    foreach ( $conventions_to_output as $convention ) {
        // get short convention name
        if ( is_object( $convention ) ) {
            $convention_key = array_search( $convention->slug, $convention_abbreviations );
        } elseif ( 2 == strlen( $convention ) ) {
            $convention_key = $convention;
        } else {
            $convention_key = array_flip( $convention_abbreviations )[$convention];
        }

        $convention_icons .= '<a class="convention-link" href="' . get_permalink( $conventions[$convention_key]['landing_page'][0] ) . '">';
            $convention_icons .= '<svg role="img" title="' . $conventions[$convention_key]['title'] . '"><use xlink:href="' . plugins_url( 'SVG/icons.min.svg#icon-' . ucfirst ($convention_abbreviations[$convention_key] ) . '_small', __FILE__ ) . '"></use></svg><span class="fallback ' . $convention_key . '">' . $conventions[$convention_key]['title'] . '</span>';
        $convention_icons .= '</a>';
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
