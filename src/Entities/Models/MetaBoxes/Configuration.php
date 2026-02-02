<?php

namespace ModularAIAssistant\Entities\Models\MetaBoxes;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

use ModularAIAssistant\Abstracts\MetaBox;

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
        return __('Model Settings', 'modular-ai-assistant');
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
                'label' => __('Model Configuration', 'modular-ai-assistant'),
                'fields' => [
                    [
                        'id' => 'model_id',
                        'label' => __('Model Name', 'modular-ai-assistant'),
                        'type' => 'text',
                        'required' => true,
                        'description' => __('e.g., gpt-4, claude-3, llama-2', 'modular-ai-assistant')
                    ],
                    [
                        'id' => 'model_endpoint',
                        'label' => __('Endpoint URL', 'modular-ai-assistant'),
                        'type' => 'url',
                        'required' => true,
                        'description' => __('Full API endpoint URL', 'modular-ai-assistant')
                    ],
                    [
                        'id' => 'model_api_key',
                        'label' => __('API Key', 'modular-ai-assistant'),
                        'type' => 'password',
                        'description' => __('Leave blank to keep existing key', 'modular-ai-assistant')
                    ],
                    [
                        'id' => 'model_active',
                        'label' => __('Options', 'modular-ai-assistant'),
                        'type' => 'checkbox',
                        'checkbox_label' => __('Active', 'modular-ai-assistant'),
                        'default' => true
                    ],
                    [
                        'id' => 'streaming_enabled',
                        'label' => __('Streaming', 'modular-ai-assistant'),
                        'type' => 'checkbox',
                        'checkbox_label' => __('Enable streaming (show response as it generates)', 'modular-ai-assistant'),
                        'default' => false
                    ],
                ]
            ]
        ];
    }
}

