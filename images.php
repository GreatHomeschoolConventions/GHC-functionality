<?php

defined( 'ABSPATH' ) or die( 'No access allowed' );

/**
 * Add custom image sizes
 */
function ghc_custom_image_sizes() {
    add_image_size( 'thumbnail-no-crop', 140, 140, false );
    add_image_size( 'pinterest-thumb', 173, 345, true );
    add_image_size( 'pinterest-medium', 346, 690, true );
    add_image_size( 'square-small', 400, 400, true );
    add_image_size( 'square-medium', 600, 600, true );
    add_image_size( 'square-large', 900, 900, true );
    add_image_size( 'small-grid-size-medium', 600, 450, true );
    add_image_size( 'small-grid-size-large', 800, 600, true );
}
add_action( 'after_setup_theme', 'ghc_custom_image_sizes' );

/**
 * Add custom image size names
 * @param  array $sizes array of named image sizes
 * @return array modified array of named image sizes
 */
function ghc_custom_image_sizes_names( $sizes ) {
    return array_merge( $sizes, array(
        'thumbnail-no-crop'         => 'Thumbnail (no crop)',
        'square-small'              => 'Square',
        'square-medium'             => 'Square',
        'square-large'              => 'Square',
        'small-grid-size-medium'    => 'Grid',
        'small-grid-size-large'     => 'Grid',
    ));
}
add_filter( 'image_size_names_choose', 'ghc_custom_image_sizes_names' );
