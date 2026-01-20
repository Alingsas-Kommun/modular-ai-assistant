<?php

namespace ModularAIAssistant\Abstracts;

use ModularAIAssistant\Utilities\Template;

if (! defined('ABSPATH')) {
    exit;
}

abstract class MetaBox
{
    protected static $post_types = [];
    protected static $id = '';
    protected static $priority = 'default';
    protected static $context = 'advanced';
    protected static $layout = 'table'; // 'table' or 'stacked'

    /**
     * Constructor
     */
    public function __construct()
    {
        add_action('add_meta_boxes', [$this, 'addMetaBox']);
        
        foreach (static::$post_types as $post_type) {
            add_action("save_post_{$post_type}", [$this, 'save'], 10, 1);
        }
        
        add_action('init', [$this, 'registerMeta']);
    }

    /**
     * Add meta box
     *
     * @return void
     */
    public function addMetaBox()
    {
        foreach (static::$post_types as $post_type) {
            add_meta_box(
                static::$id,
                $this->getTitle(),
                [$this, 'render'],
                $post_type,
                static::$context,
                static::$priority
            );
        }
    }

    /**
     * Register all meta fields
     *
     * @return void
     */
    public function registerMeta()
    {
        $fields = $this->getFields();
        
        foreach ($fields as $section) {
            if (!isset($section['fields'])) {
                continue;
            }
            
            foreach ($section['fields'] as $field) {
                foreach (static::$post_types as $post_type) {
                    $args = [
                        'type'         => $this->getMetaType($field['type']),
                        'single'       => true,
                        'show_in_rest' => $field['type'] !== 'password',
                    ];
                    
                    if (isset($field['default'])) {
                        $args['default'] = $field['default'];
                    }
                    
                    if ($field['type'] === 'textarea' || $field['type'] === 'text') {
                        $args['sanitize_callback'] = $field['type'] === 'textarea' 
                            ? 'sanitize_textarea_field' 
                            : 'sanitize_text_field';
                    } elseif ($field['type'] === 'url') {
                        $args['sanitize_callback'] = 'esc_url_raw';
                    }
                    
                    register_post_meta($post_type, '_mai_' . $field['id'], $args);
                }
            }
        }
    }

    /**
     * Get meta type for register_post_meta
     *
     * @param string $field_type
     * @return string
     */
    protected function getMetaType($field_type)
    {
        $type_map = [
            'text'     => 'string',
            'textarea' => 'string',
            'select'   => 'string',
            'url'      => 'string',
            'password' => 'string',
            'color'    => 'string',
            'number'   => 'integer',
            'checkbox' => 'boolean',
        ];
        
        return $type_map[$field_type] ?? 'string';
    }

    /**
     * Render meta box
     *
     * @param \WP_Post $post
     * @return void
     */
    public function render($post)
    {
        ob_start();
        wp_nonce_field(static::$id . '_nonce_action', static::$id . '_nonce');
        $nonce_field = ob_get_clean();
        
        Template::load('metabox/wrapper', [
            'post' => $post,
            'fields' => $this->getFields(),
            'nonce_field' => $nonce_field,
            'nonce_name' => static::$id . '_nonce',
            'metabox' => $this,
            'layout' => static::$layout,
        ]);
    }

    /**
     * Render individual field row (called from template)
     *
     * @param array $field
     * @param mixed $value
     * @param \WP_Post $post
     * @return void
     */
    public function renderFieldRow($field, $value, $post)
    {
        Template::load('metabox/field-row', [
            'field_id' => 'mai_' . $field['id'],
            'field_name' => '_mai_' . $field['id'],
            'field' => $field,
            'value' => $value,
            'post' => $post,
            'required' => isset($field['required']) && $field['required'] ? 'required' : '',
            'required_mark' => (isset($field['required']) && $field['required']) ? ' <span class="required">*</span>' : '',
            'metabox' => $this,
        ]);
    }

    /**
     * Render individual field in stacked layout (called from template)
     *
     * @param array $field
     * @param mixed $value
     * @param \WP_Post $post
     * @return void
     */
    public function renderFieldStacked($field, $value, $post)
    {
        Template::load('metabox/field-stacked', [
            'field_id' => 'mai_' . $field['id'],
            'field_name' => '_mai_' . $field['id'],
            'field' => $field,
            'value' => $value,
            'post' => $post,
            'required' => isset($field['required']) && $field['required'] ? 'required' : '',
            'required_mark' => (isset($field['required']) && $field['required']) ? ' <span class="required">*</span>' : '',
            'metabox' => $this,
        ]);
    }

    /**
     * Load field template
     *
     * @param string $type
     * @param array $args
     * @return void
     */
    public function loadFieldTemplate($type, $args)
    {
        // Map field types to template names
        $template_map = [
            'text' => 'text',
            'url' => 'text',
            'password' => 'text',
            'textarea' => 'textarea',
            'number' => 'number',
            'select' => 'select',
            'checkbox' => 'checkbox',
            'color' => 'color',
            'api_key' => 'api-key',
        ];
        
        $template_name = $template_map[$type] ?? 'text';
        
        Template::load('fields/' . $template_name, $args);
    }

    /**
     * Save meta box data
     *
     * @param int $post_id
     * @return void
     */
    public function save($post_id)
    {
        $nonce_field = static::$id . '_nonce';
        $nonce_action = static::$id . '_nonce_action';
        
        // Check nonce - nonces are validated by wp_verify_nonce(), not sanitized
        // phpcs:disable WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
        if (!isset($_POST[$nonce_field]) || 
            !wp_verify_nonce(wp_unslash($_POST[$nonce_field]), $nonce_action)) {
            return;
        }
        
        // Check permissions
        if (!current_user_can('edit_post', $post_id)) {
            return;
        }
        
        // Check autosave
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }
        
        $fields = $this->getFields();
        
        foreach ($fields as $section) {
            if (!isset($section['fields'])) {
                continue;
            }
            
            foreach ($section['fields'] as $field) {
                $field_name = '_mai_' . $field['id'];
                
                if ($field['type'] === 'checkbox') {
                    $value = isset($_POST[$field_name]) ? true : false;
                    update_post_meta($post_id, $field_name, $value);
                } elseif (isset($_POST[$field_name])) {
                    // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
                    $value = wp_unslash($_POST[$field_name]);
                    $sanitized = $this->sanitizeField($value, $field['type']);
                    update_post_meta($post_id, $field_name, $sanitized);
                }
            }
        }
    }

    /**
     * Sanitize field based on type
     *
     * @param mixed $value
     * @param string $type
     * @return mixed
     */
    protected function sanitizeField($value, $type)
    {
        switch ($type) {
            case 'text':
            case 'select':
                return sanitize_text_field($value);
                
            case 'textarea':
                return sanitize_textarea_field($value);
                
            case 'url':
                return esc_url_raw($value);
                
            case 'color':
                return sanitize_hex_color($value);
                
            case 'number':
                return absint($value);
                
            case 'checkbox':
                return (bool) $value;
                
            case 'password':
                // No sanitization for passwords
                return $value;
                
            default:
                return sanitize_text_field($value);
        }
    }

    /**
     * Get meta box title
     *
     * @return string
     */
    abstract protected function getTitle();

    /**
     * Get fields configuration
     *
     * @return array
     */
    abstract protected function getFields();
}

