<?php
    // how many speakers to show above entertainers
    $top_speakers_quantity = 20;

    // new queries
    $top_speakers_args = array (
        'post_type'              => 'speaker',
        'pagination'             => false,
        'posts_per_archive_page' => $top_speakers_quantity,
        'order'                  => 'ASC',
        'orderby'                => 'menu_order',
    );
    $entertainers_args = array (
        'post_type'              => 'special_event',
        'pagination'             => false,
        'order'                  => 'ASC',
        'orderby'                => 'menu_order',
    );

    // the queries
    $top_speakers_query = new WP_Query( $top_speakers_args );
    $entertainers_query = new WP_Query( $entertainers_args );

    // get IDs of top speakers so they aren’t included again in the bottom randomly-ordered section
    $top_speakers_IDs = wp_list_pluck( $top_speakers_query->posts, 'ID' );

    $bottom_speakers_args = array (
        'post_type'              => 'speaker',
        'pagination'             => false,
        'posts_per_archive_page' => -1,
        'offset'                 => $top_speakers_quantity,
        'order'                  => 'ASC',
        'orderby'                => 'rand',
        'post__not_in'           => $top_speakers_IDs,
        'tax_query'         => array(
            array(
                'taxonomy'  => 'ghc_speaker_category_taxonomy',
                'field'     => 'slug',
                'terms'     => 'featured',
            ),
        ),
    );

    $bottom_speakers_query = new WP_Query( $bottom_speakers_args );

    // top speakers (set with $top_speakers_quantity)
    if ( $top_speakers_query->have_posts() ) {
        echo '<div class="speaker-container">';
        while ( $top_speakers_query->have_posts() ) {
            $top_speakers_query->the_post();
            include('speaker-template.php');
        }
        echo '</div><!-- .speaker-container -->';
    } else {
        echo '<h2>More information coming soon.</h2>';
        echo do_shortcode( '[contact-form-7 id="28288" title="Signup - Convention Info"]' );
    }

    // entertainers
    if ( $entertainers_query->have_posts() ) {
        echo '<div class="special-events-container">';
        while ( $entertainers_query->have_posts() ) {
            $entertainers_query->the_post();
            include('speaker-template.php');
        }
        echo '</div><!-- .special-events-container -->';
    }

    // bottom speakers
    if ( $bottom_speakers_query->have_posts() ) {
        echo '<div class="speaker-container">';
        while ( $bottom_speakers_query->have_posts() ) {
            $bottom_speakers_query->the_post();
            include('speaker-template.php');
        }
        echo '</div><!-- .speaker-container -->';
    }

    // Restore original post data
    wp_reset_postdata();
