<?php
/**
 * Single exhibitor template
 *
 * @package GHC_Functionality_Plugin
 */

?>
<article id="post-<?php the_ID(); ?>" <?php post_class( 'ghc-cpt item' ); ?>>
	<header class="post-header">
		<h3><a href="<?php the_field( 'exhibitor_URL' ); ?>" target="_blank" rel="noopener noreferrer"><?php the_title(); ?></a></h3>
	</header><!-- entry-header -->

	<p><?php the_content(); ?></p>
</article><!-- #post -->
