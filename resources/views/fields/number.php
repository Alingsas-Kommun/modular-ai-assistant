<?php
/**
 * Number field template
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

$min = isset($field['min']) ? $field['min'] : 0;
$step = isset($field['step']) ? $field['step'] : 1;
?>

<input 
    type="number" 
    name="<?php echo esc_attr($field_name); ?>" 
    id="<?php echo esc_attr($field_id); ?>" 
    value="<?php echo esc_attr($value); ?>" 
    min="<?php echo esc_attr($min); ?>" 
    step="<?php echo esc_attr($step); ?>" 
    class="small-text" 
    <?php echo esc_attr($required); ?>
>
<?php if (isset($field['suffix'])): ?>
    <?php echo esc_html($field['suffix']); ?>
<?php endif; ?>

