<?php

namespace ModularAIAssistant\Admin;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

use ModularAIAssistant\Utilities\Template;
use ModularAIAssistant\Entities\Modules\Repository as ModulesRepository;

use function ModularAIAssistant\di;

class EditorIntegration
{
    /**
     * Constructor
     *
     * @return void
     */
    public function __construct()
    {
        add_action('add_meta_boxes', [$this, 'addMetaBox']);
    }
    
    /**
     * Add meta box to post and page edit screens
     *
     * @return void
     */
    public function addMetaBox()
    {
        add_meta_box(
            'mai_editor_integration',
            __('AI Content Analysis', 'modular-ai-assistant'),
            [$this, 'renderMetaBox'],
            ['post', 'page'],
            'side',
            'low'
        );
    }
    
    /**
     * Render meta box content
     *
     * @param \WP_Post $post Post object
     * @return void
     */
    public function renderMetaBox($post)
    {
        $modulesRepository = di(ModulesRepository::class);
        $modules = $modulesRepository->findWithEditorAnalysis();
        
        Template::load('components/editor-integration', [
            'modules' => $modules,
            'post_id' => $post->ID
        ]);
    }
}

