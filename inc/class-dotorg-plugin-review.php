<?php
/**
 * DotOrg_Plugin_Review Class File
 *
 * This file contains a class that prompts users to review a WordPress plugin
 * on WordPress.org after a specified period of usage.
 *
 * @package    Unique Headers
 * @version    1.0
 * @author     Ryan Hellyer <ryanhellyer@gmail.com>
 * @copyright  Copyright (c), Ryan Hellyer
 * @license    http://www.gnu.org/licenses/gpl.html GPL
 */

// Bail out now if the class doesn't exist.
if ( class_exists( 'DotOrg_Plugin_Review' ) ) {
	return;
}

/**
 * Plugin review class.
 * Prompts users to give a review of the plugin on WordPress.org after a period of usage.
 *
 * Heavily based on code by Rhys Wynne
 * https://winwar.co.uk/2014/10/ask-wordpress-plugin-reviews-week/
 *
 * @version   1.0
 * @copyright Copyright (c), Ryan Hellyer
 * @author Ryan Hellyer <ryanhellyer@gmail.com>
 */
class DotOrg_Plugin_Review {

	/**
	 * The plugin slug.
	 *
	 * @var string
	 */
	private $slug;

	/**
	 * The plugin name.
	 *
	 * @var string
	 */
	private $name;

	/**
	 * The time limit at which notice is shown.
	 *
	 * @var int
	 */
	private $time_limit;

	/**
	 * No debug option.
	 *
	 * @var string
	 */
	public $nobug_option;

	/**
	 * Fire the constructor up :)
	 *
	 * @param array $args The arguments.
	 */
	public function __construct( $args ) {

		$this->slug = $args['slug'];
		$this->name = $args['name'];
		if ( isset( $args['time_limit'] ) ) {
			$this->time_limit = $args['time_limit'];
		} else {
			$this->time_limit = WEEK_IN_SECONDS;
		}

		$this->nobug_option = $this->slug . '-no-bug';

		// Loading main functionality.
		add_action( 'admin_init', array( $this, 'check_installation_date' ) );
		add_action( 'admin_init', array( $this, 'set_no_bug' ), 5 );
	}

	/**
	 * Seconds to words.
	 *
	 * @param int $seconds The number of seconds.
	 */
	public function seconds_to_words( $seconds ) {

		// Get the years.
		$years = round( $seconds / YEAR_IN_SECONDS ) % 100;
		if ( $years > 1 ) {
			// translators: %s: Number of years.
			return sprintf( __( '%s years', $this->slug ), $years );
		} elseif ( $years > 0 ) {
			return __( 'a year', $this->slug );
		}

		// Get the weeks.
		$weeks = round( $seconds / WEEK_IN_SECONDS ) % 52;
		if ( $weeks > 1 ) {
			// translators: %s: Number of weeks.
			return sprintf( __( '%s weeks', $this->slug ), $weeks );
		} elseif ( $weeks > 0 ) {
			return __( 'a week', $this->slug );
		}

		// Get the days.
		$days = round( $seconds / DAY_IN_SECONDS ) % 7;
		if ( $days > 1 ) {
			// translators: %s: Number of days.
			return sprintf( __( '%s days', $this->slug ), $days );
		} elseif ( $days > 0 ) {
			return __( 'a day', $this->slug );
		}

		// Get the hours.
		$hours = round( $seconds / HOUR_IN_SECONDS ) % 24;
		if ( $hours > 1 ) {
			// translators: %s: Number of hours.
			return sprintf( __( '%s hours', $this->slug ), $hours );
		} elseif ( $hours > 0 ) {
			return __( 'an hour', $this->slug );
		}

		// Get the minutes.
		$minutes = round( $seconds / MINUTE_IN_SECONDS ) % 60;
		if ( $minutes > 1 ) {
			// translators: %s: Number of minutes.
			return sprintf( __( '%s minutes', $this->slug ), $minutes );
		} elseif ( $minutes > 0 ) {
			return __( 'a minute', $this->slug );
		}

		// Get the seconds.
		$seconds = round( $seconds ) % 60;
		if ( $seconds > 1 ) {
			// translators: %s: Number of seconds.
			return sprintf( __( '%s seconds', $this->slug ), $seconds );
		} elseif ( $seconds > 0 ) {
			return __( 'a second', $this->slug );
		}

	}

	/**
	 * Check date on admin initiation and add to admin notice if it was more than the time limit.
	 */
	public function check_installation_date() {

		if ( '1' !== get_site_option( $this->nobug_option ) ) {

			// If not installation date set, then add it.
			$install_date = get_site_option( $this->slug . '-activation-date' );
			if ( '' === $install_date ) {
				add_site_option( $this->slug . '-activation-date', time() );
			}

			// If difference between install date and now is greater than time limit, then display notice.
			$gap = time() - $install_date;
			if ( $gap > $this->time_limit ) {
				add_action( 'admin_notices', array( $this, 'display_admin_notice' ) );
			}
		}

	}

	/**
	 * Display Admin Notice, asking for a review.
	 */
	public function display_admin_notice() {

		$screen = get_current_screen();
		if ( isset( $screen->base ) && 'plugins' === $screen->base ) {

			$no_bug_url = wp_nonce_url( admin_url( '?' . $this->nobug_option . '=true' ), 'review-nonce' );
			$time       = $this->seconds_to_words( time() - get_site_option( $this->slug . '-activation-date' ) );

			echo '
		<div class="updated">
			<p>' .
			sprintf(
				/* translators: 1: Plugin name, 2: Time duration */
				esc_html__( 'You have been using the %1$s plugin for %2$s now, do you like it? If so, please leave us a review with your feedback!', 'spam-destroyer' ),
				esc_html( $this->name ),
				esc_html( $time )
			) .
			'<br /><br />
			<a onclick="location.href=\'' . esc_url( $no_bug_url ) . '\';" class="button button-primary" href="' . esc_url( 'https://wordpress.org/support/view/plugin-reviews/' . $this->slug . '#postform' ) . '" target="_blank">' . esc_html__( 'Leave A Review', 'spam-destroyer' ) . '</a>
			<a href="' . esc_url( $no_bug_url ) . '">' . esc_html__( 'No thanks.', 'spam-destroyer' ) . '</a>
			</p>
		</div>';

		}

	}

	/**
	 * Set the plugin to no longer bug users if user asks not to be.
	 */
	public function set_no_bug() {

		// Bail out if not on correct page.
		if (
		! isset( $_GET['_wpnonce'] )
		||
		(
			! wp_verify_nonce( filter_input( INPUT_GET, '_wpnonce' ), 'review-nonce' )
			||
			! is_admin()
			||
			! isset( $_GET[ $this->nobug_option ] )
			||
			! current_user_can( 'manage_options' )
		)
		) {
			return;
		}

		add_site_option( $this->nobug_option, true );

	}

}
