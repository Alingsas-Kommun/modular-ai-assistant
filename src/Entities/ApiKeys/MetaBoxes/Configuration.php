<?php

namespace ModularAIAssistant\Entities\ApiKeys\MetaBoxes;

use ModularAIAssistant\Abstracts\MetaBox;

class Configuration extends MetaBox
{
    protected static $post_types = ['mai_api_key'];
    protected static $id = 'mai_api_key_configuration';
    protected static $priority = 'high';
    protected static $context = 'normal';

    /**
     * Get meta box title
     *
     * @return string
     */
    protected function getTitle()
    {
        return __('API Key Configuration', 'modular-ai-assistant');
    }

    /**
     * Get fields configuration
     *
     * @return array
     */
    protected function getFields()
    {
        return [
            'api_key_settings' => [
                'label' => __('API Key Settings', 'modular-ai-assistant'),
                'fields' => [
                    [
                        'id' => 'api_key_value',
                        'label' => __('API Key', 'modular-ai-assistant'),
                        'type' => 'api_key',
                        'description' => __('This key is automatically generated. Copy it to use in your API requests.', 'modular-ai-assistant')
                    ],
                    [
                        'id' => 'api_key_description',
                        'label' => __('Description', 'modular-ai-assistant'),
                        'type' => 'textarea',
                        'description' => __('Optional description for this API key (e.g., "Mobile app", "External website")', 'modular-ai-assistant')
                    ],
                    [
                        'id' => 'api_key_active',
                        'label' => __('Status', 'modular-ai-assistant'),
                        'type' => 'checkbox',
                        'description' => __('Enable or disable this API key', 'modular-ai-assistant'),
                        'default' => true
                    ],
                ],
            ],
        ];
    }

}

