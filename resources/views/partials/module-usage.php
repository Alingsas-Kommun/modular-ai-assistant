<?php
/**
 * Module Usage MetaBox Template
 *
 * @var WP_Post $post
 */
?>
<p><?php esc_html_e('Use these shortcodes to display this module:', 'modular-ai'); ?></p>

<div class="modular-ai-module-example" style="margin-bottom: 15px;">
    <h4 style="margin-bottom: 5px;"><?php esc_html_e('Basic Usage:', 'modular-ai'); ?></h4>
    <code style="background: #f0f0f1; padding: 5px 10px; display: inline-block;">[modular-ai id="<?php echo absint($post->ID); ?>"]</code>
    <p class="description" style="margin-top: 5px;"><?php esc_html_e('Uses configured content', 'modular-ai'); ?></p>
</div>

<div class="modular-ai-module-example" style="margin-bottom: 15px;">
    <h4 style="margin-bottom: 5px;"><?php esc_html_e('Custom Query:', 'modular-ai'); ?></h4>
    <code style="background: #f0f0f1; padding: 5px 10px; display: inline-block;">[modular-ai id="<?php echo absint($post->ID); ?>" q="Your custom question here"]</code>
    <p class="description" style="margin-top: 5px;"><?php esc_html_e('Uses custom question instead of configured content', 'modular-ai'); ?></p>
</div>

<div class="modular-ai-module-example" style="margin-bottom: 15px;">
    <h4 style="margin-bottom: 5px;"><?php esc_html_e('Modal Display:', 'modular-ai'); ?></h4>
    <code style="background: #f0f0f1; padding: 5px 10px; display: inline-block;">[modular-ai id="<?php echo absint($post->ID); ?>" modal="true"]</code>
    <p class="description" style="margin-top: 5px;"><?php esc_html_e('Shows response in modal popup (button)', 'modular-ai'); ?></p>
</div>

<div class="modular-ai-module-example" style="margin-bottom: 15px;">
    <h4 style="margin-bottom: 5px;"><?php esc_html_e('Modal with Custom Button:', 'modular-ai'); ?></h4>
    <code style="background: #f0f0f1; padding: 5px 10px; display: inline-block;">[modular-ai id="<?php echo absint($post->ID); ?>" modal="true" button_text="Get AI Help"]</code>
    <p class="description" style="margin-top: 5px;"><?php esc_html_e('Modal with custom button text', 'modular-ai'); ?></p>
</div>

<div class="modular-ai-module-example">
    <h4 style="margin-bottom: 5px;"><?php esc_html_e('Debug Mode:', 'modular-ai'); ?></h4>
    <code style="background: #f0f0f1; padding: 5px 10px; display: inline-block;">[modular-ai id="<?php echo absint($post->ID); ?>" show_curl="true"]</code>
    <p class="description" style="margin-top: 5px;"><?php esc_html_e('Shows CURL command for debugging', 'modular-ai'); ?></p>
</div>

