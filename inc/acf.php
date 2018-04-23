<?php
/**
 * ACF Pro-related functions
 *
 * @package GHC Functionality Plugin
 */

defined( 'ABSPATH' ) or die( 'No access allowed' );

if ( ! function_exists( 'ghc_admin_options' ) ) {
	/**
	 * Add ACF options page
	 */
	function ghc_admin_options() {
		if ( function_exists( 'acf_add_options_page' ) ) {
			acf_add_options_page(
				array(
					'page_title' => 'GHC Options',
					'menu_title' => 'GHC Options',
					'menu_slug'  => 'theme-options',
					'capability' => 'edit_posts',
					'redirect'   => false,
				)
			);
		}
	}
}
add_action( 'after_setup_theme', 'ghc_admin_options' );

if ( ! function_exists( 'ghc_acf_init' ) ) {
	/**
	 * Add Google Maps API key for ACF use
	 */
	function ghc_acf_init() {
		acf_update_setting( 'google_api_key', get_option( 'options_api_key' ) );
	}
}
add_action( 'acf/init', 'ghc_acf_init' );

if ( ! function_exists( 'ghc_acf_json_save_point' ) ) {
	/**
	 * Set ACF local JSON save directory
	 *
	 * @param  string $path ACF local JSON save directory
	 * @return string ACF local JSON save directory
	 */
	function ghc_acf_json_save_point( $path ) {
		return plugin_dir_path( __FILE__ ) . '/../acf-json';
	}
}
add_filter( 'acf/settings/save_json', 'ghc_acf_json_save_point' );

if ( ! function_exists( 'ghc_acf_json_load_point' ) ) {
	/**
	 * Set ACF local JSON open directory
	 *
	 * @param  array $path ACF local JSON open directory
	 * @return array ACF local JSON open directory
	 */
	function ghc_acf_json_load_point( $path ) {
		$paths[] = plugin_dir_path( __FILE__ ) . '/../acf-json';
		return $paths;
	}
}
add_filter( 'acf/settings/load_json', 'ghc_acf_json_load_point' );
