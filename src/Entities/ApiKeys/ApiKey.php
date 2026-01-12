<?php

namespace ModularAI\Entities\ApiKeys;

use ModularAI\Abstracts\PostType;

if (! defined('ABSPATH')) {
    exit;
}

class ApiKey extends PostType
{
    protected static $post_type_slug = 'mai_api_key';
    protected static $menu_icon = 'dashicons-admin-network';
    protected static $show_in_rest = false;
    protected static $supports = ['title'];

    /**
     * Constructor
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        
        add_action('save_post_mai_api_key', [$this, 'generateApiKey'], 10, 3);
    }

    /**
     * Get post type labels
     *
     * @return array
     */
    protected function getLabels()
    {
        return [
            'name'                  => _x('API Keys', 'Post type general name', 'modular-ai'),
            'singular_name'         => _x('API Key', 'Post type singular name', 'modular-ai'),
            'menu_name'             => _x('API Keys', 'Admin Menu text', 'modular-ai'),
            'name_admin_bar'        => _x('API Key', 'Add New on Toolbar', 'modular-ai'),
            'add_new'               => __('Add New', 'modular-ai'),
            'add_new_item'          => __('Add New API Key', 'modular-ai'),
            'new_item'              => __('New API Key', 'modular-ai'),
            'edit_item'             => __('Edit API Key', 'modular-ai'),
            'view_item'             => __('View API Key', 'modular-ai'),
            'all_items'             => __('API Keys', 'modular-ai'),
            'search_items'          => __('Search API Keys', 'modular-ai'),
            'not_found'             => __('No API keys found.', 'modular-ai'),
            'not_found_in_trash'    => __('No API keys found in Trash.', 'modular-ai'),
        ];
    }

    /**
     * Get post type description
     *
     * @return string
     */
    protected function getDescription()
    {
        return __('API keys for external access', 'modular-ai');
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
                'slug' => 'api_key',
                'title' => __('API Key', 'modular-ai'),
                'priority' => 2,
            ],
            [
                'slug' => 'status',
                'title' => __('Status', 'modular-ai'),
                'priority' => 3,
            ],
            [
                'slug' => 'created',
                'title' => __('Created', 'modular-ai'),
                'priority' => 4,
            ],
            [
                'slug' => 'last_used',
                'title' => __('Last Used', 'modular-ai'),
                'priority' => 5,
            ],
        ];
    }

    /**
     * Render api_key column
     *
     * @param int $post_id
     * @return void
     */
    protected function renderColumnApiKey($post_id)
    {
        $api_key = get_post_meta($post_id, '_mai_api_key_value', true);
        if ($api_key) {
            echo '<code>' . esc_html(substr($api_key, 0, 8)) . '...' . esc_html(substr($api_key, -4)) . '</code>';
        } else {
            echo '<em>' . esc_html__('Not generated', 'modular-ai') . '</em>';
        }
    }

    /**
     * Render status column
     *
     * @param int $post_id
     * @return void
     */
    protected function renderColumnStatus($post_id)
    {
        $active = get_post_meta($post_id, '_mai_api_key_active', true);
        if ($active) {
            echo '<span style="color: green;">●</span> ' . esc_html__('Active', 'modular-ai');
        } else {
            echo '<span style="color: red;">●</span> ' . esc_html__('Inactive', 'modular-ai');
        }
    }

    /**
     * Render created column
     *
     * @param int $post_id
     * @return void
     */
    protected function renderColumnCreated($post_id)
    {
        $post = get_post($post_id);
        echo esc_html(get_the_date('Y-m-d H:i', $post));
    }

    /**
     * Render last_used column
     *
     * @param int $post_id
     * @return void
     */
    protected function renderColumnLastUsed($post_id)
    {
        $last_used = get_post_meta($post_id, '_mai_api_key_last_used', true);
        if ($last_used) {
            echo esc_html(date_i18n('Y-m-d H:i', $last_used));
        } else {
            echo '<em>' . esc_html__('Never', 'modular-ai') . '</em>';
        }
    }

    /**
     * Generate API key on post creation
     *
     * @param int $post_id
     * @param \WP_Post $post
     * @param bool $update
     * @return void
     */
    public function generateApiKey($post_id, $post, $update)
    {
        // Only generate key for new posts
        if ($update) {
            return;
        }

        // Check if key already exists
        $existing_key = get_post_meta($post_id, '_mai_api_key_value', true);
        if ($existing_key) {
            return;
        }

        // Generate secure random key
        $api_key = 'mai_' . wp_generate_password(32, false);
        
        // Store the key
        update_post_meta($post_id, '_mai_api_key_value', $api_key);
        
        // Set as active by default
        update_post_meta($post_id, '_mai_api_key_active', true);
    }
}
