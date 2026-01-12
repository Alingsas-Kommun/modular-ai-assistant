<?php
/**
 * API Key field template
 *
 * @var string $field_id
 * @var string $field_name
 * @var string $value
 * @var array $field
 * @var string $required
 * @var \WP_Post $post
 */

if (! defined('ABSPATH')) {
    exit;
}
?>
<div x-data="apiKeyConfiguration(<?php echo absint($post->ID); ?>)" class="modular-ai-api-key-wrapper">
    <div class="modular-ai-api-key-field-group">
        <input 
            type="text" 
            name="<?php echo esc_attr($field_name); ?>" 
            id="<?php echo esc_attr($field_id); ?>" 
            value="<?php echo esc_attr($value); ?>" 
            class="regular-text modular-ai-api-key-input" 
            x-ref="apiKeyInput"
            readonly
            <?php echo esc_attr($required); ?>
        >
        
        <?php if ($value): ?>
            <button 
                type="button" 
                class="button modular-ai-copy-button" 
                :class="{ 'copied': copied }"
                @click="copyToClipboard()"
                title="<?php echo esc_attr(__('Copy to Clipboard', 'modular-ai')); ?>"
            >
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" x-show="!copied">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                </svg>
                
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" x-show="copied" x-cloak>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                <span x-text="copied ? '<?php echo esc_js(__('Copied!', 'modular-ai')); ?>' : '<?php echo esc_js(__('Copy', 'modular-ai')); ?>'"></span>
            </button>
            
            <button 
                type="button" 
                class="button modular-ai-regenerate-button" 
                @click="regenerateKey()"
                :disabled="regenerating"
                title="<?php echo esc_attr(__('Generate new API key', 'modular-ai')); ?>"
            >
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" :class="{ 'spinning': regenerating }">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                </svg>

                <span x-text="regenerating ? '<?php echo esc_js(__('Generating...', 'modular-ai')); ?>' : '<?php echo esc_js(__('Regenerate', 'modular-ai')); ?>'"></span>
            </button>
        <?php endif; ?>
    </div>
</div>

