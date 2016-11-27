<?php

/**
 * Add taxonomy specific header images
 *
 * @copyright Copyright (c), Ryan Hellyer
 * @license http://www.gnu.org/licenses/gpl.html GPL
 * @author Ryan Hellyer <ryanhellyer@gmail.com>
 * @since 1.0
 */
class Unique_Header_Taxonomy_Header_Images {

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
	 * The title of the meta box
	 *
	 * @since 1.3
	 * @access   private
	 * @var      string    $title
	 */
	private $title;

	/**
	 * The set custom image text
	 *
	 * @since 1.3
	 * @access   private
	 * @var      string    $set_custom_image
	 */
	private $set_custom_image;

	/**
	 * The remove custom image text
	 *
	 * @since 1.3
	 * @access   private
	 * @var      string    $remove_custom_image
	 */
	private $remove_custom_image;

	/**
	 * List of taxonomies to add meta boxes to
	 *
	 * @since 1.3
	 * @access   private
	 * @var      string    $taxonomies
	 */
	private $taxonomies;

	/**
	 * The text for uploading header images
	 *
	 * @since 1.3
	 * @access   private
	 * @var      string    $upload_header_image
	 */
	private $upload_header_image;

	/**
	 * Class constructor
	 * 
	 * Adds methods to appropriate hooks
	 * 
	 * @author Ryan Hellyer <ryanhellyer@gmail.com>
	 * @since 1.0
	 */
	public function __construct( $args ) {

		$this->name                = $args['name'];
		$this->name_underscores    = str_replace( '-', '_', $args['name'] );
		$this->title               = $args['title'];
		$this->set_custom_image    = $args['set_custom_image'];
		$this->remove_custom_image = $args['remove_custom_image'];
		$this->taxonomies          = $args['taxonomies'];
		$this->upload_header_image = $args['upload_header_image'];

		add_action( 'admin_init',             array( $this, 'add_fields' ) );
		add_filter( 'theme_mod_header_image', array( $this, 'header_image_filter' ), 5 );
	}

	/**
	 * Adding fields to taxonomy pages.
	 *
	 * @author Ryan Hellyer <ryanhellyer@gmail.com>
	 * @since 1.0
	 */
	public function add_fields() {

		// Add actions for administration pages
		if ( is_admin() ) {

			// Add hooks for each taxonomy
			foreach( $this->taxonomies as $taxonomy ) {
				add_action( $taxonomy . '_edit_form_fields', array( $this, 'extra_fields' ), 1 );
				add_action( 'edit_' . $taxonomy,             array( $this, 'storing_taxonomy_data' ) );
			}

 		}

	}

	/*
	 * Filter for modifying the output of get_header()
	 *
	 * @author Ryan Hellyer <ryanhellyer@gmail.com>
	 * @since 1.0
	 */
	public function header_image_filter( $url ) {

		/* We need to grab the current taxonomy ID
		 * Unfortunately, categories and post tags behave different, so we
		 * are checking for their presense and processing them slightly 
		 * differently.
		 */
		if ( is_category() ) {
			$tax_ID = get_query_var( 'cat' );
		} elseif( is_tag() || is_tag() || is_tax() ) {

			// Now we can loop through all taxonomies
			foreach( $this->taxonomies as $taxonomy ) {

				// We need to ignore categories since we have already processed them				
				if ( 'category' != $taxonomy ) {

					// Tags behave oddly, so need to use a different query var accordingly
					if ( 'post_tag' == $taxonomy ) {
						$tax_info = get_query_var( 'tag' );
					} else {
						$tax_info = get_query_var( $taxonomy );
					}

					$tax = get_term_by( 'slug', $tax_info, $taxonomy );
					if ( isset( $tax->term_id ) ) {
						$tax_ID = $tax->term_id;
					}
				}
			}
		}


		// Bail out now if no term set
		if ( ! isset( $tax_ID ) ) {
			return $url;
		}

		// Grab stored taxonomy header
		$attachment_id = get_term_meta( $tax_ID, 'taxonomy-header-image', true );

		// Grab attachment's SRC if we have an ID, otherwise fallback to legacy support for the older URL system from earlier versions of the plugin
		if ( is_numeric( $attachment_id ) ) {
			$new_url = Custom_Image_Meta_Box::get_attachment_src( $attachment_id );
		} else {

			// Falling back to taxonomy meta plugin functionality
			$attachment_id = get_metadata( 'taxonomy', $tax_ID, 'taxonomy-header-image', true );

			if ( is_numeric( $attachment_id ) ) {
				$new_url = Custom_Image_Meta_Box::get_attachment_src( $attachment_id );
			} else {
				$new_url = $attachment_id; // Defaulting back to really old version of the plugin which used URL's insteaded of attachment ID's
			}

		}

		// Only use new URL if it isn't blank ... 
		if ( '' != $new_url ) {
			$url = esc_url( $new_url );
		}

		return $url; // Do not escape here, as WordPress sometimes assigns non-URLs for the header image
	}

	/**
	 * Storing the taxonomy header image selection
	 * 
	 * @author Ryan Hellyer <ryanhellyer@gmail.com>
	 * @since 1.0
	 */
	public function storing_taxonomy_data() {

		// Bail out now if POST vars not set
		if ( ! isset( $_POST[$this->name . '-nonce'] ) || ! isset( $_POST[$this->name . '-id'] ) ) {
			return;
		}

		// Bail out now if nonce doesn't verify
		if ( ! wp_verify_nonce( $_POST[$this->name . '-nonce'], $this->name ) ) {
			return;
		}

		// Sanitize inputs
		$tag_ID = absint( $_POST['tag_ID'] );
		$attachment_id = $_POST[$this->name . '-id'];
		if ( is_numeric( $attachment_id ) ) {
			$attachment_id = absint( $attachment_id );
		} elseif ( is_string( $attachment_id ) ) {
			$attachment_id = $this->get_attachment_id_from_url( $attachment_id );

			// If still a string, then give up and treat it as a URL
			if ( ! is_numeric( $attachment_id ) ) {
				$attachment_id = esc_url( $attachment_id );
			}
		}

		// Save the term meta data
		update_term_meta( $tag_ID, 'taxonomy-header-image', $attachment_id );
	}

	/*
	 * Legacy method
	 * Used to obtain the attachment ID, if a URL is detected
	 * 
	 * URL's were used in earlier versions of the plugin. This was upgraded
	 * for version 1.3 to utilize attachment ID's instead. These older URL's
	 * will be supported into the forseeable future though, since it is not
	 * possible to access ALL URL's as attachment ID's.
	 *
	 * The code for this method was modified from code originally written by Phillip Newcomer
	 * https://philipnewcomer.net/2012/11/get-the-attachment-id-from-an-image-url-in-wordpress/
	 *
	 * @global   object    $wpdb            The WordPress database object
	 * @param    string    $url             The URL we are trying to find the attachment ID for
	 * @return   int       $attachment_id   The attachment ID for the input URL
	 */
	private function get_attachment_id_from_url( $url = '' ) {
		global $wpdb;
		$attachment_id = false;

		// If there is no url, return false
		if ( '' == $url ) {
			return false;
		}

		// Get the upload directory paths
		$upload_dir_paths = wp_upload_dir();

		// Make sure the upload path base directory exists in the attachment URL, to verify that we're working with a media library image
		if ( false !== strpos( $url, $upload_dir_paths['baseurl'] ) ) {

			// If this is the URL of an auto-generated thumbnail, get the URL of the original image
			$url = preg_replace( '/-\d+x\d+(?=\.(jpg|jpeg|png|gif)$)/i', '', $url );

			// Remove the upload path base directory from the attachment URL
			$url = str_replace( $upload_dir_paths['baseurl'] . '/', '', $url );

			// Finally, run a custom database query to get the attachment ID from the modified attachment URL
			$attachment_id = $wpdb->get_var( $wpdb->prepare( "SELECT wposts.ID FROM $wpdb->posts wposts, $wpdb->postmeta wpostmeta WHERE wposts.ID = wpostmeta.post_id AND wpostmeta.meta_key = '_wp_attached_file' AND wpostmeta.meta_value = '%s' AND wposts.post_type = 'attachment'", $url ) );

		}

		if ( false == $attachment_id ) {
			return $url;
		}

		return $attachment_id;
	}

	/**
	 * Extra fields
	 * 
	 * @author Ryan Hellyer <ryanhellyer@gmail.com>
	 * @since 1.0
	 */
	public function extra_fields() {

		$tag_ID = absint( $_GET['tag_ID'] );
		$attachment_id = get_term_meta( $tag_ID, 'taxonomy-header-image', true );

		// We need to cater for legacy URL's as well as the newer attachment ID's
		if ( is_numeric( $attachment_id ) ) {
			$url = Custom_Image_Meta_Box::get_attachment_src( $attachment_id );
			$title = Custom_Image_Meta_Box::get_attachment_title( $attachment_id );
		} else {

			// Falling back to taxonomy meta plugin functionality
			$attachment_id = get_metadata( 'taxonomy', $tag_ID, 'taxonomy-header-image', true );
			if ( is_numeric( $attachment_id ) ) {
				$url = Custom_Image_Meta_Box::get_attachment_src( $attachment_id );
				$title = Custom_Image_Meta_Box::get_attachment_title( $attachment_id );
			} elseif ( is_string( $attachment_id ) ) {
				$url = $attachment_id; // The attachment ID is actually the URL
				$title = ''; // We don't know the title since it's an attachment
			}

		}

		?>
		<tr valign="top">
			<th scope="row"><?php echo esc_html( $this->upload_header_image ); ?></th>
			<td>
				<div id="unique-header" class="postbox " >
					<div class="inside">

						<p class="hide-if-no-js">
							<a title="<?php echo esc_attr( $this->set_custom_image ); ?>" href="javascript:;" id="<?php echo esc_attr( 'set-' . $this->name . '-thumbnail' ); ?>" class="set-custom-meta-image-thumbnail"><?php echo esc_html( $this->set_custom_image ); ?></a>
						</p>

						<div id="<?php echo esc_attr( $this->name . '-container' ); ?>" class="custom-meta-image-container hidden">
							<img src="<?php echo esc_url( $url ); ?>" alt="<?php echo esc_attr( $title ); ?>" title="<?php echo esc_attr( $title ); ?>" />
						</div><!-- #<?php esc_attr( $this->name . '-image-container' ); ?> -->

						<p class="hide-if-no-js hidden">
							<a title="<?php echo esc_attr( $this->remove_custom_image ); ?>" href="javascript:;" id="<?php echo esc_attr( 'remove-' . $this->name . '-thumbnail' ); ?>" class="remove-custom-meta-image-thumbnail"><?php echo esc_html( $this->remove_custom_image ); ?></a>
						</p><!-- .hide-if-no-js -->

						<p id="<?php echo esc_attr( $this->name . '-info' ); ?>" class="custom-meta-image-info">
							<input type="hidden" id="<?php echo esc_attr( $this->name . '-id' ); ?>" class="custom-meta-image-id" name="<?php echo esc_attr( $this->name . '-id' ); ?>" value="<?php echo esc_attr( $attachment_id ); ?>" />
						</p><!-- #<?php echo esc_attr( $this->name . '-image-info' ); ?> -->

						<?php wp_nonce_field( $this->name, $this->name . '-nonce' ); ?>

					</div>
				</div>
			</td>
		</tr>
		<?php


	}

}
