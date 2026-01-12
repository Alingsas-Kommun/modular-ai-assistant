<?php
/**
 * Select field template
 *
 * @var string $field_id
 * @var string $field_name
 * @var string $value
 * @var array $field
 * @var string $required
 * 
 * phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
 * These are template variables extracted from an array, not true global variables.
**/

if (! defined('ABSPATH')) {
    exit;
}
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

