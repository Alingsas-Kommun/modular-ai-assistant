<?php

namespace ModularAI\Abstracts;

use ModularAI\Utilities\Template;

if (! defined('ABSPATH')) {
    exit;
}

abstract class SettingsPage
{
    protected static $parent_slug = '';
    protected static $menu_slug = '';
    protected static $capability = 'manage_options';
    protected static $icon = '';
    protected static $position = null;

    /**
     * Constructor
     */
    public function __construct()
    {
        add_action('admin_menu', [$this, 'registerPage']);
        add_action('admin_init', [$this, 'registerSettings']);
        add_filter('wp_redirect', [$this, 'preserveTabOnSave'], 10, 2);
    }

    /**
     * Get page title
     *
     * @return string
     */
    abstract protected function getTitle();

    /**
     * Get menu title
     *
     * @return string
     */
    abstract protected function getMenuTitle();

    /**
     * Get fields configuration
     *
     * @return array
     */
    abstract protected function getFields();

    /**
     * Register the settings page
     *
     * @return void
     */
    public function registerPage()
    {
        if (static::$parent_slug) {
            add_submenu_page(
                static::$parent_slug,
                $this->getTitle(),
                $this->getMenuTitle(),
                static::$capability,
                static::$menu_slug,
                [$this, 'render']
            );
        } else {
            add_menu_page(
                $this->getTitle(),
                $this->getMenuTitle(),
                static::$capability,
                static::$menu_slug,
                [$this, 'render'],
                static::$icon,
                static::$position
            );
        }
    }

    /**
     * Register all settings
     *
     * @return void
     */
    public function registerSettings()
    {
        $fields = $this->getFields();
        
        foreach ($fields as $tab_key => $tab) {
            if (!isset($tab['sections'])) {
                continue;
            }
            
            foreach ($tab['sections'] as $section_key => $section) {
                // Register section
                add_settings_section(
                    $section_key,
                    $section['label'] ?? '',
                    function() use ($section) {
                        if (isset($section['description'])) {
                            echo '<p>' . esc_html($section['description']) . '</p>';
                        }
                    },
                    static::$menu_slug . '_' . $tab_key
                );
                
                if (!isset($section['fields'])) {
                    continue;
                }
                
                // Register fields
                foreach ($section['fields'] as $field) {
                    $option_name = '_mai_' . $field['id'];
                    
                    // Register the setting
                    register_setting(
                        static::$menu_slug . '_' . $tab_key,
                        $option_name,
                        [
                            'type' => $this->getOptionType($field['type']),
                            'sanitize_callback' => function($value) use ($field) {
                                return $this->sanitizeField($value, $field['type']);
                            },
                            'default' => $field['default'] ?? null,
                        ]
                    );
                    
                    // Add settings field
                    add_settings_field(
                        $option_name,
                        $field['label'],
                        [$this, 'renderField'],
                        static::$menu_slug . '_' . $tab_key,
                        $section_key,
                        [
                            'field' => $field,
                            'option_name' => $option_name,
                        ]
                    );
                }
            }
        }
    }

    /**
     * Get option type for register_setting
     *
     * @param string $field_type
     * @return string
     */
    protected function getOptionType($field_type)
    {
        $type_map = [
            'text' => 'string',
            'textarea' => 'string',
            'select' => 'string',
            'url' => 'string',
            'color' => 'string',
            'number' => 'integer',
            'checkbox' => 'boolean',
        ];
        
        return $type_map[$field_type] ?? 'string';
    }

    /**
     * Render the settings page
     *
     * @return void
     */
    public function render()
    {
        $fields = $this->getFields();
        $tabs = array_keys($fields);
        
        // phpcs:ignore WordPress.Security.NonceVerification.Recommended
        $active_tab = isset($_GET['tab']) ? sanitize_key($_GET['tab']) : $tabs[0];
        
        if (!isset($fields[$active_tab])) {
            $active_tab = $tabs[0];
        }
        
        Template::load('settings/wrapper', [
            'title' => $this->getTitle(),
            'tabs' => $fields,
            'active_tab' => $active_tab,
            'menu_slug' => static::$menu_slug,
            'settings_page' => $this,
        ]);
    }

    /**
     * Render individual field
     *
     * @param array $args
     * @return void
     */
    public function renderField($args)
    {
        $field = $args['field'];
        $option_name = $args['option_name'];
        $value = get_option($option_name, $field['default'] ?? '');
        
        $this->loadFieldTemplate($field['type'], [
            'field_id' => $option_name,
            'field_name' => $option_name,
            'field' => $field,
            'value' => $value,
            'required' => isset($field['required']) && $field['required'] ? 'required' : '',
        ]);
        
        if (isset($field['description'])) {
            echo '<p class="description">' . esc_html($field['description']) . '</p>';
        }
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
            'textarea' => 'textarea',
            'number' => 'number',
            'select' => 'select',
            'checkbox' => 'checkbox',
            'color' => 'color',
        ];
        
        $template_name = $template_map[$type] ?? 'text';
        
        // Reuse metabox field templates
        Template::load('fields/' . $template_name, $args);
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
                
            default:
                return sanitize_text_field($value);
        }
    }

    /**
     * Preserve tab parameter when redirecting after save
     *
     * @param string $location
     * @param int $status
     * @return string
     */
    public function preserveTabOnSave($location, $status)
    {
        // Only modify redirect if we're on our settings page
        // phpcs:ignore WordPress.Security.NonceVerification.Missing
        if (isset($_POST['option_page']) && strpos(sanitize_text_field(wp_unslash($_POST['option_page'])), static::$menu_slug) === 0) {
            // phpcs:ignore WordPress.Security.NonceVerification.Recommended
            if (isset($_GET['tab'])) {
                // phpcs:ignore WordPress.Security.NonceVerification.Recommended
                $tab = sanitize_key($_GET['tab']);
                $location = add_query_arg('tab', $tab, $location);
            }
        }
        
        return $location;
    }
}


