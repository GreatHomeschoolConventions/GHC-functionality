<?php
/**
 * Speaker loop template
 *
 * @package GHC_Functionality_Plugin
 */

	$top_speakers_args = array(
		'post_type'              => 'speaker',
		'pagination'             => false,
		'posts_per_archive_page' => -1,
		'order'                  => 'ASC',
		'orderby'                => 'menu_order',
		'tax_query'              => array(
			array(
				'taxonomy' => 'ghc_speaker_category_taxonomy',
				'field'    => 'slug',
				'terms'    => 'featured',
			),
		),
	);

	$top_speakers_query = new WP_Query( $top_speakers_args );

	if ( $top_speakers_query->have_posts() ) {
		echo '<div class="speaker-container ghc-cpt container">';
		while ( $top_speakers_query->have_posts() ) {
			$top_speakers_query->the_post();
			$thumbnail_size = 'medium';
			include 'speaker-template.php';
		}
		echo '</div><!-- .speaker-container.ghc-cpt.container -->';
	} else {
		echo '<h2>More information coming soon.</h2>';
		echo do_shortcode( '[contact-form-7 id="28288" title="Signup - Convention Info"]' );
	}

	// Restore original post data.
	wp_reset_postdata();
