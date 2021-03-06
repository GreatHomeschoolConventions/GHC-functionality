<?php
/**
 * Single session grid template
 *
 * @package GHC_Functionality_Plugin
 */

	$speaker_slug = get_post_meta( get_the_ID(), 'session-speaker', true );
	$speaker      = get_page_by_path( $speaker_slug, OBJECT, 'speaker' );
?>
<article id="<?php the_ID(); ?>" <?php post_class(); ?>>
	<header class="post-header">
		<h3>
			<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
		</h3>
		<?php
		// Add speaker photo.
		if ( $speaker ) {
			echo '<div class="thumbnail">
				<a href="' . esc_url( get_permalink( $speaker->ID ) ) . '">' . ( has_post_thumbnail( $speaker->ID ) ? get_the_post_thumbnail( $speaker->ID, array( 80, 80 ) ) : '' ) . '<br/>' . get_the_title( $speaker->ID ) . '</a>
			</div>';
		}

		// Add hook for convention and special tracks.
		do_action( 'ghc_workshops_shortcode_after_title' );
		?>
	</header>
	<div class="post-content">
		<?php
		if ( get_the_excerpt() ) {
			the_excerpt();
		} else {
			echo '<p>More information coming soon&hellip;</p>';
		}
		?>
	</div>
</article>
