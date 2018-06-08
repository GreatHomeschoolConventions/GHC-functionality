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
	private function __construct() {}

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
