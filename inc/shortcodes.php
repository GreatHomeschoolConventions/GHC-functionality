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

// add shortcode for speaker page
add_shortcode( 'speaker_archive', 'speaker_archive_shortcode' );
function speaker_archive_shortcode() {
    ob_start();
    include( 'archive-speaker.php' );
    return ob_get_clean();
}