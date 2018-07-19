<?php
/**
 * ACF Pro-related functions
 *
 * @package GHC_Functionality_Plugin
 */

if ( ! function_exists( 'add_filter' ) ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit();
}

/**
 * ACF Pro-related functions
 */
class GHC_ACF extends GHC_Base {

	/**
	 * Kick things off.
	 */
	public function __construct() {
		if ( function_exists( 'acf_add_options_page' ) ) {
			add_action( 'after_setup_theme', array( $this, 'admin_options' ) );
			add_action( 'acf/init', array( $this, 'acf_init' ) );
			add_filter( 'acf/settings/save_json', array( $this, 'acf_json_save_point' ) );
			add_filter( 'acf/settings/load_json', array( $this, 'acf_json_load_point' ) );
		}
	}

	/**
	 * Add ACF options page.
	 *
	 * @return  void Adds options page.
	 */
	public function admin_options() {
		$parent = acf_add_options_page(
			array(
				'page_title' => 'GHC Options',
				'menu_title' => 'GHC Options',
				'menu_slug'  => 'theme-options',
				'capability' => 'manage_options',
				'icon_url'   => 'dashicons-admin-settings',
				'redirect'   => false,
			)
		);

		$theme = wp_get_theme();

		if ( 'GHC E3 Bundle' === $theme->get( 'Name' ) ) {
			acf_add_options_sub_page(
				array(
					'page_title'  => 'E3 Options',
					'menu_title'  => 'E3 Options',
					'parent_slug' => $parent['menu_slug'],
					'capability'  => 'manage_options',
					'redirect'    => false,
				)
			);
		}
	}

	/**
	 * Add Google Maps API key for ACF use.
	 *
	 * @return void Sets Google API key.
	 */
	public function acf_init() {
		acf_update_setting( 'google_api_key', get_option( 'options_api_key' ) );
	}

	/**
	 * Set ACF local JSON save directory.
	 *
	 * @param  string $path ACF local JSON save directory.
	 *
	 * @return string ACF local JSON save directory.
	 */
	public function acf_json_save_point( string $path ) : string {
		return $this->plugin_dir_path() . '/acf-json';
	}

	/**
	 * Set ACF local JSON open directory.
	 *
	 * @param  array $paths ACF local JSON open directory.
	 *
	 * @return array ACF local JSON open directory.
	 */
	public function acf_json_load_point( array $paths ) : array {
		$paths[] = $this->plugin_dir_path() . '/acf-json';
		return $paths;
	}

}

new GHC_ACF();
