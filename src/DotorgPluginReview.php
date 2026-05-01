<?php

declare(strict_types=1);

namespace RyanHellyer\UniqueHeaders;

class DotorgPluginReview
{
    private string $slug;
    private string $name;
    private int $timeLimit;
    public string $nobugOption;

    public function __construct(array $args)
    {
        $this->slug = $args['slug'];
        $this->name = $args['name'];
        $this->timeLimit = $args['time_limit'] ?? WEEK_IN_SECONDS;
        $this->nobugOption = $this->slug . '-no-bug';

        add_action('admin_init', [$this, 'checkInstallationDate']);
        add_action('admin_init', [$this, 'setNoBug'], 5);
    }

    public function secondsToWords(int $seconds): string
    {
        $years = (int) round($seconds / YEAR_IN_SECONDS) % 100;
        if ($years > 1) {
            return sprintf(__('%s years', 'unique-headers'), $years);
        }
        if ($years > 0) {
            return __('a year', 'unique-headers');
        }

        $weeks = (int) round($seconds / WEEK_IN_SECONDS) % 52;
        if ($weeks > 1) {
            return sprintf(__('%s weeks', 'unique-headers'), $weeks);
        }
        if ($weeks > 0) {
            return __('a week', 'unique-headers');
        }

        $days = (int) round($seconds / DAY_IN_SECONDS) % 7;
        if ($days > 1) {
            return sprintf(__('%s days', 'unique-headers'), $days);
        }
        if ($days > 0) {
            return __('a day', 'unique-headers');
        }

        $hours = (int) round($seconds / HOUR_IN_SECONDS) % 24;
        if ($hours > 1) {
            return sprintf(__('%s hours', 'unique-headers'), $hours);
        }
        if ($hours > 0) {
            return __('an hour', 'unique-headers');
        }

        $minutes = (int) round($seconds / MINUTE_IN_SECONDS) % 60;
        if ($minutes > 1) {
            return sprintf(__('%s minutes', 'unique-headers'), $minutes);
        }
        if ($minutes > 0) {
            return __('a minute', 'unique-headers');
        }

        $secs = (int) round($seconds) % 60;
        if ($secs > 1) {
            return sprintf(__('%s seconds', 'unique-headers'), $secs);
        }

        return __('a second', 'unique-headers');
    }

    public function checkInstallationDate(): void
    {
        if (get_site_option($this->nobugOption) === '1') {
            return;
        }

        $installDate = get_site_option($this->slug . '-activation-date');
        if ($installDate === false) {
            add_site_option($this->slug . '-activation-date', time());
            return;
        }

        $gap = time() - $installDate;
        if ($gap > $this->timeLimit) {
            add_action('admin_notices', [$this, 'displayAdminNotice']);
        }
    }

    public function displayAdminNotice(): void
    {
        $screen = get_current_screen();
        if (!isset($screen->base) || $screen->base !== 'plugins') {
            return;
        }

        $noBugUrl = wp_nonce_url(
            admin_url('?' . $this->nobugOption . '=true'),
            'review-nonce'
        );
        $time = $this->secondsToWords(time() - (int) get_site_option($this->slug . '-activation-date'));

        ?>
        <div class="updated">
            <p>
                <?php
                echo sprintf(
                    esc_html__(
                        'You have been using the %1$s plugin for %2$s now, do you like it? If so, please leave us a review with your feedback!',
                        'unique-headers'
                    ),
                    esc_html($this->name),
                    esc_html($time)
                );
                ?>
                <br /><br />
                <a onclick="location.href='<?php echo esc_url($noBugUrl); ?>';" class="button button-primary" href="<?php echo esc_url('https://wordpress.org/support/view/plugin-reviews/' . $this->slug . '#postform'); ?>" target="_blank"><?php echo esc_html__('Leave A Review', 'unique-headers'); ?></a>
                <a href="<?php echo esc_url($noBugUrl); ?>"><?php echo esc_html__('No thanks.', 'unique-headers'); ?></a>
            </p>
        </div>
        <?php
    }

    public function setNoBug(): void
    {
        if (
            !isset($_GET['_wpnonce'])
            || !wp_verify_nonce(
                sanitize_key(wp_unslash($_GET['_wpnonce'])),
                'review-nonce'
            )
            || !is_admin()
            || !isset($_GET[$this->nobugOption])
            || !current_user_can('manage_options')
        ) {
            return;
        }

        add_site_option($this->nobugOption, true);
    }
}
