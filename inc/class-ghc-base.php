<?php
/**
 * Base GHC plugin class
 *
 * @author AndrewRMinion Design
 *
 * @package WordPress
 * @subpackage GHC_Functionality
 */

if ( ! function_exists( 'add_filter' ) ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit();
}

/**
 * Base GHC class
 */
class GHC_Base {
	/**
	 * Plugin version
	 *
	 * @var string Plugin version string for cache-busting
	 */
	private $version = '3.2.10';

	/**
	 * All active conventions
	 *
	 * @var array
	 */
	protected $conventions = array();

	/**
	 * Convention abbreviations
	 *
	 * @var array
	 */
	protected $conventions_abbreviations = array();

	/**
	 * Convention dates
	 *
	 * @var array
	 */
	protected $conventions_dates = array();

	/**
	 * Get this plugin directory path
	 *
	 * @return string Base path for this plugin’s directory
	 */
	protected function plugin_dir_path() {
		return plugin_dir_path( GHC_PLUGIN_FILE );
	}

	/**
	 * Get this plugin directory URL
	 *
	 * @param  string [$path       = ''] Optional path to append
	 * @return string Base URL for this plugin’s directory
	 */
	protected function plugin_dir_url( $path = '' ) {
		return plugin_dir_url( GHC_PLUGIN_FILE ) . $path;
	}

	/**
	 * Kick things off
	 *
	 * @private
	 */
	public function __construct() {

		include_once 'class-ghc-post-types.php'; // Loaded in class.
		include_once 'class-ghc-conventions.php'; // Loaded in class.

		include_once 'class-ghc-acf.php'; // Loaded in class.
		include_once 'class-ghc-content.php'; // Loaded in class.

//		include_once 'cpts.php';
//		include_once 'functions.php';
//		include_once 'images.php';
//		include_once 'shortcodes.php';
//		include_once 'woocommerce.php';

		// Set up convention info.
		add_action( 'after_setup_theme', array( $this, 'get_conventions' ) );
		add_action( 'after_setup_theme', array( $this, 'get_conventions_abbreviations' ) );
		add_action( 'after_setup_theme', array( $this, 'get_conventions_dates' ) );

		// Register/enqueue assets.
		add_action( 'wp_enqueue_scripts', array( $this, 'register_assets' ) );

		// TODO: move to GHC_Exhibitors sub-class.
		add_action( 'admin_enqueue_scripts', 'ghc_register_backend_resources' );
	}

	/**
	 * Get all convention info
	 *
	 * @return array All convention info.
	 */
	public function get_conventions_info() {
		if ( 0 === count( $this->conventions ) ) {
			$conventions       = new GHC_Conventions();
			$this->conventions = $conventions->get_conventions_info();
		}

		return $this->conventions;
	}

	/**
	 * Get convention abbreviations
	 *
	 * @return array Convention abbreviations.
	 */
	public function get_conventions_abbreviations() {
		if ( 0 === count( $this->conventions_abbreviations ) ) {
			$conventions                     = new GHC_Conventions();
			$this->conventions_abbreviations = $conventions->get_conventions_abbreviations();
		}

		return $this->conventions_abbreviations;
	}

	/**
	 * Get convention dates
	 *
	 * @return array Convention dates.
	 */
	public function get_conventions_dates() {
		if ( 0 === count( $this->conventions_dates ) ) {
			$conventions             = new GHC_Conventions();
			$this->conventions_dates = $conventions->get_conventions_dates();
		}

		return $this->conventions_dates;
	}

	/**
	 * Register or enqueue frontend assets
	 */
	public function register_assets() {
		wp_enqueue_style( 'ghc-functionality', $this->plugin_dir_url( 'dist/css/style.min.css' ), array(), $this->version );

		wp_enqueue_script( 'ghc-popups', $this->plugin_dir_url( 'dist/js/popups.min.js' ), array( 'jquery', 'popup-maker-site' ), $this->version, true );
	}

	// TODO: move to sub-class.
	public function ghc_register_frontend_resources() {
		wp_register_script( 'ghc-woocommerce', $this->plugin_dir_url( 'dist/js/woocommerce.min.js' ), array( 'jquery', 'woocommerce' ), $this->version );
		wp_register_script( 'ghc-price-sheets', $this->plugin_dir_url( 'dist/js/price-sheets.min.js' ), array( 'jquery' ), $this->version );
		wp_register_script( 'ghc-workshop-filter', $this->plugin_dir_url( 'dist/js/workshop-filter.min.js' ), array( 'jquery' ), $this->version );

		// Load WooCommerce script only on WC pages.
		if ( function_exists( 'is_product' ) && function_exists( 'is_cart' ) ) {
			if ( is_product() || is_cart() ) {
				wp_enqueue_script( 'ghc-woocommerce' );
			}
		}
	}

	// TODO: move to sub-class.
	function ghc_register_backend_resources() {
		global $post_type;
		if ( 'exhibitor' === $post_type ) {
			wp_enqueue_script( 'ghc-exhibitor-backend', plugins_url( 'js/exhibitor-backend.min.js', __FILE__ ), array( 'jquery' ), $this->version, true );
		}
	}

}
