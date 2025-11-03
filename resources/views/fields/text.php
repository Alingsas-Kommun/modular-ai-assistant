<?php
/**
 * Text field template
 *
 * @var string $field_id
 * @var string $field_name
 * @var string $value
 * @var array $field
 * @var string $required
 */
?>

<input 
    type="<?php echo esc_attr($field['type']); ?>" 
    name="<?php echo esc_attr($field_name); ?>" 
    id="<?php echo esc_attr($field_id); ?>" 
    value="<?php echo esc_attr($value); ?>" 
    class="regular-text" 
    <?php echo esc_attr($required); ?>
>

