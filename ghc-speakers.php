<?php
/**
 * Plugin Name: GHC Speakers
 * Plugin URI: https://github.com/macbookandrew/ghc-speakers
 * Description: A simple plugin to add a “speakers” custom post type for use on Great Homeschool Convention’s website.
 * Version: 1.0
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
		'menu_name'           => __( 'Speaker', 'GHC' ),
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
		'taxonomies'          => array( 'category', 'post_tag' ),
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
