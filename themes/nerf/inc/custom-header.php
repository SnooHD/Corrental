<?php
/**
 * Custom Header functionality for Nerf
 *
 * @package WordPress
 * @subpackage Nerf
 * @since Nerf 1.0
 */

/**
 * Set up the WordPress core custom header feature.
 *
 * @uses nerf_header_style()
 */
function nerf_custom_header_setup() {
	$color_scheme        = nerf_get_color_scheme();
	$default_text_color  = trim( $color_scheme[4], '#' );

	/**
	 * Filter Nerf custom-header support arguments.
	 *
	 * @since Nerf 1.0
	 *
	 * @param array $args {
	 *     An array of custom-header support arguments.
	 *
	 *     @type string $default_text_color     Default color of the header text.
	 *     @type int    $width                  Width in pixels of the custom header image. Default 954.
	 *     @type int    $height                 Height in pixels of the custom header image. Default 1300.
	 *     @type string $wp-head-callback       Callback function used to styles the header image and text
	 *                                          displayed on the blog.
	 * }
	 */
	add_theme_support( 'custom-header', apply_filters( 'nerf_custom_header_args', array(
		'default-text-color'     => $default_text_color,
		'width'                  => 954,
		'height'                 => 1300,
		'wp-head-callback'       => 'nerf_header_style',
	) ) );
}
add_action( 'after_setup_theme', 'nerf_custom_header_setup' );

if ( ! function_exists( 'nerf_header_style' ) ) :
/**
 * Styles the header image and text displayed on the blog.
 *
 * @since Nerf 1.0
 *
 * @see nerf_custom_header_setup()
 */
function nerf_header_style() {
	return '';
}
endif; // nerf_header_style

