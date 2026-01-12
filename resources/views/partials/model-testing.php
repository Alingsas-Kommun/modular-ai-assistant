<?php
/**
 * Model Testing MetaBox Template
 *
 * @var WP_Post $post
**/

if (! defined('ABSPATH')) {
    exit;
}
?>

<div x-data="modelTest">
    <p><?php esc_html_e('Click the button to test if the model can be reached with current settings.', 'modular-ai'); ?></p>

    <p>
        <button 
            type="button" 
            class="button button-secondary" 
            @click="testModel(<?php echo esc_attr($post->ID); ?>)"
            :disabled="loading"
        >
            <span x-text="loading ? '<?php esc_attr_e('Testing...', 'modular-ai'); ?>' : '<?php esc_attr_e('Test Model', 'modular-ai'); ?>'"></span>
        </button>
    </p>

    <div 
        x-show="result" 
        x-html="result"
        class="notice"
        :class="resultType === 'success' ? 'notice-success' : 'notice-error'"
        style="display: none;"
    ></div>
</div>

