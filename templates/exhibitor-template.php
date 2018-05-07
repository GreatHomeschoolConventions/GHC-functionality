<?php
/**
 * Single exhibitor template
 *
 * @package GHC_Functionality_Plugin
 */

// Ensure that URLs have a protocol prefix.
$exhibitor_url = get_field( 'exhibitor_URL' );
if ( strpos( $exhibitor_url, 'http' ) !== 0 ) {
	$exhibitor_url = 'http://' . $exhibitor_url;
}

?>
<article id="post-<?php the_ID(); ?>" <?php post_class( 'ghc-cpt item' ); ?>>
	<header class="post-header">
		<h3><a href="<?php echo esc_url( $exhibitor_url ); ?>" target="_blank" rel="noopener noreferrer"><?php the_title(); ?></a></h3>
	</header><!-- entry-header -->

	<p><?php the_content(); ?></p>
</article><!-- #post -->
