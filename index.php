<?php
/*
Plugin Name: Unique Headers
Plugin URI: http://geek.ryanhellyer.net/
Description: Unique Headers
Version: 1.3
Author: Ryan Hellyer
Author URI: http://geek.ryanhellyer.net/

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
 * Load classes
 * 
 * @since 1.0
 * @author Ryan Hellyer <ryanhellyer@gmail.com>
 */
require( 'inc/class-unique-headers-taxonomy-header-images.php' );
require( 'inc/class-unique-headers-display.php' );
require( 'inc/class-custom-image-meta-box.php' );
require( 'inc/legacy.php' );


/**
 * Instantiate classes
 * 
 * @since 1.3
 * @author Ryan Hellyer <ryanhellyer@gmail.com>
 */
function unique_headers_instantiate_classes() {
//die('this does not work with custom-header, but works with every single other string. But i need this to work for compatibilty iwth Tadlocks plugin :/');
	$name = 'custom-header-image'; // THis says "custom-header" instead of "unique-header" to ensure compatibilty with Justin Tadlock's Custom Header Extended plugin which originally used a different post meta key value than the Unique Headers plugin
	$args = array(
		'name'                => $name,
		'dir_uri'             => plugin_dir_url( __FILE__ ) . 'assets',
		'title'               => __( 'Custom header', 'unique-headers' ),
		'set_custom_image'    => __( 'Set Custom Header Image', 'unique-headers' ),
		'remove_custom_image' => __( 'Remove Custom Header Image', 'unique-headers' ),
		'post_types'          => apply_filters( 'unique_headers_post_types', array( 'post', 'page' ) ),
	);

	// Add support for post-types
	if ( is_admin() ) {
		new Custom_Image_Meta_Box( $args );
	} else {
		new Unique_Headers_Display( array( 'name' => $name ) );
	}

	// Add support for taxonomies
	if ( function_exists( 'get_term_meta' ) ) {
		$args['taxonomies']          = apply_filters( 'unique_headers_taxonomies', array( 'category', 'post_tag' ) );
		$args['upload_header_image'] = __( 'Upload header image', 'unique-headers' );

		new Unique_Header_Taxonomy_Header_Images( $args );
	}

}
add_action( 'plugins_loaded', 'unique_headers_instantiate_classes' );

/*
 * Setup localization for translations
 *
 * @since 1.3
 * @author Ryan Hellyer <ryanhellyer@gmail.com>
 */
function unique_headers_localization() {

	// Localization
	load_plugin_textdomain(
		'unique-headers', // Unique identifier
		false, // Deprecated abs path
		dirname( plugin_basename( __FILE__ ) ) . '/languages/' // Languages folder
	);

}
unique_headers_localization();
