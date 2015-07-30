<?php
/**
 * Plugin Name: GHC Speakers and Exhibitors
 * Plugin URI: https://github.com/macbookandrew/ghc-speakers
 * Description: A simple plugin to add a “speakers” custom post type for use on Great Homeschool Convention’s website.
 * Version: 1.3
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

}
// Hook into the 'init' action to register custom post types
add_action( 'init', 'custom_post_types', 0 );

// Register Custom Taxonomies
function ghc_speaker_taxonomies() {

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
	register_taxonomy( 'ghc_conventions_taxonomy', array( 'speaker', 'exhibitor' ), $convention_args );

}
// Hook into the 'init' action to register custom taxonomy
add_action( 'init', 'ghc_speaker_taxonomies', 0 );

// add metabox for featured/general speakers
add_action( 'add_meta_boxes', 'add_featured_speaker_metabox' );
function add_featured_speaker_metabox( $post_type, $post ) {
    add_meta_box( 'featured-speaker', 'Featured Speaker', 'print_featured_speaker_metabox', 'speaker' );
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
function add_url_metabox( $post_type, $post ) {
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
