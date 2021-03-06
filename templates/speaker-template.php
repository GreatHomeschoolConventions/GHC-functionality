<?php
/**
 * Single speaker template
 *
 * @author AndrewRMinion Design
 * @package GHC_Functionality_Plugin
 */

$conventions = GHC_Conventions::get_instance();
$speakers    = GHC_Speakers::get_instance();

if ( ! is_array( $shortcode_attributes ) ) {
	$shortcode_attributes['show'] = 'image,conventions,name,bio,workshops';
	if ( ! isset( $thumbnail_size ) ) {
		if ( 'speaker' === get_post_type() ) {
			$thumbnail_size = 'medium';
		} elseif ( 'special_event' === get_post_type() ) {
			$thumbnail_size = 'special-event-large';
		}
	}
}
?><article id="post-<?php the_ID(); ?>" <?php post_class( 'ghc-cpt item contains-' . str_replace( ',', ' contains-', esc_attr( $shortcode_attributes['show'] ) ) ); ?>>
	<?php if ( has_post_thumbnail() && strpos( $shortcode_attributes['show'], 'image' ) !== false ) { ?>
		<div class="thumbnail">
			<a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>">
				<?php the_post_thumbnail( $thumbnail_size ); ?>
			</a>
		</div>
	<?php } ?>

	<?php if ( strpos( $shortcode_attributes['show'], 'name' ) !== false || strpos( $shortcode_attributes['show'], 'title' ) !== false || strpos( $shortcode_attributes['show'], 'conventions' ) !== false || strpos( $shortcode_attributes['show'], 'related_speakers' ) !== false ) { ?>
		<header class="post-header">
			<?php if ( strpos( $shortcode_attributes['show'], 'name' ) !== false || strpos( $shortcode_attributes['show'], 'title' ) !== false ) { ?>
				<h3 class="post-title"><a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>"><?php the_title(); ?></a></h3>
			<?php } ?>
			<?php if ( strpos( $shortcode_attributes['show'], 'related_speakers' ) !== false ) { ?>
				<div class="related-speakers">
					<?php
					foreach ( get_field( 'related_speakers' ) as $speaker ) {
						?>
						<a href="<?php echo esc_url( get_the_permalink( $speaker ) ); ?>" title="<?php echo wp_kses_post( get_the_title( $speaker ) ); ?>"><?php echo wp_kses_post( get_the_post_thumbnail( $speaker, 'square-miniscule', array( 'class' => 'speaker miniscule' ) ) ); ?></a>
						<?php
					}
					?>
				</div>
			<?php } ?>
			<?php if ( strpos( $shortcode_attributes['show'], 'conventions' ) !== false ) { ?>
				<div class="conventions-attending">
					<?php echo wp_kses_post( $conventions->get_icons( get_the_terms( get_the_ID(), 'ghc_conventions_taxonomy' ) ) ); ?>
				</div>
			<?php } ?>
			<?php if ( strpos( $shortcode_attributes['show'], 'bio' ) !== false ) { ?>
				<?php echo wp_kses_post( $speakers->get_short_bio( get_the_ID() ) ); ?>
			<?php } ?>
		</header>
		<!-- entry-header -->

		<?php if ( strpos( $shortcode_attributes['show'], 'excerpt' ) !== false ) { ?>
			<div class="excerpt"><?php the_excerpt(); ?></div>
		<?php } ?>

		<p class="text-center"><a class="button" href="<?php the_permalink(); ?>" title="<?php the_title(); ?>">Read More</a></p>

		<?php if ( strpos( $shortcode_attributes['show'], 'workshops' ) !== false && get_field( 'related_workshops' ) !== null ) { ?>
			<p class="text-center"><a class="button" href="<?php the_permalink(); ?>#workshops">Workshops</a></p>
		<?php } ?>
	<?php } ?>
</article><!-- .speaker -->
