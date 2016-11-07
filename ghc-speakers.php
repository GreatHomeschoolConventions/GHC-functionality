<?php
/**
 * Plugin Name: GHC Functionality
 * Plugin URI: https://github.com/macbookandrew/ghc-speakers
 * Description: Add speakers, exhibitors, sponsors, and hotels
 * Version: 2.3.4
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

CONST GHC_SPEAKERS_VERSION = '2.3.4';

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
    add_image_size( 'thumbnail-no-crop', 140, 140, false );
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
        'thumbnail-no-crop'         => 'Thumbnail (no crop)',
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
    wp_register_script( 'price-sheets', plugins_url( 'js/price-sheets.min.js', __FILE__ ), array( 'jquery' ), GHC_SPEAKERS_VERSION );

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
    $hotels_rewrite = array(
        'slug'                => 'hotels',
        'with_front'          => true,
        'pages'               => true,
        'feeds'               => true,
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
        'rewrite'             => $hotels_rewrite,
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
add_action( 'ghc_meet_the_author', 'ghc_meet_the_author' );
function ghc_meet_the_author( $content ) {
    if ( is_singular() && get_the_author_meta( 'speaker_match' ) ) {
        $meet_the_author_output = '<p class="no-margin">Meet <a href="' . get_permalink( get_the_author_meta( 'speaker_match' ) ) . '">' . get_the_author() . '</a> at these conventions:</p>';

        $meet_the_author_output .= output_convention_icons( get_the_terms( get_the_author_meta( 'speaker_match' ), 'ghc_conventions_taxonomy' ) );
    }
    echo $meet_the_author_output;
}

// add related sponsors to all posts/pages/CPTs
add_filter( 'the_content', 'ghc_related_sponsors', 15 );
function ghc_related_sponsors( $content ) {
    if ( is_singular() ) {
        // get related sponsors
        $related_sponsors = get_field( 'related_sponsors' );

        if ( $related_sponsors ) {
            // set up query args
            $related_sponsors_query_args = array(
                'post_type'         => 'sponsor',
                'orderby'           => 'menu_order',
                'order'             => 'ASC',
                'posts_per_page'    => -1,
                'post__in'          => $related_sponsors,
            );

            $related_sponsors_query = new WP_Query( $related_sponsors_query_args );

            if ( $related_sponsors_query->have_posts() ) {
                $content .= '<div id="sponsor-stripe">
                <h3 class="gdlr-item-title gdlr-skin-title gdlr-title-small">SPONSORS</h3>
                <div class="sponsors">';

                while ( $related_sponsors_query->have_posts() ) {
                    $related_sponsors_query->the_post();
                    $content .= '<div class="sponsor">';
                    $grayscale_logo = get_field( 'grayscale_logo' );
                    $permalink = get_permalink();

                    if ( $grayscale_logo ) {
                        $content .= '<a href="' . $permalink . '"><img class="wp-post-image sponsor wp-image-' . $grayscale_logo['id'] . '" src="' . $grayscale_logo['url'] . '" alt="' . $grayscale_logo['alt'] . '" title="' . $grayscale_logo['title'] . '" /></a>';
                    } else {
                        $content .= '<a href="' . $permalink . '">' . get_the_post_thumbnail() . '</a>';
                    }
                    $content .= '</div><!-- .sponsor -->';
                }
                $content .= '</div><!-- .sponsors -->
                </div><!-- #sponsor-stripe -->';
            }

            // reset post data
            wp_reset_postdata();
        }
    }
    return $content;
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

// use custom hotel archive template
add_filter( 'archive_template', 'ghc_hotel_archive' );
function ghc_hotel_archive( $archive_template ) {
    global $post;

    if ( is_post_type_archive( 'hotel' ) ) {
        $archive_template = plugin_dir_path( __FILE__ ) . '/inc/archive-hotel.php';
    }
    return $archive_template;
}

// add hotel info to single views
add_filter( 'the_content', 'ghc_hotel_single_view' );
function ghc_hotel_single_view( $content ) {
    if ( is_single() && 'hotel' == get_post_type() ) {
        // get convention info
        global $conventions, $convention_abbreviations;
        $conventions_taxonomy = get_the_terms( get_the_ID(), 'ghc_conventions_taxonomy' );
        $this_convention = array_flip( $convention_abbreviations )[$conventions_taxonomy[0]->slug];

        // get hotel details
        ob_start();
        include( 'inc/hotel-details.php' );
        $content .= ob_get_clean();

        if ( get_field( 'hotel_URL' ) && ! get_field( 'sold_out' ) ) {
            $content .= '<a class="accommodation-button-text gdlr-button" target="_blank" href="' . get_field( 'hotel_URL' ) . '">BOOK ONLINE NOW</a>';
        }
    }

    return $content;
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
    /* $conventions: each key is the two-letter abbreviation */

    // convention abbreviations
    global $convention_abbreviations;
    foreach ( $conventions as $key => $values ) {
        $convention_abbreviations[$key] = strtolower( implode( '', $values['convention_short_name'] ) );
    }
    /* $convention_abbreviations: each key is the two-letter abbreviation */

    // convention URLs
    global $convention_urls;
    foreach ( $conventions as $key => $values ) {
        $convention_urls[$key] = get_permalink( $values['ID'] );
    }
    /* $convention_urls: each key is the two-letter abbreviation */

    // convention dates (end dates)
    global $convention_dates;
    foreach ( $conventions as $key => $values ) {
        $convention_dates[$key] = mktime( get_field( 'end_date', $values['ID'] ) );
    }
    /* $convention_dates: each key is the two-letter abbreviation, and the value is the Unix time */

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
        echo '<meta property="og:video:width" content="' . $featured_video_meta->snippet->thumbnails->maxres->width . '" />';
        echo '<meta property="og:video:height" content="' . $featured_video_meta->snippet->thumbnails->maxres->height . '" />';

        // placeholder image
        echo '<meta property="og:image" content="' . $featured_video_meta->snippet->thumbnails->maxres->url . '" />';
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
        $youtube_thumbnail = $youtube_meta->items[0];

        // save post meta
        update_post_meta( $post_id, 'featured_video_meta', $youtube_thumbnail );
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

/**
 * Show product short description on registration page
 */
add_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_single_excerpt', 5);

/**
 * Replace related products with cross-sells and set a high number of columns
 */
add_action( 'woocommerce_after_single_product_summary', 'ghc_show_special_event_tickets', 5 );
function ghc_show_special_event_tickets() {
    global $post;

    // get terms and filter out the “Registration” item
    $terms = get_the_terms( $post->ID, 'product_cat' );
    foreach ( $terms as $term ) {
        $convention_term_array = array( 'Texas', 'Southeast', 'Midwest', 'California' );
        if ( in_array( $term->name, $convention_term_array ) ) {
            $convention_category = $term->term_id;
        }
    }

    // set up query args
    $special_events_query_args = array (
        'post__not_in'      => array( $post->ID ),
        'posts_per_page'    => -1,
        'post_status'       => 'publish',
        'post_type'         => 'product',
        'orderby'           => 'menu_order',
        'order'             => 'ASC',
        'tax_query'         => array(
            array(
                'taxonomy'  => 'product_cat',
                'field'     => 'id',
                'terms'     => $convention_category
            ),
            array(
                'taxonomy'  => 'product_cat',
                'field'     => 'slug',
                'terms'     => 'special-events'
            )
        )
    );

    $special_events = new WP_Query( $special_events_query_args );

    // loop through results
    if ( $special_events->have_posts() ) {
        ?>
        <div class="cross-sells">
            <h2>Special Events</h2>
            <?php woocommerce_product_loop_start(); ?>

                <?php while ( $special_events->have_posts() ) : $special_events->the_post(); ?>

                    <?php wc_get_template_part( 'content', 'product' ); ?>

                <?php endwhile; // end of the loop. ?>

            <?php woocommerce_product_loop_end(); ?>
        </div>
    <?php }

    // reset global query
    wp_reset_query();
}
add_filter( 'woocommerce_cross_sells_columns', 'woocommerce_remove_cross_sells_columns', 10, 1 );
function woocommerce_remove_cross_sells_columns( $columns ) {
    return 10;
}

/**
 * Disable the add to cart button if trying to add another registration
 */
function woocommerce_single_variation_add_to_cart_button() {
    global $product;

    // check this product’s categories to determine if it’s a registration product
    $this_product_terms = get_the_terms( $product->ID, 'product_cat' );
    if ( $this_product_terms ) {
        foreach ( $this_product_terms as $this_term ) {
            if ( 'Registration' == $this_term->name ) {
                $check_cart_for_registration = true;
            }
        }
    }

    if ( $check_cart_for_registration ) {
        // set up query args
        $all_conventions_query_args = array (
            'posts_per_page'    => -1,
            'post_status'       => 'publish',
            'post_type'         => 'product',
            'fields'            => 'ids',
            'tax_query'         => array(
                array(
                    'taxonomy'  => 'product_cat',
                    'field'     => 'slug',
                    'terms'     => 'registration'
                )
            )
        );

        $all_conventions = new WP_Query( $all_conventions_query_args );

        // loop through results and add IDs to an array
        $all_convention_variation_IDs = array();
        if ( $all_conventions->have_posts() ) {
            while( $all_conventions->have_posts() ) {
                $all_conventions->the_post();
                $all_convention_variation_IDs[] = get_the_ID();
            }
        }

        // reset global query
        wp_reset_query();

        // check cart products against registration items
        foreach( WC()->cart->get_cart() as $cart_item_key => $values ) {
            $in_cart_product = $values['data'];

            if ( in_array( $in_cart_product->id, $all_convention_variation_IDs ) ) {
                $disable_purchase = true;
                add_filter( 'woocommerce_product_single_add_to_cart_text', function() {
                    return 'Please check out before adding another convention to your cart.';
                } );
            }
        }
    }

    // output buttons
    echo '<div class="variations_button">';
        woocommerce_quantity_input( array( 'input_value' => isset( $_POST['quantity'] ) ? wc_stock_amount( $_POST['quantity'] ) : 1 ) );
        echo '<button type="submit" class="single_add_to_cart_button button alt';
        if ( $disable_purchase ) {
            echo ' disabled" disabled="true';
        }
        echo '">' . esc_html( $product->single_add_to_cart_text() ) . '</button>
        <input type="hidden" name="add-to-cart" value="' . absint( $product->id )  .'" />
        <input type="hidden" name="product_id" value="' . absint( $product->id ) . '" />
        <input type="hidden" name="variation_id" class="variation_id" value="" />
    </div>';
}

/**
 * Set the max special event ticket quantities to number of purchased tickets
 */
add_action( 'woocommerce_single_product_summary', 'ghc_check_for_individual_registration_in_cart' );
function ghc_check_for_individual_registration_in_cart() {
    // loop over products in cart searching for an individual product
    foreach( WC()->cart->get_cart() as $cart_item_key => $values ) {
        if ( strpos( $values['variation']['attribute_registration-type'], 'Shopping Only' ) !== false ) {
            // add filter for simple products
            add_filter( 'woocommerce_quantity_input_max', function() { return 0; } );
            // add filter for variable products
            add_filter( 'woocommerce_available_variation', function() { return 0; } );
        } elseif ( strpos( $values['variation']['attribute_attendee-type'], 'Individual' ) !== false ) {
            // add filter for simple products
            add_filter( 'woocommerce_quantity_input_max', function() { return 1; } );
            // add filter for variable products
            add_filter( 'woocommerce_available_variation', 'ghc_restrict_max_quantity_variable' );
        } else {
            // get the addons quantity and restrict to that number
            foreach( $values['addons'] as $value ) {
                if ( 'Family members' == $value['name'] ) {
                    global $max_special_event_tickets;
                    $max_special_event_tickets = esc_attr( $value['value'] );
                    // add filter for simple products
                    add_filter( 'woocommerce_quantity_input_max', 'ghc_restrict_max_quantity_simple' );
                    // add filter for variable products
                    add_filter( 'woocommerce_available_variation', 'ghc_restrict_max_quantity_variable' );
                }
            }
        }
    }
}
function ghc_restrict_max_quantity_simple() {
    global $max_special_event_tickets;
    return $max_special_event_tickets;
}
function ghc_restrict_max_quantity_variable( $variations ) {
    global $max_special_event_tickets;

    if ( $max_special_event_tickets ) {
        $variations['max_qty'] = $max_special_event_tickets;
    } else {
        $variations['max_qty'] = '1';
    }
    return $variations;
}
// check in cart
add_filter( 'woocommerce_cart_item_quantity', 'ghc_testing', 10, 3 );
function ghc_testing( $product_quantity, $cart_item_key, $cart_item ) {
    foreach( WC()->cart->get_cart() as $cart_item_key => $values ) {
        if ( strpos( $values['variation']['attribute_attendee-type'], 'Individual' ) !== false ) {
            // set max quantity to 1 if Individual is present for simple products
            $product_quantity = str_replace( 'min="0"', 'min="0" max="1"', $product_quantity );
        } else {
            foreach( $values['addons'] as $value ) {
                if ( 'Family members' == $value['name'] ) {
            // set max quantity to number of family members
            $product_quantity = str_replace( 'min="0"', 'min="0" max="' . $value['value'] . '"', $product_quantity );
                }
            }
        }
    }
    return $product_quantity;
}

/**
 * Show convention icons on product category archives
 */
add_action( 'woocommerce_before_shop_loop_item', 'ghc_show_product_category_conventions', 15 );
function ghc_show_product_category_conventions() {
    if ( is_tax( 'product_cat' ) ) {
        echo '<div class="tax-convention-icon"></div>';
    }
}

/**
 * Modify WooCommerce email styles
 */
add_action( 'woocommerce_email_header', 'tweak_woocommerce_email_header' );
function tweak_woocommerce_email_header( $email_heading ) {
    echo '<style type="text/css">
        div[style*="padding:70px 0 70px 0"] { padding-top: 0; }
        #template_header_image img { height: 75px !important; }
        .highlighted {
            font-size: larger;
            font-weight: bold;
            background-color: yellow;
        }
        #template_header h1, #template_footer, table[style*="background-color:#00456a"], tfoot tr:nth-child(2) { display: none !important; }
    </style>';
}

/**
 * Add special track info to each applicable speaker
 */
add_action( 'gdlr_before_speaker_biography', 'ghc_speaker_list_special_tracks', 8 );
function ghc_speaker_list_special_tracks() {
    if ( is_single() && 'speaker' == get_post_type() ) {
        $special_tracks = wp_get_post_terms( get_the_ID(), 'ghc_special_tracks_taxonomy' );
        $special_tracks_count = count( $special_tracks );

        if ( $special_tracks_count > 0 ) {
            // set up content
            $track_output = '';
            $track_index = 1;
            foreach ( $special_tracks as $special_track ) {
                $track_output .= '<a href="' . get_term_link( $special_track->term_id, 'ghc_special_tracks_taxonomy' ) . '">' . $special_track->name . '</a> track';

                // check for sponsors
                $sponsors = get_field( 'related_sponsors', 'ghc_special_tracks_taxonomy_' . $special_track->term_id );
                if ( $sponsors ) {
                    $sponsor_index = 1;
                    $track_output .= ' (sponsored by ';
                    foreach( $sponsors as $sponsor ) {
                        $track_output .= '<a href="' . get_permalink( $sponsor ) . '">' . get_the_title( $sponsor ) . '</a>';
                        if ( count( $sponsors ) > 2 ) {
                            $track_output .= ', ';
                            if ( count( $sponsors ) == $index ) {
                                $track_output .= ' and ';
                            }
                        } elseif ( count( $sponsors ) == 2 && $sponsor_index != 2 ) {
                            $track_output .= ' and ';
                        }
                        $sponsor_index++;
                    }
                    $track_output .= ')';
                }

                if ( $special_tracks_count > 2 ) {
                    $track_output .= ', ';
                    if ( $track_index == $special_tracks_count ) {
                        $track_output .= ' and ';
                    }
                } elseif ( $special_tracks_count == 2 && $track_index != 2 ) {
                    $track_output .= ' and ';
                }
                $track_index++;
            }

            // output content
            echo sprintf( '<h4 class="gdlr-speaker-biography-title">Special Tracks</h2>
            <p>We are honored to have %1$s participating in this year&rsquo;s %2$s.</p>',
                           get_the_title(),
                           $track_output
                           );
        }
    }
}
