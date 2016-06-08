<?php

/**
 * Add a custom image meta box
 *
 * @copyright Copyright (c), Ryan Hellyer
 * @license http://www.gnu.org/licenses/gpl.html GPL
 * @author Ryan Hellyer <ryanhellyer@gmail.com>
 * @since 1.3
 */
class Unique_Headers_Display {

	/**
	 * The name of the image meta
	 *
	 * @since 1.3
	 * @access   private
	 * @var      string    $name
	 */
	private $name;

	/**
	 * The name of the image meta, with forced underscores instead of dashes
	 * This is to ensure that meta keys and filters do not use dashes.
	 *
	 * @since 1.3
	 * @access   private
	 * @var      string    $name_underscores
	 */
	private $name_underscores;

	/**
	 * Class constructor
	 * Adds methods to appropriate hooks
	 * 
	 * @since 1.3
	 */
	public function __construct( $args ) {
		$this->name_underscores    = str_replace( '-', '_', $args['name'] );

		// Add filter for post header image to load custom url with possible custom image size
		// Hook first to ensure it loads before other header-image filters
		add_filter( 'theme_mod_header_image', array( $this, 'header_image_custom_image_size' ), 1 );

		// Add filter for post header image (uses increased priority to ensure that single post thumbnails aren't overridden by category images)
		add_filter( 'theme_mod_header_image', array( $this, 'header_image_filter' ), 20 );

	}

	/*
	 * Filter for modifying image size used in url of get_header()
	 *
	 * @since 1.7
	 * @param    string     $url         The header image URL
	 * @return   string     $custom_url  The new custom header image URL
	 */
	public function header_image_custom_image_size( $url ) {

		// Default header image 
		$default_header_image = get_theme_mod( 'header_image_data' );

		// Get the attachment_id of the header-image
		$default_header_image_id = (is_object($default_header_image) && !empty($default_header_image->attachment_id)) ? $default_header_image->attachment_id : '';

		// Set default header image as url
		if ( is_numeric( $default_header_image_id ) ) {
			$url = Custom_Image_Meta_Box::get_attachment_src( $default_header_image_id );
		}

		return $url;
	}


	/*
	 * Filter for modifying the output of get_header()
	 *
	 * @since 1.3
	 * @param    string     $url         The header image URL
	 * @return   string     $custom_url  The new custom header image URL
	 */
	public function header_image_filter( $url ) {

		// Bail out now if not in post (is_single or is_page) or blog (is_home)
		if ( ! is_single() && ! is_page() && ! is_home() ) {
			return $url;
		}

		// Get current post ID (if on blog, then checks current posts page for it's ID)
		if ( is_home() ) {
			$post_id = get_option( 'page_for_posts' );
		} else {
			$post_id = get_the_ID();
		}

		// Get attachment ID
		$attachment_id = Custom_Image_Meta_Box::get_attachment_id( $post_id, $this->name_underscores );

		// Generate new URL
		if ( is_numeric( $attachment_id ) ) {
			$url = Custom_Image_Meta_Box::get_attachment_src( $attachment_id );
		}

		return $url;
	}

}
