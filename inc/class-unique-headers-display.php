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

		// Add filter for post header image (uses increased priority to ensure that single post thumbnails aren't overridden by category images)
		add_filter( 'theme_mod_header_image', array( $this, 'header_image_filter' ), 20 );

	}

	/*
	 * Filter for modifying the output of get_header()
	 *
	 * @since 1.3
	 * @param    string     $url         The header image URL
	 * @return   string     $custom_url  The new custom header image URL
	 */
	public function header_image_filter( $url ) {

		// Bail out now if not in post or page
		if ( ! is_single() && ! is_page() ) {
			return $url;
		}

		// Get custom URL
		$attachment_id = Custom_Image_Meta_Box::get_attachment_id( get_the_ID(), $this->name_underscores );
		$custom_url = Custom_Image_Meta_Box::get_attachment_src( $attachment_id );

		// If custom URL doesn't exist, then output original URL
		if ( false == $custom_url ) {
			return $url;
		}

		return $custom_url;
	}

}
