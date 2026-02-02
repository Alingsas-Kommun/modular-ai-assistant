<?php
/**
 * Checkbox field template
 *
 * @var string $field_id
 * @var string $field_name
 * @var string $value
 * @var array $field
 * @var string $required
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
?>
<label>
    <input 
        type="checkbox" 
        name="<?php echo esc_attr($field_name); ?>" 
        id="<?php echo esc_attr($field_id); ?>" 
        value="1" 
        <?php checked($value, true); ?>
    >
    <?php if (isset($field['checkbox_label'])): ?>
        <?php echo esc_html($field['checkbox_label']); ?>
    <?php endif; ?>
</label>

