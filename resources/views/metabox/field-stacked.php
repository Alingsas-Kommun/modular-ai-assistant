<?php
/**
 * MetaBox Stacked Field Template
 *
 * @var string $field_id
 * @var string $field_name
 * @var array $field
 * @var mixed $value
 * @var WP_Post $post
 * @var string $required
 * @var string $required_mark
 * @var MetaBox $metabox
 */
?>

<div class="modular-ai-field-stacked" style="margin-bottom: 20px;">
    <label for="<?php echo esc_attr($field_id); ?>" style="display: block; margin-bottom: 5px; font-weight: 600;">
        <?php echo esc_html($field['label']); ?><?php echo wp_kses_post($required_mark); ?>
    </label>
    
    <div class="modular-ai-field-input">
        <?php
        $metabox->loadFieldTemplate($field['type'], [
            'field_id' => $field_id,
            'field_name' => $field_name,
            'value' => $value,
            'field' => $field,
            'required' => $required,
            'post' => $post
        ]);
        ?>
    </div>
    
    <?php if (isset($field['description'])): ?>
        <p class="description" style="margin-top: 5px; margin-bottom: 0;">
            <?php echo esc_html($field['description']); ?>
        </p>
    <?php endif; ?>
</div>

