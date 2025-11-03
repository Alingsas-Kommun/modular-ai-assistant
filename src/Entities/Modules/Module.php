<?php

namespace ModularAI\Entities\Modules;

use ModularAI\Abstracts\PostType;

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
            'name'                  => _x('Modules', 'Post type general name', 'modular-ai'),
            'singular_name'         => _x('Module', 'Post type singular name', 'modular-ai'),
            'menu_name'             => _x('Modules', 'Admin Menu text', 'modular-ai'),
            'name_admin_bar'        => _x('Module', 'Add New on Toolbar', 'modular-ai'),
            'add_new'               => __('Add New', 'modular-ai'),
            'add_new_item'          => __('Add New Module', 'modular-ai'),
            'new_item'              => __('New Module', 'modular-ai'),
            'edit_item'             => __('Edit Module', 'modular-ai'),
            'view_item'             => __('View Module', 'modular-ai'),
            'all_items'             => __('Modules', 'modular-ai'),
            'search_items'          => __('Search Modules', 'modular-ai'),
            'parent_item_colon'     => __('Parent Modules:', 'modular-ai'),
            'not_found'             => __('No modules found.', 'modular-ai'),
            'not_found_in_trash'    => __('No modules found in Trash.', 'modular-ai'),
            'archives'              => __('Module archives', 'modular-ai'),
            'insert_into_item'      => __('Insert into module', 'modular-ai'),
            'uploaded_to_this_item' => __('Uploaded to this module', 'modular-ai'),
            'filter_items_list'     => __('Filter modules list', 'modular-ai'),
            'items_list_navigation' => __('Modules list navigation', 'modular-ai'),
            'items_list'            => __('Modules list', 'modular-ai'),
        ];
    }

    /**
     * Get post type description
     *
     * @return string
     */
    protected function getDescription()
    {
        return __('AI modules for content generation and analysis', 'modular-ai');
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
                'title' => __('Model', 'modular-ai'),
                'priority' => 2,
            ],
            [
                'slug' => 'output',
                'title' => __('Output', 'modular-ai'),
                'priority' => 3,
            ],
            [
                'slug' => 'public',
                'title' => __('Public', 'modular-ai'),
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
