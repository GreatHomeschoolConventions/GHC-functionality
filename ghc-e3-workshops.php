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
    if ( ! is_admin() && ! is_singular() && 'e3_workshop' == $query->get( 'post_type' ) ) {
        $query->set( 'orderby', 'post_title' );
        $query->set( 'order', 'ASC' );
        $query->set( 'posts_per_page', '-1' );
    }

    return $query;
}
add_action( 'pre_get_posts', 'ghc_e3_post_order' );

/**
 * Add speaker info and media player to E3 workshop content
 * @param  string $content HTML content
 * @return string modified HTML content
 */
function ghc_e3_content( $content ) {
    $new_content = '';
    if ( 'e3_workshop' == get_post_type() ) {
        $speaker_company = get_field( 'e3_speaker_company' );
        $speaker_company_url = get_field( 'e3_speaker_company_url' );

        $new_content .= '<div class="entry-meta"><p class="speaker-info">' . get_field( 'e3_speaker_name' );
        if ( $speaker_company ) {
            $new_content .= ' | ';
            if ( $speaker_company_url ) {
                $new_content .= '<a target="_blank" href="' . $speaker_company_url . '">' . $speaker_company . '</a>';
            } else {
                $new_content .= $speaker_company;
            }
        }
        $new_content .= '</p></div>';

        $new_content .= '
        <audio class="wp-audio-shortcode" controls="controls" preload="metadata" style="width: 100%">
            <source type="audio/mpeg" src="' . ghc_e3_get_signed_URL( get_field( 'e3_workshop_media_url' ) ) . '" />
        </audio>';
    }

    wp_enqueue_style( 'wp-mediaelement' );
    wp_enqueue_script( 'wp-mediaelement' );

    return $new_content . $content;
}
add_filter( 'the_content', 'ghc_e3_content' );
add_filter( 'the_excerpt', 'ghc_e3_content' );

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
