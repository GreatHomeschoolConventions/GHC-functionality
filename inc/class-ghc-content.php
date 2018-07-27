<?php
/**
 * Content-related functions
 *
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
class GHC_Content extends GHC_Base {

	/**
	 * Kick things off
	 *
	 * @private
	 */
	public function __construct() {
		// General.
		add_filter( 'body_class', array( $this, 'add_slug_body_class' ) );

		// Convention icons.
		add_filter( 'the_excerpt', array( $this, 'archive_icons' ) );
		add_filter( 'the_content', array( $this, 'archive_icons' ) );

		// Hotels.
		add_filter( 'the_content', array( $this, 'add_hotel_single_content' ) );

		// Sponsors.
		add_filter( 'the_content', array( $this, 'add_related_sponsors' ), 15 );

		// Social media.
		add_action( 'wp_head', array( $this, 'add_opengraph_video' ), 8 );
		add_action( 'acf/save_post', array( $this, 'opengraph_video_get_meta' ), 20 );

		// Special tracks.
		add_filter( 'the_content', array( $this, 'list_special_tracks' ), 8 );
		add_filter( 'get_the_archive_description', array( $this, 'list_special_track_sponsors' ) );

		// Speakers and Special Events.
		add_filter( 'the_content', array( $this, 'show_locations' ), 11 );
		add_filter( 'the_excerpt', array( $this, 'show_locations' ), 11 );

		// Speakers.
		add_filter( 'the_content', array( $this, 'show_title_info' ), 12 );
		add_filter( 'the_content', array( $this, 'show_related_workshops' ), 11 );

		// Workshops.
		add_filter( 'the_content', array( $this, 'show_related_speaker' ), 8 );

		// Hotels.
		add_filter( 'the_content', array( $this, 'show_hotel_details' ) );
	}

	/**
	 * Show convention icons for each exhibitor
	 *
	 * @param  string $content HTML content.
	 *
	 * @return string modified content.
	 */
	public function archive_icons( string $content ) : string {
		global $post;
		$new_content = '';
		if ( 'exhibitor' === get_post_type( $post->ID ) ) {
			// TODO: don’t add icon to exhibitor archive description.
			if ( ! is_tax() ) {
				$conventions  = GHC_Conventions::get_instance();
				$new_content .= $conventions->get_icons( $post->ID );
			}
			if ( is_singular() && get_field( 'exhibitor_URL', $post->ID ) ) {
				$new_content .= '<p><a class="button" href="' . get_field( 'exhibitor_URL', $post->ID ) . '" target="_blank" rel="noopener noreferrer">Visit website&rarr;</a></p>';
			}
		}
		return $new_content . $content;
	}

	/**
	 * Add hotel info to single views.
	 *
	 * @param  string $content HTML content.
	 *
	 * @return string Modified HTML content.
	 */
	public function add_hotel_single_content( string $content ) : string {
		if ( is_singular( 'hotel' ) ) {
			// Get convention info.
			$conventions_taxonomy = get_the_terms( get_the_ID(), 'ghc_conventions_taxonomy' );
			$this_convention      = array_flip( $this->get_conventions_abbreviations() )[ $conventions_taxonomy[0]->slug ];

			// Get hotel details.
			ob_start();
			include $this->plugin_dir_path( 'templates/hotel-details.php' );

			if ( get_field( 'hotel_URL' ) && ! get_field( 'sold_out' ) ) {
				echo '<a class="button book-hotel" target="_blank" rel="noopener noreferrer" href="' . esc_url( get_field( 'hotel_URL' ) ) . '">Book Online Now</a>';
			}
			$content .= ob_get_clean();
		}

		return $content;
	}

	/**
	 * Add related sponsor(s) to posts/pages.
	 *
	 * @param  string $content HTML content.
	 *
	 * @return string modified HTML content.
	 */
	public function add_related_sponsors( string $content ) : string {
		if ( is_singular() ) {
			// Get related sponsors.
			$related_sponsors = get_field( 'related_sponsors' );

			if ( isset( $related_sponsors ) && ! empty( $related_sponsors ) ) {
				$show_content_with_logo = get_field( 'show_content_with_logo' );

				$related_sponsors_query_args = array(
					'post_type'      => 'sponsor',
					'orderby'        => 'menu_order',
					'order'          => 'ASC',
					'posts_per_page' => -1,
					'post__in'       => $related_sponsors,
				);

				$related_sponsors_query = new WP_Query( $related_sponsors_query_args );

				if ( $related_sponsors_query->have_posts() ) {
					$content .= '<div id="related-sponsors">
					<h3 class="related-sponsors">Sponsors</h3>
					<div class="sponsor-container ghc-cpt container show-content-' . $show_content_with_logo . '">';

					while ( $related_sponsors_query->have_posts() ) {
						$related_sponsors_query->the_post();
						$content .= '<div class="sponsor">
						<div class="post-thumbnail">
						<a href="' . get_permalink() . '">' . get_the_post_thumbnail( get_the_ID(), 'post-thumbnail', array( 'class' => 'sponsor' ) ) . '</a>';

						if ( $show_content_with_logo ) {
							$content .= apply_filters( 'the_content', get_the_content() );
						}

						$content .= '</div>
						</div><!-- .sponsor -->';
					}
					$content .= '</div><!-- .sponsor-container.ghc-cpt.container -->
					</div><!-- #sponsor-container.ghc-cpt.container -->';
				}

				// Restore original post data.
				wp_reset_postdata();
			}
		}
		return $content;
	}

	/**
	 * Add slug to body class.
	 *
	 * @param  array $classes Body classes.
	 *
	 * @return array Body classes.
	 */
	public function add_slug_body_class( array $classes ) : array {
		global $post;
		if ( isset( $post ) ) {
			$classes[] = $post->post_type . '-' . $post->post_name;
		}

		return $classes;
	}

	/**
	 * Add video OpenGraph data if featured_video is specified.
	 *
	 * @return  void Prints <meta> tags.
	 */
	public function add_opengraph_video() {
		$featured_video = get_field( 'featured_video' );
		if ( ! empty( $featured_video ) ) {
			$video_id                 = $this->get_video_id( $featured_video );
			$featured_video_thumbnail = get_post_meta( get_the_ID(), 'featured_video_thumbnail', true );

			// Check for old-style meta.
			// FUTURE: remove after featured_video_meta no longer exists in db.
			if ( empty( $featured_video_thumbnail ) ) {
				$featured_video_meta = get_post_meta( get_the_ID(), 'featured_video_meta', true );
				if ( is_object( $featured_video_meta ) ) {
					$featured_video_thumbnail = $featured_video_meta->snippet->thumbnails->maxres;
				}

				// Delete old meta.
				update_post_meta( get_the_ID(), 'featured_video_thumbnail', $featured_video_thumbnail );
				delete_post_meta( get_the_ID(), 'featured_video_meta' );
			}

			// Add video tags.
			if ( isset( $featured_video ) && is_object( $featured_video_thumbnail ) ) {
				echo '<meta property="og:video" content="' . esc_url( $featured_video ) . '" />';
				echo strpos( $featured_video, 'https' ) !== false ? '<meta property="og:video:secure_url" content="' . esc_url( $featured_video ) . '" />' : '';
				echo '<meta property="og:video:width" content="' . esc_attr( $featured_video_thumbnail->width ) . '" />';
				echo '<meta property="og:video:height" content="' . esc_attr( $featured_video_thumbnail->height ) . '" />';

				// Add placeholder image.
				echo '<meta property="og:image" content="' . esc_url( $featured_video_thumbnail->url ) . '" />';
			}
		}
	}

	/**
	 * Save featured_video thumbnail to postmeta
	 *
	 * @param  int|string $post_id WP post ID.
	 *
	 * @return bool Whether postmeta was succesfully updated.
	 */
	public function opengraph_video_get_meta( $post_id ) : bool {
		if ( ! empty( get_field( 'featured_video' ) ) ) {
			$video_id = $this->get_video_id( esc_url( get_field( 'featured_video' ) ) );

			// Set up request.
			$youtube_api_url = 'https://www.googleapis.com/youtube/v3/videos?part=snippet&id=' . $video_id . '&key=' . get_option( 'options_api_key' );
			$response_args   = array(
				'headers' => 'Referer: ' . site_url(),
			);
			$response        = wp_remote_get( $youtube_api_url, $response_args );

			// Parse response.
			$youtube_meta      = json_decode( $response['body'] );
			$youtube_thumbnail = $youtube_meta->items[0]->snippet->thumbnails->maxres;

			// Save post meta.
			return update_post_meta( $post_id, 'featured_video_thumbnail', $youtube_thumbnail );
		} else {
			// Delete post meta if no video is specified.
			return delete_post_meta( $post_id, 'featured_video_thumbnail' );
		}
	}

	/**
	 * Retrieve ID from YouTube URL.
	 *
	 * @param  string $video_url Public URL of video.
	 *
	 * @return string video ID.
	 */
	public function get_video_id( string $video_url ) : string {
		if ( strpos( $video_url, '//youtu.be' ) !== false ) {
			$video_id = basename( wp_parse_url( $video_url, PHP_URL_PATH ) );
		} elseif ( strpos( $video_url, 'youtube.com' ) !== false ) {
			parse_str( wp_parse_url( $video_url, PHP_URL_QUERY ), $video_array );
			$video_id = $video_array['v'];
		}

		return $video_id;
	}

	/**
	 * Add special track info to speakers/workshops.
	 *
	 * @param  string $content HTML content.
	 *
	 * @return string modified HTML content.
	 */
	public function list_special_tracks( string $content ) : string {
		$intro_content = '';

		if ( is_singular( array( 'speaker', 'workshop' ) ) ) {
			$special_tracks       = wp_get_post_terms( get_the_ID(), 'ghc_special_tracks_taxonomy' );
			$special_tracks_count = count( $special_tracks );

			if ( $special_tracks_count > 0 ) {
				// Set up content.
				$track_output = '';
				$track_index  = 1;
				foreach ( $special_tracks as $special_track ) {
					$track_output .= '<a href="' . esc_url( get_term_link( $special_track->term_id, 'ghc_special_tracks_taxonomy' ) ) . '">' . esc_attr( $special_track->name ) . '</a> track';

					// Check for sponsors.
					$track_output .= $this->get_special_track_related_sponsor_names( $special_track->term_id );

					if ( $special_tracks_count > 2 ) {
						$track_output .= ', ';
						if ( $track_index === $special_tracks_count ) {
							$track_output .= ' and ';
						}
					} elseif ( 2 === $special_tracks_count && 2 !== $track_index ) {
						$track_output .= ' and ';
					}
					$track_index++;
				}

				// Output content.
				$intro_content = '<h4 class="related-special-tracks">Special Tracks</h2>';

				if ( 'speaker' === get_post_type() ) {
					// Translators: %1$s: speaker name(s); %2$s special track name(s).
					$intro_content = sprintf(
						'<p>We are honored to have %1$s participating in this year&rsquo;s %2$s.</p>',
						get_the_title(),
						$track_output
					);
				} elseif ( 'workshop' === get_post_type() ) {
					// Translators: %1$s: speaker name(s); %2$s special track name(s).
					$intro_content = sprintf(
						'<p>%1$s is part of this year&rsquo;s %2$s.</p>',
						get_the_title(),
						$track_output
					);
				}
			}
		}

		return $intro_content . $content;
	}

	/**
	 * Get special track related sponsor name(s) and link(s).
	 *
	 * @param  int    $term_id ghc_special_track term ID.
	 * @param  string $context Either “inline” or “standalone”.
	 *
	 * @return string HTML output with sponsor name(s) and link(s).
	 */
	private function get_special_track_related_sponsor_names( int $term_id, string $context = 'inline' ) : string {
		$track_output = '';
		$sponsors     = get_field( 'related_sponsors', 'ghc_special_tracks_taxonomy_' . $term_id );
		if ( $sponsors ) {
			$sponsor_index = 1;
			if ( 'inline' === $context ) {
				$track_output .= ' <small>(sponsored by ';
			} elseif ( 'standalone' === $context ) {
				$track_output .= '<p>This track is sponsored by ';
			}
			foreach ( $sponsors as $sponsor ) {
				$track_output .= '<a href="' . esc_url( get_permalink( $sponsor ) ) . '">' . wp_kses_post( get_the_title( $sponsor ) ) . '</a>';
				if ( count( $sponsors ) > 2 ) {
					$track_output .= ', ';
					if ( count( $sponsors ) === $index ) {
						$track_output .= ' and ';
					}
				} elseif ( 2 === count( $sponsors ) && 2 !== $sponsor_index ) {
					$track_output .= ' and ';
				}
				$sponsor_index++;
			}
			if ( 'inline' === $context ) {
				$track_output .= ')</small>';
			} elseif ( 'standalone' === $context ) {
				$track_output .= '.</p>';
				$track_output .= '<div id="related-sponsors">
					<div class="sponsor-container ghc-cpt container">';
				foreach ( $sponsors as $sponsor ) {
					$track_output .= '<div class="sponsor">
						<div class="post-thumbnail">
						<a href="' . esc_url( get_permalink( $sponsor ) ) . '">' . wp_kses_post( get_the_post_thumbnail( $sponsor, 'post-thumbnail', array( 'class' => 'sponsor' ) ) ) . '</a>
						</div>
						</div><!-- .sponsor -->';
				}
				$track_output .= '</div><!-- .sponsor-container.ghc-cpt.container -->
				</div><!-- #sponsor-container.ghc-cpt.container -->';
			}
		}

		return $track_output;
	}

	/**
	 * Add sponsor info to special track archive.
	 *
	 * @param  string $content HTML archive description.
	 *
	 * @return string HTML archive description with sponsor(s) name(s) and link(s) appended.
	 */
	public function list_special_track_sponsors( string $content ) : string {
		$content .= $this->get_special_track_related_sponsor_names( get_queried_object_id(), 'standalone' );
		return $content;
	}

	/**
	 * Add speaker location info to each speaker/workshop.
	 *
	 * @param  string $content HTML content or excerpt.
	 *
	 * @return string modified HTML content or excerpt.
	 */
	public function show_locations( string $content ) : string {
		if ( is_singular( array( 'special_event', 'speaker', 'workshop' ) ) || is_tax( 'ghc_special_tracks_taxonomy' ) ) {
			$post_terms = get_the_terms( get_the_ID(), 'ghc_conventions_taxonomy' );

			if ( $post_terms ) {
				$conventions = GHC_Conventions::get_instance();
				$content     = '<p class="conventions">' . $conventions->get_icons( $post_terms ) . '</p>' . $content;
			}
		}

		return $content;
	}

	/**
	 * Show title and subtitle on speaker bio pages.
	 *
	 * @param  string $content HTML content.
	 *
	 * @return string modified HTML content.
	 */
	public function show_title_info( string $content ) : string {
		if ( is_singular( 'speaker' ) ) {
			$speaker = GHC_Speakers::get_instance();
			$content = $speaker->get_short_bio( get_the_ID() ) . $content;
		}
		return $content;
	}

	/**
	 * Add workshops list to each speaker and related workshops to each workshop.
	 *
	 * @param  string $content HTML content.
	 *
	 * @return string modified HTML content.
	 */
	public function show_related_workshops( string $content ) : string {
		$workshop_content = '';

		if ( is_singular( array( 'speaker', 'workshop' ) ) ) {
			$this_post_type = get_post_type();

			if ( 'speaker' === $this_post_type ) {
				$speaker_id        = get_the_ID();
				$workshop_content .= '<p><a class="button" href="' . esc_url( get_home_url() ) . '/speakers/">All Featured Speakers</a></p>';
			} elseif ( 'workshop' === $this_post_type ) {
				$related_speakers = get_field( 'speaker' );
				if ( count( $related_speakers ) === 1 ) {
					$speaker_id = $related_speakers[0]->ID;
				}
			}

			$related_workshops = get_field( 'related_workshops', $speaker_id );

			// Remove this workshop from the array since `post__in` causes `post__not_in` to be ignored.
			$key = array_search( get_the_ID(), $related_workshops, true );
			if ( is_array( $related_workshops ) && 'workshop' === $this_post_type && false !== $key ) {
				unset( $related_workshops[ $key ] );
			}

			if ( is_int( $speaker_id ) && count( $related_workshops ) > 0 ) {
				$related_workshops_args = array(
					'post_type'      => 'workshop',
					'posts_per_page' => -1,
					'orderby'        => 'title',
					'order'          => 'ASC',
					'post__in'       => $related_workshops,
				);

				if ( 'workshop' === $this_post_type ) {
					$related_workshops_args['post__not_in'] = array( get_the_ID() );
				}
				$related_workshops = new WP_Query( $related_workshops_args );

				if ( $related_workshops->have_posts() ) {
					$workshop_content .= '<div class="related-workshops" id="workshops"><h2>' . ( 'workshop' === $this_post_type ? 'Other ' : '' ) . 'Workshops by ' . ( 'speaker' === $this_post_type ? get_the_title() : $related_speakers[0]->post_title ) . '</h2>';

					if ( 'speaker' === $this_post_type ) {
						while ( $related_workshops->have_posts() ) {
							$related_workshops->the_post();
							$workshop_content .= '<h3><a href="' . esc_url( get_permalink() ) . '">' . wp_kses_post( get_the_title() ) . '</a></h3>
							<p>' . apply_filters( 'wpautop', get_the_content() ) . '</p>';
						}
					} else {
						$workshop_content .= '<ul>';
						while ( $related_workshops->have_posts() ) {
							$related_workshops->the_post();
							$workshop_content .= '<li><a href="' . esc_url( get_permalink() ) . '">' . wp_kses_post( get_the_title() ) . '</a></li>';
						}
						$workshop_content .= '</ul>';
					}
				}

				wp_reset_postdata();

				$workshop_content .= '</div>';
			}
		}

		return $content . $workshop_content;
	}

	/**
	 * Add related speaker(s) to workshops.
	 *
	 * @param  string $content post content.
	 *
	 * @return string post content with speaker info added.
	 */
	public function show_related_speaker( string $content ) : string {
		$speaker_content = '';

		if ( is_singular( 'workshop' ) ) {
			$this_speaker = get_post_meta( get_the_ID(), 'speaker', true );

			$speaker_content .= do_shortcode( '[speaker_info postid="' . implode( ',', $this_speaker ) . '" ' . ( count( $this_speaker ) === 1 ? 'align="right"' : '' ) . ']' );
		}

		return $speaker_content . $content;
	}

	/**
	 * Add hotel details to single hotel views.
	 *
	 * @param  string $content post content.
	 *
	 * @return string post content with hotel info appended.
	 */
	public function show_hotel_details( string $content ) : string {
		if ( 'hotel' === get_post_type() ) {
			$conventions_taxonomy = get_the_terms( get_the_ID(), 'ghc_conventions_taxonomy' );
			$this_convention      = array_flip( $this->get_conventions_abbreviations() )[ $conventions_taxonomy[0]->slug ];

			ob_start();
			include $this->plugin_dir_path( 'templates/hotel-details.php' );
			$content .= ob_get_clean();
		}

		return $content;
	}
}

new GHC_Content();
