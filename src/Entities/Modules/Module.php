<?php

namespace ModularAIAssistant\Entities\Modules;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

use ModularAIAssistant\Abstracts\PostType;

class Module extends PostType
{
    protected static $post_type_slug = 'mai_module';
    protected static $menu_icon = 'dashicons-admin-plugins';

    /**
     * Get post type labels
     *
     * @return array
     */
    protected function getLabels()
    {
        return [
            'name'                  => _x('Modules', 'Post type general name', 'modular-ai-assistant'),
            'singular_name'         => _x('Module', 'Post type singular name', 'modular-ai-assistant'),
            'menu_name'             => _x('Modules', 'Admin Menu text', 'modular-ai-assistant'),
            'name_admin_bar'        => _x('Module', 'Add New on Toolbar', 'modular-ai-assistant'),
            'add_new'               => __('Add New', 'modular-ai-assistant'),
            'add_new_item'          => __('Add New Module', 'modular-ai-assistant'),
            'new_item'              => __('New Module', 'modular-ai-assistant'),
            'edit_item'             => __('Edit Module', 'modular-ai-assistant'),
            'view_item'             => __('View Module', 'modular-ai-assistant'),
            'all_items'             => __('Modules', 'modular-ai-assistant'),
            'search_items'          => __('Search Modules', 'modular-ai-assistant'),
            'parent_item_colon'     => __('Parent Modules:', 'modular-ai-assistant'),
            'not_found'             => __('No modules found.', 'modular-ai-assistant'),
            'not_found_in_trash'    => __('No modules found in Trash.', 'modular-ai-assistant'),
            'archives'              => __('Module archives', 'modular-ai-assistant'),
            'insert_into_item'      => __('Insert into module', 'modular-ai-assistant'),
            'uploaded_to_this_item' => __('Uploaded to this module', 'modular-ai-assistant'),
            'filter_items_list'     => __('Filter modules list', 'modular-ai-assistant'),
            'items_list_navigation' => __('Modules list navigation', 'modular-ai-assistant'),
            'items_list'            => __('Modules list', 'modular-ai-assistant'),
        ];
    }

    /**
     * Get post type description
     *
     * @return string
     */
    protected function getDescription()
    {
        return __('AI modules for content generation and analysis', 'modular-ai-assistant');
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
                'slug' => 'model',
                'title' => __('Model', 'modular-ai-assistant'),
                'priority' => 2,
            ],
            [
                'slug' => 'output',
                'title' => __('Output', 'modular-ai-assistant'),
                'priority' => 3,
            ],
            [
                'slug' => 'public',
                'title' => __('Public', 'modular-ai-assistant'),
                'priority' => 4,
            ],
        ];
    }

    /**
     * Render model column
     *
     * @param int $post_id
     * @return void
     */
    protected function renderColumnModel($post_id)
    {
        $model_ref = get_post_meta($post_id, '_mai_model_ref', true);
        if ($model_ref) {
            $model_title = get_the_title($model_ref);
            echo esc_html($model_title);
        } else {
            echo '—';
        }
    }

    /**
     * Render output column
     *
     * @param int $post_id
     * @return void
     */
    protected function renderColumnOutput($post_id)
    {
        $output = get_post_meta($post_id, '_mai_output', true);
        echo esc_html($output ?: 'plain');
    }

    /**
     * Render public column
     *
     * @param int $post_id
     * @return void
     */
    protected function renderColumnPublic($post_id)
    {
        echo get_post_meta($post_id, '_mai_public', true) ? '✓' : '—';
    }

    /**
     * Render cache column
     *
     * @param int $post_id
     * @return void
     */
    protected function renderColumnCache($post_id)
    {
        $cache_ttl = get_post_meta($post_id, '_mai_cache_ttl', true);
        if ($cache_ttl) {
            echo esc_html($cache_ttl . 's');
        } else {
            echo '—';
        }
    }
}
