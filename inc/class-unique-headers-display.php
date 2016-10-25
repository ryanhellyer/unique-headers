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
		add_filter( 'wp_calculate_image_srcset', array( $this, 'header_srcset_filter' ), 20, 5 );

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

	/*
	 * Filter for modifying the srcset values.
	 * This is required for when the srcset attribute is used within the header.
	 *
	 * @since 1.7
	 *
	 * @param    string     $srcset        The new array of URLs
	 * @param    array      $size_array    Array of width and height values in pixels (in that order).
	 * @param    string     $image_src     The 'src' of the image.
	 * @param    array      $image_meta    The image meta data as returned by 'wp_get_attachment_metadata()'.
	 * @param    int        $attachment_id Optional. The image attachment ID to pass to the filter. Default 0.
	 *
	 * @return   string     $srcset    The new array of URLs
	 */
	public function header_srcset_filter( $srcset, $size_array, $image_src, $image_meta, $attachment_id = 0 ) {
//			return $srcset;

		// Bail out if not on header ID
		if ( $attachment_id != get_custom_header()->attachment_id ) {
			return $srcset;
		}

		// Changing each URL within the set
		if ( is_array( $srcset ) ) {
			foreach( $srcset as $size => $set ) {
				$srcset[ $size ]['url'] = $this->header_image_filter( $srcset[ $size ]['url'] );
			}
		}

		return $srcset;
	}

}
