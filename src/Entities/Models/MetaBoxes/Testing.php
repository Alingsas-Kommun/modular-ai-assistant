<?php

namespace ModularAIAssistant\Entities\Models\MetaBoxes;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

use ModularAIAssistant\Abstracts\MetaBox;
use ModularAIAssistant\Utilities\Template;

class Testing extends MetaBox
{
    protected static $post_types = ['mai_model'];
    protected static $id = 'mai_model_testing';
    protected static $priority = 'default';
    protected static $context = 'normal';

    /**
     * Get meta box title
     *
     * @return string
     */
    protected function getTitle()
    {
        return __('Test Connection', 'modular-ai-assistant');
    }

    /**
     * Get fields configuration
     *
     * @return array
     */
    protected function getFields()
    {
        // This metabox has custom rendering, no standard fields
        return [];
    }

    /**
     * Custom render for test connection
     *
     * @param \WP_Post $post
     * @return void
     */
    public function render($post)
    {
        Template::load('partials/model-testing', [
            'post' => $post,
        ]);
    }

    /**
     * Override save to do nothing for this metabox
     *
     * @param int $post_id
     * @return void
     */
    public function save($post_id)
    {
        // No fields to save for test connection metabox
    }
}

