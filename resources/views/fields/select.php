<?php
/**
 * Select field template
 *
 * @var string $field_id
 * @var string $field_name
 * @var string $value
 * @var array $field
 * @var string $required
 */
?>
<select 
    name="<?php echo esc_attr($field_name); ?>" 
    id="<?php echo esc_attr($field_id); ?>" 
    class="regular-text" 
    <?php echo esc_attr($required); ?>
>
    <?php if (isset($field['options'])): ?>
        <?php foreach ($field['options'] as $option_value => $option_label): ?>
            <option value="<?php echo esc_attr($option_value); ?>" <?php selected($value, $option_value); ?>>
                <?php echo esc_html($option_label); ?>
            </option>
        <?php endforeach; ?>
    <?php endif; ?>
</select>

