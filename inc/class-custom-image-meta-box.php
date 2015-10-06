<?php

/**
 * Add a custom image meta box
 *
 * @copyright Copyright (c), Ryan Hellyer
 * @license http://www.gnu.org/licenses/gpl.html GPL
 * @author Ryan Hellyer <ryanhellyer@gmail.com>
 * @since 1.3
 */
class Custom_Image_Meta_Box {

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
	 * The directory URI, for accessing JS and CSS files
	 *
	 * @since 1.3
	 * @access   private
	 * @var      string    $dir_uri
	 */
	private $dir_uri;

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
	 * The current version of the class
	 *
	 * @since 1.3
	 * @access   private
	 * @var      string    $version
	 */
	private $version = '1.3';

	/**
	 * The post types to add meta boxes to
	 *
	 * @since 1.3
	 * @access   private
	 * @var      string    $post_types
	 */
	private $post_types;

	/**
	 * Class constructor
	 * Adds methods to appropriate hooks
	 * 
	 * @since 1.3
	 */
	public function __construct( $args ) {

		$this->name                = $args['name'];
		$this->name_underscores    = str_replace( '-', '_', $args['name'] );
		$this->dir_uri             = $args['dir_uri'];
		$this->title               = $args['title'];
		$this->set_custom_image    = $args['set_custom_image'];
		$this->remove_custom_image = $args['remove_custom_image'];
		$this->post_types          = $args['post_types'];

		// Add meta box
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_styles' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_action( 'add_meta_boxes',        array( $this, 'add_meta_box' ) );
		add_action( 'save_post',             array( $this, 'save_post' ) );

	}

	/*
	 * Get the attachment ID from the post ID
	 *
	 * @since 1.3
	 * @access   static     This method is static so that front-end scripts can access the attachment ID
	 * @param    int        $post_id         The post ID
	 * @return   int        $attachment_id   The attachment ID
	 */
	static function get_attachment_id( $post_id, $name ) {

		$attachment_id = get_post_meta( $post_id, '_' . $name . '_id', true );

		// Check for fallback images
		if ( ! $attachment_id ) {
			$attachment_id = apply_filters( 'unique_header_fallback_images', $post_id );
		}

		return $attachment_id;
	}

	/*
	 * Get the attachment URL from the attachment ID
	 *
	 * @since 1.3
	 * @access   static     This method is static so that front-end scripts can access the attachments SRC
	 * @param    int        $attachment_id   The attachment ID
	 * @return   string
	 */
	static function get_attachment_src( $attachment_id ) {

		// Grab URL from WordPress
		$url = wp_get_attachment_image_src( $attachment_id, 'full' );
		$url = $url[0];

		return $url;
	}

	/**
	 * Registers the JavaScript for handling the media uploader.
	 *
	 * @since 1.3
	 * @access   static   This method is static so that front-end scripts can access the attachments title
	 * @param    int      $attachment_id   The attachment ID
	 * @return   string   $title           The attachment title
	 */
	static function get_attachment_title( $attachment_id ) {

		// Get title (trip and strip_tags method was adapted from WordPress core)
		$title = trim( strip_tags( get_post_meta( $attachment_id, '_wp_attachment_image_alt', true ) ) );

		return $title;
	}

	/**
	 * Renders the meta box on the post and pages.
	 *
	 * @since 1.3
	 */
	public function add_meta_box() {

		foreach ( $this->post_types as $screen ) {

			add_meta_box(
				$this->name,
				$this->title,
				array( $this, 'display_custom_meta_image' ),
				$screen,
				'side'
			);

		}

	}

	/**
	 * Registers the JavaScript for handling the media uploader.
	 *
	 * @since 1.3
	 */
	public function enqueue_scripts() {

		wp_enqueue_media();

		wp_enqueue_script(
			$this->name,
			$this->dir_uri . '/admin.js',
			array( 'jquery' ),
			$this->version,
			'all'
		);

		wp_localize_script( $this->name, 'custom_meta_image_name', $this->name );

	}

	/**
	 * Registers the stylesheets for handling the meta box
	 *
	 * @since 1.3
	 */
	public function enqueue_styles() {

		wp_enqueue_style(
			$this->name,
			$this->dir_uri . '/admin.css',
			array()
		);

	}

	/**
	 * Sanitized and saves the post featured footer image meta data specific with this post.
	 *
	 * @since    1.3
	 * @param    int    $post_id    The ID of the post with which we're currently working.
	 * @return   int    $post_id    The ID of the post with which we're currently working.
	 */
	public function save_post( $post_id ) {

		// Bail out now if POST vars not set
		if ( ! isset( $_POST[$this->name . '-nonce'] ) || ! isset( $_POST[$this->name . '-id'] ) ) {
			return $post_id;
		}

		// Bail out now if nonce doesn't verify
		if ( ! wp_verify_nonce( $_POST[$this->name . '-nonce'], $this->name ) ) {
			return $post_id;
		}

		// Sanitize the attachment ID
		$attachment_id = sanitize_text_field( $_POST[$this->name . '-id'] );

		// Save the attachment ID in the database
		update_post_meta( $post_id, '_' . $this->name_underscores . '_id', $attachment_id );

		// Return the post ID
		return $post_id;
	}

	/**
	 * Renders the view that displays the contents for the meta box that for triggering
	 * the meta box.
	 *
	 * @param    WP_Post    $post    The post object
	 * @since    1.3
	 */
	public function display_custom_meta_image( $post ) {

		// Get required values
		$attachment_id = $this->get_attachment_id( $post->ID, $this->name_underscores );
		$url = $this->get_attachment_src( $attachment_id );
		$title = $this->get_attachment_title( $attachment_id );

		wp_nonce_field( $this->name, $this->name . '-nonce' );
		?>

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
		</p><!-- #<?php echo esc_attr( $this->name . '-image-info' ); ?> --><?php

		return $post;
	}

}
