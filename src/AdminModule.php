<?php

declare(strict_types=1);

namespace RyanHellyer\UniqueHeaders;

use Inpsyde\Modularity\Module\ExecutableModule;
use Psr\Container\ContainerInterface;

class AdminModule implements ExecutableModule
{
    private AttachmentHelper $attachmentHelper;
    private string $name = 'custom-header-image';
    private string $nameUnderscores;
    private string $dirUri;
    private string $version = '1.3';

    public function __construct(AttachmentHelper $attachmentHelper)
    {
        $this->attachmentHelper = $attachmentHelper;
        $this->nameUnderscores = str_replace('-', '_', $this->name);
        $this->dirUri = plugin_dir_url(dirname(__DIR__) . '/index.php') . 'assets';
    }

    public function id(): string
    {
        return 'unique-headers-admin';
    }

    public function run(ContainerInterface $container): bool
    {
        add_action('init', [$this, 'loadTextdomain']);
        add_action('init', [$this, 'wpdbFix']);

        add_filter('unique_header_fallback_images', [$this, 'fallbackImages']);

        if (is_admin()) {
            new DotorgPluginReview(
                [
                    'slug' => 'unique-headers',
                    'name' => 'Unique Headers',
                    'time_limit' => WEEK_IN_SECONDS,
                ]
            );
        }

        add_action('add_meta_boxes', [$this, 'addMetaBox']);
        add_action('admin_enqueue_scripts', [$this, 'enqueueScripts']);
        add_action('admin_enqueue_scripts', [$this, 'enqueueStyles']);
        add_action('save_post', [$this, 'savePost']);

        return true;
    }

    public function loadTextdomain(): void
    {
        load_plugin_textdomain(
            'unique-headers',
            false,
            dirname(plugin_basename(dirname(__DIR__) . '/index.php')) . '/languages'
        );
    }

    public function wpdbFix(): void
    {
        if (!class_exists('Taxonomy_Metadata')) {
            return;
        }

        global $wpdb;

        $wpdb->taxonomymeta = $wpdb->prefix . 'taxonomymeta';
    }

    public function fallbackImages(int $postId): int
    {
        $keys = [
            'post_custom-header_thumbnail_id',
            'page_custom-header_thumbnail_id',
            'kd_custom-header_post_id',
            'kd_custom-header_page_id',
            '_unique_header_id',
            '_custom_header_image',
        ];

        $attachmentId = 0;
        $keysToRemove = [];

        foreach ($keys as $key) {
            if (!$attachmentId) {
                $found = (int) get_post_meta($postId, $key, true);
                if ($found) {
                    $attachmentId = $found;
                    $keysToRemove[] = $key;
                }
            }
        }

        if (!$attachmentId) {
            return 0;
        }

        update_post_meta($postId, '_custom_header_image_id', $attachmentId);

        foreach ($keysToRemove as $key) {
            delete_post_meta($postId, $key);
        }

        return $attachmentId;
    }

    public function addMetaBox(): void
    {
        $postTypes = get_post_types(['public' => true]);

        foreach ($postTypes as $screen) {
            add_meta_box(
                $this->name,
                __('Custom header', 'unique-headers'),
                [$this, 'displayMetaBox'],
                $screen,
                'side'
            );
        }
    }

    public function enqueueScripts(): void
    {
        wp_enqueue_media();

        wp_enqueue_script(
            $this->name,
            $this->dirUri . '/admin.js',
            [],
            $this->version,
            true
        );

        wp_localize_script($this->name, 'custom_meta_image_name', [$this->name]);
    }

    public function enqueueStyles(): void
    {
        wp_enqueue_style(
            $this->name,
            $this->dirUri . '/admin.css',
            [],
            $this->version
        );
    }

    public function savePost(int $postId): void
    {
        if (!current_user_can('edit_post', $postId)) {
            return;
        }

        if (
            !isset($_POST[$this->name . '-nonce'])
            || !isset($_POST[$this->name . '-id'])
        ) {
            return;
        }

        $nonce = sanitize_text_field(wp_unslash($_POST[$this->name . '-nonce']));
        if (!wp_verify_nonce($nonce, $this->name)) {
            return;
        }

        $attachmentId = sanitize_text_field(wp_unslash($_POST[$this->name . '-id']));
        update_post_meta($postId, '_' . $this->nameUnderscores . '_id', $attachmentId);
    }

    public function displayMetaBox(\WP_Post $post): void
    {
        $attachmentId = $this->attachmentHelper->getId($post->ID, $this->nameUnderscores);
        $url = $this->attachmentHelper->getSrc($attachmentId);
        $title = $this->attachmentHelper->getTitle($attachmentId);
        $name = $this->name;

        require __DIR__ . '/../views/meta-box.php';
    }
}
