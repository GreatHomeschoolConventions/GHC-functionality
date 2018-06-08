<?php
/**
 * GHC images
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
 * GHC images
 */
class GHC_Images extends GHC_Base {

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
		add_filter( 'upload_mimes', array( $this, 'mime_types' ) );
		add_action( 'after_setup_theme', array( $this, 'custom_image_sizes' ) );
		add_filter( 'image_size_names_choose', array( $this, 'ghc_custom_image_sizes_names' ) );
		add_filter( 'wp_get_attachment_image_attributes', array( $this, 'responzive_img_sizes' ), 25, 3 );
	}

 	/**
	 * Return only one instance of this class.
	 *
	 * @return GHC_Images class.
	 */
	public function get_instance() : GHC_Images {
		if ( null === self::$instance ) {
			self::$instance = new GHC_Woocommerce();
		}

		return self::$instance;
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
	public function responsive_img_sizes( array $attr, WP_Post $attachment, $size ) : array {
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
		// If changed, update custom_image_sizes and custom_image_sizes_names.

		return $attr;
	}

	/**
	 * Add custom image sizes
	 *
	 * @return  void Registers custom image sizes.
	 */
	public function custom_image_sizes() {
		// Update responsive_image_sizes() function as well.
		add_image_size( 'thumbnail-no-crop', 140, 140, false );
		add_image_size( 'pinterest-thumb', 173, 345, true );
		add_image_size( 'pinterest-medium', 346, 690, true );
		add_image_size( 'square-tiny', 150, 150, true );
		add_image_size( 'square-thumb', 250, 250, true );
		add_image_size( 'square-small', 400, 400, true );
		add_image_size( 'square-medium', 600, 600, true );
		add_image_size( 'square-large', 900, 900, true );
		add_image_size( 'small-grid-size', 400, 300, true );
		add_image_size( 'small-grid-size-medium', 600, 450, true );
		add_image_size( 'small-grid-size-large', 800, 600, true );
		add_image_size( 'special-event-small', 300, 150, true );
		add_image_size( 'special-event-medium', 450, 225, true );
		add_image_size( 'special-event-large', 600, 300, true );
		// Update responsive_image_sizes() function as well.
	}

	/**
	 * Add custom image size names.
	 *
	 * @param  array $sizes array of named image sizes.
	 *
	 * @return array modified array of named image sizes.
	 */
	public function custom_image_sizes_names( array $sizes ) : array {
		return array_merge(
			$sizes, array(
				'thumbnail-no-crop'      => 'Thumbnail (no crop)',
				'square-thumb'           => 'Square',
				'square-small'           => 'Square',
				'square-medium'          => 'Square',
				'square-large'           => 'Square',
				'small-grid'             => 'Grid',
				'small-grid-size-medium' => 'Grid',
				'small-grid-size-large'  => 'Grid',
				'special-event-small'    => 'Special Event',
				'special-event-medium'   => 'Special Event',
				'special-event-large'    => 'Special Event',
			)
		);
	}

	/**
	 * Allow ICS and SVG file uploads.
	 *
	 * @param  array $mime_types Array of allowed mime types.
	 *
	 * @return array Modified array.
	 */
	public function mime_types( array $mime_types ) : array {
		$mime_types['ics'] = 'text/calendar';
		$mime_types['svg'] = 'image/svg+xml';
		return $mime_types;
	}

}

GHC_Images::get_instance();
