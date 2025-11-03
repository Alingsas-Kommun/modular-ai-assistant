<?php

namespace ModularAI\Entities\Models;

use ModularAI\Abstracts\PostType;

class Model extends PostType
{
    protected static $post_type_slug = 'mai_model';
    protected static $menu_icon = 'dashicons-admin-settings';

    /**
     * Get post type labels
     *
     * @return array
     */
    protected function getLabels()
    {
        return [
            'name'                  => _x('Models', 'Post type general name', 'modular-ai'),
            'singular_name'         => _x('Model', 'Post type singular name', 'modular-ai'),
            'menu_name'             => _x('Models', 'Admin Menu text', 'modular-ai'),
            'name_admin_bar'        => _x('Model', 'Add New on Toolbar', 'modular-ai'),
            'add_new'               => __('Add New', 'modular-ai'),
            'add_new_item'          => __('Add New Model', 'modular-ai'),
            'new_item'              => __('New Model', 'modular-ai'),
            'edit_item'             => __('Edit Model', 'modular-ai'),
            'view_item'             => __('View Model', 'modular-ai'),
            'all_items'             => __('Models', 'modular-ai'),
            'search_items'          => __('Search Models', 'modular-ai'),
            'parent_item_colon'     => __('Parent Models:', 'modular-ai'),
            'not_found'             => __('No models found.', 'modular-ai'),
            'not_found_in_trash'    => __('No models found in Trash.', 'modular-ai'),
            'archives'              => __('Model archives', 'modular-ai'),
            'insert_into_item'      => __('Insert into model', 'modular-ai'),
            'uploaded_to_this_item' => __('Uploaded to this model', 'modular-ai'),
            'filter_items_list'     => __('Filter models list', 'modular-ai'),
            'items_list_navigation' => __('Models list navigation', 'modular-ai'),
            'items_list'            => __('Models list', 'modular-ai'),
        ];
    }

    /**
     * Get post type description
     *
     * @return string
     */
    protected function getDescription()
    {
        return __('AI models configuration', 'modular-ai');
    }

    /**
     * Get custom columns configuration
     *
     * @return array
     */
    protected function getColumns()
    {
        return [
            [
                'slug' => 'model_id',
                'title' => __('Model ID', 'modular-ai'),
                'priority' => 2,
            ],
            [
                'slug' => 'endpoint',
                'title' => __('Endpoint', 'modular-ai'),
                'priority' => 3,
            ],
            [
                'slug' => 'streaming',
                'title' => __('Streaming', 'modular-ai'),
                'priority' => 4,
            ],
            [
                'slug' => 'active',
                'title' => __('Active', 'modular-ai'),
                'priority' => 5,
            ],
        ];
    }

    /**
     * Render model_id column
     *
     * @param int $post_id
     * @return void
     */
    protected function renderColumnModelId($post_id)
    {
        echo esc_html(get_post_meta($post_id, '_mai_model_id', true));
    }

    /**
     * Render endpoint column
     *
     * @param int $post_id
     * @return void
     */
    protected function renderColumnEndpoint($post_id)
    {
        echo esc_html(get_post_meta($post_id, '_mai_model_endpoint', true));
    }

    /**
     * Render streaming column
     *
     * @param int $post_id
     * @return void
     */
    protected function renderColumnStreaming($post_id)
    {
        echo get_post_meta($post_id, '_mai_streaming_enabled', true) ? '✓' : '—';
    }

    /**
     * Render active column
     *
     * @param int $post_id
     * @return void
     */
    protected function renderColumnActive($post_id)
    {
        echo get_post_meta($post_id, '_mai_model_active', true) ? '✓' : '—';
    }
}
