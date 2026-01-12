<?php
/**
 * MetaBox Field Row Template
 *
 * @var string $field_id
 * @var string $field_name
 * @var array $field
 * @var mixed $value
 * @var WP_Post $post
 * @var string $required
 * @var string $required_mark
 * @var MetaBox $metabox
**/

if (! defined('ABSPATH')) {
    exit;
}
?>
<tr>
    <th scope="row">
        <label for="<?php echo esc_attr($field_id); ?>">
            <?php echo esc_html($field['label']); ?><?php echo wp_kses_post($required_mark); ?>
        </label>
    </th>
    <td>
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
        
        <?php if (isset($field['description'])): ?>
            <p class="description"><?php echo esc_html($field['description']); ?></p>
        <?php endif; ?>
    </td>
</tr>

