<?php

namespace ModularAI\Entities\Models\MetaBoxes;

use ModularAI\Abstracts\MetaBox;

class Configuration extends MetaBox
{
    protected static $post_types = ['mai_model'];
    protected static $id = 'mai_model_configuration';
    protected static $priority = 'high';
    protected static $context = 'normal';

    /**
     * Get meta box title
     *
     * @return string
     */
    protected function getTitle()
    {
        return __('Model Settings', 'modular-ai');
    }

    /**
     * Get fields configuration
     *
     * @return array
     */
    protected function getFields()
    {
        return [
            'model_settings' => [
                'label' => __('Model Configuration', 'modular-ai'),
                'fields' => [
                    [
                        'id' => 'model_id',
                        'label' => __('Model Name', 'modular-ai'),
                        'type' => 'text',
                        'required' => true,
                        'description' => __('e.g., gpt-4, claude-3, llama-2', 'modular-ai')
                    ],
                    [
                        'id' => 'model_endpoint',
                        'label' => __('Endpoint URL', 'modular-ai'),
                        'type' => 'url',
                        'required' => true,
                        'description' => __('Full API endpoint URL', 'modular-ai')
                    ],
                    [
                        'id' => 'model_api_key',
                        'label' => __('API Key', 'modular-ai'),
                        'type' => 'password',
                        'description' => __('Leave blank to keep existing key', 'modular-ai')
                    ],
                    [
                        'id' => 'model_active',
                        'label' => __('Options', 'modular-ai'),
                        'type' => 'checkbox',
                        'checkbox_label' => __('Active', 'modular-ai'),
                        'default' => true
                    ],
                    [
                        'id' => 'streaming_enabled',
                        'label' => __('Streaming', 'modular-ai'),
                        'type' => 'checkbox',
                        'checkbox_label' => __('Enable streaming (show response as it generates)', 'modular-ai'),
                        'default' => false
                    ],
                ]
            ]
        ];
    }
}

