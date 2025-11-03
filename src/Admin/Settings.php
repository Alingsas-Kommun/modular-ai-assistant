<?php

namespace ModularAI\Admin;

use ModularAI\Abstracts\SettingsPage;

class Settings extends SettingsPage
{
    protected static $parent_slug = 'modular-ai';
    protected static $menu_slug = 'modular-ai-settings';
    protected static $capability = 'manage_options';

    /**
     * Get page title
     *
     * @return string
     */
    protected function getTitle()
    {
        return __('Modular AI Settings', 'modular-ai');
    }

    /**
     * Get menu title
     *
     * @return string
     */
    protected function getMenuTitle()
    {
        return __('Settings', 'modular-ai');
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
                'label' => __('General', 'modular-ai'),
                'sections' => [
                    'general_settings' => [
                        'label' => __('Toggle Features', 'modular-ai'),
                        'description' => __('Toggle the features of the plugin.', 'modular-ai'),
                        'fields' => [
                            [
                                'id' => 'enable_shortcode',
                                'label' => __('Enable Shortcode', 'modular-ai'),
                                'type' => 'checkbox',
                                'description' => __('Enable the [modular-ai] shortcode functionality.', 'modular-ai'),
                                'default' => true,
                            ],
                        ],
                    ],
                ],
            ],
            'appearance' => [
                'label' => __('Appearance', 'modular-ai'),
                'sections' => [
                    'color_settings' => [
                        'label' => __('Color Settings', 'modular-ai'),
                        'description' => __('Customize the colors used in the frontend display.', 'modular-ai'),
                        'fields' => [
                            [
                                'id' => 'primary_color',
                                'label' => __('Primary Color', 'modular-ai'),
                                'type' => 'color',
                                'description' => __('The primary color (default: purple #9333ea).', 'modular-ai'),
                                'default' => '#9333ea',
                            ],
                            [
                                'id' => 'secondary_color',
                                'label' => __('Secondary Color', 'modular-ai'),
                                'type' => 'color',
                                'description' => __('The secondary color (default: blue #3b82f6).', 'modular-ai'),
                                'default' => '#3b82f6',
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }
}


