<?php
/**
 * GHC Shortcodes
 *
 * @author AndrewRMinion Design
 * @package GHC_Functionality_Plugin
 */

if ( ! function_exists( 'add_filter' ) ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit();
}

/**
 * Content-related functions
 */
class GHC_Speakers extends GHC_Base {

	/**
	 * Subclass instance.
	 *
	 * @var null
	 */
	private static $instance = null;

 	/**
	 * Return only one instance of this class.
	 *
	 * @return GHC_Speakers class.
	 */
	public function get_instance() : GHC_Speakers {
		if ( null === self::$instance ) {
			self::$instance = new GHC_Speakers();
		}

		return self::$instance;
	}

	/**
	 * Kick things off
	 *
	 * @access  private
	 */
	private function __construct() {}

	/**
	 * Get speaker’s position and company name/link.
	 *
	 * @param  int    $post_id WP post ID.
	 *
	 * @return string          HTML content.
	 */
	public function get_short_bio( int $post_id ) : string {
	    $speaker_position = get_field( 'position', $post_id );
	    $speaker_company = get_field( 'company', $post_id );
	    $speaker_company_URL = get_field( 'company_url', $post_id );

	    ob_start();
	    if ( $speaker_position || $speaker_company ) {
	        echo '<p class="entry-meta speaker-info">';
	        if ( $speaker_position ) {
	            echo esc_attr( $speaker_position );
	        }
	        if ( $speaker_position && $speaker_company ) {
	            echo ' <span class="separator">|</span> ';
	        }
	        if ( $speaker_company ) {
	            echo ( $speaker_company_URL ? '<a target="_blank" rel="noopener noreferrer" href="' . esc_url( $speaker_company_URL ) . '">' : '' ) . esc_attr( $speaker_company ) . ( $speaker_company_URL ? '</a>' : '' );
	        }
	        echo '</p>';
	    }
	    return ob_get_clean();
	}

}

GHC_Speakers::get_instance();