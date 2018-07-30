<?php
/**
 * AJAX functions
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
class GHC_Ajax extends GHC_Base {

	/**
	 * Subclass instance.
	 *
	 * @var null
	 */
	private static $instance = null;

	/**
	 * Return only one instance of this class.
	 *
	 * @return GHC_Ajax class.
	 */
	public static function get_instance() : GHC_Ajax {
		if ( null === self::$instance ) {
			self::$instance = new GHC_Ajax();
		}

		return self::$instance;
	}

	/**
	 * Kick things off
	 *
	 * @access  private
	 */
	private function __construct() {
		add_action( 'wp_ajax_get_speakers_by_content_tag', array( $this, 'get_speakers_by_content_tag' ) );
		add_action( 'wp_ajax_nopriv_get_speakers_by_content_tag', array( $this, 'get_speakers_by_content_tag' ) );
	}

	/**
	 * Get speakers for content type request.
	 *
	 * @since  4.0.0
	 *
	 * @return void Prints HTML content and dies.
	 */
	public function get_speakers_by_content_tag() : string {
		$category_id = intval( $_POST['category'] ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotValidated

		$speakers_args = array(
			'tax_query' => array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
				array(
					'taxonomy' => 'ghc_content_tags_taxonomy',
					'field'    => 'term_id',
					'terms'    => $category_id,
				),
			),
		);

		$speakers_query = new WP_Query( $speakers_args );

		if ( $speakers_query->have_posts() ) {
			while ( $speakers_query->have_posts() ) {
				$speakers_query->the_post();
				include $this->plugin_dir_path( 'templates/carousel-single.php' );
			}
		}
		wp_reset_postdata();
		wp_die();
	}

}

GHC_Ajax::get_instance();
