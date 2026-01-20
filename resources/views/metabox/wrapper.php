<?php
/**
 * MetaBox Wrapper Template
 *
 * @var WP_Post $post
 * @var array $fields
 * @var string $nonce_field
 * @var string $nonce_name
 * @var MetaBox $metabox
 * @var string $layout 'table' or 'stacked'
 * 
 * phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
 * These are template variables extracted from an array, not true global variables.
**/

if (! defined('ABSPATH')) {
    exit;
}
?>

<?php
    // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
    echo $nonce_field;
?>

<div class="modular-ai-assistant-metabox-wrapper modular-ai-assistant-layout-<?php echo esc_attr($layout); ?>">
    <?php foreach ($fields as $section_key => $section): ?>
        <?php if (isset($section['label'])): ?>
            <h3><?php echo esc_html($section['label']); ?></h3>
        <?php endif; ?>
        
        <?php if (isset($section['fields'])): ?>
            <?php if ($layout === 'stacked'): ?>
                <div class="modular-ai-assistant-fields-stacked">
                    <?php foreach ($section['fields'] as $field): ?>
                        <?php
                            $value = get_post_meta($post->ID, '_mai_' . $field['id'], true);
                            $metabox->renderFieldStacked($field, $value, $post);
                        ?>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <table class="form-table">
                    <?php foreach ($section['fields'] as $field): ?>
                        <?php
                            $value = get_post_meta($post->ID, '_mai_' . $field['id'], true);
                            $metabox->renderFieldRow($field, $value, $post);
                        ?>
                    <?php endforeach; ?>
                </table>
            <?php endif; ?>
        <?php endif; ?>
    <?php endforeach; ?>
</div>

