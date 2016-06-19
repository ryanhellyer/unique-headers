<?php

/**
 * Do not continue processing since file was called directly
 * 
 * @since 1.6.1
 * @author Ryan Hellyer <ryanhellyer@gmail.com>
 */
if ( ! defined( 'ABSPATH' ) ) {
	die( 'Eh! What you doin in here?' );
}

/*
 * Legacy fallback for old images
 *
 * Older versions of the plugin piggy backed on featured thumbnails plugins
 * These plugins were phased out due to not keeping pace with WordPress core functionality
 * This filter is here to maintain backwards compatibilty with the meta keys used by those plugins
 * Future versions of the Unique Headers plugin will phase out this legacy code
 *
 * Any old images found, are updated to use the new meta key, to improve performance and avoid 
 * this function being required in future versions.
 *
 * @since 1.3
 * @param   int    $post_id          The current post ID
 * @param   int    $attachment_id    The attachment ID
 */
function unique_header_fallback_images( $post_id ) {
	$attachment_id = '';

	// Loop through the legacy meta keys, looking for header images
	$keys = array(
		'post_custom-header_thumbnail_id',
		'page_custom-header_thumbnail_id',
		'kd_custom-header_post_id',
		'kd_custom-header_page_id',
		'_unique_header_id', // This is due to version 1.3.8 which shouldn't have been released
		'_custom_header_image', // temporary
	);

	foreach( $keys as $key ) {
		if ( '' == $attachment_id ) {
			$attachment_id = get_post_meta( $post_id, $key, true );
			if ( '' != $attachment_id ) {
				$keys_to_remove[] = $key; // Create list of keys which need deleted
			}
		}
	}

	// If no attachment found, then return false. Otherwise, convert the data to the new format and delete old keys
	if ( '' == $attachment_id ) {
		return false;
	} else {

		// Update to use new meta key
		update_post_meta( $post_id, '_custom_header_image_id', $attachment_id );

		// Delete unused meta keys
		foreach( $keys_to_remove as $key ) {
			delete_post_meta( $post_id, $key );
		}
	}

	return $attachment_id;
}
add_filter( 'unique_header_fallback_images', 'unique_header_fallback_images' );

/*
 * Quick touchup to wpdb.
 * This is a throwback to the taxonomy meta data plugin.
 * Once that plugin has been upgraded and migrated users taxonomy data over, this function will not longer be required.
 *
 * @global  object  $wpdb  The main WordPress database object
 */
function unique_header_wpdbfix() {

	// Bail out now if Taxonomy Metadata plugin not installed
	if ( ! class_exists( 'Taxonomy_Metadata' ) ) {
		return;
	}

	global $wpdb;
	$wpdb->taxonomymeta = "{$wpdb->prefix}taxonomymeta";
}
add_action( 'init', 'unique_header_wpdbfix' );
