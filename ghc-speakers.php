<?php
/**
 * Plugin Name: GHC Speakers
 * Plugin URI: https://github.com/macbookandrew/ghc-speakers
 * Description: A simple plugin to add a “speakers” custom post type for use on Great Homeschool Convention’s website.
 * Version: 1.0.1
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


// Register Custom Post Type
function ghc_speakers() {

	$labels = array(
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
	$rewrite = array(
		'slug'                => 'speakers',
		'with_front'          => true,
		'pages'               => true,
		'feeds'               => true,
	);
	$args = array(
		'label'               => __( 'speaker', 'GHC' ),
		'description'         => __( 'Speakers', 'GHC' ),
		'labels'              => $labels,
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
		'rewrite'             => $rewrite,
		'capability_type'     => 'page',
	);
	register_post_type( 'speaker', $args );

}

// Hook into the 'init' action
add_action( 'init', 'ghc_speakers', 0 );

// Register Custom Taxonomy
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
	register_taxonomy( 'ghc_conventions_taxonomy', array( 'speaker' ), $convention_args );

}

// Hook into the 'init' action
add_action( 'init', 'ghc_speaker_taxonomies', 0 );

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
