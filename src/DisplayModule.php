<?php

declare(strict_types=1);

namespace RyanHellyer\UniqueHeaders;

use Inpsyde\Modularity\Module\ExecutableModule;
use Psr\Container\ContainerInterface;

class DisplayModule implements ExecutableModule
{
    private AttachmentHelper $attachmentHelper;
    private string $name = 'custom-header-image';
    private string $nameUnderscores;
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

        $attachmentId = get_term_meta($taxId, 'taxonomy-header-image', true);

        if (is_numeric($attachmentId)) {
            $newUrl = $this->attachmentHelper->getSrc((int) $attachmentId);
        } else {
            $legacy = get_metadata('taxonomy', $taxId, 'taxonomy-header-image', true);
            if (is_numeric($legacy)) {
                $newUrl = $this->attachmentHelper->getSrc((int) $legacy);
            } else {
                $newUrl = $legacy ?: '';
            }
        }

        return $newUrl !== '' ? esc_url($newUrl) : $url;
    }

    public function taxonomyModifyHeaderImageData($data)
    {
        if (!is_tag() && !is_tax() && !is_category()) {
            return $data;
        }

        $taxId = $this->getCurrentTaxonomyId();
        if (!$taxId) {
            return $data;
        }

        return $this->setAttachmentData($data, (int) get_term_meta($taxId, 'taxonomy-header-image', true));
    }

    public function headerSrcsetFilter($srcset, $sizeArray, $imageSrc, $imageMeta, $attachmentId = 0)
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
        $attachmentId = absint(wp_unslash($_POST[$this->name . '-id']));

        update_term_meta($tagId, 'taxonomy-header-image', $attachmentId);
    }

    public function extraFields(\WP_Term $term): void
    {
        $tagId = $term->term_id;
        $attachmentId = get_term_meta($tagId, 'taxonomy-header-image', true);

        if (is_numeric($attachmentId)) {
            $url = $this->attachmentHelper->getSrc((int) $attachmentId);
            $title = $this->attachmentHelper->getTitle((int) $attachmentId);
        } else {
            $legacy = get_metadata('taxonomy', $tagId, 'taxonomy-header-image', true);
            if (is_numeric($legacy)) {
                $url = $this->attachmentHelper->getSrc((int) $legacy);
                $title = $this->attachmentHelper->getTitle((int) $legacy);
                $attachmentId = $legacy;
            } else {
                $url = $legacy ?: '';
                $title = '';
                $attachmentId = $legacy;
            }
        }

        wp_nonce_field($this->name, $this->name . '-nonce');
        ?>
        <tr valign="top">
            <th scope="row"><?php echo esc_html__('Upload header image', 'unique-headers'); ?></th>
            <td>
                <div id="unique-header" class="postbox">
                    <div class="inside">
                        <p class="hide-if-no-js">
                            <a title="<?php echo esc_attr__('Set Custom Header Image', 'unique-headers'); ?>" href="javascript:;" id="<?php echo esc_attr('set-' . $this->name . '-thumbnail'); ?>" class="set-custom-meta-image-thumbnail"><?php echo esc_html__('Set Custom Header Image', 'unique-headers'); ?></a>
                        </p>

                        <div id="<?php echo esc_attr($this->name . '-container'); ?>" class="custom-meta-image-container hidden">
                            <img src="<?php echo esc_url($url); ?>" alt="<?php echo esc_attr($title); ?>" title="<?php echo esc_attr($title); ?>" />
                        </div>

                        <p class="hide-if-no-js hidden">
                            <a title="<?php echo esc_attr__('Remove Custom Header Image', 'unique-headers'); ?>" href="javascript:;" id="<?php echo esc_attr('remove-' . $this->name . '-thumbnail'); ?>" class="remove-custom-meta-image-thumbnail"><?php echo esc_html__('Remove Custom Header Image', 'unique-headers'); ?></a>
                        </p>

                        <p id="<?php echo esc_attr($this->name . '-info'); ?>" class="custom-meta-image-info">
                            <input type="hidden" id="<?php echo esc_attr($this->name . '-id'); ?>" class="custom-meta-image-id" name="<?php echo esc_attr($this->name . '-id'); ?>" value="<?php echo esc_attr((string) $attachmentId); ?>" />
                        </p>
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
            $attachmentId = (int) get_term_meta($taxId, 'taxonomy-header-image', true);
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

    /**
     * @param object|null $data
     * @return object|null
     */
    private function setAttachmentData($data, int $attachmentId)
    {
        if (!$attachmentId) {
            return $data;
        }

        if ($data === null || empty($data)) {
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
