<?php
/**
 * Module Container Template
 * Handles both inline and modal display modes
 * Can be invoked via shortcode, admin UI, or other methods
 *
 * @var int $module_id
 * @var string $query
 * @var bool $show_curl
 * @var string $instance_id
 * @var bool $modal
 * @var string $button_text (only for modal mode)
 * @var string $button_class (only for modal mode)
 * @var bool $hide_trigger (hide trigger button in modal mode, default false)
 * @var string $modal_title (modal header title, default 'AI Overview')
 * @var int|null $post_id (optional post ID for context)
 * @var string $context (context: 'frontend' or 'editor', default 'frontend')
 * @var bool|null $streaming (streaming override parameter)
 *
 * phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
 * These are template variables extracted from an array, not true global variables.
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

$hide_trigger = $hide_trigger ?? false;
$modal_title = $modal_title ?? __('AI Overview', 'modular-ai-assistant');
$post_id = $post_id ?? null;
$context = $context ?? 'frontend';
$streaming = $streaming ?? null;
?>

<div x-data="modularAIModule(<?php echo esc_attr($module_id); ?>, '<?php echo esc_js($query); ?>', <?php echo $modal ? 'true' : 'false'; ?>, <?php echo $show_curl ? 'true' : 'false'; ?>, '<?php echo esc_attr($instance_id); ?>', <?php echo $post_id ? esc_attr($post_id) : 'null'; ?>, <?php echo $streaming !== null ? ($streaming ? 'true' : 'false') : 'null'; ?>)">
    <?php if ($modal): ?>
        <?php if (!$hide_trigger): ?>
            <button @click="fetchResponse" 
                    class="<?php echo esc_attr($button_class); ?>"
                    aria-haspopup="dialog"
                    :aria-expanded="showModal">
                <?php echo esc_html($button_text); ?>
            </button>
        <?php endif; ?>
        
        <div 
            x-show="showModal" 
            class="modular-ai-assistant-modal" 
            @click.self="showModal = false"
            @keydown.escape.window="showModal = false"
            role="dialog"
            aria-modal="true"
            aria-labelledby="modular-ai-assistant-modal-title-<?php echo esc_attr($instance_id); ?>"
            x-cloak
        >
            <div class="modular-ai-assistant-modal-content">
                <div class="modular-ai-assistant-ai-header">
                    <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="modular-ai-assistant-ai-icon">
                        <path 
                            d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" 
                            stroke="var(--modular-ai-assistant-primary-color)" 
                            stroke-width="2" 
                            stroke-linecap="round" 
                            stroke-linejoin="round"
                        />
                    </svg>

                    <h3 id="modular-ai-assistant-modal-title-<?php echo esc_attr($instance_id); ?>"><?php echo esc_html($modal_title); ?></h3>
                </div>
                
                <button 
                    @click="showModal = false" 
                    class="modular-ai-assistant-modal-close"
                    aria-label="<?php esc_attr_e('Close modal', 'modular-ai-assistant'); ?>"
                    type="button"
                >
                    <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M6 18L18 6M6 6l12 12" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </button>

                <div class="modular-ai-assistant-modal-body">
                    <?php include __DIR__ . '/module-content.php'; ?>
                </div>
            
                <?php if ($context === 'editor'): ?>
                    <div class="modular-ai-assistant-modal-footer">
                        <button @click="copyAnalysis" class="modular-ai-assistant-action-btn" :disabled="!response">
                            <span class="dashicons" :class="copiedState === 'analysis' ? 'dashicons-yes' : 'dashicons-clipboard'"></span>
                            <span x-text="copiedState === 'analysis' ? '<?php esc_attr_e('Copied!', 'modular-ai-assistant'); ?>' : '<?php esc_attr_e('Copy analysis', 'modular-ai-assistant'); ?>'"></span>
                        </button>
                        <button @click="copyCurl" class="modular-ai-assistant-action-btn" :disabled="!curlPreview" x-show="curlPreview" x-cloak>
                            <span class="dashicons" :class="copiedState === 'curl' ? 'dashicons-yes' : 'dashicons-clipboard'"></span>
                            <span x-text="copiedState === 'curl' ? '<?php esc_attr_e('Copied!', 'modular-ai-assistant'); ?>' : '<?php esc_attr_e('Copy CURL', 'modular-ai-assistant'); ?>'"></span>
                        </button>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    <?php else: ?>
        <div class="modular-ai-assistant-module">
            <div class="modular-ai-assistant-ai-header">
                <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="modular-ai-assistant-ai-icon">
                    <path 
                        d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" 
                        stroke="var(--modular-ai-assistant-primary-color)" 
                        stroke-width="2" 
                        stroke-linecap="round" 
                        stroke-linejoin="round"
                    />
                </svg>

                <h3><?php echo esc_html($modal_title); ?></h3>
            </div>
            
            <?php include __DIR__ . '/module-content.php'; ?>
        </div>
    <?php endif; ?>
</div>
