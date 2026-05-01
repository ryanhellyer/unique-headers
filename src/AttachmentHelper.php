<?php

declare(strict_types=1);

namespace RyanHellyer\UniqueHeaders;

class AttachmentHelper
{
    public function getId(int $postId, string $name): int
    {
        $attachmentId = (int) get_post_meta($postId, '_' . $name . '_id', true);

        if (!$attachmentId) {
            $attachmentId = (int) apply_filters('unique_header_fallback_images', $postId);
        }

        return $attachmentId;
    }

    public function getSrc(int $attachmentId): string
    {
        $src = wp_get_attachment_image_src($attachmentId, 'full');

        return $src[0] ?? '';
    }

    public function getDimensions(int $attachmentId, string $dimension = 'width'): int
    {
        $data = wp_get_attachment_image_src($attachmentId, 'full');

        if ($dimension === 'width' && isset($data[1])) {
            return (int) $data[1];
        }

        if ($dimension === 'height' && isset($data[2])) {
            return (int) $data[2];
        }

        return 0;
    }

    public function getTitle(int $attachmentId): string
    {
        return trim(
            wp_strip_all_tags(
                (string) get_post_meta($attachmentId, '_wp_attachment_image_alt', true)
            )
        );
    }
}
