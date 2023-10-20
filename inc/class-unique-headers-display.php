<?php
/**
 * Custom Image Meta Box Display Class.
 *
 * This class extends the Unique_Headers_Core class and provides methods
 * for displaying and modifying custom header images on single posts, pages,
 * and the blog homepage.
 *
 * @package    Unique_Headers
 * @subpackage Display
 * @author     Ryan Hellyer <ryanhellyer@gmail.com>
 * @copyright  Copyright (c), Ryan Hellyer
 * @license    http://www.gnu.org/licenses/gpl.html GPL
 * @version    1.3
 */

/**
 * Add a custom image meta box
 *
 * @copyright Copyright (c), Ryan Hellyer
 * @license http://www.gnu.org/licenses/gpl.html GPL
 * @author Ryan Hellyer <ryanhellyer@gmail.com>
 * @since 1.3
 */
class Unique_Headers_Display extends Unique_Headers_Core {

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
	 * @param array $args The arguments.
	 * @since 1.3
	 */
	public function __construct( $args ) {

		$this->name_underscores = str_replace( '-', '_', $args['name'] );

		// Add filter for post header image (uses increased priority to ensure that single post thumbnails aren't overridden by category images).
		add_filter( 'theme_mod_header_image', array( $this, 'header_image_filter' ), 20 );
		add_filter( 'wp_calculate_image_srcset', array( $this, 'header_srcset_filter' ), 20, 5 );
		add_filter( 'theme_mod_header_image_data', array( $this, 'modify_header_image_data' ) );

	}

	/**
	 * Filter for modifying the output of get_header()
	 *
	 * @since 1.3
	 * @param string $url The header image URL.
	 * @return string $custom_url The new custom header image URL
	 */
	public function header_image_filter( $url ) {

		// Bail out now if not in post (is_single or is_page) or blog (is_home).
		if ( ! is_single() && ! is_page() && ! is_home() ) {
			return $url;
		}

		// Get current post ID (if on blog, then checks current posts page for it's ID).
		if ( is_home() ) {
			$post_id = get_option( 'page_for_posts' );
		} else {
			$post_id = get_the_ID();
		}

		// Get attachment ID.
		$attachment_id = Custom_Image_Meta_Box::get_attachment_id( $post_id, $this->name_underscores );

		// Generate new URL.
		if ( is_numeric( $attachment_id ) ) {
			$url = Custom_Image_Meta_Box::get_attachment_src( $attachment_id );
		}

		return $url;
	}

	/**
	 * Modify the header image data.
	 * Required to make TwentySixteen work.
	 * This is a replica of the method in Unique_Headers_Taxonomy_Header_Images().
	 *
	 * @param array $data The data.
	 * @return array The modified data with new attachment ID
	 */
	public function modify_header_image_data( $data ) {

		// Bail out now if not in post (is_single or is_page) or blog (is_home).
		if ( ! is_single() && ! is_page() && ! is_home() ) {
			return $data;
		}

		// Get current post ID (if on blog, then checks current posts page for it's ID).
		if ( is_home() ) {
			$post_id = get_option( 'page_for_posts' );
		} else {
			$post_id = get_the_ID();
		}

		// Get attachment ID.
		$attachment_id = Custom_Image_Meta_Box::get_attachment_id( $post_id, $this->name_underscores );

		// Set new data based on new header image attachment ID.
		if ( is_numeric( $attachment_id ) ) {

			// Create object.
			if ( null === $data ) {
				$data = (object) null;
			}

			if ( is_object( $data ) ) {
				$data->attachment_id = $attachment_id;
				$data->width         = Custom_Image_Meta_Box::get_attachment_dimensions( $attachment_id, 'width' );
				$data->height        = Custom_Image_Meta_Box::get_attachment_dimensions( $attachment_id, 'height' );
			}
		}

		return $data;
	}

}
