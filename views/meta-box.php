<?php

wp_nonce_field($name, $name . '-nonce');
?>

<p class="hide-if-no-js">
    <a title="<?php echo esc_attr__('Set Custom Header Image', 'unique-headers'); ?>" href="javascript:;" id="<?php echo esc_attr('set-' . $name . '-thumbnail'); ?>" class="set-custom-meta-image-thumbnail"><?php echo esc_html__('Set Custom Header Image', 'unique-headers'); ?></a>
</p>

<div id="<?php echo esc_attr($name . '-container'); ?>" class="custom-meta-image-container hidden">
    <img src="<?php echo esc_url($url); ?>" alt="<?php echo esc_attr($title); ?>" title="<?php echo esc_attr($title); ?>" />
</div>

<p class="hide-if-no-js hidden">
    <a title="<?php echo esc_attr__('Remove Custom Header Image', 'unique-headers'); ?>" href="javascript:;" id="<?php echo esc_attr('remove-' . $name . '-thumbnail'); ?>" class="remove-custom-meta-image-thumbnail"><?php echo esc_html__('Remove Custom Header Image', 'unique-headers'); ?></a>
</p>

<p id="<?php echo esc_attr($name . '-info'); ?>" class="custom-meta-image-info">
    <input type="hidden" id="<?php echo esc_attr($name . '-id'); ?>" class="custom-meta-image-id" name="<?php echo esc_attr($name . '-id'); ?>" value="<?php echo esc_attr((string) $attachmentId); ?>" />
</p>
