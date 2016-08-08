<?php

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

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
            if ( has_term( 'speaker', 'ghc_speakers_taxonomy' ) ) {
                $thumb_array = array( 'class' => 'speaker-thumb' );
            } elseif ( has_term( 'entertainer', 'ghc_speakers_taxonomy' ) ) {
                $thumb_array = array( 'class' => 'entertainer-thumb' );
            }

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
                        foreach ( get_the_terms_sorted( get_the_ID(), 'ghc_conventions_taxonomy' ) as $convention ) {
                            global $convention_urls;
                            $related_convention = get_field( 'related_convention', $convention );
                            $shortcode_content .= '<a class="speaker-convention-link" href="' . $convention_urls[strtolower( get_field( 'convention_short_name', $related_convention ) ) ] . '">';
                                $shortcode_content .= '<svg class="large" role="img" title="' . $convention->name . '"><use xlink:href="' . get_stylesheet_directory_uri() . '/images/icons.svg#icon-' . $convention->name . '_large"></use></svg><svg class="small" role="img" title="' . $convention->name . '"><use xlink:href="' . get_stylesheet_directory_uri() . '/images/icons.svg#icon-' . $convention->name . '_small"></use></svg><span class="fallback ' . strtolower( $convention->name ) . '">' . $convention->name . '</span>';
                            $shortcode_content .= '</a>';
                        }
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

// add shortcode for convention icon only
// accepts `convention` attribute either as abbreviation or full name
add_shortcode( 'convention_icon', 'convention_icon_shortcode' );
function convention_icon_shortcode( $attributes ) {
    $shortcode_attributes = shortcode_atts( array (
        'convention'    => NULL,
        'small'         => NULL,
    ), $attributes );
    $this_convention = strtolower( esc_attr( $shortcode_attributes['convention'] ) );
    $this_small = esc_attr( $shortcode_attributes['small'] );
    global $convention_abbreviations, $convention_urls;

    if ( strlen( $this_convention ) > 2 ) {
        $this_convention = str_replace( $convention_abbreviations, array_keys( $convention_abbreviations ), $this_convention );
    }

    if ( array_key_exists( $this_convention, $convention_urls ) ) {
        $this_URL = $convention_urls[$this_convention];
        $this_name = ucfirst( $convention_abbreviations[$this_convention] );
    }

    $shortcode_content .= '<a class="speaker-convention-link convention-shortcode';
    if ( $this_small ) {
        $shortcode_content .= ' small';
    }
    $shortcode_content .= '" href="' . $this_URL . '">';
        $shortcode_content .= '<svg class="small" role="img" title="' . $this_name . '"><use xlink:href="' . get_stylesheet_directory_uri() . '/images/icons.svg#icon-' . $this_name . '_small"></use></svg><span class="fallback ' . ucwords( $this_name ) . '">' . $this_name . '</span>';
    $shortcode_content .= '</a>';

    return $shortcode_content;
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
        foreach( explode( ',', $shortcode_attributes['convention'] ) as $convention ) {
            if ( in_array( $convention, array_keys( $convention_urls ) ) && ( $convention_dates[$convention] >= time() ) ) {
                $shortcode_content .= '<svg class="small" role="img" title="' . $this_name . '"><use xlink:href="' . get_stylesheet_directory_uri() . '/images/icons.svg#icon-' . ucwords( $convention_abbreviations[$convention] ) . '_small"></use></svg><span class="fallback ' . ucwords( $convention_abbreviations[$convention] ) . '">' . $convention . '</span>';
            }
        }

        // output bottom of button
        $shortcode_content .= '</a></h3></div>' . "\n";

        return $shortcode_content;
    }
}
