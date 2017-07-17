<?php

defined( 'ABSPATH' ) or die( 'No access allowed' );

/**
 * Flush rewrite rules on (de)activation
 */
function ghc_speakers_activate() {
    ghc_register_cpts();
    flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'ghc_speakers_activate' );
register_deactivation_hook( __FILE__, 'flush_rewrite_rules' );

/**
 * Register CPTs
 */
function ghc_register_cpts() {

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
        'menu_position'       => 33,
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
        'menu_position'       => 38,
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
        'slug'                => 'locations-cpt',
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
        'menu_position'       => 31,
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
        'taxonomies'          => array( 'ghc_speaker_category_taxonomy', 'ghc_conventions_taxonomy', 'ghc_special_tracks_taxonomy' ),
        'hierarchical'        => true,
        'public'              => true,
        'show_ui'             => true,
        'show_in_menu'        => true,
        'menu_position'       => 32,
        'menu_icon'           => 'dashicons-businessman',
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
        'menu_position'       => 34,
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
        'menu_position'       => 35,
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
        'taxonomies'          => array( 'ghc_conventions_taxonomy', 'ghc_special_tracks_taxonomy', 'ghc_workshop_locations_taxonomy', ),
        'hierarchical'        => true,
        'public'              => true,
        'show_ui'             => true,
        'show_in_menu'        => true,
        'menu_position'       => 36,
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

}
// Hook into the 'init' action to register custom post types
add_action( 'init', 'ghc_register_cpts' );

/**
 * Register custom taxonomies
 */
function ghc_register_taxonomies() {
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
    register_taxonomy( 'ghc_conventions_taxonomy', array( 'page', 'post', 'exhibitor', 'hotel', 'location', 'speaker', 'special_event', 'workshop' ), $convention_args );

    $speaker_category_labels = array(
        'name'                       => _x( 'Speaker Categories', 'Taxonomy General Name', 'GHC' ),
        'singular_name'              => _x( 'Speaker Category', 'Taxonomy Singular Name', 'GHC' ),
        'menu_name'                  => __( 'Speaker Categories', 'GHC' ),
        'all_items'                  => __( 'All Speaker Categories', 'GHC' ),
        'parent_item'                => __( 'Parent Speaker Category', 'GHC' ),
        'parent_item_colon'          => __( 'Parent Speaker Category:', 'GHC' ),
        'new_item_name'              => __( 'New Speaker Category Name', 'GHC' ),
        'add_new_item'               => __( 'Add New Speaker Category', 'GHC' ),
        'edit_item'                  => __( 'Edit Speaker Category', 'GHC' ),
        'update_item'                => __( 'Update Speaker Category', 'GHC' ),
        'view_item'                  => __( 'View Speaker Category', 'GHC' ),
        'separate_items_with_commas' => __( 'Separate Speaker Categories with commas', 'GHC' ),
        'add_or_remove_items'        => __( 'Add or remove Speaker Categories', 'GHC' ),
        'choose_from_most_used'      => __( 'Choose from the most used', 'GHC' ),
        'popular_items'              => __( 'Popular Speaker Categories', 'GHC' ),
        'search_items'               => __( 'Search Speaker Categories', 'GHC' ),
        'not_found'                  => __( 'Not Found', 'GHC' ),
    );
    $speaker_category_rewrite = array(
        'slug'                       => 'speakers/type',
        'with_front'                 => true,
        'hierarchical'               => true,
    );
    $speaker_category_args = array(
        'labels'                     => $speaker_category_labels,
        'hierarchical'               => true,
        'public'                     => true,
        'show_ui'                    => true,
        'show_admin_column'          => true,
        'show_in_nav_menus'          => true,
        'show_tagcloud'              => false,
        'rewrite'                    => $speaker_category_rewrite,
    );
    register_taxonomy( 'ghc_speaker_category_taxonomy', array( 'speaker' ), $speaker_category_args );

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
    register_taxonomy( 'ghc_special_tracks_taxonomy', array( 'speaker', 'workshop' ), $track_args );

    $workshop_location_labels = array(
        'name'                       => _x( 'Workshop Locations', 'Taxonomy General Name', 'GHC' ),
        'singular_name'              => _x( 'Workshop Location', 'Taxonomy Singular Name', 'GHC' ),
        'menu_name'                  => __( 'Workshop Locations', 'GHC' ),
        'all_items'                  => __( 'All Workshop Locations', 'GHC' ),
        'parent_item'                => __( 'Parent Workshop Location', 'GHC' ),
        'parent_item_colon'          => __( 'Parent Workshop Location:', 'GHC' ),
        'new_item_name'              => __( 'New Workshop Location Name', 'GHC' ),
        'add_new_item'               => __( 'Add New Workshop Location', 'GHC' ),
        'edit_item'                  => __( 'Edit Workshop Location', 'GHC' ),
        'update_item'                => __( 'Update Workshop Location', 'GHC' ),
        'view_item'                  => __( 'View Workshop Location', 'GHC' ),
        'separate_items_with_commas' => __( 'Separate workshop locations with commas', 'GHC' ),
        'add_or_remove_items'        => __( 'Add or remove workshop locations', 'GHC' ),
        'choose_from_most_used'      => __( 'Choose from the most used', 'GHC' ),
        'popular_items'              => __( 'Popular Workshop Locations', 'GHC' ),
        'search_items'               => __( 'Search Workshop Locations', 'GHC' ),
        'not_found'                  => __( 'Not Found', 'GHC' ),
    );
    $workshop_location_rewrite = array(
        'slug'                       => 'workshop-locations',
        'with_front'                 => true,
        'hierarchical'               => true,
    );
    $workshop_location_args = array(
        'labels'                     => $workshop_location_labels,
        'hierarchical'               => true,
        'public'                     => true,
        'show_ui'                    => true,
        'show_admin_column'          => true,
        'show_in_nav_menus'          => true,
        'show_tagcloud'              => true,
        'rewrite'                    => $workshop_location_rewrite,
    );
    register_taxonomy( 'ghc_workshop_locations_taxonomy', array( 'special_event', 'workshop' ), $workshop_location_args );

}
add_action( 'init', 'ghc_register_taxonomies' );

/**
 * Insert a menu separator
 */
function ghc_admin_menu_separator() {
    $position = 30;
    global $menu;
    $menu[$position] = array(
        0 => '',
        1 => 'read',
        2 => 'separator' . $position,
        3 => '',
        4 => 'wp-menu-separator',
    );
}
add_action( 'admin_menu', 'ghc_admin_menu_separator' );

/**
 * Add sort order header to speakers backend
 * @param  array $columns array of all columns
 * @return array array of modified columns
 */
function ghc_speaker_columns( $columns ) {
    $columns['menu_order'] = 'Order';
    return $columns;
}
add_filter( 'manage_edit-speaker_columns', 'ghc_speaker_columns' );

/**
 * Display sort order on speakers backend
 * @param string  $column  column name
 * @param integer $post_id post ID
 */
function ghc_speaker_column_content( $column, $post_id ) {
    global $post;
    if ( 'menu_order' == $column ) {
        echo $post->menu_order;
    }
}
add_action( 'manage_speaker_posts_custom_column', 'ghc_speaker_column_content', 10, 2 );

/**
 * Make speakers order column header sortable
 * @param  array $columns array of all columns
 * @return array array of modified columns
 */
function ghc_speaker_sortable_columns( $columns ) {
    $columns['menu_order'] = 'menu_order';
    return $columns;
}
add_filter( 'manage_edit-speaker_sortable_columns', 'ghc_speaker_sortable_columns' );

/**
 * Add workshop date/time and speaker headers to workshops backend
 * @param  array $columns array of all columns
 * @return array array of modified columns
 */
function ghc_workshop_columns( $columns ) {
    $columns['date_time'] = 'Date/Time';
    $columns['speaker'] = 'Speaker';
    unset( $columns['date'] );
    return $columns;
}
add_filter( 'manage_edit-workshop_columns', 'ghc_workshop_columns' );

/**
 * Display speaker name on workshops backend
 * @param string  $column  column name
 * @param integer $post_id post ID
 */
function ghc_workshop_column_content( $column, $post_id ) {
    global $post;
    if ( 'date_time' == $column ) {
        echo date( 'n/d/y g:i A', strtotime( get_field( 'date_and_time' ) ) );
    } elseif ( 'related_speaker' == $column ) {
        $workshop_speaker = get_field( 'related_speaker' );
        if ( $workshop_speaker && 1 == count( $workshop_speaker ) ) {
            echo $workshop_speaker[0]->post_title;
        } elseif ( count( $workshop_speaker ) > 1 ) {
            $speaker_string = NULL;
            foreach ( $workshop_speaker as $speaker ) {
                $speaker_string .= $speaker->post_title . ', ';
            }
            echo rtrim( $speaker_string, ', ' );
        } else {
            return;
        }
    }
}
add_action( 'manage_workshop_posts_custom_column', 'ghc_workshop_column_content', 10, 2 );

/**
 * Make workshop speaker name header sortable
 * @param  array $columns array of all columns
 * @return array array of modified columns
 */
function ghc_workshop_sortable_columns( $columns ) {
    $columns['date_time'] = 'date_time';
    $columns['speaker'] = 'speaker';
    return $columns;
}
add_filter( 'manage_edit-workshop_sortable_columns', 'ghc_workshop_sortable_columns' );

/**
 * Sort workshops by speaker name
 * @param object $query WP_Query
 */
function ghc_sort_workshops_admin( $query ) {
    if ( 'workshop' == $query->query['post_type'] && is_admin() && $query->is_main_query() ) {
        if ( 'date_time' == $query->get( 'orderby' ) && 'menu_order title' == $query->get( 'orderby' ) ) {
            $query->set( 'meta_key', 'date_and_time' );
        } elseif ( 'related_speaker' == $query->get( 'orderby' ) ) {
            $query->set( 'meta_key', 'related_speaker' );
        }
    }
}
add_action( 'pre_get_posts', 'ghc_sort_workshops_admin' );

/**
 * Sort speakers and hide non-featured speakers
 * @param  object $query WP_Query
 */
function ghc_speakers_order( $query ) {
    if ( 'speaker' == $query->query['post_type'] ) {
        $query->set( 'orderby', 'menu_order' );
        $query->set( 'order', 'ASC' );

        if ( ! is_admin() && $query->is_main_query() && is_post_type_archive( 'speaker' ) ) {
            $query->set( 'tax_query', array(
                'taxonomy'  => 'ghc_speaker_category_taxonomy',
                'field'     => 'slug',
                'terms'     => 'featured',
            ));
        }
    }
}
add_action( 'pre_get_posts', 'ghc_speakers_order' );

/**
 * Sort exhibitors
 * @param object $query WP_Query
 */
function ghc_modify_exhibitor_archive( $query ) {
    if ( 'exhibitor' == $query->query['post_type'] && ! is_admin() && $query->is_main_query() ) {
        $query->set( 'posts_per_page', '-1' );
        $query->set( 'pagination', 'false' );
        $query->set( 'orderby', 'title' );
        $query->set( 'order', 'ASC' );
    }
}
add_action( 'pre_get_posts', 'ghc_modify_exhibitor_archive' );

/**
 * Sort sponsors
 * @param object $query WP_Query
 */
function ghc_modify_sponsor_archive( $query ) {
    if ( 'sponsor' == $query->query['post_type'] && ! is_admin() && $query->is_main_query() && is_post_type_archive( 'sponsor' ) ) {
        $query->set( 'posts_per_page', -1 );
        $query->set( 'orderby', 'menu_order' );
        $query->set( 'order', 'ASC' );
    }
}
add_action( 'pre_get_posts', 'ghc_modify_sponsor_archive' );

// modify exhibitor archive to show convention icons and site URLs
/**
 * Show convention icons for each exhibitor
 * @param  string $content HTML content
 * @return string modified content
 */
function ghc_exhibitor_archive_icons( $content ) {
    global $post;
    if ( 'exhibitor' == get_post_type( $post->ID ) ) {
        $new_content = '';
        if ( get_field( 'exhibitor_URL', $post->ID ) ) {
            $new_content .= '<p><a href="' . get_field( 'exhibitor_URL', $post->ID ) . '" target="_blank">Visit website&rarr;</a></p>';
        }
        $new_content .= output_convention_icons( $post->ID );
    }
    return $new_content . $content;
}
add_filter( 'the_content', 'ghc_exhibitor_archive_icons' );

/**
 * Change exhibitor URL to be their website URL
 * @param  string  $post_link post permalink
 * @param  object  $post      WP_Post
 * @param  boolean $leavename whether or not to keep the post name
 * @param  boolean $sample    whether or not it’s a sample permalink
 * @return string  modified link
 */
function ghc_exhibitor_post_type_link( $post_link, $post, $leavename, $sample ) {
    if ( 'exhibitor' == get_post_type( $post ) ) {
        $post_link = get_field( 'exhibitor_URL', $post->ID );
    }
    return $post_link;
}
add_filter( 'post_type_link', 'ghc_exhibitor_post_type_link', 10, 4 );

/**
 * Show convention icons on special events archive
 * @param  [[Type]] $content [[Description]]
 * @return [[Type]] [[Description]]
 */
function ghc_special_events_archive_icons( $content ) {
    global $post;
    $new_content = '';
    if ( 'special_event' == get_post_type( $post->ID ) ) {
        $new_content .= output_convention_icons( $post->ID );
    }
    return $new_content . $content;
}
add_filter( 'get_the_excerpt', 'ghc_special_events_archive_icons' );
