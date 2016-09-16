    <div class="gdlr-isotope">
        <?php
        // how many speakers to show above entertainers
        $top_speakers_quantity = 18;

        // new queries
        $top_speakers_args = array (
            'post_type'              => 'speaker',
            'meta_key'               => 'featured_speaker',
            'meta_compare'           => '!=',
            'meta_value'             => 'no',
            'pagination'             => false,
            'posts_per_archive_page' => $top_speakers_quantity,
            'order'                  => 'ASC',
            'orderby'                => 'menu_order',
            'cache_results'          => true,
            'update_post_meta_cache' => true,
            'update_post_term_cache' => true,
        );
        $entertainers_args = array (
            'post_type'              => 'special_event',
            'pagination'             => false,
            'order'                  => 'ASC',
            'orderby'                => 'menu_order',
            'cache_results'          => true,
            'update_post_meta_cache' => true,
            'update_post_term_cache' => true,
        );

        // the queries
        $top_speakers_query = new WP_Query( $top_speakers_args );
        $entertainers_query = new WP_Query( $entertainers_args );

        // get IDs of top speakers so they aren’t included again in the bottom randomly-ordered section
        $top_speakers_IDs = wp_list_pluck( $top_speakers_query->posts, 'ID' );

        $bottom_speakers_args = array (
            'post_type'              => 'speaker',
            'meta_key'               => 'featured_speaker',
            'meta_compare'           => '!=',
            'meta_value'             => 'no',
            'pagination'             => false,
            'posts_per_archive_page' => -1,
            'offset'                 => $top_speakers_quantity,
            'order'                  => 'ASC',
            'orderby'                => 'rand',
            'cache_results'          => true,
            'update_post_meta_cache' => true,
            'update_post_term_cache' => true,
            'post__not_in'           => $top_speakers_IDs,
        );

        $bottom_speakers_query = new WP_Query( $bottom_speakers_args );


        // output page content
        echo '<div class="row gdl-page-row-wrapper">';
        echo '<div class="gdl-page-left mb0">';

        echo '<div class="row">';
        echo '<div class="gdl-page-item mb0 pb20">';

        echo '<div id="blog-item-holder" class="blog-item-holder">';

        // top 12 (set number on lines 15 and 48)
        if ( $top_speakers_query->have_posts() ) {
            $counter = 1;
            while ( $top_speakers_query->have_posts() ) {
                $top_speakers_query->the_post();
                echo '<div class="four columns">';
                    include('speaker-template.php');
                echo '</div>';
                if ( 0 == $counter % 3 ) {
                    echo '<div class="clear"></div>';
                }
                $counter++;
            }
        } else {
            echo '<h2>More information coming soon.</h2>';
            echo do_shortcode( '[contact-form-7 id="28288" title="Signup - Convention Info"]' );
        }

        // entertainers
        if ( $entertainers_query->have_posts() ) {
            $counter = 1;
            while ( $entertainers_query->have_posts() ) {
                $entertainers_query->the_post();
                echo '<div class="six columns">';
                    include('speaker-template.php');
                echo '</div>';
                if ( 0 == $counter % 2 ) {
                    echo '<div class="clear"></div>';
                }
                $counter++;
            }
        }

        // bottom speakers
        if ( $bottom_speakers_query->have_posts() ) {
            $counter = 1;
            while ( $bottom_speakers_query->have_posts() ) {
                $bottom_speakers_query->the_post();
                echo '<div class="four columns">';
                    include('speaker-template.php');
                echo '</div>';
                if ( 0 == $counter % 3 ) {
                    echo '<div class="clear"></div>';
                }
                $counter++;
            }
        }

        // Restore original Post Data
        wp_reset_postdata(); ?>
        <div class="clear"></div>
    </div> <!-- .gdlr-isotope -->
