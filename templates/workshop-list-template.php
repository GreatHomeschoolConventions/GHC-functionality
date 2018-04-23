<?php
/**
 * Workshop list template
 *
 * @package GHC_Functionality_Plugin
 */

$speakers        = get_field( 'speaker' );
$speakers_string = null;
if ( $speakers ) {
	foreach ( $speakers as $speaker ) {
		$speakers_string .= '<a href="' . get_permalink( $speaker ) . '">' . get_the_title( $speaker ) . '</a>, ';
	}
	$speakers_string = rtrim( $speakers_string, ', ' );
}

$exhibitors = get_field( 'related_exhibitors' );
if ( $exhibitors ) {
	$exhibitors_string = null;
	foreach ( $exhibitors as $exhibitor ) {
		$exhibitors_string .= '<a href="' . get_permalink( $exhibitor ) . '" target="_blank" rel="noopener noreferrer">' . get_the_title( $exhibitor ) . '</a>, ';
	}
	$exhibitors_string = rtrim( $exhibitors_string, ', ' );
}
?>
<tr id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<td data-cell-title="Time" class="time"><i class="fa fa-clock-o"></i><?php echo date( 'g:i A', strtotime( get_field( 'date_and_time' ) ) ); ?></td>
	<td data-cell-title="Location" class="location"><i class="fa fa-map-marker"></i><?php echo get_the_term_list( get_the_ID(), 'ghc_session_locations_taxonomy' ); ?></td>
	<td data-cell-title="Speaker" class="speaker"><i class="fa fa-user"></i><?php echo $speakers_string . ( $exhibitors ? ' <span class="exhibitor">(' . $exhibitors_string . ')</span>' : '' ); ?></td>
	<td data-cell-title="Session Title" class="title"><i class="fa fa-book"></i><a href="<?php the_permalink( get_field( 'session_description' ) ); ?>"><?php the_title(); ?></a></td>
	<?php
	if ( is_user_logged_in() && current_user_can( 'edit_others_posts' ) ) {
		echo '<td><a href="' . get_edit_post_link() . '">Edit</a></td>';
	}
	?>
</tr>
