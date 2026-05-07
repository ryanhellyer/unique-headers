<?php

declare(strict_types=1);

namespace RyanHellyer\UniqueHeaders;

use RyanHellyer\UniqueHeaders\Vendor\Inpsyde\Modularity\Module\ExecutableModule;
use RyanHellyer\UniqueHeaders\Vendor\Psr\Container\ContainerInterface;

class DisplayModule implements ExecutableModule
{
    private AttachmentHelper $attachmentHelper;
    private string $name = 'custom-header-image';
    private string $nameUnderscores;

    /** @var array<string, string> */
    private array $taxonomies = [];

    public function __construct(AttachmentHelper $attachmentHelper)
    {
        $this->attachmentHelper = $attachmentHelper;
        $this->nameUnderscores = str_replace('-', '_', $this->name);
    }

    public function id(): string
    {
        return 'unique-headers-display';
    }

    public function run(ContainerInterface $container): bool
    {
        add_filter('theme_mod_header_image', [$this, 'postHeaderImageFilter'], 20);
        add_filter('wp_calculate_image_srcset', [$this, 'headerSrcsetFilter'], 20, 5);
        add_filter('theme_mod_header_image_data', [$this, 'postModifyHeaderImageData']);

        if (function_exists('get_term_meta')) {
            $this->taxonomies = get_taxonomies(['public' => true]);
            add_action('admin_init', [$this, 'addTaxonomyFields']);
            add_filter('theme_mod_header_image', [$this, 'taxonomyHeaderImageFilter'], 5);
            add_filter('theme_mod_header_image_data', [$this, 'taxonomyModifyHeaderImageData']);
        }

        return true;
    }

    public function postHeaderImageFilter(string $url): string
    {
        if (!is_single() && !is_page() && !is_home()) {
            return $url;
        }

        $postId = is_home() ? (int) get_option('page_for_posts') : (int) get_the_ID();
        $attachmentId = $this->attachmentHelper->getId($postId, $this->nameUnderscores);

        if ($attachmentId) {
            $url = $this->attachmentHelper->getSrc($attachmentId);
        }

        return $url;
    }

    /**
     * @param object|null $data
     * @return object|null
     */
    public function postModifyHeaderImageData($data)
    {
        if (!is_single() && !is_page() && !is_home()) {
            return $data;
        }

        $postId = is_home() ? (int) get_option('page_for_posts') : (int) get_the_ID();

        return $this->setAttachmentData($data, $this->attachmentHelper->getId($postId, $this->nameUnderscores));
    }

    public function taxonomyHeaderImageFilter(string $url): string
    {
        $taxId = $this->getCurrentTaxonomyId();
        if (!$taxId) {
            return $url;
        }

        $attachmentId = $this->getTaxonomyAttachmentId($taxId);
        if ($attachmentId) {
            return esc_url($this->attachmentHelper->getSrc($attachmentId));
        }

        $legacy = get_metadata('taxonomy', $taxId, 'taxonomy-header-image', true);
        if (is_string($legacy) && $legacy !== '') {
            return esc_url($legacy);
        }

        return $url;
    }

    /**
     * @param object|null $data
     * @return object|null
     */
    public function taxonomyModifyHeaderImageData($data)
    {
        if (!is_tag() && !is_tax() && !is_category()) {
            return $data;
        }

        $taxId = $this->getCurrentTaxonomyId();
        if (!$taxId) {
            return $data;
        }

        return $this->setAttachmentData($data, $this->getTaxonomyAttachmentId($taxId));
    }

    /**
     * @param array<int, array{url: string}>|string $srcset
     * @param array<int, int> $sizeArray
     * @param string $imageSrc
     * @param array<string, mixed> $imageMeta
     * @param int $attachmentId
     * @return array<int, array{url: string}>|string
     */
    public function headerSrcsetFilter($srcset, $sizeArray, $imageSrc, $imageMeta, int $attachmentId = 0)
    {
        if (
            !isset(get_custom_header()->attachment_id)
            || get_custom_header()->attachment_id !== $attachmentId
        ) {
            return $srcset;
        }

        if (!is_array($srcset)) {
            return $srcset;
        }

        $currentUrl = $this->getCurrentHeaderUrl();
        if ($currentUrl === '') {
            return $srcset;
        }

        foreach ($srcset as $size => $set) {
            $srcset[$size]['url'] = $currentUrl;
        }

        return $srcset;
    }

    public function addTaxonomyFields(): void
    {
        if (!is_admin()) {
            return;
        }

        foreach ($this->taxonomies as $taxonomy) {
            add_action($taxonomy . '_edit_form_fields', [$this, 'extraFields'], 1);
            add_action('edit_' . $taxonomy, [$this, 'storeTaxonomyData']);
        }
    }

    public function storeTaxonomyData(): void
    {
        if (
            !isset($_POST[$this->name . '-nonce'])
            || !isset($_POST[$this->name . '-id'])
            || !isset($_POST['tag_ID'])
        ) {
            return;
        }

        $nonce = sanitize_key(wp_unslash($_POST[$this->name . '-nonce']));
        if (!wp_verify_nonce($nonce, $this->name)) {
            return;
        }

        $tagId = absint(wp_unslash($_POST['tag_ID']));

        if (!current_user_can('edit_term', $tagId)) {
            return;
        }

        $attachmentId = absint(wp_unslash($_POST[$this->name . '-id']));

        update_term_meta($tagId, 'taxonomy-header-image', $attachmentId);
    }

    public function extraFields(\WP_Term $term): void
    {
        $tagId = $term->term_id;
        $attachmentId = $this->getTaxonomyAttachmentId($tagId);

        if ($attachmentId) {
            $url = $this->attachmentHelper->getSrc($attachmentId);
            $title = $this->attachmentHelper->getTitle($attachmentId);
        } else {
            $legacy = get_metadata('taxonomy', $tagId, 'taxonomy-header-image', true);
            $url = is_string($legacy) ? $legacy : '';
            $title = '';
            $attachmentId = $legacy;
        }

        $name = $this->name;
        ?>
        <tr valign="top">
            <th scope="row"><?php echo esc_html__('Upload header image', 'unique-headers'); ?></th>
            <td>
                <div id="unique-header" class="postbox">
                    <div class="inside">
                        <?php require __DIR__ . '/../views/meta-box.php'; ?>
                    </div>
                </div>
            </td>
        </tr>
        <?php
    }

    private function getCurrentHeaderUrl(): string
    {
        $postId = 0;

        if (is_home()) {
            $postId = (int) get_option('page_for_posts');
        } elseif (is_single() || is_page()) {
            $postId = (int) get_the_ID();
        }

        if ($postId) {
            $attachmentId = $this->attachmentHelper->getId($postId, $this->nameUnderscores);
            if ($attachmentId) {
                return $this->attachmentHelper->getSrc($attachmentId);
            }
        }

        $taxId = $this->getCurrentTaxonomyId();
        if ($taxId) {
            $attachmentId = $this->getTaxonomyAttachmentId($taxId);
            if ($attachmentId) {
                return $this->attachmentHelper->getSrc($attachmentId);
            }
        }

        return '';
    }

    private function getCurrentTaxonomyId(): int
    {
        if (is_category()) {
            return (int) get_query_var('cat');
        }

        if (is_tag() || is_tax()) {
            foreach ($this->taxonomies as $taxonomy) {
                if ($taxonomy === 'category') {
                    continue;
                }

                $slug = $taxonomy === 'post_tag' ? get_query_var('tag') : get_query_var($taxonomy);

                if ($slug) {
                    $term = get_term_by('slug', $slug, $taxonomy);
                    if (isset($term->term_id)) {
                        return (int) $term->term_id;
                    }
                }
            }
        }

        return 0;
    }

    private function getTaxonomyAttachmentId(int $taxId): int
    {
        $attachmentId = get_term_meta($taxId, 'taxonomy-header-image', true);

        if (is_numeric($attachmentId)) {
            return (int) $attachmentId;
        }

        $legacy = get_metadata('taxonomy', $taxId, 'taxonomy-header-image', true);

        return is_numeric($legacy) ? (int) $legacy : 0;
    }

    /**
     * @param object|null $data
     * @return object|null
     */
    private function setAttachmentData($data, int $attachmentId)
    {
        if (!$attachmentId) {
            return $data;
        }

        if ($data === null) {
            $data = (object) null;
        }

        if (is_object($data)) {
            $data->attachment_id = $attachmentId;
            $data->width = $this->attachmentHelper->getDimensions($attachmentId, 'width');
            $data->height = $this->attachmentHelper->getDimensions($attachmentId, 'height');
        }

        return $data;
    }
}
