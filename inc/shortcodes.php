<?php

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

// add shortcode for convention CTA
add_shortcode( 'convention_cta', 'convention_cta_shortcode' );
function convention_cta_shortcode( $attributes ) {
    global $conventions, $convention_abbreviations;

    $shortcode_attributes = shortcode_atts( array (
        'convention'    => NULL,
    ), $attributes );
    $this_convention = strtolower( esc_attr( $shortcode_attributes['convention'] ) );

    $CTA_array = array_filter( $conventions[$this_convention], 'get_current_CTA', ARRAY_FILTER_USE_BOTH );
    $current_CTA = str_replace( '_begin_date', '', key( $CTA_array ) );

    return apply_filters( 'the_content', $conventions[$this_convention][$current_CTA . '_cta_content'][0] );
}

// filter to get only the currently active CTA based on dates
function get_current_CTA( $value, $key ) {
    // check if this is a CTA key
    if ( 0 === strpos( $key, 'cta_' ) ) {
        // check begin and end dates
        if ( ( false !== strpos( $key, '_begin_date' ) && '' !== $value[0] && strtotime( $value[0] ) <= time() ) || ( ( false !== strpos( $key, '_end_date' ) && '' !== $value[0] && strtotime( $value[0] ) >= time() ) ) ) {
            return true;
        } else {
            return false;
        }
    }
}

// add shortcode for convention icon only
// accepts `convention` attribute either as abbreviation or full name
add_shortcode( 'convention_icon', 'convention_icon_shortcode' );
function convention_icon_shortcode( $attributes ) {
    $shortcode_attributes = shortcode_atts( array (
        'convention'    => NULL,
    ), $attributes );
    $this_convention = strtolower( esc_attr( $shortcode_attributes['convention'] ) );

    return output_convention_icons( $this_convention );
}

// add shortcode for discretionary registration buttons
// accepts `convention` attribute either as abbreviation or full name
add_shortcode( 'discretionary_registration', 'discretionary_registration_shortcode' );
function discretionary_registration_shortcode( $attributes ) {
    $shortcode_attributes = shortcode_atts( array (
        'convention'    => NULL,
        'year'          => NULL,
        'intro_text'    => NULL,
    ), $attributes );
    // first check agaist dates
    global $convention_dates;
    foreach( $convention_dates as $convention_date ) {
        if ( time() <= $convention_date ) {
            $continue = true;
        }
    }

    if ( $continue == true ) {
        global $convention_abbreviations, $convention_urls;

        // output wrapper
        $shortcode_content .= '<div class="discretionary-registration">';

        // output intro text
        if ( $shortcode_attributes['intro_text'] ) {
            $shortcode_content .= '<p>' . esc_attr( $shortcode_attributes['intro_text'] ) . '</p>';
        }

        // output top of button
        $shortcode_content .= '<h3 class="large-button orange speaker-convention-link convention-shortcode"><a href="' . esc_url( home_url() ) . '/registration/">Register <strong>now</strong>!';

        // output line break if conventions present
        if ( $convention ) {
            $shortcode_content .= '<br/>';
        }

        // output each convention icon
        $shortcode_content .= output_convention_icons( explode( ',', $shortcode_attributes['convention'] ) );

        // output bottom of button
        $shortcode_content .= '</a></h3></div>' . "\n";

        return $shortcode_content;
    }
}

// add shortcode for hotels grid on location pages
add_shortcode( 'hotel_grid', 'hotel_grid_shortcode' );
function hotel_grid_shortcode( $attributes ) {
    $shortcode_attributes = shortcode_atts( array (
        'convention'    => NULL,
    ), $attributes );
    $this_convention = strtolower( esc_attr( $shortcode_attributes['convention'] ) );

    ob_start();
    include( 'archive-hotel.php' );
    return ob_get_clean();
}

// add shortcode for related sponsors
add_shortcode( 'related_sponsor', 'related_sponsor_shortcode' );
function related_sponsor_shortcode( $attributes ) {
    // get related sponsors
    $related_sponsors = get_field( 'related_sponsors' );

    // set up query args
    $related_sponsors_query_args = array(
        'post_type'         => 'sponsor',
        'orderby'           => 'menu_order',
        'order'             => 'ASC',
        'posts_per_page'    => -1,
    );

    if ( $related_sponsors ) {
        $related_sponsors_query_args['post__in'] = $related_sponsors;
    }

    $related_sponsors_query = new WP_Query( $related_sponsors_query_args );

    if ( $related_sponsors_query->have_posts() ) {
        $shortcode_content = '<div id="sponsor-stripe">
        <h3 class="gdlr-item-title gdlr-skin-title gdlr-title-small">SPONSORS</h3>
        <div class="sponsors">';

        while ( $related_sponsors_query->have_posts() ) {
            $related_sponsors_query->the_post();
            $shortcode_content .= '<div class="sponsor">';
            $grayscale_logo = get_field( 'grayscale_logo' );
            $permalink = get_permalink();

            if ( $grayscale_logo ) {
                $shortcode_content .= '<a href="' . $permalink . '"><img class="wp-post-image sponsor wp-image-' . $grayscale_logo['id'] . '" src="' . $grayscale_logo['url'] . '" alt="' . $grayscale_logo['alt'] . '" title="' . $grayscale_logo['title'] . '" /></a>';
            } else {
                $shortcode_content .= '<a href="' . $permalink . '">' . get_the_post_thumbnail() . '</a>';
            }
            $shortcode_content .= '</div><!-- .sponsor -->';
        }
        $shortcode_content .= '</div><!-- .sponsors -->
        </div><!-- #sponsor-stripe -->';
    }

    return $shortcode_content;
}

// add shortcode for speaker page
add_shortcode( 'speaker_archive', 'speaker_archive_shortcode' );
function speaker_archive_shortcode() {
    ob_start();
    include( 'archive-speaker.php' );
    return ob_get_clean();
}

// add shortcode for speaker grid
add_shortcode( 'speaker_grid', 'speaker_grid_shortcode' );
function speaker_grid_shortcode( $attributes ) {
    global $convention_abbreviations;
    $shortcode_attributes = shortcode_atts( array (
        'convention'    => NULL,
    ), $attributes );
    $this_convention = strtolower( esc_attr( $shortcode_attributes['convention'] ) );

    // arguments
    $speaker_grid_args = array(
        'post_type'         => 'speaker',
        'posts_per_page'    => -1,
        'orderby'           => 'menu_order',
        'order'             => 'ASC',
        'tax_query' => array(
            array(
                'taxonomy'  => 'ghc_conventions_taxonomy',
                'field'     => 'slug',
                'terms'     => $convention_abbreviations[$this_convention],
            )
        ),
    );

    // query
    $speaker_grid_query = new WP_Query( $speaker_grid_args );

    // loop
    if ( $speaker_grid_query->have_posts() ) {
        echo '<div class="speaker-item-wrapper">
            <div class="speaker-item-holder gdlr-speaker-type-round">';
        while ( $speaker_grid_query->have_posts() ) {
            $speaker_grid_query->the_post();
            include( 'speaker-grid-template.php' );
        }
        echo '</div>
        </div>';
    }

    // reset post data
    wp_reset_postdata();
}

// add shortcode for speaker info
// accepts `postid`, `pagename`, `align`, `no_conventions`, and `photo_only` attributes
add_shortcode( 'speaker_info', 'speaker_info_shortcode' );
function speaker_info_shortcode( $attributes ) {
    $shortcode_attributes = shortcode_atts( array(
        'postid'            => NULL,
        'pagename'          => NULL,
        'align'             => NULL,
        'no_conventions'    => NULL,
        'photo_only'        => NULL,
        'extra_classes'     => NULL,
    ), $attributes );
    $this_postid = esc_attr( $shortcode_attributes['postid'] );
    $this_pagename = esc_attr( $shortcode_attributes['pagename'] );
    $this_alignment = esc_attr( $shortcode_attributes['align'] );
    $no_conventions = esc_attr( $shortcode_attributes['no_conventions'] );
    $photo_only = esc_attr( $shortcode_attributes['photo_only'] );
    $extra_classes = esc_attr( $shortcode_attributes['extra_classes'] );

    // WP_Query arguments
    $args = array (
        'post_type'              => array( 'speaker' ),
        'posts_per_page'         => '-1',
    );
    if ( $this_postid ) { $args['p'] = $this_postid; }
    if ( $this_pagename ) { $args['pagename'] = $this_pagename; }
    if ( ( $this_alignment ) && ( strpos( $this_alignment, 'align' ) === false ) ) { $this_alignment = 'align' . $this_alignment; }

    // The Query
    $speaker_query = new WP_Query( $args );

    // The Loop
    if ( $speaker_query->have_posts() ) {
        while ( $speaker_query->have_posts() ) {
            $speaker_query->the_post();
            $thumb_array = array( 'class' => 'speaker-thumb' );

            $shortcode_output = NULL;
            $shortcode_content .= '<div class="speaker-container';
            if ( $this_alignment ) { $shortcode_content .= ' ' . $this_alignment; }
            if ( $extra_classes ) { $shortcode_content .= ' ' . $extra_classes; }
            $shortcode_content .= '">';
            $shortcode_content .= '<a href="' . get_permalink() . '">';
            $shortcode_content .= get_the_post_thumbnail( get_the_ID(), 'medium', $thumb_array );
            $shortcode_content .= '</a>';
            if ( ! $photo_only ) {
                $shortcode_content .= '<div class="info">';
                $shortcode_content .= '<h2><a href="' . get_permalink() . '">' . get_the_title() . '</a></h2>';
                if ( ! $no_conventions ) {
                    $shortcode_content .= '<div class="conventions-attending">';
                    $shortcode_content .= output_convention_icons( get_the_ID() );
                    $shortcode_content .= '</div>';
                }
                $shortcode_content .= '</div>';
            }
            $shortcode_content .= '</div><!-- .speaker-container -->';
        }
    }

    // Restore original Post Data
    wp_reset_postdata();

    // return shortcode content
    return $shortcode_content;
}

// add shortcode for list of speakers
// accepts `convention` attribute
add_shortcode( 'speaker_list', 'speaker_list_shortcode' );
function speaker_list_shortcode( $attributes ) {
    global $convention_abbreviations;
    $shortcode_attributes = shortcode_atts( array (
        'convention'    => NULL,
        'posts_per_page'    => -1,
        'offset'            => NULL,
        'ul_class'          => NULL,
        'li_class'          => NULL,
        'a_class'           => NULL,
    ), $attributes );
    // workaround for posts_per_page overriding offset
    if ( $shortcode_attributes['offset'] != NULL && $shortcode_attributes['posts_per_page'] == -1 ) {
        $shortcode_attributes['posts_per_page'] = 500;
    }
    $this_convention = strtolower( esc_attr( $shortcode_attributes['convention'] ) );

    // arguments
    $speaker_list_args = array(
        'post_type'         => 'speaker',
        'meta_key'          => 'featured_speaker',
        'meta_compare'      => '!=',
        'meta_value'        => 'no',
        'posts_per_page'    => esc_attr( $shortcode_attributes['posts_per_page'] ),
        'offset'            => esc_attr( $shortcode_attributes['offset'] ),
        'orderby'           => 'menu_order',
        'order'             => 'ASC',
    );

    // conventions
    if ( $this_convention ) {
        $speaker_list_args = array_merge( $speaker_list_args, array(
            'tax_query' => array(
                array(
                    'taxonomy'  => 'ghc_conventions_taxonomy',
                    'field'     => 'slug',
                    'terms'     => $convention_abbreviations[$this_convention],
                )
            ),
        ));
    }

    // query
    $speaker_list_query = new WP_Query( $speaker_list_args );

    // loop
    if ( $speaker_list_query->have_posts() ) {
        $shortcode_content = '<ul class="speaker-list ' . esc_attr( $shortcode_attributes['ul_class'] ) . '">';
        while ( $speaker_list_query->have_posts() ) {
            $speaker_list_query->the_post();
            $shortcode_content .= '<li class="' . esc_attr( $shortcode_attributes['li_class'] ) . '"><a class="' . esc_attr( $shortcode_attributes['a_class'] ) . '" href="' . get_permalink() . '">' . get_the_title() . '</a></li>';
        }
        echo '</ul>';
    }

    // reset post data
    wp_reset_postdata();

    return $shortcode_content;
}

// add shortcode for sponsors of a particular track
// accepts `track` attribute
add_shortcode( 'special_track_speakers', 'special_track_speakers_shortcode' );
function special_track_speakers_shortcode( $attributes ) {
    $shortcode_attributes = shortcode_atts( array (
        'track'    => NULL,
    ), $attributes );

    // arguments
    $special_track_speakers_args = array(
        'post_type'         => 'speaker',
        'posts_per_page'    => -1,
        'orderby'           => 'menu_order',
        'order'             => 'ASC',
        'tax_query' => array(
            array(
                'taxonomy'  => 'ghc_special_tracks_taxonomy',
                'field'     => 'slug',
                'terms'     => $shortcode_attributes['track'],
            )
        ),
    );

    // query
    $special_track_speakers_query = new WP_Query( $special_track_speakers_args );

    // loop
    if ( $special_track_speakers_query->have_posts() ) {
        $shortcode_content = '<div class="speaker-item-wrapper">
            <div class="speaker-item-holder gdlr-speaker-type-round">';
        while ( $special_track_speakers_query->have_posts() ) {
            $special_track_speakers_query->the_post();
            ob_start();
            include( 'speaker-grid-template.php' );
            $shortcode_content .= ob_get_clean();
        }
        $shortcode_content .= '</div>
        </div>';
    }

    // reset post data
    wp_reset_postdata();

    return $shortcode_content;
}
