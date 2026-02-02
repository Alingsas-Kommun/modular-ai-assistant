<?php

namespace ModularAIAssistant\Entities\Modules\MetaBoxes;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

use ModularAIAssistant\Abstracts\MetaBox;
use ModularAIAssistant\Entities\Models\Repository as ModelsRepository;
use ModularAIAssistant\Services\ModuleCacheService;

use function ModularAIAssistant\di;

class Configuration extends MetaBox
{
    protected static $post_types = ['mai_module'];
    protected static $id = 'mai_module_configuration';
    protected static $priority = 'high';
    protected static $context = 'normal';

    /**
     * Get meta box title
     *
     * @return string
     */
    protected function getTitle()
    {
        return __('Module Configuration', 'modular-ai-assistant');
    }

    /**
     * Get fields configuration
     *
     * @return array
     */
    protected function getFields()
    {
        $modelsRepository = di(ModelsRepository::class);
        $models = $modelsRepository->findActive();
        
        $model_options = ['' => __('— Select Model —', 'modular-ai-assistant')];
        foreach ($models as $model) {
            $label = $model['title'];
            if (!empty($model['model_id'])) {
                $label .= ' (' . $model['model_id'] . ')';
            }
            $model_options[$model['id']] = $label;
        }
        
        return [
            'basic_configuration' => [
                'label' => __('Basic Configuration', 'modular-ai-assistant'),
                'fields' => [
                    [
                        'id' => 'model_ref',
                        'label' => __('AI Model', 'modular-ai-assistant'),
                        'type' => 'select',
                        'required' => true,
                        'options' => $model_options,
                        'description' => __('Select which AI model to use for this module.', 'modular-ai-assistant')
                    ],
                    [
                        'id' => 'system',
                        'label' => __('System Prompt', 'modular-ai-assistant'),
                        'type' => 'textarea',
                        'rows' => 6,
                        'required' => true,
                        'description' => __('Instructions for the AI. This defines the AI\'s role and behavior.', 'modular-ai-assistant')
                    ],
                    [
                        'id' => 'user_prompt_type',
                        'label' => __('Content Source', 'modular-ai-assistant'),
                        'type' => 'select',
                        'default' => 'custom',
                        'options' => [
                            'custom' => __('Custom text (below)', 'modular-ai-assistant'),
                            'page_content' => __('All page text', 'modular-ai-assistant'),
                            'page_title' => __('Page title only', 'modular-ai-assistant'),
                            'page_excerpt' => __('Page excerpt only', 'modular-ai-assistant'),
                        ],
                        'description' => __('What content should be sent to the AI?', 'modular-ai-assistant')
                    ],
                    [
                        'id' => 'user',
                        'label' => __('Custom Text', 'modular-ai-assistant'),
                        'type' => 'textarea',
                        'rows' => 4,
                        'description' => __('Used only if "Custom text" is selected above.', 'modular-ai-assistant')
                    ],
                    [
                        'id' => 'output',
                        'label' => __('Output Format', 'modular-ai-assistant'),
                        'type' => 'select',
                        'default' => 'plain',
                        'options' => [
                            'plain' => __('Plain Text (escaped)', 'modular-ai-assistant'),
                            'html' => __('HTML (sanitized)', 'modular-ai-assistant'),
                        ],
                        'description' => __('How should the AI response be formatted?', 'modular-ai-assistant')
                    ],
                    [
                        'id' => 'markdown_enabled',
                        'label' => __('Markdown', 'modular-ai-assistant'),
                        'type' => 'checkbox',
                        'checkbox_label' => __('Enable markdown formatting', 'modular-ai-assistant'),
                        'default' => false,
                        'description' => __('Convert markdown syntax to HTML in AI responses.', 'modular-ai-assistant')
                    ],
                    [
                        'id' => 'editor_analysis_enabled',
                        'label' => __('Editor Integration', 'modular-ai-assistant'),
                        'type' => 'checkbox',
                        'checkbox_label' => __('Enable AI analysis button in the editor', 'modular-ai-assistant'),
                        'default' => false,
                        'description' => __('Adds an analysis button in the editor that uses this AI module to analyze content.', 'modular-ai-assistant')
                    ],
                    [
                        'id' => 'streaming_override',
                        'label' => __('Streaming', 'modular-ai-assistant'),
                        'type' => 'select',
                        'default' => 'model_default',
                        'options' => [
                            'model_default' => __('Use model default', 'modular-ai-assistant'),
                            'enabled' => __('Enabled', 'modular-ai-assistant'),
                            'disabled' => __('Disabled', 'modular-ai-assistant'),
                        ],
                        'description' => __('Control how responses are delivered. Streaming shows content as it generates. Instant is better for short responses or cached content.', 'modular-ai-assistant')
                    ],
                ]
            ],
            'cache' => [
                'label' => __('Cache', 'modular-ai-assistant'),
                'fields' => [
                    [
                        'id' => 'cache_ttl',
                        'label' => __('Cache TTL', 'modular-ai-assistant'),
                        'type' => 'number',
                        'default' => 0,
                        'description' => __('Cache duration in seconds. Use 0 to turn off caching.', 'modular-ai-assistant')
                    ],
                ],
            ]
        ];
    }

    /**
     * Save meta box data and clear cache
     *
     * @param int $post_id
     * @return void
     */
    public function save($post_id)
    {
        // Call parent save method
        parent::save($post_id);
        
        // Clear cache for this module when configuration is updated
        $cacheService = di(ModuleCacheService::class);
        $cacheService->clear($post_id);
    }
}

