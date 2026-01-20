<?php

namespace ModularAIAssistant\Admin;

use ModularAIAssistant\Abstracts\SettingsPage;

class Settings extends SettingsPage
{
    protected static $parent_slug = 'modular-ai-assistant';
    protected static $menu_slug = 'modular-ai-assistant-settings';
    protected static $capability = 'manage_options';

    /**
     * Get page title
     *
     * @return string
     */
    protected function getTitle()
    {
        return __('Modular AI Assistant Settings', 'modular-ai-assistant');
    }

    /**
     * Get menu title
     *
     * @return string
     */
    protected function getMenuTitle()
    {
        return __('Settings', 'modular-ai-assistant');
    }

    /**
     * Get fields configuration
     *
     * @return array
     */
    protected function getFields()
    {
        return [
            'general' => [
                'label' => __('General', 'modular-ai-assistant'),
                'sections' => [
                    'general_settings' => [
                        'label' => __('Toggle Features', 'modular-ai-assistant'),
                        'description' => __('Toggle the features of the plugin.', 'modular-ai-assistant'),
                        'fields' => [
                            [
                                'id' => 'enable_shortcode',
                                'label' => __('Enable Shortcode', 'modular-ai-assistant'),
                                'type' => 'checkbox',
                                'description' => __('Enable the [modular-ai-assistant] shortcode functionality.', 'modular-ai-assistant'),
                                'default' => true,
                            ],
                        ],
                    ],
                ],
            ],
            'appearance' => [
                'label' => __('Appearance', 'modular-ai-assistant'),
                'sections' => [
                    'color_settings' => [
                        'label' => __('Color Settings', 'modular-ai-assistant'),
                        'description' => __('Customize the colors used in the frontend display.', 'modular-ai-assistant'),
                        'fields' => [
                            [
                                'id' => 'primary_color',
                                'label' => __('Primary Color', 'modular-ai-assistant'),
                                'type' => 'color',
                                'description' => __('The primary color (default: purple #9333ea).', 'modular-ai-assistant'),
                                'default' => '#9333ea',
                            ],
                            [
                                'id' => 'secondary_color',
                                'label' => __('Secondary Color', 'modular-ai-assistant'),
                                'type' => 'color',
                                'description' => __('The secondary color (default: blue #3b82f6).', 'modular-ai-assistant'),
                                'default' => '#3b82f6',
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }
}


