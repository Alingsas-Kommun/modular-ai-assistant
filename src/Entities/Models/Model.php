<?php

namespace ModularAIAssistant\Entities\Models;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

use ModularAIAssistant\Abstracts\PostType;

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
            'name'                  => _x('Models', 'Post type general name', 'modular-ai-assistant'),
            'singular_name'         => _x('Model', 'Post type singular name', 'modular-ai-assistant'),
            'menu_name'             => _x('Models', 'Admin Menu text', 'modular-ai-assistant'),
            'name_admin_bar'        => _x('Model', 'Add New on Toolbar', 'modular-ai-assistant'),
            'add_new'               => __('Add New', 'modular-ai-assistant'),
            'add_new_item'          => __('Add New Model', 'modular-ai-assistant'),
            'new_item'              => __('New Model', 'modular-ai-assistant'),
            'edit_item'             => __('Edit Model', 'modular-ai-assistant'),
            'view_item'             => __('View Model', 'modular-ai-assistant'),
            'all_items'             => __('Models', 'modular-ai-assistant'),
            'search_items'          => __('Search Models', 'modular-ai-assistant'),
            'parent_item_colon'     => __('Parent Models:', 'modular-ai-assistant'),
            'not_found'             => __('No models found.', 'modular-ai-assistant'),
            'not_found_in_trash'    => __('No models found in Trash.', 'modular-ai-assistant'),
            'archives'              => __('Model archives', 'modular-ai-assistant'),
            'insert_into_item'      => __('Insert into model', 'modular-ai-assistant'),
            'uploaded_to_this_item' => __('Uploaded to this model', 'modular-ai-assistant'),
            'filter_items_list'     => __('Filter models list', 'modular-ai-assistant'),
            'items_list_navigation' => __('Models list navigation', 'modular-ai-assistant'),
            'items_list'            => __('Models list', 'modular-ai-assistant'),
        ];
    }

    /**
     * Get post type description
     *
     * @return string
     */
    protected function getDescription()
    {
        return __('AI models configuration', 'modular-ai-assistant');
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
                'title' => __('Model ID', 'modular-ai-assistant'),
                'priority' => 2,
            ],
            [
                'slug' => 'endpoint',
                'title' => __('Endpoint', 'modular-ai-assistant'),
                'priority' => 3,
            ],
            [
                'slug' => 'streaming',
                'title' => __('Streaming', 'modular-ai-assistant'),
                'priority' => 4,
            ],
            [
                'slug' => 'active',
                'title' => __('Active', 'modular-ai-assistant'),
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
