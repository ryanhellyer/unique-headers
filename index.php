<?php
/*
Plugin Name: Unique Headers
Plugin URI: https://geek.hellyer.kiwi/plugins/unique-headers/
Description: Unique Headers
Version: 1.7.2
Author: Ryan Hellyer
Author URI: https://geek.hellyer.kiwi/
Text Domain: unique-headers
License: GPL2

------------------------------------------------------------------------
Copyright Ryan Hellyer

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA

*/



/**
 * Do not continue processing since file was called directly
 * 
 * @since 1.0
 * @author Ryan Hellyer <ryanhellyer@gmail.com>
 */
if ( ! defined( 'ABSPATH' ) ) {
	die( 'Eh! What you doin in here?' );
}


/**
 * Add a custom image meta box
 *
 * @copyright Copyright (c), Ryan Hellyer
 * @license http://www.gnu.org/licenses/gpl.html GPL
 * @author Ryan Hellyer <ryanhellyer@gmail.com>
 * @since 1.3
 */
class Unique_Headers_Instantiate {

	/**
	 * Class constructor
	 * Adds methods to appropriate hooks
	 * 
	 * @since 1.3.10
	 */
	public function __construct() {

		// Load classes
		require( 'inc/class-unique-headers-taxonomy-header-images.php' );
		require( 'inc/class-unique-headers-display.php' );
		require( 'inc/class-custom-image-meta-box.php' );
		require( 'inc/legacy.php' );

		// Loading dotorg plugin review code
		if ( is_admin() ) {
			require( 'inc/class-dotorg-plugin-review.php' );
			new DotOrg_Plugin_Review(
				array(
					'slug'        => 'unique-headers', // The plugin slug
					'name'        => 'Unique Headers', // The plugin name
					'time_limit'  => WEEK_IN_SECONDS,  // The time limit at which notice is shown
				)
			);
		}

		// Add hooks
		add_action( 'plugins_loaded', array( $this, 'localization' ), 5 );
		add_action( 'init', array( $this, 'instantiate_classes' ), 20 );
	}

	/**
	 * Instantiate classes
	 * 
	 * @since 1.3
	 */
	public function instantiate_classes() {

		$name = 'custom-header-image'; // This says "custom-header" instead of "unique-header" to ensure compatibilty with Justin Tadlock's Custom Header Extended plugin which originally used a different post meta key value than the Unique Headers plugin
		$args = array(
			'name'                => $name,
			'dir_uri'             => plugin_dir_url( __FILE__ ) . 'assets',
			'title'               => __( 'Custom header', 'unique-headers' ),
			'set_custom_image'    => __( 'Set Custom Header Image', 'unique-headers' ),
			'remove_custom_image' => __( 'Remove Custom Header Image', 'unique-headers' ),
			'post_types'          => get_post_types( array( 'public' => true ) ),
		);

		// Add support for post-types
		if ( is_admin() ) {
			new Custom_Image_Meta_Box( $args );
		} else {
			new Unique_Headers_Display( array( 'name' => $name ) );
		}

		// Add support for taxonomies (this conditional can be removed after the release of WordPress 4.4 - plus the taxonmies argument above can be moved into the main array then)
		if ( function_exists( 'get_term_meta' ) ) {

			// Add support for publicly available taxonomies - this can be moved into the main arguments array above after the release of WordPress 4.4
			$args['taxonomies'] = get_taxonomies( array( 'public'=>true ) );

			// Add upload text
			$args['upload_header_image'] = __( 'Upload header image', 'unique-headers' );

			new Unique_Header_Taxonomy_Header_Images( $args );
		}

	}

	/*
	 * Setup localization for translations
	 *
	 * @since 1.3
	 */
	public function localization() {

		// Localization
		load_plugin_textdomain(
			'unique-headers', // Unique identifier
			false, // Deprecated abs path
			dirname( plugin_basename( __FILE__ ) ) . '/languages/' // Languages folder
		);

	}

}
new Unique_Headers_Instantiate;
