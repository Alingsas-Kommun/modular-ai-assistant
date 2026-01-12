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

if (! defined('ABSPATH')) {
    exit;
}
?>

<?php if (empty($modules)): ?>
    <p><?php esc_html_e('No AI modules with editor integration enabled.', 'modular-ai'); ?></p>
<?php else: ?>
    <div class="modular-ai-editor-integration">
        <p><?php esc_html_e('Analyze your content with AI to get improvement suggestions.', 'modular-ai'); ?></p>
        <p>
            <label for="modular-ai-module-selector-<?php echo esc_attr($post_id); ?>">
                <?php esc_html_e('Select AI Module:', 'modular-ai'); ?>
            </label>
        </p>
        <select id="modular-ai-module-selector-<?php echo esc_attr($post_id); ?>" class="widefat">
            <option value=""><?php esc_html_e('Select AI Module', 'modular-ai'); ?></option>
            <?php foreach ($modules as $module): ?>
                <option value="<?php echo esc_attr($module['id']); ?>">
                    <?php echo esc_html($module['title']); ?>
                </option>
            <?php endforeach; ?>
        </select>
        
        <p style="margin-top: 12px;">
            <button type="button" class="button button-primary modular-ai-analyze-btn" data-post-id="<?php echo esc_attr($post_id); ?>">
                <span class="dashicons dashicons-lightbulb"></span>
                <span><?php esc_html_e('Analyze Content', 'modular-ai'); ?></span>
            </button>
        </p>
        
        <div id="modular-ai-editor-modal-<?php echo esc_attr($post_id); ?>" style="display: none;"></div>
    </div>
<?php endif; ?>

