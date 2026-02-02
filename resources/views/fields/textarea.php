<?php
/**
 * Textarea field template
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

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

$rows = isset($field['rows']) ? $field['rows'] : 4;
?>

<textarea 
    name="<?php echo esc_attr($field_name); ?>" 
    id="<?php echo esc_attr($field_id); ?>" 
    rows="<?php echo esc_attr($rows); ?>" 
    class="large-text" 
    <?php echo esc_attr($required); ?>><?php echo esc_textarea($value); ?>
</textarea>

