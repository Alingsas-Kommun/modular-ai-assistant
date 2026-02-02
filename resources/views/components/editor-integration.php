<?php
/**
 * Editor Integration Component
 *
 * @var array $modules
 * @var int $post_id
 * 
 * phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
 * These are template variables extracted from an array, not true global variables.
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
?>

<?php if (empty($modules)): ?>
    <p><?php esc_html_e('No AI modules with editor integration enabled.', 'modular-ai-assistant'); ?></p>
<?php else: ?>
    <div class="modular-ai-assistant-editor-integration">
        <p><?php esc_html_e('Analyze your content with AI to get improvement suggestions.', 'modular-ai-assistant'); ?></p>
        <p>
            <label for="modular-ai-assistant-module-selector-<?php echo esc_attr($post_id); ?>">
                <?php esc_html_e('Select AI Module:', 'modular-ai-assistant'); ?>
            </label>
        </p>
        <select id="modular-ai-assistant-module-selector-<?php echo esc_attr($post_id); ?>" class="widefat">
            <option value=""><?php esc_html_e('Select AI Module', 'modular-ai-assistant'); ?></option>
            <?php foreach ($modules as $module): ?>
                <option value="<?php echo esc_attr($module['id']); ?>">
                    <?php echo esc_html($module['title']); ?>
                </option>
            <?php endforeach; ?>
        </select>
        
        <p style="margin-top: 12px;">
            <button type="button" class="button button-primary modular-ai-assistant-analyze-btn" data-post-id="<?php echo esc_attr($post_id); ?>">
                <span class="dashicons dashicons-lightbulb"></span>
                <span><?php esc_html_e('Analyze Content', 'modular-ai-assistant'); ?></span>
            </button>
        </p>
        
        <div id="modular-ai-assistant-editor-modal-<?php echo esc_attr($post_id); ?>" style="display: none;"></div>
    </div>
<?php endif; ?>

