<?php

defined( 'ABSPATH' ) or die( 'No access allowed' );

/**
 * Add custom field on user profile screens to match with speaker CPT
 *
 * @param object $user WP_User
 */
function show_speaker_matching_box( $user ) {
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
			echo '<option value="' . get_the_ID() . '"';
			if ( get_user_meta( $user->ID, 'speaker_match', true ) == get_the_ID() ) {
				echo ' selected="selected"'; }
			echo '>' . get_the_title() . '</option>';
		}
	}
				echo '</select>
            </td>
        </tr>
    </table>';
}
add_action( 'show_user_profile', 'show_speaker_matching_box' );
add_action( 'edit_user_profile', 'show_speaker_matching_box' );

/**
 * Save custom user profile field
 *
 * @param  integer $user_id WP user ID
 */
function ghc_save_extra_profile_fields( $user_id ) {

	if ( current_user_can( 'edit_user', $user_id ) ) {
		update_usermeta( $user_id, 'speaker_match', esc_attr( $_POST['speaker_match'] ) );
	}
}
add_action( 'personal_options_update', 'ghc_save_extra_profile_fields' );
add_action( 'edit_user_profile_update', 'ghc_save_extra_profile_fields' );

// FUTURE: add select2 to dropdown
