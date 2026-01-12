<?php
/**
 * Color field template
 *
 * @var string $field_id
 * @var string $field_name
 * @var string $value
 * @var array $field
 * @var string $required
 */

if (! defined('ABSPATH')) {
    exit;
}
?>

<input 
    type="text" 
    name="<?php echo esc_attr($field_name); ?>" 
    id="<?php echo esc_attr($field_id); ?>" 
    value="<?php echo esc_attr($value); ?>" 
    class="modular-ai-color-picker" 
    data-default-color="<?php echo esc_attr($field['default'] ?? ''); ?>"
    <?php echo esc_attr($required); ?>
>