
/**
 * Callback function for the 'click' event of the 'Set Footer Image'
 * anchor in its meta box.
 *
 * Displays the media uploader for selecting an image.
 *
 * @param    object    $    A reference to the jQuery object
 * @since    0.1.0
 */
function renderMediaUploader( $ ) {
	'use strict';

	var file_frame, image_data, json;

	/**
	 * If an instance of file_frame already exists, then we can open it
	 * rather than creating a new instance.
	 */
	if ( undefined !== file_frame ) {

		file_frame.open();
		return;

	}

	/**
	 * If we're this far, then an instance does not exist, so we need to
	 * create our own.
	 *
	 * We're using the 'select' frame to avoid conflicts with the
	 * shortcode UI plugin, which expects wpActiveEditor to be set
	 * (only available when opening from TinyMCE).
	 */
	file_frame = wp.media.frames.file_frame = wp.media({
		frame:    'select',
		multiple: false
	});

	file_frame.on( 'select', function() {

		// Read the JSON data returned from the Media Uploader
		json = file_frame.state().get( 'selection' ).first().toJSON();

		// First, make sure that we have the URL of an image to display
		if ( 0 > $.trim( json.url.length ) ) {
			return;
		}

		// After that, set the properties of the image and display it
		$( '#'+custom_meta_image_name+'-container' )
			.children( 'img' )
				.attr( 'src', json.url )
				.attr( 'alt', json.caption )
				.attr( 'title', json.title )
				.show()
			.parent()
			.removeClass( 'hidden' );

		// Next, hide the anchor responsible for allowing the user to select an image
		$( '#'+custom_meta_image_name+'-container' )
			.prev()
			.hide();

		// Display the anchor for the removing the featured image
		$( '#'+custom_meta_image_name+'-container' )
			.next()
			.show();

		// Store the image's information into the meta data fields
		$( '#'+custom_meta_image_name+'-id' ).val( json.id );

	});

	// Now display the actual file_frame
	file_frame.open();

}

/**
 * Callback function for the 'click' event of the 'Remove Footer Image'
 * anchor in its meta box.
 *
 * Resets the meta box by hiding the image and by hiding the 'Remove
 * Footer Image' container.
 *
 * @param    object    $    A reference to the jQuery object
 * @since    0.2.0
 */
function resetUploadForm( $ ) {
	'use strict';

	// First, we'll hide the image
	$( '#'+custom_meta_image_name+'-container' )
		.children( 'img' )
		.hide();

	// Then display the previous container
	$( '#'+custom_meta_image_name+'-container' )
		.prev()
		.show();

	// We add the 'hidden' class back to this anchor's parent
	$( '#'+custom_meta_image_name+'-container' )
		.next()
		.hide()
		.addClass( 'hidden' );

	// Finally, we reset the meta data input fields
	$( '#'+custom_meta_image_name+'-info' )
		.children()
		.val( '' );

}

/**
 * Checks to see if the input field for the thumbnail source has a value.
 * If so, then the image and the 'Remove featured image' anchor are displayed.
 *
 * Otherwise, the standard anchor is rendered.
 *
 * @param    object    $    A reference to the jQuery object
 * @since    1.0.0
 */
function renderFeaturedImage( $ ) {

	/* If a thumbnail URL has been associated with this image
	 * Then we need to display the image and the reset link.
	 */
	if ( '' !== $.trim ( $( '#'+custom_meta_image_name+'-id' ).val() ) ) {

		$( '#'+custom_meta_image_name+'-container' ).removeClass( 'hidden' );

		$( '#set-'+custom_meta_image_name+'-thumbnail' )
			.parent()
			.hide();

		$( '#remove-'+custom_meta_image_name+'-thumbnail' )
			.parent()
			.removeClass( 'hidden' );

	}

}

(function( $ ) {
	'use strict';

	$(function() {

		renderFeaturedImage( $ );

		$( '#set-'+custom_meta_image_name+'-thumbnail' ).on( 'click', function( evt ) {

			// Stop the anchor's default behavior
			evt.preventDefault();

			// Display the media uploader
			renderMediaUploader( $ );

		});

		$( '#remove-'+custom_meta_image_name+'-thumbnail' ).on( 'click', function( evt ) {

			// Stop the anchor's default behavior
			evt.preventDefault();

			// Remove the image, toggle the anchors
			resetUploadForm( $ );

		});

	});

})( jQuery );