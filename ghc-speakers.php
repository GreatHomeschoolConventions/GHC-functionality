<?php
/**
 * Plugin Name: GHC Functionality
 * Plugin URI: https://github.com/macbookandrew/ghc-speakers
 * Description: Add speakers, exhibitors, sponsors, and hotels
 * Version: 2.1
 * Author: AndrewRMinion Design
 * Author URI: http://andrewrminion.com
 * Copyright: 2015 AndrewRMinion Design (andrew@andrewrminion.com)

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

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

CONST GHC_SPEAKERS_VERSION = 2.1;

// flush rewrite rules on activation/deactivation
function ghc_speakers_activate() {
    flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'ghc_speakers_activate' );

function ghc_speakers_deactivate() {
    flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'ghc_speakers_deactivate' );

// add custom image sizes
add_action( 'after_setup_theme', 'ghc_custom_image_sizes' );
function ghc_custom_image_sizes() {
    add_image_size( 'square-small', 400, 400, true );
    add_image_size( 'square-medium', 600, 600, true );
    add_image_size( 'square-large', 900, 900, true );
    add_image_size( 'small-grid-size-medium', 600, 450, true );
    add_image_size( 'small-grid-size-large', 800, 600, true );
}

// add custom image sizes to WP admin
add_filter( 'image_size_names_choose', 'ghc_custom_image_sizes_names' );
function ghc_custom_image_sizes_names( $sizes ) {
    return array_merge( $sizes, array(
        'square-small'              => 'Square',
        'square-medium'             => 'Square',
        'square-large'              => 'Square',
        'small-grid-size-medium'    => 'Grid',
        'small-grid-size-large'     => 'Grid',
    ));
}

// register JS and styles
function register_plugin_resources() {
    wp_register_script( 'modernizr-svg', plugins_url( 'js/modernizr-svg.min.js', __FILE__ ), array(), '3.3.1' );
    wp_register_script( 'ghc-woocommerce', plugins_url( 'js/woocommerce.min.js', __FILE__ ), array( 'woocommerce' ), GHC_SPEAKERS_VERSION );

    wp_enqueue_style( 'ghc-speakers', plugins_url( 'css/style.min.css', __FILE__ ), array(), GHC_SPEAKERS_VERSION );

    // load WooCommerce script only on WC pages
    if ( function_exists( 'is_product' ) && function_exists( 'is_cart' ) ) {
        if ( is_product() || is_cart() ) {
            wp_enqueue_script( 'ghc-woocommerce' );
        }
    }
}
add_action( 'wp_enqueue_scripts', 'register_plugin_resources' );

// Register Custom Post Types
function custom_post_types() {

    $locations_labels = array(
        'name'                => _x( 'Locations', 'Post Type General Name', 'GHC' ),
        'singular_name'       => _x( 'Location', 'Post Type Singular Name', 'GHC' ),
        'menu_name'           => __( 'Locations', 'GHC' ),
        'name_admin_bar'      => __( 'Location', 'GHC' ),
        'parent_item_colon'   => __( 'Parent Location:', 'GHC' ),
        'all_items'           => __( 'All Locations', 'GHC' ),
        'add_new_item'        => __( 'Add New Location', 'GHC' ),
        'add_new'             => __( 'Add New', 'GHC' ),
        'new_item'            => __( 'New Location', 'GHC' ),
        'edit_item'           => __( 'Edit Location', 'GHC' ),
        'update_item'         => __( 'Update Location', 'GHC' ),
        'view_item'           => __( 'View Location', 'GHC' ),
        'search_items'        => __( 'Search Location', 'GHC' ),
        'not_found'           => __( 'Not found', 'GHC' ),
        'not_found_in_trash'  => __( 'Not found in Trash', 'GHC' ),
    );
    $locations_rewrite = array(
        'slug'                => 'locations',
        'with_front'          => true,
        'pages'               => true,
        'feeds'               => true,
    );
    $locations_args = array(
        'label'               => __( 'location', 'GHC' ),
        'description'         => __( 'Locations', 'GHC' ),
        'labels'              => $locations_labels,
        'supports'            => array( 'title', 'editor', 'author', 'excerpt', 'thumbnail', 'revisions', 'page-attributes', ),
        'taxonomies'          => array( 'ghc_conventions_taxonomy' ),
        'hierarchical'        => true,
        'public'              => true,
        'show_ui'             => true,
        'show_in_menu'        => true,
        'menu_position'       => 5,
        'menu_icon'           => 'dashicons-location',
        'show_in_admin_bar'   => true,
        'show_in_nav_menus'   => true,
        'can_export'          => true,
        'has_archive'         => 'locations',
        'exclude_from_search' => false,
        'publicly_queryable'  => true,
        'rewrite'             => $locations_rewrite,
        'capability_type'     => 'page',
    );
    register_post_type( 'location', $locations_args );

    $special_events_labels = array(
        'name'                => _x( 'Special Events', 'Post Type General Name', 'GHC' ),
        'singular_name'       => _x( 'Special Event', 'Post Type Singular Name', 'GHC' ),
        'menu_name'           => __( 'Special Events', 'GHC' ),
        'name_admin_bar'      => __( 'Special Event', 'GHC' ),
        'parent_item_colon'   => __( 'Parent Special Event:', 'GHC' ),
        'all_items'           => __( 'All Special Events', 'GHC' ),
        'add_new_item'        => __( 'Add New Special Event', 'GHC' ),
        'add_new'             => __( 'Add New', 'GHC' ),
        'new_item'            => __( 'New Special Event', 'GHC' ),
        'edit_item'           => __( 'Edit Special Event', 'GHC' ),
        'update_item'         => __( 'Update Special Event', 'GHC' ),
        'view_item'           => __( 'View Special Event', 'GHC' ),
        'search_items'        => __( 'Search Special Event', 'GHC' ),
        'not_found'           => __( 'Not found', 'GHC' ),
        'not_found_in_trash'  => __( 'Not found in Trash', 'GHC' ),
    );
    $special_events_rewrite = array(
        'slug'                => 'special-events',
        'with_front'          => true,
        'pages'               => true,
        'feeds'               => true,
    );
    $special_events_args = array(
        'label'               => __( 'special_event', 'GHC' ),
        'description'         => __( 'Special Events', 'GHC' ),
        'labels'              => $special_events_labels,
        'supports'            => array( 'title', 'editor', 'excerpt', 'thumbnail', 'revisions', 'page-attributes', ),
        'taxonomies'          => array( 'ghc_conventions_taxonomy' ),
        'hierarchical'        => true,
        'public'              => true,
        'show_ui'             => true,
        'show_in_menu'        => true,
        'menu_position'       => 5,
        'menu_icon'           => 'dashicons-star-filled',
        'show_in_admin_bar'   => true,
        'show_in_nav_menus'   => true,
        'can_export'          => true,
        'has_archive'         => 'special-events',
        'exclude_from_search' => false,
        'publicly_queryable'  => true,
        'rewrite'             => $special_events_rewrite,
        'capability_type'     => 'page',
    );
    register_post_type( 'special_event', $special_events_args );

    $speakers_labels = array(
        'name'                => _x( 'Speakers', 'Post Type General Name', 'GHC' ),
        'singular_name'       => _x( 'Speaker', 'Post Type Singular Name', 'GHC' ),
        'menu_name'           => __( 'Speakers', 'GHC' ),
        'name_admin_bar'      => __( 'Speaker', 'GHC' ),
        'parent_item_colon'   => __( 'Parent Speaker:', 'GHC' ),
        'all_items'           => __( 'All Speakers', 'GHC' ),
        'add_new_item'        => __( 'Add New Speaker', 'GHC' ),
        'add_new'             => __( 'Add New', 'GHC' ),
        'new_item'            => __( 'New Speaker', 'GHC' ),
        'edit_item'           => __( 'Edit Speaker', 'GHC' ),
        'update_item'         => __( 'Update Speaker', 'GHC' ),
        'view_item'           => __( 'View Speaker', 'GHC' ),
        'search_items'        => __( 'Search Speaker', 'GHC' ),
        'not_found'           => __( 'Not found', 'GHC' ),
        'not_found_in_trash'  => __( 'Not found in Trash', 'GHC' ),
    );
    $speakers_rewrite = array(
        'slug'                => 'speakers',
        'with_front'          => true,
        'pages'               => true,
        'feeds'               => true,
    );
    $speakers_args = array(
        'label'               => __( 'speaker', 'GHC' ),
        'description'         => __( 'Speakers', 'GHC' ),
        'labels'              => $speakers_labels,
        'supports'            => array( 'title', 'editor', 'excerpt', 'thumbnail', 'revisions', 'page-attributes', ),
        'taxonomies'          => array( 'ghc_speakers_taxonomy', 'ghc_conventions_taxonomy', 'ghc_special_tracks_taxonomy' ),
        'hierarchical'        => true,
        'public'              => true,
        'show_ui'             => true,
        'show_in_menu'        => true,
        'menu_position'       => 5,
        'menu_icon'           => 'dashicons-admin-users',
        'show_in_admin_bar'   => true,
        'show_in_nav_menus'   => true,
        'can_export'          => true,
        'has_archive'         => 'speakers/all',
        'exclude_from_search' => false,
        'publicly_queryable'  => true,
        'rewrite'             => $speakers_rewrite,
        'capability_type'     => 'page',
    );
    register_post_type( 'speaker', $speakers_args );

    $exhibitors_labels = array(
        'name'                => _x( 'Exhibitors', 'Post Type General Name', 'GHC' ),
        'singular_name'       => _x( 'Exhibitor', 'Post Type Singular Name', 'GHC' ),
        'menu_name'           => __( 'Exhibitors', 'GHC' ),
        'name_admin_bar'      => __( 'Exhibitor', 'GHC' ),
        'parent_item_colon'   => __( 'Parent Exhibitor:', 'GHC' ),
        'all_items'           => __( 'All Exhibitors', 'GHC' ),
        'add_new_item'        => __( 'Add New Exhibitor', 'GHC' ),
        'add_new'             => __( 'Add New', 'GHC' ),
        'new_item'            => __( 'New Exhibitor', 'GHC' ),
        'edit_item'           => __( 'Edit Exhibitor', 'GHC' ),
        'update_item'         => __( 'Update Exhibitor', 'GHC' ),
        'view_item'           => __( 'View Exhibitor', 'GHC' ),
        'search_items'        => __( 'Search Exhibitor', 'GHC' ),
        'not_found'           => __( 'Not found', 'GHC' ),
        'not_found_in_trash'  => __( 'Not found in Trash', 'GHC' ),
    );
    $exhibitors_rewrite = array(
        'slug'                => 'exhibitor',
        'with_front'          => true,
        'pages'               => true,
        'feeds'               => true,
    );
    $exhibitors_args = array(
        'label'               => __( 'exhibitor', 'GHC' ),
        'description'         => __( 'Exhibitors', 'GHC' ),
        'labels'              => $exhibitors_labels,
        'supports'            => array( 'title', 'revisions', 'page-attributes', ),
        'taxonomies'          => array( 'ghc_conventions_taxonomy' ),
        'hierarchical'        => true,
        'public'              => true,
        'show_ui'             => true,
        'show_in_menu'        => true,
        'menu_position'       => 5,
        'menu_icon'           => 'dashicons-store',
        'show_in_admin_bar'   => true,
        'show_in_nav_menus'   => true,
        'can_export'          => true,
        'has_archive'         => 'exhibitors',
        'exclude_from_search' => false,
        'publicly_queryable'  => true,
        'rewrite'             => $exhibitors_rewrite,
        'capability_type'     => 'page',
    );
    register_post_type( 'exhibitor', $exhibitors_args );

    $sponsors_labels = array(
        'name'                => _x( 'Sponsors', 'Post Type General Name', 'GHC' ),
        'singular_name'       => _x( 'Sponsor', 'Post Type Singular Name', 'GHC' ),
        'menu_name'           => __( 'Sponsors', 'GHC' ),
        'name_admin_bar'      => __( 'Sponsor', 'GHC' ),
        'parent_item_colon'   => __( 'Parent Sponsor:', 'GHC' ),
        'all_items'           => __( 'All Sponsors', 'GHC' ),
        'add_new_item'        => __( 'Add New Sponsor', 'GHC' ),
        'add_new'             => __( 'Add New', 'GHC' ),
        'new_item'            => __( 'New Sponsor', 'GHC' ),
        'edit_item'           => __( 'Edit Sponsor', 'GHC' ),
        'update_item'         => __( 'Update Sponsor', 'GHC' ),
        'view_item'           => __( 'View Sponsor', 'GHC' ),
        'search_items'        => __( 'Search Sponsor', 'GHC' ),
        'not_found'           => __( 'Not found', 'GHC' ),
        'not_found_in_trash'  => __( 'Not found in Trash', 'GHC' ),
    );
    $sponsors_rewrite = array(
        'slug'                => 'sponsors',
        'with_front'          => true,
        'pages'               => true,
        'feeds'               => true,
    );
    $sponsors_args = array(
        'label'               => __( 'sponsor', 'GHC' ),
        'description'         => __( 'Sponsors', 'GHC' ),
        'labels'              => $sponsors_labels,
        'supports'            => array( 'title', 'editor', 'excerpt', 'thumbnail', 'revisions', 'page-attributes', ),
        'hierarchical'        => true,
        'public'              => true,
        'show_ui'             => true,
        'show_in_menu'        => true,
        'menu_position'       => 5,
        'menu_icon'           => 'dashicons-awards',
        'show_in_admin_bar'   => true,
        'show_in_nav_menus'   => true,
        'can_export'          => true,
        'has_archive'         => 'sponsors',
        'exclude_from_search' => false,
        'publicly_queryable'  => true,
        'rewrite'             => $sponsors_rewrite,
        'capability_type'     => 'page',
    );
    register_post_type( 'sponsor', $sponsors_args );

    $hotels_labels = array(
        'name'                => _x( 'Hotels', 'Post Type General Name', 'GHC' ),
        'singular_name'       => _x( 'Hotel', 'Post Type Singular Name', 'GHC' ),
        'menu_name'           => __( 'Hotels', 'GHC' ),
        'name_admin_bar'      => __( 'Hotel', 'GHC' ),
        'parent_item_colon'   => __( 'Parent Hotel:', 'GHC' ),
        'all_items'           => __( 'All Hotels', 'GHC' ),
        'add_new_item'        => __( 'Add New Hotel', 'GHC' ),
        'add_new'             => __( 'Add New', 'GHC' ),
        'new_item'            => __( 'New Hotel', 'GHC' ),
        'edit_item'           => __( 'Edit Hotel', 'GHC' ),
        'update_item'         => __( 'Update Hotel', 'GHC' ),
        'view_item'           => __( 'View Hotel', 'GHC' ),
        'search_items'        => __( 'Search Hotels', 'GHC' ),
        'not_found'           => __( 'Not found', 'GHC' ),
        'not_found_in_trash'  => __( 'Not found in Trash', 'GHC' ),
    );
    $hotels_args = array(
        'label'               => __( 'Hotel', 'GHC' ),
        'description'         => __( 'Hotels', 'GHC' ),
        'labels'              => $hotels_labels,
        'supports'            => array( 'title', 'editor', 'author', 'thumbnail', 'revisions', 'custom-fields', 'page-attributes', ),
        'taxonomies'          => array( 'ghc_conventions_taxonomy' ),
        'hierarchical'        => false,
        'public'              => true,
        'show_ui'             => true,
        'show_in_menu'        => true,
        'menu_position'       => 5,
        'menu_icon'           => 'dashicons-admin-home',
        'show_in_admin_bar'   => true,
        'show_in_nav_menus'   => true,
        'can_export'          => true,
        'has_archive'         => true,
        'exclude_from_search' => false,
        'publicly_queryable'  => true,
        'capability_type'     => 'page',
    );
    register_post_type( 'hotel', $hotels_args );


}
// Hook into the 'init' action to register custom post types
add_action( 'init', 'custom_post_types', 0 );

// Register Custom Taxonomies
function ghc_taxonomies() {
    $track_labels = array(
        'name'                       => _x( 'Special Tracks', 'Taxonomy General Name', 'GHC' ),
        'singular_name'              => _x( 'Special Track', 'Taxonomy Singular Name', 'GHC' ),
        'menu_name'                  => __( 'Special Tracks', 'GHC' ),
        'all_items'                  => __( 'All Special Tracks', 'GHC' ),
        'parent_item'                => __( 'Parent Special Track', 'GHC' ),
        'parent_item_colon'          => __( 'Parent Special Track:', 'GHC' ),
        'new_item_name'              => __( 'New Special Track Name', 'GHC' ),
        'add_new_item'               => __( 'Add New Special Track', 'GHC' ),
        'edit_item'                  => __( 'Edit Special Track', 'GHC' ),
        'update_item'                => __( 'Update Special Track', 'GHC' ),
        'view_item'                  => __( 'View Special Track', 'GHC' ),
        'separate_items_with_commas' => __( 'Separate Special Tracks with commas', 'GHC' ),
        'add_or_remove_items'        => __( 'Add or remove Special Tracks', 'GHC' ),
        'choose_from_most_used'      => __( 'Choose from the most used', 'GHC' ),
        'popular_items'              => __( 'Popular Special Tracks', 'GHC' ),
        'search_items'               => __( 'Search Special Tracks', 'GHC' ),
        'not_found'                  => __( 'Not Found', 'GHC' ),
    );
    $track_rewrite = array(
        'slug'                       => 'special-tracks',
        'with_front'                 => true,
        'hierarchical'               => true,
    );
    $track_args = array(
        'labels'                     => $track_labels,
        'hierarchical'               => true,
        'public'                     => true,
        'show_ui'                    => true,
        'show_admin_column'          => true,
        'show_in_nav_menus'          => true,
        'show_tagcloud'              => true,
        'rewrite'                    => $track_rewrite,
    );
    register_taxonomy( 'ghc_special_tracks_taxonomy', array( 'speaker', 'session' ), $track_args );

    $convention_labels = array(
        'name'                       => _x( 'Conventions', 'Taxonomy General Name', 'GHC' ),
        'singular_name'              => _x( 'Convention', 'Taxonomy Singular Name', 'GHC' ),
        'menu_name'                  => __( 'Conventions', 'GHC' ),
        'all_items'                  => __( 'All Conventions', 'GHC' ),
        'parent_item'                => __( 'Parent Convention', 'GHC' ),
        'parent_item_colon'          => __( 'Parent Convention:', 'GHC' ),
        'new_item_name'              => __( 'New Convention Name', 'GHC' ),
        'add_new_item'               => __( 'Add New Convention', 'GHC' ),
        'edit_item'                  => __( 'Edit Convention', 'GHC' ),
        'update_item'                => __( 'Update Convention', 'GHC' ),
        'view_item'                  => __( 'View Convention', 'GHC' ),
        'separate_items_with_commas' => __( 'Separate conventions with commas', 'GHC' ),
        'add_or_remove_items'        => __( 'Add or remove conventions', 'GHC' ),
        'choose_from_most_used'      => __( 'Choose from the most used', 'GHC' ),
        'popular_items'              => __( 'Popular Conventions', 'GHC' ),
        'search_items'               => __( 'Search Conventions', 'GHC' ),
        'not_found'                  => __( 'Not Found', 'GHC' ),
    );
    $convention_args = array(
        'labels'                     => $convention_labels,
        'hierarchical'               => true,
        'public'                     => true,
        'show_ui'                    => true,
        'show_admin_column'          => true,
        'show_in_nav_menus'          => true,
        'show_tagcloud'              => true,
        'rewrite'                    => false,
    );
    register_taxonomy( 'ghc_conventions_taxonomy', array( 'page', 'post', 'location', 'speaker', 'exhibitor', 'hotel', 'session', 'special_event' ), $convention_args );

}
// Hook into the 'init' action to register custom taxonomy
add_action( 'init', 'ghc_taxonomies', 0 );

// add “order” to speaker column headers
function ghc_speaker_columns( $columns ) {
    $columns['menu_order'] = 'Order';
    return $columns;
}
add_filter( 'manage_edit-speaker_columns', 'ghc_speaker_columns' );

// add “order” to speaker column details
function ghc_speaker_column_content( $column, $post_id ) {
    global $post;
    if ( 'menu_order' == $column ) {
        echo $post->menu_order;
    }
}
add_action( 'manage_speaker_posts_custom_column', 'ghc_speaker_column_content', 10, 2 );

// make “order” column header sortable
function ghc_speaker_sortable_columns( $columns ) {
    $columns['menu_order'] = 'menu_order';
    return $columns;
}
add_filter( 'manage_edit-speaker_sortable_columns', 'ghc_speaker_sortable_columns' );

// add exhibitor backend JS
add_action( 'admin_enqueue_scripts', 'include_exhibitor_backend_js' );
function include_exhibitor_backend_js() {
    global $post_type;
    if ( 'exhibitor' == $post_type ) {
        wp_enqueue_script( 'exhibitor-backend', plugins_url( 'js/exhibitor-backend.min.js', __FILE__ ), array( 'jquery' ), NULL, true );
    }
}

// add custom field to user profile screens to match with speakers CPT
add_action( 'show_user_profile', 'show_speaker_matching_box' );
add_action( 'edit_user_profile', 'show_speaker_matching_box' );
function show_speaker_matching_box( $user ) {
    echo '<h3>Select a speaker to match to this author</h3>
    <table class="form-table">
        <tr>
            <th><label for="speaker_match">Speaker</label></th>
            <td>
                <select name="speaker_match" id="speaker_match">
                    <option value="">- Select one -</option>';
                    $speakers_query_args = array(
                        'post_type'              => array( 'speaker' ),
                        'posts_per_page'         => '-1',
                    );
                    $speakers_query = new WP_Query( $speakers_query_args );
                    if ( $speakers_query->have_posts() ) {
                        while ( $speakers_query->have_posts() ) {
                            $speakers_query->the_post();
                            echo '<option value="' . get_the_ID() . '"';
                            if ( get_user_meta( $user->ID, 'speaker_match', true ) == get_the_ID() ) { echo ' selected="selected"'; }
                            echo '>' . get_the_title() . '</option>';
                        }
                    }
                echo '</select>
            </td>
        </tr>
    </table>';
}

// save custom user profile field
add_action( 'personal_options_update', 'ghc_save_extra_profile_fields' );
add_action( 'edit_user_profile_update', 'ghc_save_extra_profile_fields' );
function ghc_save_extra_profile_fields( $user_id ) {

    if ( !current_user_can( 'edit_user', $user_id ) ) {
        return false;
    }

    update_usermeta( $user_id, 'speaker_match', esc_attr( $_POST['speaker_match'] ) );
}

// add meet_the_author function for blog posts
function meet_the_author() {
    if ( get_the_author_meta( 'speaker_match' ) ) {
        $meet_the_author_output = '<p class="no-margin">Meet <a href="' . get_permalink( get_the_author_meta( 'speaker_match' ) ) . '">' . get_the_author() . '</a> at these conventions:</p>';

        foreach ( get_the_terms_sorted( get_the_author_meta( 'speaker_match' ), 'ghc_conventions_taxonomy' ) as $author_convention ) {
            $meet_the_author_output .= do_shortcode( '[convention_icon convention="' . $author_convention->name . '"]' );
        }

        return $meet_the_author_output;
    }
}

// always sort speakers by menu_order
add_filter( 'pre_get_posts', 'ghc_speakers_order' );
function ghc_speakers_order( $query ) {
    if ( 'speaker' == $query->query['post_type'] && ! is_admin() ) {
        $query->query['orderby'] = 'menu_order';
        $query->query_vars['orderby'] = 'menu_order';
        $query->query['order'] = 'ASC';
        $query->query_vars['order'] = 'ASC';
    }

    return $query;
}

// modify speaker archive query to hide general speakers
add_action( 'pre_get_posts', 'ghc_modify_speaker_archive' );
function ghc_modify_speaker_archive( $query ) {
    if ( ! is_admin() && $query->is_main_query() && is_post_type_archive( 'speaker' ) ) {
        $query->set( 'meta_key', 'featured_speaker' );
        $query->set( 'meta_compare', '!=' );
        $query->set( 'meta_value', 'no' );
    }
}

// modify exhibitor archive query to show all, sorted by name
add_action( 'pre_get_posts', 'ghc_modify_exhibitor_archive' );
function ghc_modify_exhibitor_archive( $query ) {
    if ( ! is_admin() && $query->is_main_query() && is_post_type_archive( 'exhibitor' ) ) {
        $query->set( 'posts_per_page', '-1' );
        $query->set( 'pagination', 'false' );
        $query->set( 'order', 'ASC' );
        $query->set( 'orderby', 'title' );
    }
}

// modify sponsor archive query to sort by page order
add_action( 'pre_get_posts', 'ghc_modify_sponsor_archive', 1 );
function ghc_modify_sponsor_archive( $query ) {
    if ( ! is_admin() && $query->is_main_query() && is_post_type_archive( 'sponsor' ) ) {
        $query->set( 'posts_per_page', -1 );
        $query->set( 'order', 'ASC' );
        $query->set( 'orderby', 'menu_order' );
    }
}

// modify exhibitor archive to show convention icons and site URLs
add_filter( 'the_content', 'ghc_exhibitor_archive_icons' );
function ghc_exhibitor_archive_icons( $content ) {
    global $post;
    if ( 'exhibitor' == get_post_type( $post->ID ) ) {
        if ( get_field( 'exhibitor_URL', $post->ID ) ) {
            echo '<p><a href="' . get_field( 'exhibitor_URL', $post->ID ) . '" target="_blank">Visit website&rarr;</a></p>';
        }
        echo output_convention_icons( $post->ID );
    }
    return $content;
}

// modify exhibitor post types to link to their website
add_filter( 'post_type_link', 'ghc_exhibitor_post_type_link', 10, 4 );
function ghc_exhibitor_post_type_link( $post_link, $post, $leavename, $sample ) {
    if ( 'exhibitor' == get_post_type( $post ) ) {
        $post_link = get_field( 'exhibitor_URL', $post->ID );
    }
    return $post_link;
}

// add options
if( function_exists('acf_add_options_page') ) {

    acf_add_options_page(array(
        'page_title' 	=> 'Options',
        'menu_title'	=> 'Options',
        'menu_slug' 	=> 'theme-options',
        'capability'	=> 'edit_posts',
        'redirect'		=> false
    ));

    acf_add_options_sub_page(array(
        'page_title' 	=> 'Call to Action Widget Content',
        'menu_title'	=> 'CTA Widget',
        'menu_slug' 	=> 'cta-widget',
        'capability'	=> 'edit_posts',
        'parent_slug'   => 'theme-options',
    ));

}

// add extra function to get_the_terms by term_id order rather than alphabetical
// adapted from https://wordpress.org/support/topic/use-with-get_the_terms#post-5093011
function get_the_terms_sorted( $post_id, $taxonomy ) {
    $terms = get_the_terms( $post_id, $taxonomy );
    if ( $terms ) {
        usort( $terms, 'cmp_by_custom_order' );
    }
    return $terms;
}
function cmp_by_custom_order( $a, $b ) {
    return $a->term_id - $b->term_id;
}

// get options
function publish_convention_details() {
    echo '<h2>Upcoming Great Homeschool Conventions Events</h2>';

    // WP_Query arguments
    $args = array (
        'post_type'              => array( 'location' ),
        'posts_per_page'         => -1,
        'post_status'            => 'publish',
        'order'                  => 'DESC',
        'orderby'                => 'meta_value',
        'meta_query'             => array(
            array(
                'key'       => 'begin_date',
                'type'      => 'NUMERIC',
            ),
        ),
        'no_found_rows'          => true,
    );

    // The Query
    $locations_query = new WP_Query( $args );

    // The Loop
    if ( $locations_query->have_posts() ) {
        while ( $locations_query->have_posts() && $counter == 0) {
            $locations_query->the_post(); ?>
            <p><strong>&#9658;&nbsp;<a href="<?php the_permalink(); ?>"><?php the_title() ;?></a></strong>, <?php the_field( 'convention_center_name' ); ?><br/>
                <?php echo get_field( 'city' ) . ', ' . get_field( 'state' ) . ': ' . date( 'F j', get_field( 'begin_date' ) ) . '&ndash;' . date( 'j, Y', get_field( 'end_date' ) ); ?></p>
        <?php }
    }
    wp_reset_postdata();
}

// get convention info
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
        while ( $locations_query->have_posts() && $counter == 0) {
            $locations_query->the_post();

            $convention_info = array(
                'ID'        => get_the_ID(),
                'title'     => get_the_title(),
                'permalink' => get_the_permalink(),
            );
            $convention_key = strtolower( get_field( 'convention_abbreviated_name' ) );
            $conventions[$convention_key] = array_merge( $convention_info, get_post_meta( get_the_ID() ) );
        }
    }
    wp_reset_postdata();

    // convention abbreviations
    global $convention_abbreviations;
    foreach ( $conventions as $key => $values ) {
        $convention_abbreviations[$key] = strtolower( implode( '', $values['convention_short_name'] ) );
    }

    // convention URLs
    global $convention_urls;
    foreach ( $conventions as $key => $values ) {
        $convention_urls[$key] = get_permalink( $values['ID'] );
    }

    // convention dates (end dates)
    global $convention_dates;
    foreach ( $conventions as $key => $values ) {
        $convention_dates[$key] = mktime( get_field( 'end_date', $values['ID'] ) );
    }

}
add_action( 'wp_head', 'convention_info' );

// include shortcodes
if ( ! is_admin() ) {
    require_once( 'inc/shortcodes.php' );
}

// GitHub updater
if ( is_admin() ) {
    include_once( 'updater.php' );

    $config = array(
        'slug'                  => plugin_basename( __FILE__ ),
        'proper_folder_name'    => 'ghc-speakers',
        'api_url'               => 'https://api.github.com/repos/macbookandrew/ghc-speakers',
        'raw_url'               => 'https://raw.github.com/macbookandrew/ghc-speakers/master',
        'github_url'            => 'https://github.com/macbookandrew/ghc-speakers',
        'zip_url'               => 'https://github.com/macbookandrew/ghc-speakers/zipball/master',
        'sslverify'             => true,
        'requires'              => '3.0',
        'tested'                => '3.3',
        'readme'                => 'README.md',
        'access_token'          => ''
    );
    new WP_GitHub_Updater( $config );

}

/**
 * Output convention icons
 * @param array  $conventions       Array of WP_Term objects
 * @param array  $args              Array of options arguments
 * @return string $convention_icons HTML string with content
 */
function output_convention_icons( $input_conventions, $args = NULL ) {
    global $conventions, $convention_abbreviations;
    $convention_icons = NULL;
    wp_enqueue_script( 'modernizr-svg' );

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

        $convention_icons .= '<a class="speaker-convention-link" href="' . get_permalink( $conventions[$convention_key]['landing_page'][0] ) . '">';
            $convention_icons .= '<svg role="img" title="' . $conventions[$convention_key]['title'] . '"><use xlink:href="' . plugins_url( 'SVG/icons.min.svg#icon-' . ucfirst ($convention_abbreviations[$convention_key] ) . '_small', __FILE__ ) . '"></use></svg><span class="fallback ' . $convention_key . '">' . $conventions[$convention_key]['title'] . '</span>';
        $convention_icons .= '</a>';
    }

    // add filter hook
    $convention_icons = apply_filters( 'ghc_convention_icons', $convention_icons );

    return $convention_icons;
}

// helper function to sort convention order correctly
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

// add Google Maps API key
add_action('acf/init', 'ghc_acf_init');
function ghc_acf_init() {
    acf_update_setting('google_api_key', get_option( 'options_api_key' ) );
}

// add video Open Graph data
add_action( 'wp_head', 'ghc_opengraph_video', 8 );
function ghc_opengraph_video() {
    $featured_video = get_field( 'featured_video' );
    if ( $featured_video ) {
        $video_ID = get_video_ID( $featured_video );
        $featured_video_meta = get_post_meta( get_the_ID(), 'featured_video_meta', true );

        // video
        echo '<meta property="og:video" content="' . $featured_video . '" />';
        echo strpos( $featured_video, 'https' ) !== false ? '<meta property="og:video:secure_url" content="' . $featured_video . '" />' : '' ;
        echo '<meta property="og:video:width" content="' . $featured_video_meta['width'] . '" />';
        echo '<meta property="og:video:height" content="' . $featured_video_meta['height'] . '" />';

        // placeholder image
        echo '<meta property="og:image" content="' . $featured_video_meta['url'] . '" />';
    }
}

// retrieve featured video meta and save to postmeta
add_action( 'acf/save_post', 'ghc_opengraph_video_get_meta', 20 );
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
        $youtube_thumbnail = $youtube_meta->items[0]->snippet->thumbnails->maxres;

        // save post meta
        $youtube_thumbnail_array = array(
            'url'    => $youtube_thumbnail->url,
            'width'  => $youtube_thumbnail->width,
            'height' => $youtube_thumbnail->height,
        );

        update_post_meta( $post_id, 'featured_video_meta', $youtube_thumbnail_array );
    }
}

/**
 * Retrieve ID from video URL
 * @param  string $video_url public URL of video
 * @return string video ID parameter
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
