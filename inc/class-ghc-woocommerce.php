<?php
/**
 * GHC WooCommerce
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
 * GHC WooCommerce
 */
class GHC_Woocommerce extends GHC_Base {

	/**
	 * Subclass instance.
	 *
	 * @var null
	 */
	private static $instance = null;

	/**
	 * Kick things off
	 */
	private function __construct() {
		add_action( 'wp_enqueue_scripts', array( $this, 'register_frontend_resources' ) );
	}

 	/**
	 * Return only one instance of this class.
	 *
	 * @return GHC_Woocommerce class.
	 */
	public function get_instance() : GHC_Woocommerce {
		if ( null === self::$instance ) {
			self::$instance = new GHC_Woocommerce();
		}

		return self::$instance;
	}

	/**
	 * Register and enqueue WooCommerce scripts.
	 *
	 * @return void Enqueues WooCommerce assets.
	 */
	public function register_frontend_resources() {
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

	/**
	 * Add product attribute classes
	 *
	 * @param  integer $variation_id Product variation ID.
	 *
	 * @return void Prints `post_class` output.
	 */
	function product_post_class( string $variation_id = '' ) {
		$variation_classes = array();

		if ( $variation_id ) {
			if ( 'product_variation' == get_post_type( $variation_id ) ) {
				$variation = new WC_Product_Variation( $variation_id );
				foreach ( $variation->get_attributes() as $key => $value ) {
					$variation_classes[] = 'attribute_' . $key . '-' . strtolower( str_replace( ' ', '-', $value ) );
				}
			}
		}

		post_class( $variation_classes );
	}

}

GHC_Woocommerce::get_instance();
