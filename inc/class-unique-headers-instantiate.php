<?php
/**
 * Unique Headers Instantiation Class.
 *
 * This class is responsible for instantiating other classes and setting up hooks.
 * It handles the localization, includes necessary files, and initializes other classes
 * based on the context (admin or front-end).
 *
 * @package    Unique_Headers
 * @subpackage Instantiate
 * @author     Ryan Hellyer <ryanhellyer@gmail.com>
 * @copyright  Copyright (c), Ryan Hellyer
 * @license    http://www.gnu.org/licenses/gpl.html GPL
 * @version    1.3.10
 */

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

		// Load classes.
		require 'class-custom-image-meta-box.php';
		require 'legacy.php';

		// Loading dotorg plugin review code.
		if ( is_admin() ) {
			require 'class-dotorg-plugin-review.php';
			new DotOrg_Plugin_Review(
				array(
					'slug'       => 'unique-headers', // The plugin slug.
					'name'       => 'Unique Headers', // The plugin name.
					'time_limit' => WEEK_IN_SECONDS,  // The time limit at which notice is shown.
				)
			);
		}

		// Add hooks.
		add_action( 'plugins_loaded', array( $this, 'localization' ), 5 );
		add_action( 'init', array( $this, 'instantiate_classes' ), 20 );
	}

	/**
	 * Instantiate classes
	 *
	 * @since 1.3
	 */
	public function instantiate_classes() {

		$name = 'custom-header-image'; // This says "custom-header" instead of "unique-header" to ensure compatibilty with Justin Tadlock's Custom Header Extended plugin which originally used a different post meta key value than the Unique Headers plugin.
		$args = array(
			'name'                => $name,
			'dir_uri'             => plugin_dir_url( dirname( __FILE__ ) ) . 'assets',
			'title'               => __( 'Custom header', 'unique-headers' ),
			'set_custom_image'    => __( 'Set Custom Header Image', 'unique-headers' ),
			'remove_custom_image' => __( 'Remove Custom Header Image', 'unique-headers' ),
			'post_types'          => get_post_types( array( 'public' => true ) ),
		);

		// Add support for post-types.
		if ( is_admin() ) {
			new Custom_Image_Meta_Box( $args );
		} else {
			new Unique_Headers_Display( array( 'name' => $name ) );
		}

		// Add support for taxonomies (this conditional can be removed after the release of WordPress 4.4 - plus the taxonmies argument above can be moved into the main array then).
		if ( function_exists( 'get_term_meta' ) ) {

			// Add support for publicly available taxonomies - this can be moved into the main arguments array above after the release of WordPress 4.4.
			$args['taxonomies'] = get_taxonomies( array( 'public' => true ) );

			// Add upload text.
			$args['upload_header_image'] = __( 'Upload header image', 'unique-headers' );

			new Unique_Headers_Taxonomy_Header_Images( $args );
		}

	}

	/**
	 * Setup localization for translations
	 *
	 * @since 1.3
	 */
	public function localization() {

		// Localization.
		load_plugin_textdomain(
			'unique-headers', // Unique identifier.
			'', // Deprecated abs path.
			dirname( plugin_basename( __FILE__ ) ) . '/languages/' // Languages folder.
		);

	}

}
