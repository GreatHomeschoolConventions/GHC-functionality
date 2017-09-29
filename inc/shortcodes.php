<?php

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

/**
 * Shortcode to display a convention’s CTA
 * @param  array  $attributes shortcode parameters including convention
 * @return string HTML content
 */
function convention_cta_shortcode( $attributes ) {
    global $conventions, $convention_abbreviations;

    $shortcode_attributes = shortcode_atts( array (
        'convention'    => NULL,
    ), $attributes );
    $this_convention = strtolower( esc_attr( $shortcode_attributes['convention'] ) );

    $CTA_array = array_filter( $conventions[$this_convention]['cta_list'], 'get_current_CTA' );
    $current_CTA = array_pop($CTA_array)['cta_content'];

    return apply_filters( 'the_content', $current_CTA );
}
add_shortcode( 'convention_cta', 'convention_cta_shortcode' );

/**
 * Shortcode to display a single convention icon
 * @param  array  $attributes shortcode parameters, including `convention` as a two-letter abbreviation or full name
 * @return string HTML output
 */
function convention_icon_shortcode( $attributes ) {
    $shortcode_attributes = shortcode_atts( array (
        'convention'    => NULL,
    ), $attributes );
    $this_convention = strtolower( esc_attr( $shortcode_attributes['convention'] ) );

    return output_convention_icons( $this_convention );
}
add_shortcode( 'convention_icon', 'convention_icon_shortcode' );

/**
 * Shortcode to display a discretionary registration button based on current date
 *
 * array[]
 *      ['convention']  string  two-letter abbreviation or full name
 *      ['year']        integer four-digit year
 *      ['intro_text']  string  string to display before CTA button
 *
 * @param  array  $attributes shortcode parameters (see above)
 * @return string HTML output
 */
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
add_shortcode( 'discretionary_registration', 'discretionary_registration_shortcode' );

/**
 * Shortcode to display all exhibitors for a given convention
 * @param  array[] $attributes shortcode parameters
 *                            ['convention']    two-letter convention abbreviation
 *                            ['style']         type of list to display; allowed values include “large,” “small,” and “list”
 * @return string  HTML output
 */
function exhibitor_list_shortcode( $attributes ) {
    $shortcode_attributes = shortcode_atts( array (
        'convention'    => NULL,
        'style'          => 'large',
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
        ob_start();
        if ( $shortcode_attributes['style'] == 'list' ) {
            echo '<ul class="exhibitor-container ' . esc_attr( $shortcode_attributes['style'] ) . '">';
        } else {
            echo '<div class="exhibitor-container ' . esc_attr( $shortcode_attributes['style'] ) . '">';
        }

        while ( $exhibitor_query->have_posts() ) {
            $exhibitor_query->the_post();
            if ( $shortcode_attributes['style'] == 'large' ) {
                require( plugin_dir_path( __FILE__ ) . '../templates/exhibitor-template.php' );
            } else {
                if ( $shortcode_attributes['style'] == 'list' ) {
                    echo '<li id="post-' . get_the_ID() . '" class="' . implode( ' ', get_post_class() ) . '">';
                }
                echo '<a href="' . get_field( 'exhibitor_URL' ) . '" target="_blank" rel="noopener">' . get_the_title() . '</a>';
                if ( $shortcode_attributes['style'] == 'list' ) {
                    echo '</li>';
                }
            }
        }

        if ( $shortcode_attributes['style'] == 'list' ) {
            echo '</ul>';
        } else {
            echo '</div>';
        }

        $shortcode_content = ob_get_clean();
    }

    wp_reset_postdata();

    return $shortcode_content;
}
add_shortcode( 'exhibitor_list', 'exhibitor_list_shortcode' );

/**
 * Shortcode to display exhibit hall hours
 * @return string HTML output
 */
function exhibit_hall_hours_shortcode() {
    return get_field( 'exhibit_hall_hours', 'option' );
}
add_shortcode( 'exhibit_hall_hours', 'exhibit_hall_hours_shortcode' );

/**
 * Shortcode to display hotels
 * @param  array  $attributes shortcode parameters, including `convention` as a two-letter abbreviation or full name
 * @return string HTML output
 */
function hotel_grid_shortcode( $attributes ) {
    $shortcode_attributes = shortcode_atts( array (
        'convention'    => NULL,
    ), $attributes );
    $this_convention = strtolower( esc_attr( $shortcode_attributes['convention'] ) );

    ob_start();
    require( plugin_dir_path( __FILE__ ) . '../templates/loop-hotel.php' );
    return ob_get_clean();
}
add_shortcode( 'hotel_grid', 'hotel_grid_shortcode' );

/**
 * Shortcode to display price sheet
 * @param  array  $attributes shortcode parameters, including `convention` as a two-letter abbreviation or full name
 * @return string HTML output
 */
function price_sheet_shortcode( $attributes ) {
    $shortcode_attributes = shortcode_atts( array (
        'convention'    => NULL,
    ), $attributes );
    $this_convention = strtolower( esc_attr( $shortcode_attributes['convention'] ) );

    wp_enqueue_script( 'ghc-price-sheets' );
    // get content
    ob_start();
    include( plugin_dir_path( __FILE__ ) . '/../price-sheets/price-sheet-' . $this_convention . '.html' );

    return ob_get_clean();
}
add_shortcode( 'price_sheet', 'price_sheet_shortcode' );

/**
 * Shortcode to display custom registration
 * @param  array  $attributes shortcode parameters
 * @return string HTML output
 */
function registration_page_shortcode( $attributes ) {
    $shortcode_attributes = shortcode_atts( array (
    ), $attributes );

    ob_start();

    echo '<div class="register">';

    $registration_args = array(
        'category'          => 'registration',
        'orderby'           => 'menu_order',
        'order'             => 'ASC',
        'posts_per_page'    => -1,
    );

    $registration_query = wc_get_products( $registration_args );

    $special_events_args = array(
        'category'          => 'special-events',
        'orderby'           => 'menu_order',
        'order'             => 'ASC',
        'posts_per_page'    => -1,
    );

    $special_events_query = wc_get_products( $special_events_args );

    if ( count( $registration_query ) > 0 ) { ?>

        <h3>Convention</h3>
        <p>Choose one:</p>
        <?php global $conventions; ?>
        <?php foreach ( $conventions as $convention ) { ?>
            <?php $convention_abbreviation = strtolower( $convention['convention_abbreviated_name'][0] ); ?>
            <input class="registration-choice convention" type="radio" name="convention" value="<?php echo $convention_abbreviation; ?>" id="convention-<?php echo $convention_abbreviation; ?>" <?php if ( ! $convention_checked && date( 'Ymd' ) < $convention['begin_date'][0] ) { echo ' checked="checked"'; $convention_checked = true; } ?> />
                <label class="registration-choice convention theme bg <?php echo $convention_abbreviation; ?>" for="convention-<?php echo $convention_abbreviation; ?>">
                    <h2><?php echo $convention['convention_short_name'][0]; ?></h2>
                    <p class="info"><?php echo ghc_format_date_range( $convention['begin_date'][0], $convention['end_date'][0], 'Ymd' ); ?></p>
                </label>
        <?php } ?>

        <h3>Attendee Type</h3>
        <p>Choose one:</p>
        <input class="registration-choice" type="radio" name="attendee-type" value="individual" id="attendee-individual" />
            <label class="registration-choice dashicons-before dashicons-admin-users" for="attendee-individual">Individual</label>
        <input class="registration-choice" type="radio" name="attendee-type" value="family" id="attendee-family" checked="checked" />
            <label class="registration-choice dashicons-before dashicons-groups" for="attendee-family">Family</label>

        <label class="registration-choice" for="family-members">Number of Family Members Attending:
            <input class="registration-choice" type="number" name="family-members" min="2" max="20" value="2" placeholder="2" id="family-members"/>
        </label>

        <table class="products">
        <tbody>
        <?php
        wp_enqueue_script( 'ghc-woocommerce' );

        foreach ( $registration_query as $this_product ) {
            $product_object = get_post( $this_product->get_id() );
            setup_postdata( $GLOBALS['post'] =& $product_object );
            global $product;

            if ( $product->is_type( 'variable' ) ) {
                $variations = $product->get_available_variations();

                foreach ( $variations as $variation_array ) {
                    $variation = new WC_Product_Variation( $variation_array['variation_id'] );
                    require( plugin_dir_path( __FILE__ ) . '../templates/registration-table-row-variation.php' );
                }

            } else {
                require( plugin_dir_path( __FILE__ ) . '../templates/registration-table-row.php' );
            }
        }

        if ( count( $special_events_query ) > 0 ) {
            foreach ( $special_events_query as $this_product ) {
                $product_object = get_post( $this_product->get_id() );
                setup_postdata( $GLOBALS['post'] =& $product_object );
                global $product;

                if ( $product->is_type( 'variable' ) ) {
                    $variations = $product->get_available_variations();

                    foreach ( $variations as $variation_array ) {
                        $variation = new WC_Product_Variation( $variation_array['variation_id'] );
                        require( plugin_dir_path( __FILE__ ) . '../templates/registration-table-row-variation.php' );
                    }

                } else {
                    require( plugin_dir_path( __FILE__ ) . '../templates/registration-table-row.php' );
                }
            }
        } ?>
        </tbody>
        <tfoot>
            <tr class="cart-totals">
                <td colspan="2">Total</td>
                <td>
                    <span class="custom-cart-total"><?php echo WC()->cart->get_cart_total(); ?></span>
                </td>
                <td>
                    <a class="button" href="<?php echo wc_get_cart_url(); ?>" title="<?php _e( 'View your shopping cart' ); ?>">Check Out&rarr;</a>
                </td>
            </tr>
        </tfoot>

        <?php echo '</table>';
    }

    echo '</div><!-- .register -->';

    wp_reset_postdata();

    return ob_get_clean();
}
add_shortcode( 'registration_page', 'registration_page_shortcode' );

/**
 * Shortcode to display custom speaker/special event archive
 * @return string HTML of entire archive
 */
function speaker_archive_shortcode() {
    ob_start();
    echo '<div class="speaker-archive">';
        require( plugin_dir_path( __FILE__ ) . '../templates/loop-speaker.php' );
    echo '</div>';
    return ob_get_clean();
}
add_shortcode( 'speaker_archive', 'speaker_archive_shortcode' );

/**
 * Shortcode to display speaker grid
 * @param  array  $attributes shortcode parameters, including `convention` as a two-letter abbreviation or full name
 *                           ['convention']     string      two-letter abbreviation or short convention name
 *                           ['posts_per_page'] integer     number of posts to display; defaults to -1 (all)
 *                           ['offset']         integer     number of posts to skip
 *                           ['show']           string      comma-separated list of elements to show; allowed values include any combination of the following: image, conventions, name, bio, excerpt
 *                           ['image_size']     string      named image size or two comma-separated integers creating an image size array
 * @return string HTML output
 */
function speaker_grid_shortcode( $attributes ) {
    $attributes['post_type'] = 'speaker';
    return ghc_cpt_grid( $attributes );
}
add_shortcode( 'speaker_grid', 'speaker_grid_shortcode' );

/**
 * Shortcode to display speaker(s) info
 *
 * @param  array  $attributes shortcode parameters (see array above)
 *                           ['postid']          integer post ID for a specific speaker
 *                           ['pagename']        string  post slug for a specific speaker
 *                           ['align']           string  align right, left, or center
 *                           ['no_conventions']  boolean whether or not to show convention icons beneath speaker’s name
 *                           ['extra_classes']   string  extra classes to add to the output
 * @return string HTML output
 */
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
add_shortcode( 'speaker_info', 'speaker_info_shortcode' );

/**
 * Shortcode to display a list of speakers
 * @param  array  $attributes shortcode parameters (see array above)
 *                           ['convention']      string  two-letter abbreviation or full name
 *                           ['posts_per_page']  integer number of posts to display
 *                           ['offset']          integer how many posts to skip
 *                           ['ul_class']        string  class(es) to add to the wrapping <ul>
 *                           ['li_class']        string  class(es) to add to each speaker <li>
 *                           ['a_class']         string  class(es) to add to each speaker <a>
 * @return string HTML output
 */
function speaker_list_shortcode( $attributes ) {
    global $convention_abbreviations;
    $shortcode_attributes = shortcode_atts( array (
        'convention'        => NULL,
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
        'posts_per_page'    => esc_attr( $shortcode_attributes['posts_per_page'] ),
        'offset'            => esc_attr( $shortcode_attributes['offset'] ),
        'orderby'           => 'menu_order',
        'order'             => 'ASC',
        'tax_query'         => array(
            array(
                'taxonomy'  => 'ghc_speaker_category_taxonomy',
                'field'     => 'slug',
                'terms'     => 'featured',
            ),
        ),
    );

    // conventions
    if ( $this_convention ) {
        $speaker_list_args['tax_query'] = array_merge( $speaker_list_args['tax_query'], array(
            'relation' => 'AND',
            array(
                'taxonomy'  => 'ghc_conventions_taxonomy',
                'field'     => 'slug',
                'terms'     => $convention_abbreviations[$this_convention],
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
add_shortcode( 'speaker_list', 'speaker_list_shortcode' );

/**
 * Shortcode to display special event grid
 * @param  array  $attributes shortcode parameters, including `convention` as a two-letter abbreviation or full name
 *                           ['post_type']      string      post type; defaults to 'special_event'
 *                           ['convention']     string      two-letter abbreviation or short convention name
 *                           ['posts_per_page'] integer     number of posts to display; defaults to -1 (all)
 *                           ['offset']         integer     number of posts to skip
 *                           ['show']           string      comma-separated list of elements to show; allowed values include any combination of the following: image, conventions, name, bio, excerpt
 *                           ['image_size']     string      named image size or two comma-separated integers creating an image size array
 * @return string HTML output
 */
function special_event_grid_shortcode( $attributes ) {
    $attributes['post_type'] = 'special_event';
    return ghc_cpt_grid( $attributes );
}
add_shortcode( 'special_event_grid', 'special_event_grid_shortcode' );

/**
 * Show a list of special events
 * @param  array  $attributes shortcode parameters
 * @return string HTML output
 */
function special_event_list_shortcode( $attributes ) {
    $special_event_list_args = array(
        'taxonomy'  => 'ghc_special_tracks_taxonomy',
        'title_li'  => '',
        'echo'      => false,
        );

    return '<ul>' . wp_list_categories( $special_event_list_args ) . '</ul>';
}
add_shortcode( 'special_event_list', 'special_event_list_shortcode' );

/**
 * Shortcode to display sponsors for a particular track
 * @param  array  $attributes shortcode parameters, including the `track` slug
 * @return string HTML output
 */
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
        $shortcode_content = '<div class="speaker-container">';
        while ( $special_track_speakers_query->have_posts() ) {
            $special_track_speakers_query->the_post();
            ob_start();
            require( plugin_dir_path( __FILE__ ) . '../templates/speaker-template.php' );
            $shortcode_content .= ob_get_clean();
        }
        $shortcode_content .= '</div>';
    }

    // reset post data
    wp_reset_postdata();

    return $shortcode_content;
}
add_shortcode( 'special_track_speakers', 'special_track_speakers_shortcode' );

/**
 * Shortcode to display all sponsors
 *
 * array[]
 *      ['gray']    boolean whether to show the featured image or the gray version
 *      ['width']   integer width in pixels for the output image
 *
 * @param  array  $attributes shortcode parameters (see above array)
 * @return string HTML output
 */
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
        $shortcode_content = '<div class="sponsor-container">';
        while ( $sponsors_query->have_posts() ) {
            $sponsors_query->the_post();
            $shortcode_content .= '<article id="post-' . get_the_ID() . '" class="' . implode( ' ', get_post_class() ) . '">';
                $shortcode_content .= '<a href="' . get_permalink() . '">
                <div class="sponsor-thumbnail">';
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
                $shortcode_content .= '</div></a>
            </article>';
        }
        $shortcode_content .= '</div>';
    }

    // reset post data
    wp_reset_postdata();

    return $shortcode_content;
}
add_shortcode( 'sponsors', 'sponsors_shortcode' );

/**
 * Display list of workshops
 * @param  array  $attributes shortcode attributes
 *                           ['convention']     two-letter convention abbreviation
 *                           ['posts_per_page'] number of posts to show (defaults to all; if offset is specified, then is set to 500)
 *                           ['offset']         number of posts to skip (useful mainly in conjunction with posts_per_page)
 *                           ['speaker']        include only workshops from this speaker, specified by post ID
 * @return string HTML output
 */
function workshop_list_shortcode( $attributes ) {
    global $convention_abbreviations;
    $shortcode_attributes = shortcode_atts( array (
        'convention'        => NULL,
        'posts_per_page'    => -1,
        'offset'            => NULL,
        'speaker'           => NULL,
    ), $attributes );
    // workaround for posts_per_page overriding offset
    if ( $shortcode_attributes['offset'] != NULL && $shortcode_attributes['posts_per_page'] == -1 ) {
        $shortcode_attributes['posts_per_page'] = 500;
    }
    $this_convention = strtolower( esc_attr( $shortcode_attributes['convention'] ) );

    // arguments
    $workshop_list_args = array(
        'post_type'         => 'workshop',
        'posts_per_page'    => esc_attr( $shortcode_attributes['posts_per_page'] ),
        'offset'            => esc_attr( $shortcode_attributes['offset'] ),
        'orderby'           => array( 'menu_order', 'title' ),
        'order'             => 'ASC',
    );

    // conventions
    if ( $this_convention ) {
        $this_convention_speaker_args = array(
            'post_type'         => 'speaker',
            'posts_per_page'    => -1,
            'tax_query'         => array(
                array(
                    'taxonomy'  => 'ghc_conventions_taxonomy',
                    'field'     => 'slug',
                    'terms'     => $convention_abbreviations[$this_convention],
                ),
            ),
        );

        $this_convention_speaker = new WP_Query( $this_convention_speaker_args );

        $this_convention_speaker_array = array();

        if ( $this_convention_speaker->have_posts() ) {
            while ( $this_convention_speaker->have_posts() ) {
                $this_convention_speaker->the_post();

                $this_convention_speaker_array[] = get_the_ID();
            }
        }

        wp_reset_postdata();

        $workshop_list_args['meta_query'] = array(
            'relation'  => 'OR',
        );

        foreach ( $this_convention_speaker_array as $this_speaker ) {
            $workshop_list_args['meta_query'][] = array(
                'key'       => 'speaker',
                'value'     => '"' . $this_speaker . '"', // add double quotes since the field is serialized; this prevents partial ID matches
                'compare'   => 'LIKE',
            );
        }
    }

    // speakers
    if ( $shortcode_attributes['speaker'] ) {
        $workshop_list_args['meta_key'] = 'speaker';
        $workshop_list_args['meta_value'] = $shortcode_attributes['speaker'];
        $workshop_list_args['meta_compare'] = 'LIKE';
    }

    // query
    $workshop_list_query = new WP_Query( $workshop_list_args );

    // loop
    if ( $workshop_list_query->have_posts() ) {
        $shortcode_content = '<ul class="workshop-list">';
        while ( $workshop_list_query->have_posts() ) {
            $workshop_list_query->the_post();
            $speaker = get_field( 'speaker' );

            if ( $speaker && is_null( $shortcode_attributes['speaker'] ) ) {
                $speaker_string = ' <span class="entry-meta"> | ';
                foreach ( $speaker as $this_speaker ) {
                    $speaker_string .= apply_filters( 'the_title', $this_speaker->post_title ) . ', ';
                }
                $speaker_string = rtrim( $speaker_string, ', ' ) . '</span>';
            } else {
                $speaker_string = '';
            }

            $shortcode_content .= '<li><a href="' . get_permalink() . '">' . get_the_title() . '</a>' . $speaker_string . '</li>';
        }

        if ( $shortcode_attributes['posts_per_page'] !== -1 && ! is_null( $shortcode_attributes['convention'] ) ) {
            $shortcode_content .= '<li><a href="' . home_url() . '??post_type=workshop&ghc_conventions_taxonomy=' . $convention_abbreviations[$this_convention] . '">And <strong>many</strong> more!</a></li>';
        }

        $shortcode_content .= '</ul>';
    }

    // reset post data
    wp_reset_postdata();

    return $shortcode_content;
}
add_shortcode( 'workshop_list', 'workshop_list_shortcode' );

/**
 * Shortcode to display workshop schedule
 * @param  array  $attributes shortcode parameters, including `convention` as a two-letter abbreviation or full name
 * @return string HTML output
 */
function workshops_schedule_shortcode( $attributes ) {
    $shortcode_attributes = shortcode_atts( array (
        'convention'    => NULL,
    ), $attributes );
    global $convention_abbreviations, $wpdb;

    // enqueue filter script
    wp_enqueue_script( 'ghc-workshop-filter' );

    // get all special tracks
    $categories_args = array(
        'taxonomy'  => 'ghc_special_tracks_taxonomy',
        'echo'      => false,
        'title_li'  => NULL,
    );
    $special_tracks = get_terms( $categories_args );

    $shortcode_content = '<section class="workshop-schedule legend">
    <h3>Special Tracks</h3>
    <p>';
    foreach ( $special_tracks as $special_track ) {
        $shortcode_content .= '<a href="' . home_url() . '/special-tracks/' . $special_track->slug . '" class="legend-key ' . $special_track->taxonomy . '-' . $special_track->slug . '" data-special-track="' . $special_track->taxonomy . '-' . $special_track->slug . '">' . $special_track->name . '</a>';
    }
    $shortcode_content .= '<a class="legend-key clear-filters" href="" data-special-track="clear" title="Clear filters">&times;</a></p>
    </section>';

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
        $shortcode_content .= '<div class="session-item-wrapper clearfix workshop-schedule">
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
                            require( plugin_dir_path( __FILE__ ) . '../templates/workshop-list-template.php' );
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
add_shortcode( 'workshops_schedule', 'workshops_schedule_shortcode' );
