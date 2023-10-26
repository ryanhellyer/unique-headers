<?php
/**
Plugin Name: Unique Headers
Plugin URI: https://geek.hellyer.kiwi/plugins/unique-headers/
Description: Unique Headers
Version: 1.9.3
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

@package unique-headers
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
 * Autoload the classes.
 * Includes the classes, and automatically instantiates them via spl_autoload_register().
 *
 * @param string $class The class being instantiated.
 */
function autoload_unique_headers( $class ) {

	// Bail out if not loading a Media Manager class.
	if ( 'Unique_Headers_' !== substr( $class, 0, 15 ) ) {
		return;
	}

	// Convert from the class name, to the classes file name.
	$file_data = strtolower( $class );
	$file_data = str_replace( '_', '-', $file_data );
	$file_name = 'class-' . $file_data . '.php';

	// Get the classes file path.
	$dir  = dirname( __FILE__ );
	$path = $dir . '/inc/' . $file_name;

	// Include the class (spl_autoload_register will automatically instantiate it for us).
	require $path;
}
spl_autoload_register( 'autoload_unique_headers' );

new Unique_Headers_Instantiate();
