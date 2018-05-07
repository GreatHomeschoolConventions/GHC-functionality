<?php
/**
 * GHC speakers
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
 * GHC Speakers
 */
class GHC_Speakers extends GHC_Base {

	/**
	 * Kick things off
	 *
	 * @private
	 */
	public function __construct() {
		add_action( 'show_user_profile', array( $this, 'show_speaker_matching_box' ) );
		add_action( 'edit_user_profile', array( $this, 'show_speaker_matching_box' ) );

		add_action( 'personal_options_update', array( $this, 'save_speaker_matching_field' ) );
		add_action( 'edit_user_profile_update', array( $this, 'save_speaker_matching_field' ) );
	}

	/**
	 * Get author bio and convention locations
	 *
	 * @return string HTML content
	 */
	public function get_author_bio() {
		ob_start();
		$speaker_meta = get_the_author_meta( 'speaker_match' );

		if ( ! empty ( $speaker_meta ) ) {
			$this_post_terms = get_the_terms( $speaker_meta, 'ghc_conventions_taxonomy' );
			?>
			<div class="author-info">
				<p><?php echo get_avatar( get_the_author_meta( 'ID' ), 120 ) . get_the_author_meta( 'description' ); ?></p>
				<?php if ( count( $this_post_terms ) > 0 ) { ?>
					<p>Meet <a href="<?php the_permalink( $speaker_meta ); ?>"><?php the_author(); ?></a> at these conventions:</p>
					<p><?php echo $conventions->get_icons( $this_post_terms ); ?></p>
				<?php } ?>
			</div>
			<?php
		}
		return ob_get_clean();
	}

	/**
	 * Add custom field on user profile screens to match with speaker CPT
	 *
	 * @param object $user WP_User.
	 */
	public function show_speaker_matching_box( $user ) {
		echo '<h3>Select a speaker to match to this author</h3>
		<table class="form-table">
		<tr>
		<th><label for="speaker_match">Speaker</label></th>
		<td>
		<select name="speaker_match" id="speaker_match">
		<option value="">- Select one -</option>';

		$speakers_query_args = array(
			'post_type'      => array( 'speaker' ),
			'posts_per_page' => '-1',
		);
		$speakers_query      = new WP_Query( $speakers_query_args );
		if ( $speakers_query->have_posts() ) {
			while ( $speakers_query->have_posts() ) {
				$speakers_query->the_post();
				echo '<option value="' . esc_attr( get_the_ID() ) . '"' . selected( get_the_ID(), get_user_meta( $user->ID, 'speaker_match', true ), false ) . '>' . esc_attr( get_the_title() ) . '</option>';
			}
		}

		echo '</select>
		</td>
		</tr>
		</table>';

		// FUTURE: add select2.
	}

	/**
	 * Save custom user profile field
	 *
	 * @param  integer $user_id WP user ID.
	 */
	public function save_speaker_matching_field( $user_id ) {
		if ( current_user_can( 'edit_user', $user_id ) ) {
			update_user_meta( $user_id, 'speaker_match', esc_attr( $_POST['speaker_match'] ) ); // WPCS: CSRF ok because it has already been checked by WP core at this point.
		}
	}


}

new GHC_Speakers();
