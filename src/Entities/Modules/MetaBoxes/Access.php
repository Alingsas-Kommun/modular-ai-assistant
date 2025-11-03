<?php

namespace ModularAI\Entities\Modules\MetaBoxes;

use ModularAI\Abstracts\MetaBox;

class Access extends MetaBox
{
    protected static $post_types = ['mai_module'];
    protected static $id = 'mai_module_access';
    protected static $priority = 'default';
    protected static $context = 'side';
    protected static $layout = 'stacked';

    /**
     * Get meta box title
     *
     * @return string
     */
    protected function getTitle()
    {
        return __('Access & Cache', 'modular-ai');
    }

    /**
     * Get fields configuration
     *
     * @return array
     */
    protected function getFields()
    {
        return [
            'access_settings' => [
                'fields' => [
                    [
                        'id' => 'public',
                        'label' => __('Public Access', 'modular-ai'),
                        'type' => 'checkbox',
                        'checkbox_label' => __('Allow public access via REST API', 'modular-ai'),
                        'default' => false,
                        'description' => __('Enable this to allow non-logged-in users to use this module via the REST API.', 'modular-ai')
                    ],
                ]
            ]
        ];
    }
}

