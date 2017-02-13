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

// add shortcode for convention-specific exhibitors
// accepts `convention` attribute as abbreviation
add_shortcode( 'exhibitor_list', 'exhibitor_list_shortcode' );
function exhibitor_list_shortcode( $attributes ) {
    $shortcode_attributes = shortcode_atts( array (
        'convention'    => NULL,
    ), $attributes );
    global $convention_abbreviations;
    $this_convention = strtolower( esc_attr( $shortcode_attributes['convention'] ) );

    $shortcode_content = NULL;

    $exhibitor_args = array(
        'posts_per_page'    => -1,
        'post_type'         => 'exhibitor',
        'order'             => 'ASC',
        'orderby'           => 'post_name',
        'tax_query' => array(
            array(
                'taxonomy'  => 'ghc_conventions_taxonomy',
                'field'     => 'slug',
                'terms'     => $convention_abbreviations[$this_convention],
            )
        ),
    );

    $exhibitor_query = new WP_Query( $exhibitor_args );

    if ( $exhibitor_query->have_posts() ) {
        $i = 1;
        ob_start();
        while ( $exhibitor_query->have_posts() ) {
            $exhibitor_query->the_post();

            include( 'exhibitor-template.php' );
            if ( ( $i % 3 ) == 0 ) {
                echo '<div class="clear"></div>';
            }
            $i++;
        }
        $shortcode_content = ob_get_clean();
    }

    wp_reset_postdata();

    return $shortcode_content;
}

// add shortcode for hotels grid on location pages
add_shortcode( 'hotel_grid', 'hotel_grid_shortcode' );
function hotel_grid_shortcode( $attributes ) {
    $shortcode_attributes = shortcode_atts( array (
        'convention'    => NULL,
    ), $attributes );
    $this_convention = strtolower( esc_attr( $shortcode_attributes['convention'] ) );

    ob_start();
    include( 'loop-hotel.php' );
    return ob_get_clean();
}

// add shortcode for price sheet
// accepts `convention` attribute as abbreviation
add_shortcode( 'price_sheet', 'price_sheet_shortcode' );
function price_sheet_shortcode( $attributes ) {
    $shortcode_attributes = shortcode_atts( array (
        'convention'    => NULL,
    ), $attributes );
    $this_convention = strtolower( esc_attr( $shortcode_attributes['convention'] ) );

    wp_enqueue_script( 'price-sheets' );
    // get content
    ob_start();
    include( plugin_dir_path( __FILE__ ) . '/../price-sheets/price-sheet-' . $this_convention . '.html' );

    return ob_get_clean();
}

// add shortcode for speaker page
add_shortcode( 'speaker_archive', 'speaker_archive_shortcode' );
function speaker_archive_shortcode() {
    ob_start();
    include( 'loop-speaker.php' );
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

// add shortcode for all sponsors
// accepts `gray` and `width` attributes
add_shortcode( 'sponsors', 'sponsors_shortcode' );
function sponsors_shortcode( $attributes ) {
    $shortcode_attributes = shortcode_atts( array (
        'gray'    => NULL,
        'width'   => NULL,
    ), $attributes );

    // arguments
    $sponsors_args = array(
        'post_type'         => 'sponsor',
        'posts_per_page'    => -1,
        'orderby'           => 'menu_order',
        'order'             => 'ASC',
    );

    // query
    $sponsors_query = new WP_Query( $sponsors_args );

    // loop
    if ( $sponsors_query->have_posts() ) {
        $shortcode_content = NULL;
        while ( $sponsors_query->have_posts() ) {
            $sponsors_query->the_post();
            $shortcode_content .= '<a href="' . get_permalink() . '">';
            if ( $shortcode_attributes['gray'] ) {
                if ( $shortcode_attributes['width'] ) {
                    $shortcode_content .= wp_get_attachment_image( get_field( 'grayscale_logo' )['id'], array( $shortcode_attributes['width'], -1 ) );
                } else {
                    $shortcode_content .= wp_get_attachment_image( get_field( 'grayscale_logo' )['id'] );
                }
            } else {
                if ( $shortcode_attributes['width'] ) {
                    $shortcode_content .= get_the_post_thumbnail( get_the_ID(), array( $shortcode_attributes['width'], -1 ) );
                } else {
                    $shortcode_content .= get_the_post_thumbnail();
                }
            }
            $shortcode_content .= '</a>';
        }
    }

    // reset post data
    wp_reset_postdata();

    return $shortcode_content;
}

// add shortcode for sessions
// accepts `track` attribute
add_shortcode( 'sessions_list', 'sessions_shortcode' );
function sessions_shortcode( $attributes ) {
    $shortcode_attributes = shortcode_atts( array (
        'track'    => NULL,
    ), $attributes );

    // arguments
    $session_speakers_args = array(
        'post_type'         => 'session',
        'posts_per_page'    => -1,
        'orderby'           => 'meta_value',
        'meta_key'          => 'session-speaker',
        'order'             => 'ASC',
        'tax_query'         => array(
            array(
                'taxonomy'  => 'session_category',
                'field'     => 'slug',
                'terms'     => 'sample-sessions',
            ),
        ),
    );

    // add track if specified
    if ( isset( $shortcode_attributes['track'] ) ) {
        $session_speakers_args['tax_query'] = array_merge( $session_speakers_args['tax_query'], array( array(
                'taxonomy'  => 'ghc_special_tracks_taxonomy',
                'field'     => 'slug',
                'terms'     => $shortcode_attributes['track'],
            )));
    }

    // query
    $session_speakers_query = new WP_Query( $session_speakers_args );

    // loop
    if ( $session_speakers_query->have_posts() ) {
        $shortcode_content = '<div class="session-item-wrapper clearfix">
            <div class="session-item-holder">';
        $i = 1;
        while ( $session_speakers_query->have_posts() ) {
            $session_speakers_query->the_post();
            ob_start();
            include( 'session-grid-template.php' );
            $shortcode_content .= ob_get_clean();
            if ( $i % 3 == 0 ) {
                $shortcode_content .= '<div class="clear"></div>';
            }
            $i++;
        }
        $shortcode_content .= '</div>
        </div>';
    }

    // reset post data
    wp_reset_postdata();

    return $shortcode_content;
}

// add shortcode for workshops schedule
// accept `convention` attribute
add_shortcode( 'workshops_schedule', 'workshops_schedule_shortcode' );
function workshops_schedule_shortcode( $attributes ) {
    $shortcode_attributes = shortcode_atts( array (
        'convention'    => NULL,
    ), $attributes );
    global $convention_abbreviations, $wpdb;

    // get total number of days
    $distinct_dates = $wpdb->get_results( $wpdb->prepare( '
    SELECT DISTINCT DATE(meta.meta_value) AS workshop_date
    FROM %1$spostmeta meta
    JOIN %1$spostmeta meta2 ON meta2.post_ID = meta.post_ID
    JOIN %1$sposts posts ON posts.ID = meta.post_id
    JOIN %1$sterms terms ON meta2.meta_value = terms.term_id
    JOIN %1$sterm_taxonomy term_taxonomy ON term_taxonomy.term_id = terms.term_id
    WHERE term_taxonomy.taxonomy = "ghc_conventions_taxonomy"
    AND terms.slug LIKE "%2$s"
    AND posts.post_type = "workshop"
    AND meta.meta_key = "date_and_time"
    AND meta2.meta_key = "convention"
    ORDER BY meta.meta_value', $wpdb->prefix, $convention_abbreviations[$shortcode_attributes['convention']] ) );

    if ( $distinct_dates ) {
        $shortcode_content = '<div class="session-item-wrapper clearfix workshop-schedule">
            <div class="gdlr-session-item gdlr-tab-session-item gdlr-item">';

        // table header
        $shortcode_content .= '<div class="gdlr-session-item-head">';
        $i = 1;
        foreach ( $distinct_dates as $date ) {
            $shortcode_content .= '<div class="gdlr-session-item-head-info ' . ( $i == 1 ? 'gdlr-active' : '' ) . '" data-tab="gdlr-tab-' . $i . '">
                <div class="gdlr-session-head-day">Day ' . $i  . '</div>
                <div class="gdlr-session-head-date">' . date( 'M. d, Y', strtotime( $date->workshop_date ) ) . '</div>
            </div>';
            $i++;
        }
        $shortcode_content .= '<div class="clear"></div></div><!-- .gdlr-session-item-head -->';

        // table body
        $i = 1;
        foreach ( $distinct_dates as $date ) {
            $shortcode_content .= '<div class="gdlr-session-item-tab-content gdlr-tab-' . $i . ' ' . ( $i == 1 ? 'gdlr-active': '' ) . '">';

            // get distinct times
            $distinct_times = $wpdb->get_results( $wpdb->prepare( 'SELECT DISTINCT meta_value AS workshop_time FROM %1$spostmeta meta JOIN %1$sposts posts ON posts.ID = meta.post_id WHERE posts.post_type = "workshop" AND meta.meta_key = "date_and_time" AND DATE(meta.meta_value) = "%2$s" ORDER BY meta.meta_value;', $wpdb->prefix, $date->workshop_date ) );

            if ( $distinct_times ) {
                foreach ( $distinct_times as $time ) {

                    // arguments
                    $workshops_args = array(
                        'post_type'         => 'workshop',
                        'posts_per_page'    => -1,
                        'meta_key'          => 'date_and_time',
                        'meta_value'        => $time->workshop_time,
                        'orderby'           => 'meta_value',
                        'order'             => 'ASC',
                        'tax_query'         => array(
                            array(
                                'taxonomy'  => 'ghc_conventions_taxonomy',
                                'field'     => 'slug',
                                'terms'     => $convention_abbreviations[$shortcode_attributes['convention']],
                            ),
                        ),
                    );

                    // query
                    $workshops_query = new WP_Query( $workshops_args );

                    // loop
                    if ( $workshops_query->have_posts() ) {
                        $shortcode_content .= '<div class="gdlr-session-item-content-wrapper">
                            <div class="gdlr-session-item-divider"></div>
                            <div class="gdlr-session-item-content-info">
                                <div class="gdlr-session-info">
                                    <div class="session-info session-time"><i class="fa fa-clock-o"></i>' . date( 'g:i A', strtotime( $time->workshop_time ) ) . '</div>
                                    <div class="clear"></div>
                                </div>
                            </div>
                            <div class="gdlr-session-item-content">
                            <table class="session-list">
                                <thead>
                                    <tr>
                                        <td class="time">Time</td>
                                        <td class="location">Location</td>
                                        <td class="speaker">Speaker</td>
                                        <td class="title">Session Title</td>
                                        ' . ( ( is_user_logged_in() && current_user_can( 'edit_others_posts' ) ) ? '<td>Edit</td>' : '' ) . '
                                    </tr>
                                </thead>';
                        while ( $workshops_query->have_posts() ) {
                            $workshops_query->the_post();
                            ob_start();
                            include( 'workshop-list-template.php' );
                            $shortcode_content .= ob_get_clean();
                        }
                        $shortcode_content .= '</table>
                        </div><!-- .gdlr-session-item-content -->
                        <div class="clear"></div>
                        </div><!-- .gldr-session-item-content-wrapper -->';
                    }

                    // reset post data
                    wp_reset_postdata();

                }
            }

            // close this day’s content
            $shortcode_content .= '</div><!-- .gdlr-session-item-tab-content.gdlr-tab-' . $i . ' -->';
            $i++;
        }

        // close wrapper
        $shortcode_content .= '</div><!-- .gdlr-session-item -->
        </div><!-- .session-item-wrapper -->';

    }

    return $shortcode_content;
}
