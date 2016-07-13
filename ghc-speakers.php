<?php
/**
 * Plugin Name: GHC Functionality
 * Plugin URI: https://github.com/macbookandrew/ghc-speakers
 * Description: Add speakers, exhibitors, sponsors, and hotels
 * Version: 1.5.2
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
        'taxonomies'          => array( 'ghc_speakers_taxonomy', 'ghc_conventions_taxonomy' ),
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
        'taxonomies'          => array( 'ghc_conventions_taxonomy' ),
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

// add metabox for featured/general speakers
add_action( 'add_meta_boxes', 'add_featured_speaker_metabox' );
function add_featured_speaker_metabox() {
    add_meta_box( 'featured-speaker', 'Featured Speaker', 'print_featured_speaker_metabox', 'speaker', 'side' );
}

// print metabox for featured/general speakers
function print_featured_speaker_metabox( $post ) {
    // add nonce field to check for later
    wp_nonce_field( 'featured_speaker_meta', 'featured_speaker_meta_nonce' );

    // get meta from database, if set
    $custom_meta = get_post_custom( $post->ID );
    $featured_speaker = isset( $custom_meta['featured_speaker'] ) ? esc_attr( $custom_meta['featured_speaker'][0] ) : '';

    echo '<p>Is this a featured speaker? <strong>Yes</strong> means they will be shown in the archive; <strong>No</strong> means they will be hidden from the archive.</p>
    <input type="radio" name="featured_speaker" id="featured_speaker_yes" value="yes" ';
    if ( ( $featured_speaker == 'yes' ) || ( $featured_speaker == NULL ) ) { echo 'checked="checked" '; }
    echo '/>
    <label for="featured_speaker_yes">Yes</label>&nbsp;
    <input type="radio" name="featured_speaker" id="featured_speaker_no" value="no" ';
    if ( isset( $featured_speaker ) && ( $featured_speaker == 'no' ) ) { echo 'checked="checked" '; }
    echo '/><label for="featured_speaker_no">No</label>';
}

// save metabox featured/general speaker data
add_action( 'save_post', 'save_featured_speaker_metadata' );
function save_featured_speaker_metadata( $post_id ) {
    // bail if autosave
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;

    // check for valid nonces
    if ( ! isset( $_POST['featured_speaker_meta_nonce'] ) || ! wp_verify_nonce( $_POST['featured_speaker_meta_nonce'], 'featured_speaker_meta' ) ) return;

    // check the user's permissions
    if ( ! current_user_can( 'edit_posts', $post_id ) ) return;

    // sanitize user input
    $featured_speaker_sanitized = sanitize_text_field( $_POST['featured_speaker'] );

    // update the meta fields in database
    if ( isset( $_POST['featured_speaker'] ) ) update_post_meta( $post_id, 'featured_speaker', $featured_speaker_sanitized );
}

// add metabox for exhibitor URL
add_action( 'add_meta_boxes', 'add_url_metabox' );
function add_url_metabox() {
    add_meta_box( 'exhibitor-URL', 'Exhibitor website', 'print_url_metabox', 'exhibitor' );
}

// print metabox for exhibitor URL
function print_url_metabox( $post ) {
    // add nonce field to check for later
    wp_nonce_field( 'url_meta', 'url_meta_nonce' );

    // get meta from database, if set
    $custom_meta = get_post_custom( $post->ID );
    $exhibitor_URL = isset( $custom_meta['exhibitor_URL'] ) ? esc_attr( $custom_meta['exhibitor_URL'][0] ) : '';

    echo '<label for="exhibitor_URL">Enter the exhibitor&rsquo;s website:</label><br/>
    <input type="url" name="exhibitor_URL" size="100" placeholder="http://"';
    if ( isset( $exhibitor_URL ) ) { echo ' value="' . $exhibitor_URL . '"'; }
    echo '>';
}

// save metabox exhibitor URL data
add_action( 'save_post', 'save_url_metadata' );
function save_url_metadata( $post_id ) {
    // bail if autosave
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;

    // check for valid nonces
    if ( ! isset( $_POST['url_meta_nonce'] ) || ! wp_verify_nonce( $_POST['url_meta_nonce'], 'url_meta' ) ) return;

    // check the user's permissions
    if ( ! current_user_can( 'edit_posts', $post_id ) ) return;

    // sanitize user input
    $exhibitor_URL_sanitized = sanitize_text_field( $_POST['exhibitor_URL'] );

    // update the meta fields in database
    if ( isset( $_POST['exhibitor_URL'] ) ) update_post_meta( $post_id, 'exhibitor_URL', $exhibitor_URL_sanitized );
}
add_action( 'init', 'ghc_taxonomies', 0 );

// add exhibitor backend JS
add_action( 'admin_enqueue_scripts', 'include_exhibitor_backend_js' );
function include_exhibitor_backend_js() {
    global $post_type;
    if ( 'exhibitor' == $post_type ) {
        wp_enqueue_script( 'exhibitor-backend', plugins_url( 'js/exhibitor-backend.min.js', __FILE__ ), array( 'jquery' ), NULL, true );
    }
}

// add metabox for hotels
add_action( 'add_meta_boxes', 'add_hotel_metabox' );
function add_hotel_metabox() {
    add_meta_box( 'hotel-info', 'Hotel Information', 'print_hotel_metaboxes', 'hotel', 'normal', 'high' );
}

// print metaboxes for hotels
function print_hotel_metaboxes( $post ) {
    // add nonce field to check for later
    wp_nonce_field( 'hotel_meta', 'hotel_meta_nonce' );

    // get meta from database, if set
    $custom_hotel_meta = get_post_custom( $post->ID );
    $sold_out = isset( $custom_hotel_meta['sold_out'] ) ? esc_attr( $custom_hotel_meta['sold_out'][0] ) : '';
    $discount_rate = isset( $custom_hotel_meta['discount_rate'] ) ? esc_attr( $custom_hotel_meta['discount_rate'][0] ) : '';
    $discount_rate_details = isset( $custom_hotel_meta['discount_rate_details'] ) ? esc_attr( $custom_hotel_meta['discount_rate_details'][0] ) : '';
    $discount_rate2 = isset( $custom_hotel_meta['discount_rate2'] ) ? esc_attr( $custom_hotel_meta['discount_rate2'][0] ) : '';
    $discount_rate2_details = isset( $custom_hotel_meta['discount_rate2_details'] ) ? esc_attr( $custom_hotel_meta['discount_rate2_details'][0] ) : '';
    $discount_rate3 = isset( $custom_hotel_meta['discount_rate3'] ) ? esc_attr( $custom_hotel_meta['discount_rate3'][0] ) : '';
    $discount_rate3_details = isset( $custom_hotel_meta['discount_rate3_details'] ) ? esc_attr( $custom_hotel_meta['discount_rate3_details'][0] ) : '';
    $discount_valid_date = isset( $custom_hotel_meta['discount_valid_date'] ) ? esc_attr( $custom_hotel_meta['discount_valid_date'][0] ) : '';
    $discount_group_code = isset( $custom_hotel_meta['discount_group_code'] ) ? esc_attr( $custom_hotel_meta['discount_group_code'][0] ) : '';
    $hotel_URL = isset( $custom_hotel_meta['hotel_URL'] ) ? esc_attr( $custom_hotel_meta['hotel_URL'][0] ) : '';
    $hotel_phone = isset( $custom_hotel_meta['hotel_phone'] ) ? esc_attr( $custom_hotel_meta['hotel_phone'][0] ) : '';
    $hotel_address = isset( $custom_hotel_meta['hotel_address'] ) ? esc_attr( $custom_hotel_meta['hotel_address'][0] ) : '';
    $hotel_gallery = isset( $custom_hotel_meta['hotel_gallery'] ) ? esc_attr( $custom_hotel_meta['hotel_gallery'][0] ) : '';

    echo '<input type="checkbox" name="sold_out" id="sold_out"';
    if ( $sold_out == 'true' ) { echo ' checked="checked"'; }
    echo '/><label for="sold_out">Sold Out</label><br/>';

    echo '<label for="discount_rate">Discounted Rate and Details:</label><br/>
    $<input type="number" name="discount_rate" step="0.00" placeholder="0.00"';
    if ( isset( $discount_rate ) ) { echo ' value="' . $discount_rate . '"'; }
    echo '>
    <input type="text" name="discount_rate_details" placeholder="2 Queen or 2 Double beds, etc." size="75"';
    if ( isset( $discount_rate_details ) ) { echo ' value="' . $discount_rate_details . '"'; }
    echo '><br/>';

    echo '<label for="discount_rate2">Second Discounted Rate and Details:</label><br/>
    $<input type="number" name="discount_rate2" step="0.00" placeholder="0.00"';
    if ( isset( $discount_rate2 ) ) { echo ' value="' . $discount_rate2 . '"'; }
    echo '>
    <input type="text" name="discount_rate2_details" placeholder="2 Queen or 2 Double beds, etc." size="75"';
    if ( isset( $discount_rate2_details ) ) { echo ' value="' . $discount_rate2_details . '"'; }
    echo '><br/>';

    echo '<label for="discount_rate3">Third Discounted Rate and Details:</label><br/>
    $<input type="number" name="discount_rate3" step="0.00" placeholder="0.00"';
    if ( isset( $discount_rate3 ) ) { echo ' value="' . $discount_rate3 . '"'; }
    echo '>
    <input type="text" name="discount_rate3_details" placeholder="2 Queen or 2 Double beds, etc." size="75"';
    if ( isset( $discount_rate3_details ) ) { echo ' value="' . $discount_rate3_details . '"'; }
    echo '><br/>';

    echo '<label for="discount_valid_date">Discount Until Date:</label><br/>
    <input type="date" name="discount_valid_date" placeholder="2016-02-01"';
    if ( isset( $discount_valid_date ) ) { echo ' value="' . $discount_valid_date . '"'; }
    echo '><br/>';

    echo '<label for="discount_group_code">Group Code:</label><br/>
    <input type="text" name="discount_group_code" placeholder="ABC123"';
    if ( isset( $discount_group_code ) ) { echo ' value="' . $discount_group_code . '"'; }
    echo '><br/>';

    echo '<label for="hotel_URL">Website:</label><br/>
    <input type="url" name="hotel_URL" size="100" placeholder="http://"';
    if ( isset( $hotel_URL ) ) { echo ' value="' . $hotel_URL . '"'; }
    echo '><br/>';

    echo '<label for="hotel_phone">Phone:</label><br/>
    <input type="tel" name="hotel_phone" size="100" placeholder="234-567-8901"';
    if ( isset( $hotel_phone ) ) { echo ' value="' . $hotel_phone . '"'; }
    echo '><br/>';

    echo '<label for="hotel_address">Address, City, State, Zip:</label><br/>
    <input type="text" name="hotel_address" size="100" placeholder="123 Anystreet, Schenectady, NY, 12345"';
    if ( isset( $hotel_address ) ) { echo ' value="' . $hotel_address . '"'; }
    echo '><br/>';

    echo '<label for="hotel_gallery">Photo Gallery:</label><br/>
    <select name="hotel_gallery" id="hotel_gallery">
        <option>- None -</option>';
        $hotel_gallery_query_args = array(
            'post_type'              => array( 'gdl-gallery' ),
            'posts_per_page'         => '-1',
        );
        $hotel_gallery_query = new WP_Query( $hotel_gallery_query_args );
        if ( $hotel_gallery_query->have_posts() ) {
            while ( $hotel_gallery_query->have_posts() ) {
                $hotel_gallery_query->the_post();
                echo '<option value="' . get_the_title() . '"';
                if ( str_replace( '&amp;', '&', $hotel_gallery ) == str_replace( '&#038;', '&', get_the_title() ) ) { echo ' selected="selected"'; }
                echo '>' . get_the_title() . '</option>'."\n";
            }
        }
    echo '</select>';
}

// save metaboxes for hotels
add_action( 'save_post', 'save_hotel_metadata' );
function save_hotel_metadata( $post_id ) {
    // bail if autosave
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;

    // check for valid nonces
    if ( ! isset( $_POST['hotel_meta_nonce'] ) || ! wp_verify_nonce( $_POST['hotel_meta_nonce'], 'hotel_meta' ) ) return;

    // check the user's permissions
    if ( ! current_user_can( 'edit_posts', $post_id ) ) return;

    // sanitize user input
    $discount_rate_sanitized = sanitize_text_field( $_POST['discount_rate'] );
    $discount_rate_details_sanitized = sanitize_text_field( $_POST['discount_rate_details'] );
    $discount_rate2_sanitized = sanitize_text_field( $_POST['discount_rate2'] );
    $discount_rate2_details_sanitized = sanitize_text_field( $_POST['discount_rate2_details'] );
    $discount_rate3_sanitized = sanitize_text_field( $_POST['discount_rate3'] );
    $discount_rate3_details_sanitized = sanitize_text_field( $_POST['discount_rate3_details'] );
    $discount_group_code_sanitized = sanitize_text_field( $_POST['discount_group_code'] );
    $discount_valid_date_sanitized = sanitize_text_field( $_POST['discount_valid_date'] );
    $hotel_URL_sanitized = esc_url( $_POST['hotel_URL'] );
    $hotel_phone_sanitized = sanitize_text_field( $_POST['hotel_phone'] );
    $hotel_address_sanitized = sanitize_text_field( $_POST['hotel_address'] );
    $hotel_gallery_sanitized = sanitize_text_field( $_POST['hotel_gallery'] );

    // update the meta fields in database
    if ( isset( $_POST['sold_out'] ) ) {
        update_post_meta( $post_id, 'sold_out', 'true' );
    } else {
        update_post_meta( $post_id, 'sold_out', 'false' );
    }
    if ( isset( $_POST['discount_rate'] ) ) update_post_meta( $post_id, 'discount_rate', $discount_rate_sanitized );
    if ( isset( $_POST['discount_rate_details'] ) ) update_post_meta( $post_id, 'discount_rate_details', $discount_rate_details_sanitized );
    if ( isset( $_POST['discount_rate2'] ) ) update_post_meta( $post_id, 'discount_rate2', $discount_rate2_sanitized );
    if ( isset( $_POST['discount_rate2_details'] ) ) update_post_meta( $post_id, 'discount_rate2_details', $discount_rate2_details_sanitized );
    if ( isset( $_POST['discount_rate3'] ) ) update_post_meta( $post_id, 'discount_rate3', $discount_rate3_sanitized );
    if ( isset( $_POST['discount_rate3_details'] ) ) update_post_meta( $post_id, 'discount_rate3_details', $discount_rate3_details_sanitized );
    if ( isset( $_POST['discount_group_code'] ) ) update_post_meta( $post_id, 'discount_group_code', $discount_group_code_sanitized );
    if ( isset( $_POST['discount_valid_date'] ) ) update_post_meta( $post_id, 'discount_valid_date', $discount_valid_date_sanitized );
    if ( isset( $_POST['hotel_URL'] ) ) update_post_meta( $post_id, 'hotel_URL', $hotel_URL_sanitized );
    if ( isset( $_POST['hotel_phone'] ) ) update_post_meta( $post_id, 'hotel_phone', $hotel_phone_sanitized );
    if ( isset( $_POST['hotel_address'] ) ) update_post_meta( $post_id, 'hotel_address', $hotel_address_sanitized );
    if ( isset( $_POST['hotel_gallery'] ) ) update_post_meta( $post_id, 'hotel_gallery', $hotel_gallery_sanitized );
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
