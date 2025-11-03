<?php

namespace ModularAI\Entities\ApiKeys;

use ModularAI\Entities\ApiKeys\MetaBoxes\Configuration;

class Repository
{
    /**
     * Constructor - Initializes PostType and MetaBoxes
     *
     * @return void
     */
    public function __construct()
    {
        // Register post type
        new ApiKey();
        
        // Register meta boxes
        new Configuration();
    }

    /**
     * Find an API key by post ID
     *
     * @param int $id API key post ID
     * @return array|null API key data or null if not found
     */
    public function find($id): ?array
    {
        $post = get_post($id);
        
        if (!$post || $post->post_type !== 'mai_api_key') {
            return null;
        }
        
        return [
            'id' => $post->ID,
            'title' => $post->post_title,
            'key' => get_post_meta($id, '_mai_api_key_value', true),
            'active' => (bool) get_post_meta($id, '_mai_api_key_active', true),
            'description' => get_post_meta($id, '_mai_api_key_description', true),
            'last_used' => get_post_meta($id, '_mai_api_key_last_used', true),
            'created' => strtotime($post->post_date),
        ];
    }

    /**
     * Find an API key by key string
     *
     * @param string $key API key string
     * @return array|null API key data or null if not found
     */
    public function findByKey(string $key): ?array
    {
        // Meta query required for API key authentication (small dataset, acceptable performance)
        $query = new \WP_Query([
            'post_type' => 'mai_api_key',
            'post_status' => 'publish',
            'posts_per_page' => 1,
            // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
            'meta_query' => [
                [
                    'key' => '_mai_api_key_value',
                    'value' => $key,
                    'compare' => '=',
                ],
            ],
            'no_found_rows' => true,
        ]);
        
        if (!$query->have_posts()) {
            return null;
        }
        
        return $this->find($query->posts[0]->ID);
    }

    /**
     * Check if an API key is valid (exists and is active)
     *
     * @param string $key API key string
     * @return bool
     */
    public function isValid(string $key): bool
    {
        $api_key = $this->findByKey($key);
        
        if (!$api_key) {
            return false;
        }
        
        if (!$api_key['active']) {
            return false;
        }
        
        // Update last used timestamp
        $this->updateLastUsed($api_key['id']);
        
        return true;
    }

    /**
     * Update last used timestamp for an API key
     *
     * @param int $id API key post ID
     * @return void
     */
    protected function updateLastUsed(int $id): void
    {
        update_post_meta($id, '_mai_api_key_last_used', time());
    }

    /**
     * Create a new API key
     *
     * @param string $title Key name/title
     * @param string $description Optional description
     * @return array|false API key data or false on failure
     */
    public function create(string $title, string $description = '')
    {
        $post_id = wp_insert_post([
            'post_type' => 'mai_api_key',
            'post_title' => $title,
            'post_status' => 'publish',
        ]);
        
        if (is_wp_error($post_id)) {
            return false;
        }
        
        if ($description) {
            update_post_meta($post_id, '_mai_api_key_description', $description);
        }
        
        return $this->find($post_id);
    }

    /**
     * Revoke (deactivate) an API key
     *
     * @param int $id API key post ID
     * @return bool
     */
    public function revoke(int $id): bool
    {
        $api_key = $this->find($id);
        
        if (!$api_key) {
            return false;
        }
        
        update_post_meta($id, '_mai_api_key_active', false);
        
        return true;
    }

    /**
     * Activate an API key
     *
     * @param int $id API key post ID
     * @return bool
     */
    public function activate(int $id): bool
    {
        $api_key = $this->find($id);
        
        if (!$api_key) {
            return false;
        }
        
        update_post_meta($id, '_mai_api_key_active', true);
        
        return true;
    }

    /**
     * Get all API keys
     *
     * @param bool $active_only If true, only return active keys
     * @return array
     */
    public function getAll(bool $active_only = false): array
    {
        $args = [
            'post_type' => 'mai_api_key',
            'post_status' => 'publish',
            'posts_per_page' => -1,
            'orderby' => 'date',
            'order' => 'DESC',
            'no_found_rows' => true,
        ];
        
        if ($active_only) {
            // Meta query required for filtering active API keys (small dataset, acceptable performance)
            // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
            $args['meta_query'] = [
                [
                    'key' => '_mai_api_key_active',
                    'value' => '1',
                    'compare' => '=',
                ],
            ];
        }
        
        $query = new \WP_Query($args);
        
        $keys = [];
        foreach ($query->posts as $post) {
            $key = $this->find($post->ID);
            if ($key) {
                $keys[] = $key;
            }
        }
        
        return $keys;
    }
}

