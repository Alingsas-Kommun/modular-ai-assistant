<?php

namespace ModularAI\Entities\Modules\MetaBoxes;

use ModularAI\Abstracts\MetaBox;
use ModularAI\Entities\Models\Repository as ModelsRepository;
use ModularAI\Services\ModuleCacheService;

use function ModularAI\di;

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
        return __('Module Configuration', 'modular-ai');
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
        
        $model_options = ['' => __('— Select Model —', 'modular-ai')];
        foreach ($models as $model) {
            $label = $model['title'];
            if (!empty($model['model_id'])) {
                $label .= ' (' . $model['model_id'] . ')';
            }
            $model_options[$model['id']] = $label;
        }
        
        return [
            'basic_configuration' => [
                'label' => __('Basic Configuration', 'modular-ai'),
                'fields' => [
                    [
                        'id' => 'model_ref',
                        'label' => __('AI Model', 'modular-ai'),
                        'type' => 'select',
                        'required' => true,
                        'options' => $model_options,
                        'description' => __('Select which AI model to use for this module.', 'modular-ai')
                    ],
                    [
                        'id' => 'system',
                        'label' => __('System Prompt', 'modular-ai'),
                        'type' => 'textarea',
                        'rows' => 6,
                        'required' => true,
                        'description' => __('Instructions for the AI. This defines the AI\'s role and behavior.', 'modular-ai')
                    ],
                    [
                        'id' => 'user_prompt_type',
                        'label' => __('Content Source', 'modular-ai'),
                        'type' => 'select',
                        'default' => 'custom',
                        'options' => [
                            'custom' => __('Custom text (below)', 'modular-ai'),
                            'page_content' => __('All page text', 'modular-ai'),
                            'page_title' => __('Page title only', 'modular-ai'),
                            'page_excerpt' => __('Page excerpt only', 'modular-ai'),
                        ],
                        'description' => __('What content should be sent to the AI?', 'modular-ai')
                    ],
                    [
                        'id' => 'user',
                        'label' => __('Custom Text', 'modular-ai'),
                        'type' => 'textarea',
                        'rows' => 4,
                        'description' => __('Used only if "Custom text" is selected above.', 'modular-ai')
                    ],
                    [
                        'id' => 'output',
                        'label' => __('Output Format', 'modular-ai'),
                        'type' => 'select',
                        'default' => 'plain',
                        'options' => [
                            'plain' => __('Plain Text (escaped)', 'modular-ai'),
                            'html' => __('HTML (sanitized)', 'modular-ai'),
                        ],
                        'description' => __('How should the AI response be formatted?', 'modular-ai')
                    ],
                    [
                        'id' => 'markdown_enabled',
                        'label' => __('Markdown', 'modular-ai'),
                        'type' => 'checkbox',
                        'checkbox_label' => __('Enable markdown formatting', 'modular-ai'),
                        'default' => false,
                        'description' => __('Convert markdown syntax to HTML in AI responses.', 'modular-ai')
                    ],
                    [
                        'id' => 'editor_analysis_enabled',
                        'label' => __('Editor Integration', 'modular-ai'),
                        'type' => 'checkbox',
                        'checkbox_label' => __('Enable AI analysis button in the editor', 'modular-ai'),
                        'default' => false,
                        'description' => __('Adds an analysis button in the editor that uses this AI module to analyze content.', 'modular-ai')
                    ],
                    [
                        'id' => 'streaming_override',
                        'label' => __('Streaming', 'modular-ai'),
                        'type' => 'select',
                        'default' => 'model_default',
                        'options' => [
                            'model_default' => __('Use model default', 'modular-ai'),
                            'enabled' => __('Enabled', 'modular-ai'),
                            'disabled' => __('Disabled', 'modular-ai'),
                        ],
                        'description' => __('Control how responses are delivered. Streaming shows content as it generates. Instant is better for short responses or cached content.', 'modular-ai')
                    ],
                ]
            ],
            'cache' => [
                'label' => __('Cache', 'modular-ai'),
                'fields' => [
                    [
                        'id' => 'cache_ttl',
                        'label' => __('Cache TTL', 'modular-ai'),
                        'type' => 'number',
                        'default' => 0,
                        'description' => __('Cache duration in seconds. Use 0 to turn off caching.', 'modular-ai')
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

