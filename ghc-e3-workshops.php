<?php
/**
 * Plugin Name: GHC E3 Bundle Workshopzs
 * Plugin URI: https://github.com/greathomeschoolconventions/ghc-functionality
 * Description: Add streaming-only workshop features
 * Version: 1.0.0
 * Author: AndrewRMinion Design
 * Author URI: https://andrewrminion.com
 * Copyright: 2017 AndrewRMinion Design

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

defined( 'ABSPATH' ) or die( 'No access allowed' );

if ( ! function_exists( 'ghc_admin_options' ) ) {
    include( 'inc/acf.php' );
}

/**
 * Flush rewrite rules on (de)activation
 */
function ghc_workshops_activate() {
    ghc_e3_cpts();
    flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'ghc_workshops_activate' );
register_deactivation_hook( __FILE__, 'flush_rewrite_rules' );

/**
 * Register CPTs
 */
function ghc_e3_cpts() {
    $workshops_labels = array(
        'name'                => 'E3 Workshops',
        'singular_name'       => 'E3 Workshop',
        'menu_name'           => 'E3 Workshops',
        'name_admin_bar'      => 'E3 Workshop',
        'parent_item_colon'   => 'Parent E3 Workshop:',
        'all_items'           => 'All E3 Workshops',
        'add_new_item'        => 'Add New E3 Workshop',
        'add_new'             => 'Add New',
        'new_item'            => 'New E3 Workshop',
        'edit_item'           => 'Edit E3 Workshop',
        'update_item'         => 'Update E3 Workshop',
        'view_item'           => 'View E3 Workshop',
        'search_items'        => 'Search E3 Workshop',
        'not_found'           => 'Not found',
        'not_found_in_trash'  => 'Not found in Trash',
    );
    $workshops_rewrite = array(
        'slug'                => 'e3-workshops',
        'with_front'          => true,
        'pages'               => true,
        'feeds'               => true,
    );
    $workshops_args = array(
        'label'               => __( 'workshop', 'GHC' ),
        'description'         => __( 'Workshops', 'GHC' ),
        'labels'              => $workshops_labels,
        'supports'            => array( 'title', 'editor', 'excerpt', 'thumbnail', 'revisions', 'page-attributes', ),
        'taxonomies'          => array(),
        'hierarchical'        => true,
        'public'              => true,
        'show_ui'             => true,
        'show_in_menu'        => true,
        'menu_position'       => 28,
        'menu_icon'           => 'dashicons-welcome-learn-more',
        'show_in_admin_bar'   => true,
        'show_in_nav_menus'   => true,
        'can_export'          => true,
        'has_archive'         => 'my-account/e3-workshops',
        'exclude_from_search' => false,
        'publicly_queryable'  => true,
        'rewrite'             => $workshops_rewrite,
        'capability_type'     => 'page',
    );
    register_post_type( 'e3_workshop', $workshops_args );
}
add_action( 'init', 'ghc_e3_cpts' );

/**
 * Modify workshops query
 * @param  object $query WP_Query object
 * @return object modified WP_Query object
 */
function ghc_e3_post_order( $query ) {
    if ( ! is_admin() && $query->is_archive && 'e3_workshop' == $query->get( 'post_type' ) ) {
        $query->set( 'orderby', 'post_title' );
        $query->set( 'order', 'ASC' );
        $query->set( 'posts_per_page', '-1' );
    }

    return $query;
}
add_action( 'pre_get_posts', 'ghc_e3_post_order' );

/**
 * Add media player and speaker bio to E3 workshop content
 * @param  string $content HTML content
 * @return string modified HTML content
 */
function ghc_e3_content( $content ) {
    $new_content = '';
    if ( 'e3_workshop' == get_post_type() ) {
        $speaker_bio = get_field( 'e3_speaker_biography' );
        $speaker_bio_content = '<a class="button expand-trigger">About ' . $speaker_name . ' <span class="dashicons dashicons-arrow-down-alt2"></span></a><div class="click-to-expand">' . $speaker_bio . '</div>';

        if ( is_singular() ) {
            $new_content .= '
            <audio class="wp-audio-shortcode" controls="controls" preload="none" style="width: 100%">
                <source type="audio/mpeg" src="' . ghc_e3_get_signed_URL( get_field( 'e3_workshop_media_url' ) ) . '" />
            </audio>';

            if ( $speaker_bio ) {
                $content .= '<p>' . $speaker_bio_content . '</p>';
            }

            wp_enqueue_style( 'wp-mediaelement' );
            wp_enqueue_script( 'wp-mediaelement' );
        } else {
            $content .= '<p><a class="button" href="' . get_permalink() . '">Listen Now <span class="dashicons dashicons-arrow-right-alt2"></span></a>' . ( $speaker_bio ? $speaker_bio_content : '' ) . '</p>';
        }
    }

    return $new_content . $content;
}
add_filter( 'the_content', 'ghc_e3_content' );
add_filter( 'the_excerpt', 'ghc_e3_content' );

/**
 * Add speaker info to post thumbnail
 * @param  string       $html              featured image HTML
 * @param  integer      $post_id           WP post ID
 * @param  integer      $post_thumbnail_id WP media post ID
 * @param  string/array $size              image size
 * @param  array        $attr              query string of attributes
 * @return string       featured image HTML
 */
function ghc_e3_thumbnail_content( $html, $post_id, $post_thumbnail_id, $size, $attr ) {
    if ( 'e3_workshop' == get_post_type( $post_id ) ) {
        $speaker_name = get_field( 'e3_speaker_name' );
        $speaker_company = get_field( 'e3_speaker_company' );
        $speaker_company_url = get_field( 'e3_speaker_company_url' );


        $html .= '<p class="entry-meta"><span class="speaker-name">' . $speaker_name . '</span>';
        if ( $speaker_company ) {
            $html .= '<br/>';
            if ( $speaker_company_url && is_user_logged_in() ) {
                $html .= '<a class="speaker-company" target="_blank" href="' . $speaker_company_url . '">' . $speaker_company . '</a>';
            } else {
                $html .= '<span class="speaker-company">' . $speaker_company . '</span>';
            }
        }
        $html .= '</p>';
    }
    return $html;
}
add_filter( 'post_thumbnail_html', 'ghc_e3_thumbnail_content', 10, 5 );

/**
 * Generate signed URL
 * @param  string $resource resource URL
 * @return string signed resource URL
 */
function ghc_e3_get_signed_URL( $resource ) {
    $key_pair_id = get_field( 'aws_cloudfront_key_pair_id', 'option' );
    $private_key = get_field( 'aws_cloudfront_private_key', 'option' );
    $aws_s3_domain = get_field( 'aws_s3_domain', 'option' );
    $aws_cloudfront_domain = get_field( 'aws_cloudfront_domain', 'option' );

    // use CloudFront domain instead of S3 domain
    $resource = str_replace( $aws_s3_domain, $aws_cloudfront_domain, $resource );


    // get expiration time
    $expires = time() + get_field( 'aws_cloudfront_signed_url_lifetime', 'option' );
    $json = '{"Statement":[{"Resource":"' . $resource.'","Condition":{"DateLessThan":{"AWS:EpochTime":' . $expires . '}}}]}';

    // get private key
    $key = openssl_get_privatekey( $private_key );
    if( ! $key ) {
        error_log( 'Failed to read private key: ' . openssl_error_string() );
        return $resource;
    }

    // sign the policy with the private key
    if ( ! openssl_sign( $json, $signed_policy, $key ) ) {
        error_log( 'Failed to sign url: ' . openssl_error_string() );
        return $resource;
    }

    // create signature
    $base64_signed_policy = base64_encode( $signed_policy );
    $signature = str_replace( array( '+', '=', '/' ), array( '-', '_', '~' ), $base64_signed_policy );

    // construct the URL
    $url = $resource . '?Expires=' . $expires . '&Signature=' . $signature . '&Key-Pair-Id=' . $key_pair_id;

    return $url;
}

/**
 * Tweak archive title
 * @param  string $title HTML archive title
 * @return string modified HTML archive title
 */
function ghc_e3_archive_title( $title ) {
    if ( is_category() ) {
        $title = single_cat_title( '', false );
    } elseif ( is_tag() ) {
        $title = single_tag_title( '', false );
    } elseif ( is_author() ) {
        $title = '<span class="vcard">' . get_the_author() . '</span>';
    } elseif ( is_post_type_archive() ) {
        $title = post_type_archive_title( '', false );
    } elseif ( is_tax() ) {
        $title = single_term_title( '', false );
    }

    return $title;
}
add_filter( 'get_the_archive_title', 'ghc_e3_archive_title' );

/**
 * Redirect to Checkout page when product is added to cart
 * @return string checkout URL
 */
function ghc_e3_add_to_cart_redirect() {
    global $woocommerce;
    $checkout_url = $woocommerce->cart->get_checkout_url();
    return $checkout_url;
}
add_filter( 'woocommerce_add_to_cart_redirect', 'ghc_e3_add_to_cart_redirect' );

/**
 * Change text on add-to-cart buttons
 * @return string button label
 */
function ghc_e3_cart_button_text() {
    return __( 'Buy Now', 'woocommerce' );
}
add_filter( 'woocommerce_product_single_add_to_cart_text', 'ghc_e3_cart_button_text' );

/**
 * Add shortcode for purchase page
 * @param  array  $attributes shortcode attributes
 * @return string HTML content
 */
function ghc_e3_workshop_promo_list( $attributes ) {
    $shortcode_attributes = shortcode_atts( array (
        'posts_per_page'    => -1,
        'offset'            => NULL,
    ), $attributes );

    ob_start();

    $shortcode_query_args = array(
        'post_type'         => 'e3_workshop',
        'posts_per_page'    => $shortcode_attributes['posts_per_page'],
        'offset'            => $shortcode_attributes['offset'],
    );

    $shortcode_query = new WP_Query( $shortcode_query_args );

    if ( $shortcode_query->have_posts() ) {
        echo '<section class="e3-shortcode-container">';
        while ( $shortcode_query->have_posts() ) {
            $shortcode_query->the_post();

            echo '<article class="' . implode( ' ', get_post_class() ) . '">';

            if ( has_post_thumbnail() ) {
                the_post_thumbnail( 'speaker_xs', array( 'class' => '' ) );
            }

            echo '<h3>' . get_the_title() . '</h3>
            <p><a class="workshop-description expand-trigger dashicons-after">Workshop Description <span class="dashicons dashicons-arrow-down-alt2"></span></a></p>
            <div class="excerpt click-to-expand">' . get_the_content() . '</div>
            </article>';
        }
        echo '</section>';
    }

    return ob_get_clean();
}
add_shortcode( 'e3_workshop_promo_list', 'ghc_e3_workshop_promo_list' );


/**
 * Add shortcode for buy now button
 * @param  array  $attributes shortcode attributes
 * @return string HTML content
 */
function ghc_e3_buy_now_button( $attributes ) {
    $shortcode_attributes = shortcode_atts( array (
        'product_id'    => NULL,
        'button_text'   => 'Buy Now',
    ), $attributes );

    ob_start();
    if ( $shortcode_attributes['product_id'] ) {
        echo '<section class="buy-now">
        <p><a class="button" rel="nofollow" href="' . home_url() . '/checkout/?add-to-cart=' . $shortcode_attributes['product_id'] . '">' . $shortcode_attributes['button_text'] . '</a><br/>
        <img src="' . plugin_dir_url( __FILE__ ) . 'images/credit-cards.svg" alt="credit card icons" /></p>
        </section>';
    }
    return ob_get_clean();
}
add_shortcode( 'e3_buy_now', 'ghc_e3_buy_now_button' );
