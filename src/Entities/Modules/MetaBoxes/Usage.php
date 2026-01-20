<?php

namespace ModularAIAssistant\Entities\Modules\MetaBoxes;

use ModularAIAssistant\Abstracts\MetaBox;
use ModularAIAssistant\Utilities\Template;

use function ModularAIAssistant\getSetting;

class Usage extends MetaBox
{
    protected static $post_types = ['mai_module'];
    protected static $id = 'mai_module_usage';
    protected static $priority = 'low';
    protected static $context = 'normal';

    public function __construct()
    {
        if (! getSetting('enable_shortcode', true)) {
            return;
        }

        parent::__construct();
    }

    /**
     * Get meta box title
     *
     * @return string
     */
    protected function getTitle()
    {
        return __('Usage', 'modular-ai-assistant');
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
     * Custom render for shortcode examples
     *
     * @param \WP_Post $post
     * @return void
     */
    public function render($post)
    {
        Template::load('partials/module-usage', [
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
        // No fields to save for usage metabox
    }
}

