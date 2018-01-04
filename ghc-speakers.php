<?php
/**
 * Plugin Name: GHC Functionality
 * Plugin URI: https://github.com/greathomeschoolconventions/ghc-functionality
 * Description: Add custom post types and other backend features
 * Version: 3.1.2
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
*/

defined( 'ABSPATH' ) or die( 'No access allowed' );

CONST GHC_PLUGIN_VERSION = '3.1.2';

include( 'inc/acf.php' );
include( 'inc/admin.php' );
include( 'inc/content.php' );
include( 'inc/cpts.php' );
include( 'inc/functions.php' );
include( 'inc/images.php' );
include( 'inc/shortcodes.php' );
include( 'inc/woocommerce.php' );

/**
 * Register frontend JS and styles
 */
function ghc_register_frontend_resources() {
    wp_register_script( 'ghc-woocommerce', plugins_url( 'js/woocommerce.min.js', __FILE__ ), array( 'jquery', 'woocommerce' ), GHC_PLUGIN_VERSION );
    wp_register_script( 'ghc-price-sheets', plugins_url( 'js/price-sheets.min.js', __FILE__ ), array( 'jquery' ), GHC_PLUGIN_VERSION );
    wp_register_script( 'ghc-workshop-filter', plugins_url( 'js/workshop-filter.min.js', __FILE__ ), array( 'jquery' ), GHC_PLUGIN_VERSION );

    wp_enqueue_style( 'ghc-functionality', plugins_url( 'css/style.min.css', __FILE__ ), array(), GHC_PLUGIN_VERSION );

    // load WooCommerce script only on WC pages
    if ( function_exists( 'is_product' ) && function_exists( 'is_cart' ) ) {
        if ( is_product() || is_cart() ) {
            wp_enqueue_script( 'ghc-woocommerce' );
        }
    }
}
add_action( 'wp_enqueue_scripts', 'ghc_register_frontend_resources' );

/**
 * Register backend JS and styles
 */
function ghc_register_backend_resources() {
    global $post_type;
    if ( 'exhibitor' == $post_type ) {
        wp_enqueue_script( 'ghc-exhibitor-backend', plugins_url( 'js/exhibitor-backend.min.js', __FILE__ ), array( 'jquery' ), GHC_PLUGIN_VERSION, true );
    }
}
add_action( 'admin_enqueue_scripts', 'ghc_register_backend_resources' );
