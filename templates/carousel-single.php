<?php
/**
 * Single carousel item template
 *
 * @author AndrewRMinion Design
 * @package GHC_Functionality_Plugin
 */

$conventions = GHC_Conventions::get_instance();
$speakers    = GHC_Speakers::get_instance();

if ( ! is_array( $shortcode_attributes ) ) {
	$shortcode_attributes['show'] = 'image,title';
	if ( ! isset( $thumbnail_size ) ) {
		if ( 'speaker' === get_post_type() ) {
			$thumbnail_size = 'medium';
		} elseif ( 'special_event' === get_post_type() ) {
			$thumbnail_size = 'special-event-large';
		}
	}
}
$show = explode( ',', $shortcode_attributes['show'] );

?><article id="post-<?php the_ID(); ?>" <?php post_class( 'ghc-carousel item contains-' . str_replace( ',', ' contains-', esc_attr( $shortcode_attributes['show'] ) ) ); ?>>
	<?php if ( has_post_thumbnail() && array_search( 'image', $show, true ) !== false ) { ?>
		<div class="speaker-thumbnail">
			<a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>">
				<?php the_post_thumbnail( $thumbnail_size ); ?>
			</a>
		</div>
	<?php } ?>

	<?php if ( array_search( 'title', $show, true ) !== false || array_search( 'conventions', $show, true ) !== false ) { ?>
		<header class="post-header">
			<?php if ( array_search( 'title', $show, true ) !== false ) { ?>
				<h2 class="post-title"><a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>"><?php the_title(); ?></a></h2>
			<?php } ?>
			<?php if ( array_search( 'conventions', $show, true ) !== false ) { ?>
				<div class="conventions-attending">
					<?php echo wp_kses_post( $conventions->get_icons( get_the_ID() ) ); ?>
				</div>
			<?php } ?>

			<?php if ( array_search( 'bio', $show, true ) !== false ) { ?>
				<?php echo wp_kses_post( $speakers->get_short_bio( get_the_ID() ) ); ?>
			<?php } ?>
		</header>
		<!-- .post-header -->

		<?php if ( array_search( 'excerpt', $show, true ) !== false ) { ?>
			<div class="excerpt"><?php the_excerpt(); ?></div>
		<?php } ?>
	<?php } ?>
</article>
