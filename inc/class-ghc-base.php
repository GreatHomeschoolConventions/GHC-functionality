<?php
/**
 * Base GHC plugin class
 *
 * @author AndrewRMinion Design
 * @package GHC_Functionality
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
	 * Plugin version.
	 *
	 * @var string Plugin version string for cache-busting.
	 */
	private $version = '3.2.10';

	/**
	 * All active conventions.
	 *
	 * @var array
	 */
	protected $conventions = array();

	/**
	 * Convention abbreviations.
	 *
	 * @var array
	 */
	protected $conventions_abbreviations = array();

	/**
	 * Convention dates.
	 *
	 * @var array
	 */
	protected $conventions_dates = array();

	/**
	 * Get this plugin directory path.
	 *
	 * @param  string [ $path       = ''] Optional path to append.
	 *
	 * @return string Base path for this plugin’s directory.
	 */
	protected function plugin_dir_path( $path = '' ) : string {
		return plugin_dir_path( GHC_PLUGIN_FILE ) . $path;
	}

	/**
	 * Get this plugin directory URL.
	 *
	 * @param  string [ $path       = ''] Optional path to append.
	 *
	 * @return string Base URL for this plugin’s directory
	 */
	protected function plugin_dir_url( $path = '' ) : string {
		return plugin_dir_url( GHC_PLUGIN_FILE ) . $path;
	}

	/**
	 * Kick things off.
	 *
	 * @access public
	 */
	public function __construct() {

		include_once 'class-ghc-post-types.php'; // Loaded in class.
		include_once 'class-ghc-conventions.php'; // Loaded in class.

		include_once 'class-ghc-acf.php'; // Loaded in class.
		include_once 'class-ghc-content.php'; // Loaded in class.
		include_once 'class-ghc-shortcodes.php'; // Loaded in class.
		include_once 'class-ghc-woocommerce.php'; // Loaded in class.

//		include_once 'images.php';
//		include_once 'shortcodes.php';
//		include_once 'woocommerce.php';

		// Set up convention info.
		add_action( 'after_setup_theme', array( $this, 'get_conventions_info' ) );
		add_action( 'after_setup_theme', array( $this, 'get_conventions_abbreviations' ) );
		add_action( 'after_setup_theme', array( $this, 'get_conventions_dates' ) );

		// Register/enqueue assets.
		add_action( 'wp_enqueue_scripts', array( $this, 'register_assets' ) );

		add_filter( 'upload_mimes', array( $this, 'mime_types' ) );
		add_filter( 'wp_get_attachment_image_attributes', array( $this, 'responzive_img_sizes' ), 25, 3 );

		// TODO: move to GHC_Exhibitors sub-class.
		add_action( 'admin_enqueue_scripts', 'ghc_register_backend_resources' );
	}

	/**
	 * Get all convention info.
	 *
	 * @return array All convention info.
	 */
	public function get_conventions_info() : array {
		if ( 0 === count( $this->conventions ) ) {
			$conventions       = GHC_Conventions::get_instance();
			$this->conventions = $conventions->get_conventions_info();
		}

		return $this->conventions;
	}

	/**
	 * Get convention abbreviations.
	 *
	 * @return array Convention abbreviations.
	 */
	public function get_conventions_abbreviations() : array {
		if ( 0 === count( $this->conventions_abbreviations ) ) {
			$conventions                     = GHC_Conventions::get_instance();
			$this->conventions_abbreviations = $conventions->get_conventions_abbreviations();
		}

		return $this->conventions_abbreviations;
	}

	/**
	 * Get convention dates.
	 *
	 * @return array Convention dates.
	 */
	public function get_conventions_dates() : array {
		if ( 0 === count( $this->conventions_dates ) ) {
			$conventions             = GHC_Conventions::get_instance();
			$this->conventions_dates = $conventions->get_conventions_dates();
		}

		return $this->conventions_dates;
	}

	/**
	 * Get info for a single convention.
	 *
	 * @param  string $convention Two-letter convention abbreviation.
	 *
	 * @return array  Convention info.
	 */
	public function get_single_convention_info( $convention = '' ) : array {
		if ( ! empty( $convention ) ) {
			return $this->get_conventions_info( $convention );
		}

		return array();
	}

	/**
	 * Register or enqueue frontend assets
	 *
	 * @return  void Enqueues assets.
	 */
	public function register_assets() {
		wp_enqueue_style( 'ghc-functionality', $this->plugin_dir_url( 'dist/css/style.min.css' ), array(), $this->version );

		wp_enqueue_script( 'ghc-popups', $this->plugin_dir_url( 'dist/js/popups.min.js' ), array( 'jquery', 'popup-maker-site' ), $this->version, true );
	}

	/**
	 * Format date range.
	 *
	 * @link https://codereview.stackexchange.com/a/78303 Adapted from this answer.
	 *
	 * @param  mixed  $d1      Start DateTime object or string.
	 * @param  mixed  $d2      End DateTime object or string.
	 * @param  string $format  Input date format if passed as strings.
	 *
	 * @return  string Formatted date string.
	 */
	public function format_date_range( $d1, $d2, string $format = '' ) : string {
		if ( is_string( $d1 ) && is_string( $d2 ) && ! empty( $format ) ) {
			$d1 = date_create_from_format( $format, $d1 );
			$d2 = date_create_from_format( $format, $d2 );
		}

		if ( $d1->format( 'Y-m-d' ) === $d2->format( 'Y-m-d' ) ) {
			// Same day.
			return $d1->format( 'F j, Y' );
		} elseif ( $d1->format( 'Y-m' ) === $d2->format( 'Y-m' ) ) {
			// Same calendar month.
			return $d1->format( 'F j' ) . '&ndash;' . $d2->format( 'd, Y' );
		} elseif ( $d1->format( 'Y' ) === $d2->format( 'Y' ) ) {
			// Same calendar year.
			return $d1->format( 'F j' ) . '&ndash;' . $d2->format( 'F j, Y' );
		} else {
			// General case (spans calendar years).
			return $d1->format( 'F j, Y' ) . '&ndash;' . $d2->format( 'F j, Y' );
		}
	}

	/**
	 * Allow ICS and SVG file uploads.
	 *
	 * @param  array $mime_types Array of allowed mime types.
	 *
	 * @return array Modified array.
	 */
	function mime_types( array $mime_types ) : array {
		$mime_types['ics'] = 'text/calendar';
		$mime_types['svg'] = 'image/svg+xml';
		return $mime_types;
	}

	/**
	 * Set sizes atribute for responsive images and better performance.
	 *
	 * @param  array        $attr       Markup attributes.
	 * @param  object       $attachment WP_Post image attachment post.
	 * @param  string|array $size       Named image size or array.
	 *
	 * @return array Markup attributes.
	 */
	function responsive_img_sizes( array $attr, WP_Post $attachment, $size ) : array {
		if ( is_array( $size ) ) {
			$attr['sizes'] = $size[0] . 'px';
		} elseif ( 'thumbnail-no-crop' === $size ) {
			$attr['sizes'] = '140px';
		} elseif ( 'pinterest-thumb' === $size ) {
			$attr['sizes'] = '173px';
		} elseif ( 'pinterest-medium' === $size ) {
			$attr['sizes'] = '346px';
		} elseif ( 'square-tiny' === $size ) {
			$attr['sizes'] = '150px';
		} elseif ( 'square-thumb' === $size ) {
			$attr['sizes'] = '250px';
		} elseif ( 'square-small' === $size ) {
			$attr['sizes'] = '400px';
		} elseif ( 'square-medium' === $size ) {
			$attr['sizes'] = '600px';
		} elseif ( 'square-large' === $size ) {
			$attr['sizes'] = '900px';
		} elseif ( 'small-grid-size' === $size ) {
			$attr['sizes'] = '400px';
		} elseif ( 'small-grid-size-medium' === $size ) {
			$attr['sizes'] = '600px';
		} elseif ( 'small-grid-size-large' === $size ) {
			$attr['sizes'] = '800px';
		} elseif ( 'special-event-small' === $size ) {
			$attr['sizes'] = '300px';
		} elseif ( 'special-event-medium' === $size ) {
			$attr['sizes'] = '450px';
		} elseif ( 'special-event-large' === $size ) {
			$attr['sizes'] = '600px';
		}
		return $attr;
	}

	// TODO: move to exhibitor sub-class?
	function ghc_register_backend_resources() {
		global $post_type;
		if ( 'exhibitor' === $post_type ) {
			wp_enqueue_script( 'ghc-exhibitor-backend', plugins_url( 'js/exhibitor-backend.min.js', __FILE__ ), array( 'jquery' ), $this->version, true );
		}
	}

}
