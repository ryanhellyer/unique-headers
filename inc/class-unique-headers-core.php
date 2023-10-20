<?php
/**
 * Core class for Unique Headers extension.
 *
 * This class provides the core functionality for the Unique Headers extension,
 * including methods for filtering header image srcset values.
 *
 * @package    Unique_Headers
 * @subpackage Core
 * @author     Ryan Hellyer <ryanhellyer@gmail.com>
 * @copyright  Copyright (c), Ryan Hellyer
 * @license    http://www.gnu.org/licenses/gpl.html GPL
 * @version    1.7.3
 */

/**
 * Core class which is extended by other classes.
 *
 * @copyright Copyright (c), Ryan Hellyer
 * @license http://www.gnu.org/licenses/gpl.html GPL
 * @author Ryan Hellyer <ryanhellyer@gmail.com>
 * @since 1.7.3
 */
class Unique_Headers_Core {
	/**
	 * Filter for modifying the srcset values.
	 * This is required for when the srcset attribute is used within the header.
	 *
	 * @since 1.7
	 *
	 * @param string $srcset        The new array of URLs.
	 * @param array  $size_array    Array of width and height values in pixels (in that order).
	 * @param string $image_src     The 'src' of the image.
	 * @param array  $image_meta    The image meta data as returned by 'wp_get_attachment_metadata()'.
	 * @param int    $attachment_id Optional. The image attachment ID to pass to the filter. Default 0.
	 *
	 * @return string $srcset    The new array of URLs
	 */
	public function header_srcset_filter( $srcset, $size_array, $image_src, $image_meta, $attachment_id = 0 ) {

		// Bail out if not on header ID.
		if (
			! isset( get_custom_header()->attachment_id )
			||
			get_custom_header()->attachment_id !== $attachment_id
		) {
			return $srcset;
		}

		// Changing each URL within the set.
		if ( is_array( $srcset ) ) {
			foreach ( $srcset as $size => $set ) {
				$srcset[ $size ]['url'] = $this->header_image_filter( $srcset[ $size ]['url'] );
			}
		}

		return $srcset;
	}

}
