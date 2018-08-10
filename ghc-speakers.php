<?php
/**
 * Plugin Name: GHC Functionality
 * Plugin URI: https://github.com/greathomeschoolconventions/ghc-functionality
 * Description: Add custom post types and other backend features
 * Version: 4.1.4
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
 *
 * @package GHC_Functionality_Plugin
 */

if ( ! function_exists( 'add_filter' ) ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit();
}

// Define GHC_PLUGIN_FILE.
if ( ! defined( 'GHC_PLUGIN_FILE' ) ) {
	define( 'GHC_PLUGIN_FILE', __FILE__ );
}

// Include the main class.
if ( ! class_exists( 'GHC_Base' ) ) {
	include_once dirname( __FILE__ ) . '/inc/class-ghc-base.php';
	GHC_Base::get_main_instance();
}
