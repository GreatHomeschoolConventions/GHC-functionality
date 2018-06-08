<?php
/**
 * GHC custom post types
 *
 * @package GHC_Functionality_Plugin
 */

if ( ! function_exists( 'add_filter' ) ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit();
}

/**
 * Content-related functions
 */
class GHC_Post_Types extends GHC_Base {

	/**
	 * Kick things off.
	 */
	public function __construct() {
		// Post types.
		add_action( 'init', array( $this, 'register_post_types' ) );
		add_action( 'init', array( $this, 'register_taxonomies' ) );

		// Activation/deactivation.
		register_activation_hook( GHC_PLUGIN_FILE, array( $this, 'activate' ) );
		register_deactivation_hook( GHC_PLUGIN_FILE, 'flush_rewrite_rules' );

		// Frontend.
		add_filter( 'get_the_archive_title', array( $this, 'cpt_archive_titles' ) );
		add_filter( 'get_the_archive_description', array( $this, 'cpt_archive_intro' ) );
		add_action( 'pre_get_posts', array( $this, 'speakers_order' ) );
		add_action( 'pre_get_posts', array( $this, 'modify_exhibitor_archive' ) );
		add_action( 'pre_get_posts', array( $this, 'modify_special_track_tax' ) );
		add_action( 'pre_get_posts', array( $this, 'modify_sponsor_archive' ) );

		// Admin.
		add_filter( 'manage_edit-speaker_columns', array( $this, 'speaker_columns' ) );
		add_action( 'manage_speaker_posts_custom_column', array( $this, 'speaker_column_content' ), 10, 2 );
		add_filter( 'manage_edit-speaker_sortable_columns', array( $this, 'speaker_sortable_columns' ) );
		add_filter( 'manage_edit-workshop_columns', array( $this, 'workshop_columns' ) );
		add_action( 'manage_workshop_posts_custom_column', array( $this, 'workshop_column_content' ), 10, 2 );
		add_filter( 'manage_edit-workshop_sortable_columns', array( $this, 'workshop_sortable_columns' ) );
		add_action( 'pre_get_posts', array( $this, 'sort_workshops_admin' ) );
		add_action( 'acf/save_post', array( $this, 'add_speaker_workshop_meta' ), 12 );
	}

	/**
	 * Flush rewrite rules on activation.
	 */
	public function activate() {
		$this->register_post_types();
		flush_rewrite_rules();
	}

	/**
	 * Register custom post types.
	 */
	public function register_post_types() {

		$exhibitors_labels  = array(
			'name'               => _x( 'Exhibitors', 'Post Type General Name', 'GHC' ),
			'singular_name'      => _x( 'Exhibitor', 'Post Type Singular Name', 'GHC' ),
			'menu_name'          => __( 'Exhibitors', 'GHC' ),
			'name_admin_bar'     => __( 'Exhibitor', 'GHC' ),
			'parent_item_colon'  => __( 'Parent Exhibitor:', 'GHC' ),
			'all_items'          => __( 'All Exhibitors', 'GHC' ),
			'add_new_item'       => __( 'Add New Exhibitor', 'GHC' ),
			'add_new'            => __( 'Add New', 'GHC' ),
			'new_item'           => __( 'New Exhibitor', 'GHC' ),
			'edit_item'          => __( 'Edit Exhibitor', 'GHC' ),
			'update_item'        => __( 'Update Exhibitor', 'GHC' ),
			'view_item'          => __( 'View Exhibitor', 'GHC' ),
			'search_items'       => __( 'Search Exhibitor', 'GHC' ),
			'not_found'          => __( 'Not found', 'GHC' ),
			'not_found_in_trash' => __( 'Not found in Trash', 'GHC' ),
		);
		$exhibitors_rewrite = array(
			'slug'       => 'exhibitor',
			'with_front' => true,
			'pages'      => true,
			'feeds'      => true,
		);
		$exhibitors_args    = array(
			'label'               => __( 'exhibitor', 'GHC' ),
			'description'         => __( 'Exhibitors', 'GHC' ),
			'labels'              => $exhibitors_labels,
			'supports'            => array( 'title', 'revisions', 'page-attributes' ),
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

		$hotels_labels  = array(
			'name'               => _x( 'Hotels', 'Post Type General Name', 'GHC' ),
			'singular_name'      => _x( 'Hotel', 'Post Type Singular Name', 'GHC' ),
			'menu_name'          => __( 'Hotels', 'GHC' ),
			'name_admin_bar'     => __( 'Hotel', 'GHC' ),
			'parent_item_colon'  => __( 'Parent Hotel:', 'GHC' ),
			'all_items'          => __( 'All Hotels', 'GHC' ),
			'add_new_item'       => __( 'Add New Hotel', 'GHC' ),
			'add_new'            => __( 'Add New', 'GHC' ),
			'new_item'           => __( 'New Hotel', 'GHC' ),
			'edit_item'          => __( 'Edit Hotel', 'GHC' ),
			'update_item'        => __( 'Update Hotel', 'GHC' ),
			'view_item'          => __( 'View Hotel', 'GHC' ),
			'search_items'       => __( 'Search Hotels', 'GHC' ),
			'not_found'          => __( 'Not found', 'GHC' ),
			'not_found_in_trash' => __( 'Not found in Trash', 'GHC' ),
		);
		$hotels_rewrite = array(
			'slug'       => 'hotel',
			'with_front' => true,
			'pages'      => true,
			'feeds'      => true,
		);
		$hotels_args    = array(
			'label'               => __( 'Hotel', 'GHC' ),
			'description'         => __( 'Hotels', 'GHC' ),
			'labels'              => $hotels_labels,
			'supports'            => array( 'title', 'editor', 'author', 'thumbnail', 'revisions', 'custom-fields', 'page-attributes' ),
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
			'has_archive'         => 'hotels-all',
			'exclude_from_search' => false,
			'publicly_queryable'  => true,
			'rewrite'             => $hotels_rewrite,
			'capability_type'     => 'page',
		);
		register_post_type( 'hotel', $hotels_args );

		$locations_labels  = array(
			'name'               => _x( 'Locations', 'Post Type General Name', 'GHC' ),
			'singular_name'      => _x( 'Location', 'Post Type Singular Name', 'GHC' ),
			'menu_name'          => __( 'Locations', 'GHC' ),
			'name_admin_bar'     => __( 'Location', 'GHC' ),
			'parent_item_colon'  => __( 'Parent Location:', 'GHC' ),
			'all_items'          => __( 'All Locations', 'GHC' ),
			'add_new_item'       => __( 'Add New Location', 'GHC' ),
			'add_new'            => __( 'Add New', 'GHC' ),
			'new_item'           => __( 'New Location', 'GHC' ),
			'edit_item'          => __( 'Edit Location', 'GHC' ),
			'update_item'        => __( 'Update Location', 'GHC' ),
			'view_item'          => __( 'View Location', 'GHC' ),
			'search_items'       => __( 'Search Location', 'GHC' ),
			'not_found'          => __( 'Not found', 'GHC' ),
			'not_found_in_trash' => __( 'Not found in Trash', 'GHC' ),
		);
		$locations_rewrite = array(
			'slug'       => 'locations',
			'with_front' => true,
			'pages'      => true,
			'feeds'      => true,
		);
		$locations_args    = array(
			'label'               => __( 'location', 'GHC' ),
			'description'         => __( 'Locations', 'GHC' ),
			'labels'              => $locations_labels,
			'supports'            => array( 'title', 'editor', 'author', 'excerpt', 'thumbnail', 'revisions', 'page-attributes' ),
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

		$speakers_labels  = array(
			'name'               => _x( 'Speakers', 'Post Type General Name', 'GHC' ),
			'singular_name'      => _x( 'Speaker', 'Post Type Singular Name', 'GHC' ),
			'menu_name'          => __( 'Speakers', 'GHC' ),
			'name_admin_bar'     => __( 'Speaker', 'GHC' ),
			'parent_item_colon'  => __( 'Parent Speaker:', 'GHC' ),
			'all_items'          => __( 'All Speakers', 'GHC' ),
			'add_new_item'       => __( 'Add New Speaker', 'GHC' ),
			'add_new'            => __( 'Add New', 'GHC' ),
			'new_item'           => __( 'New Speaker', 'GHC' ),
			'edit_item'          => __( 'Edit Speaker', 'GHC' ),
			'update_item'        => __( 'Update Speaker', 'GHC' ),
			'view_item'          => __( 'View Speaker', 'GHC' ),
			'search_items'       => __( 'Search Speaker', 'GHC' ),
			'not_found'          => __( 'Not found', 'GHC' ),
			'not_found_in_trash' => __( 'Not found in Trash', 'GHC' ),
		);
		$speakers_rewrite = array(
			'slug'       => 'speakers',
			'with_front' => true,
			'pages'      => true,
			'feeds'      => true,
		);
		$speakers_args    = array(
			'label'               => __( 'speaker', 'GHC' ),
			'description'         => __( 'Speakers', 'GHC' ),
			'labels'              => $speakers_labels,
			'supports'            => array( 'title', 'editor', 'excerpt', 'thumbnail', 'revisions', 'page-attributes' ),
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

		$special_events_labels  = array(
			'name'               => _x( 'Special Events', 'Post Type General Name', 'GHC' ),
			'singular_name'      => _x( 'Special Event', 'Post Type Singular Name', 'GHC' ),
			'menu_name'          => __( 'Special Events', 'GHC' ),
			'name_admin_bar'     => __( 'Special Event', 'GHC' ),
			'parent_item_colon'  => __( 'Parent Special Event:', 'GHC' ),
			'all_items'          => __( 'All Special Events', 'GHC' ),
			'add_new_item'       => __( 'Add New Special Event', 'GHC' ),
			'add_new'            => __( 'Add New', 'GHC' ),
			'new_item'           => __( 'New Special Event', 'GHC' ),
			'edit_item'          => __( 'Edit Special Event', 'GHC' ),
			'update_item'        => __( 'Update Special Event', 'GHC' ),
			'view_item'          => __( 'View Special Event', 'GHC' ),
			'search_items'       => __( 'Search Special Event', 'GHC' ),
			'not_found'          => __( 'Not found', 'GHC' ),
			'not_found_in_trash' => __( 'Not found in Trash', 'GHC' ),
		);
		$special_events_rewrite = array(
			'slug'       => 'special-events',
			'with_front' => true,
			'pages'      => true,
			'feeds'      => true,
		);
		$special_events_args    = array(
			'label'               => __( 'special_event', 'GHC' ),
			'description'         => __( 'Special Events', 'GHC' ),
			'labels'              => $special_events_labels,
			'supports'            => array( 'title', 'editor', 'excerpt', 'thumbnail', 'revisions', 'page-attributes' ),
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

		$sponsors_labels  = array(
			'name'               => _x( 'Sponsors', 'Post Type General Name', 'GHC' ),
			'singular_name'      => _x( 'Sponsor', 'Post Type Singular Name', 'GHC' ),
			'menu_name'          => __( 'Sponsors', 'GHC' ),
			'name_admin_bar'     => __( 'Sponsor', 'GHC' ),
			'parent_item_colon'  => __( 'Parent Sponsor:', 'GHC' ),
			'all_items'          => __( 'All Sponsors', 'GHC' ),
			'add_new_item'       => __( 'Add New Sponsor', 'GHC' ),
			'add_new'            => __( 'Add New', 'GHC' ),
			'new_item'           => __( 'New Sponsor', 'GHC' ),
			'edit_item'          => __( 'Edit Sponsor', 'GHC' ),
			'update_item'        => __( 'Update Sponsor', 'GHC' ),
			'view_item'          => __( 'View Sponsor', 'GHC' ),
			'search_items'       => __( 'Search Sponsor', 'GHC' ),
			'not_found'          => __( 'Not found', 'GHC' ),
			'not_found_in_trash' => __( 'Not found in Trash', 'GHC' ),
		);
		$sponsors_rewrite = array(
			'slug'       => 'sponsors',
			'with_front' => true,
			'pages'      => true,
			'feeds'      => true,
		);
		$sponsors_args    = array(
			'label'               => __( 'sponsor', 'GHC' ),
			'description'         => __( 'Sponsors', 'GHC' ),
			'labels'              => $sponsors_labels,
			'supports'            => array( 'title', 'editor', 'excerpt', 'thumbnail', 'revisions', 'page-attributes' ),
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

		$workshops_labels  = array(
			'name'               => _x( 'Workshops', 'Post Type General Name', 'GHC' ),
			'singular_name'      => _x( 'Workshop', 'Post Type Singular Name', 'GHC' ),
			'menu_name'          => __( 'Workshops', 'GHC' ),
			'name_admin_bar'     => __( 'Workshop', 'GHC' ),
			'parent_item_colon'  => __( 'Parent Workshop:', 'GHC' ),
			'all_items'          => __( 'All Workshops', 'GHC' ),
			'add_new_item'       => __( 'Add New Workshop', 'GHC' ),
			'add_new'            => __( 'Add New', 'GHC' ),
			'new_item'           => __( 'New Workshop', 'GHC' ),
			'edit_item'          => __( 'Edit Workshop', 'GHC' ),
			'update_item'        => __( 'Update Workshop', 'GHC' ),
			'view_item'          => __( 'View Workshop', 'GHC' ),
			'search_items'       => __( 'Search Workshop', 'GHC' ),
			'not_found'          => __( 'Not found', 'GHC' ),
			'not_found_in_trash' => __( 'Not found in Trash', 'GHC' ),
		);
		$workshops_rewrite = array(
			'slug'       => 'workshops',
			'with_front' => true,
			'pages'      => true,
			'feeds'      => true,
		);
		$workshops_args    = array(
			'label'               => __( 'workshop', 'GHC' ),
			'description'         => __( 'Workshops', 'GHC' ),
			'labels'              => $workshops_labels,
			'supports'            => array( 'title', 'editor', 'excerpt', 'thumbnail', 'revisions', 'page-attributes' ),
			'taxonomies'          => array( 'ghc_conventions_taxonomy', 'ghc_special_tracks_taxonomy', 'ghc_workshop_locations_taxonomy', 'post_tag' ),
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

		register_taxonomy_for_object_type( 'post_tag', 'workshop' );
	}

	/**
	 * Register custom taxonomies.
	 */
	public function register_taxonomies() {
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
		$convention_args   = array(
			'labels'            => $convention_labels,
			'hierarchical'      => true,
			'public'            => true,
			'show_ui'           => true,
			'show_admin_column' => true,
			'show_in_nav_menus' => true,
			'show_tagcloud'     => true,
			'rewrite'           => false,
		);
		register_taxonomy( 'ghc_conventions_taxonomy', array( 'page', 'post', 'exhibitor', 'hotel', 'location', 'speaker', 'special_event', 'workshop' ), $convention_args );

		$speaker_category_labels  = array(
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
			'slug'         => 'speakers/type',
			'with_front'   => true,
			'hierarchical' => true,
		);
		$speaker_category_args    = array(
			'labels'            => $speaker_category_labels,
			'hierarchical'      => true,
			'public'            => true,
			'show_ui'           => true,
			'show_admin_column' => true,
			'show_in_nav_menus' => true,
			'show_tagcloud'     => false,
			'rewrite'           => $speaker_category_rewrite,
		);
		register_taxonomy( 'ghc_speaker_category_taxonomy', array( 'speaker' ), $speaker_category_args );

		$track_labels  = array(
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
			'slug'         => 'special-tracks',
			'with_front'   => true,
			'hierarchical' => true,
		);
		$track_args    = array(
			'labels'            => $track_labels,
			'hierarchical'      => true,
			'public'            => true,
			'show_ui'           => true,
			'show_admin_column' => true,
			'show_in_nav_menus' => true,
			'show_tagcloud'     => true,
			'rewrite'           => $track_rewrite,
		);
		register_taxonomy( 'ghc_special_tracks_taxonomy', array( 'speaker', 'workshop' ), $track_args );

		$workshop_location_labels  = array(
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
			'slug'         => 'workshop-locations',
			'with_front'   => true,
			'hierarchical' => true,
		);
		$workshop_location_args    = array(
			'labels'            => $workshop_location_labels,
			'hierarchical'      => true,
			'public'            => true,
			'show_ui'           => true,
			'show_admin_column' => true,
			'show_in_nav_menus' => true,
			'show_tagcloud'     => true,
			'rewrite'           => $workshop_location_rewrite,
		);
		register_taxonomy( 'ghc_workshop_locations_taxonomy', array( 'special_event', 'workshop' ), $workshop_location_args );

	}

	/**
	 * Remove “Archives: ” from archive titles.
	 *
	 * @param  string $title Archive title.
	 *
	 * @return string modified archive title.
	 */
	public function cpt_archive_titles( string $title ) : string {
		return str_replace( 'Archives: ', '', $title );
	}

	/**
	 * Add CPT descriptions at top of archive pages
	 *
	 * @param  string $description Default description text.
	 *
	 * @return string description with custom message prepended.
	 */
	public function cpt_archive_intro( string $description ) : string {
		foreach ( get_field( 'archive_descriptions', 'option' ) as $cpt_message ) {
			if ( is_post_type_archive( $cpt_message['post_type'] ) ) {
				$description = apply_filters( 'the_content', $cpt_message['message'] );
			}
		}

		return $description;
	}

	/**
	 * Add sort order header to speakers backend
	 *
	 * @param  array $columns Array of all columns.
	 *
	 * @return array array of modified columns.
	 */
	public function speaker_columns( array $columns ) : array {
		$columns = array_merge(
			array_slice( $columns, 0, 1, true ),
			array( 'thumbnail' => 'Thumbnail' ),
			array_slice( $columns, 1, count( $column ) - 1, true ),
			array( 'menu_order' => 'Order' )
		);
		return $columns;
	}

	/**
	 * Display sort order on speakers backend
	 *
	 * @param string  $column  Column name.
	 * @param int $post_id Post ID.
	 *
	 * @return  string Content for custom column.
	 */
	public function speaker_column_content( string $column, int $post_id ) : string {
		global $post;
		if ( 'menu_order' === $column ) {
			echo esc_attr( $post->menu_order );
		} elseif ( 'thumbnail' === $column ) {
			the_post_thumbnail( array( 60, 60 ) );
		}
	}

	/**
	 * Make speakers order column header sortable
	 *
	 * @param  array $columns Array of all columns.
	 *
	 * @return array array of modified columns.
	 */
	public function speaker_sortable_columns( array $columns ) : array {
		$columns['menu_order'] = 'menu_order';
		return $columns;
	}

	/**
	 * Add workshop date/time and speaker headers to workshops backend.
	 *
	 * @param  array $columns Array of all columns.
	 *
	 * @return array Array of modified columns.
	 */
	public function workshop_columns( array $columns ) : array {
		$columns['date_time'] = 'Date/Time';
		$columns['speaker']   = 'Speaker';
		unset( $columns['date'] );
		return $columns;
	}

	/**
	 * Display speaker name on workshops backend.
	 *
	 * @param string  $column  Column name.
	 * @param int     $post_id Post ID.
	 *
	 * @return  void Prints content.
	 */
	public function workshop_column_content( string $column, int $post_id ) {
		global $post;
		if ( 'date_time' === $column ) {
			echo esc_attr( date( 'n/d/y g:i A', strtotime( get_field( 'date_and_time' ) ) ) );
		} elseif ( 'speaker' === $column ) {
			$workshop_speaker = get_field( 'speaker' );
			if ( $workshop_speaker && 1 === count( $workshop_speaker ) ) {
				echo esc_attr( $workshop_speaker[0]->post_title );
			} elseif ( count( $workshop_speaker ) > 1 ) {
				$speaker_string = null;
				foreach ( $workshop_speaker as $speaker ) {
					$speaker_string .= $speaker->post_title . ', ';
				}
				echo esc_attr( rtrim( $speaker_string, ', ' ) );
			}
		}
	}

	/**
	 * Make workshop speaker name header sortable.
	 *
	 * @param  array $columns Array of all columns.
	 *
	 * @return array array of modified columns.
	 */
	public function workshop_sortable_columns( $columns ) : array {
		$columns['date_time'] = 'date_time';
		$columns['speaker']   = 'speaker';
		return $columns;
	}

	/**
	 * Sort workshops by speaker name.
	 *
	 * @param object $query WP_Query.
	 *
	 * @return  void Sets query vars.
	 */
	public function sort_workshops_admin( WP_Query $query ) {
		if ( is_admin() && array_key_exists( 'post_type', $query->query ) && 'workshop' === $query->query['post_type'] && $query->is_main_query() ) {
			if ( 'date_time' === $query->get( 'orderby' ) && 'menu_order title' === $query->get( 'orderby' ) ) {
				$query->set( 'meta_key', 'date_and_time' );
			} elseif ( 'related_speaker' === $query->get( 'orderby' ) ) {
				$query->set( 'meta_key', 'related_speaker' );
			}
		}
	}

	/**
	 * Sort speakers and hide non-featured speakers.
	 *
	 * @param  object $query WP_Query.
	 *
	 * @return  void Sets query vars.
	 */
	public function speakers_order( WP_Query $query ) {
		if ( array_key_exists( 'post_type', $query->query ) && 'speaker' === $query->query['post_type'] ) {
			$query->set( 'orderby', 'menu_order' );
			$query->set( 'order', 'ASC' );

			if ( ! is_admin() && $query->is_main_query() && is_post_type_archive( 'speaker' ) ) {
				$query->set(
					'tax_query', array(
						'taxonomy' => 'ghc_speaker_category_taxonomy',
						'field'    => 'slug',
						'terms'    => 'featured',
					)
				);
			}
		}
	}

	/**
	 * Sort exhibitors.
	 *
	 * @param object $query WP_Query.
	 *
	 * @return  void Sets query vars.
	 */
	public function modify_exhibitor_archive( WP_Query $query ) {
		if ( array_key_exists( 'post_type', $query->query ) && 'exhibitor' === $query->query['post_type'] && ! is_admin() && $query->is_main_query() ) {
			$query->set( 'posts_per_page', '-1' );
			$query->set( 'pagination', 'false' );
			$query->set( 'orderby', 'title' );
			$query->set( 'order', 'ASC' );
		}
	}

	/**
	 * Sort special track CPTs.
	 *
	 * @param object $query WP_Query.
	 *
	 * @return  void Sets query vars.
	 */
	public function modify_special_track_tax( WP_Query $query ) {
		if ( array_key_exists( 'ghc_special_tracks_taxonomy', $query->query ) && ! is_admin() && $query->is_main_query() ) {
			$query->set( 'posts_per_page', '-1' );
			$query->set( 'pagination', 'false' );
			$query->set( 'orderby', 'post_type menu_order title' );
			$query->set( 'order', 'ASC' );
		}
	}

	/**
	 * Sort sponsors.
	 *
	 * @param object $query WP_Query.
	 *
	 * @return  void Sets query vars.
	 */
	public function modify_sponsor_archive( WP_Query $query ) {
		if ( ! is_admin() && $query->is_main_query() && is_post_type_archive( 'sponsor' ) ) {
			$query->set( 'posts_per_page', -1 );
			$query->set( 'orderby', 'menu_order' );
			$query->set( 'order', 'ASC' );
		}
	}

	/**
	 * Add all the given speaker’s workshops to post_meta for performance.
	 *
	 * @param integer $post_id WP post ID.
	 *
	 * @return  void Saves data to post_meta.
	 */
	public function add_speaker_workshop_meta( int $post_id ) {
		if ( 'speaker' === get_post_type() ) {
			$this->save_speaker_workshops( $post_id );
		} elseif ( 'workshop' === get_post_type() ) {
			$this_speaker = get_field( 'speaker', $post_id );

			foreach ( $this_speaker as $speaker ) {
				$this->save_speaker_workshops( $speaker->ID );
			}
		}
	}

	/**
	 * Get workshop IDs for a given speaker.
	 *
	 * @param  integer $speaker_id Speaker post ID.
	 *
	 * @return bool   Whether post_meta was updated.
	 */
	private function save_speaker_workshops( int $speaker_id ) : bool {
		$speaker_workshops_args = array(
			'post_type'      => 'workshop',
			'posts_per_page' => -1,
			'orderby'        => 'title',
			'order'          => 'ASC',
			'meta_query'     => array(
				'relation' => 'AND',
				array(
					'key'     => 'speaker',
					'compare' => 'EXISTS',
				),
				array(
					'key'     => 'speaker',
					'value'   => '"' . $speaker_id . '"',
					'compare' => 'LIKE',
				),
			),
		);

		$speaker_workshops_query = new WP_Query( $speaker_workshops_args );

		$speaker_workshops_array = array();
		foreach ( $speaker_workshops_query->posts as $post ) {
			$speaker_workshops_array[] = $post->ID;
		}

		return update_post_meta( $speaker_id, 'related_workshops', maybe_serialize( $speaker_workshops_array ) );
	}
}

new GHC_Post_Types();
