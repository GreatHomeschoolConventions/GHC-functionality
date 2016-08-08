<?php
/**
 * Plugin Name: GHC Functionality
 * Plugin URI: https://github.com/macbookandrew/ghc-speakers
 * Description: Add speakers, exhibitors, sponsors, and hotels
 * Version: 1.6
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

// flush rewrite rules on activation/deactivation
function ghc_speakers_activate() {
    flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'ghc_speakers_activate' );

function ghc_speakers_deactivate() {
    flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'ghc_speakers_deactivate' );


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
        'slug'                => 'special_events',
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
        'has_archive'         => 'special_events',
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
        'has_archive'         => 'speakers',
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

    $workshops_labels = array(
        'name'                => _x( 'Workshops', 'Post Type General Name', 'GHC' ),
        'singular_name'       => _x( 'Workshop', 'Post Type Singular Name', 'GHC' ),
        'menu_name'           => __( 'Workshops', 'GHC' ),
        'name_admin_bar'      => __( 'Workshop', 'GHC' ),
        'parent_item_colon'   => __( 'Parent Workshop:', 'GHC' ),
        'all_items'           => __( 'All Workshops', 'GHC' ),
        'add_new_item'        => __( 'Add New Workshop', 'GHC' ),
        'add_new'             => __( 'Add New', 'GHC' ),
        'new_item'            => __( 'New Workshop', 'GHC' ),
        'edit_item'           => __( 'Edit Workshop', 'GHC' ),
        'update_item'         => __( 'Update Workshop', 'GHC' ),
        'view_item'           => __( 'View Workshop', 'GHC' ),
        'search_items'        => __( 'Search Workshop', 'GHC' ),
        'not_found'           => __( 'Not found', 'GHC' ),
        'not_found_in_trash'  => __( 'Not found in Trash', 'GHC' ),
    );
    $workshops_rewrite = array(
        'slug'                => 'workshops',
        'with_front'          => true,
        'pages'               => true,
        'feeds'               => true,
    );
    $workshops_args = array(
        'label'               => __( 'workshop', 'GHC' ),
        'description'         => __( 'Workshops', 'GHC' ),
        'labels'              => $workshops_labels,
        'supports'            => array( 'title', 'editor', 'excerpt', 'thumbnail', 'revisions', 'page-attributes', ),
        'taxonomies'          => array( 'ghc_conventions_taxonomy', 'ghc_special_tracks_taxonomy' ),
        'hierarchical'        => true,
        'public'              => true,
        'show_ui'             => true,
        'show_in_menu'        => true,
        'menu_position'       => 5,
        'menu_icon'           => 'dashicons-welcome-learn-more',
        'show_in_admin_bar'   => true,
        'show_in_nav_menus'   => true,
        'can_export'          => true,
        'has_archive'         => 'workshops',
        'exclude_from_search' => false,
        'publicly_queryable'  => true,
        'rewrite'             => $workshops_rewrite,
        'capability_type'     => 'page',
    );
    register_post_type( 'workshop', $workshops_args );

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

    $type_labels = array(
        'name'                       => _x( 'Speaker Types', 'Taxonomy General Name', 'GHC' ),
        'singular_name'              => _x( 'Speaker Type', 'Taxonomy Singular Name', 'GHC' ),
        'menu_name'                  => __( 'Speaker Types', 'GHC' ),
        'all_items'                  => __( 'All Speaker Types', 'GHC' ),
        'parent_item'                => __( 'Parent Speaker Type', 'GHC' ),
        'parent_item_colon'          => __( 'Parent Speaker Type:', 'GHC' ),
        'new_item_name'              => __( 'New Speaker Type Name', 'GHC' ),
        'add_new_item'               => __( 'Add New Speaker Type', 'GHC' ),
        'edit_item'                  => __( 'Edit Speaker Type', 'GHC' ),
        'update_item'                => __( 'Update Speaker Type', 'GHC' ),
        'view_item'                  => __( 'View Speaker Type', 'GHC' ),
        'separate_items_with_commas' => __( 'Separate speaker types with commas', 'GHC' ),
        'add_or_remove_items'        => __( 'Add or remove speaker types', 'GHC' ),
        'choose_from_most_used'      => __( 'Choose from the most used', 'GHC' ),
        'popular_items'              => __( 'Popular Speaker Types', 'GHC' ),
        'search_items'               => __( 'Search Speaker Types', 'GHC' ),
        'not_found'                  => __( 'Not Found', 'GHC' ),
    );
    $type_args = array(
        'labels'                     => $type_labels,
        'hierarchical'               => true,
        'public'                     => true,
        'show_ui'                    => true,
        'show_admin_column'          => true,
        'show_in_nav_menus'          => true,
        'show_tagcloud'              => true,
        'rewrite'                    => false,
    );
    register_taxonomy( 'ghc_speakers_taxonomy', array( 'speaker' ), $type_args );

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
    $track_args = array(
        'labels'                     => $track_labels,
        'hierarchical'               => true,
        'public'                     => true,
        'show_ui'                    => true,
        'show_admin_column'          => true,
        'show_in_nav_menus'          => true,
        'show_tagcloud'              => true,
        'rewrite'                    => true,
    );
    register_taxonomy( 'ghc_special_tracks_taxonomy', array( 'speaker', 'workshop' ), $track_args );

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
    register_taxonomy( 'ghc_conventions_taxonomy', array( 'location', 'speaker', 'exhibitor', 'hotel', 'workshop', 'special_event' ), $convention_args );

}
// Hook into the 'init' action to register custom taxonomy
add_action( 'init', 'ghc_taxonomies', 0 );

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
                <?php echo get_field( 'city' ) . ', ' . get_field( 'state' ) . ': ' . date( 'F j', get_field( 'begin_date' ) ) . '&ndash;' . date( 'j, Y', get_fieeld( 'end_date' ) ); ?></p>
        <?php }
    }
    wp_reset_postdata();
}

function convention_info() {
    global $conventions;
    $conventions = array();

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
            $locations_query->the_post();

            $convention_info = array(
                'ID'    => get_the_ID(),
                'title' => get_the_title(),
            );
            $convention_key = strtolower( get_field( 'convention_short_name' ) );
            $conventions[$convention_key] = array_merge( $convention_info, get_post_meta( get_the_ID() ) );
        }
    }
    wp_reset_postdata();

    // convention abbreviations
    global $convention_abbreviations;
    foreach ( $conventions as $key => $values ) {
        $convention_abbreviations[$key] = strtolower( get_field( 'convention_name', $values['ID'] ) );
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
