<?php

namespace ModularAIAssistant\Entities\Models;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

use ModularAIAssistant\Entities\Models\MetaBoxes\Configuration;
use ModularAIAssistant\Entities\Models\MetaBoxes\Testing;

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
        new Model();
        
        // Register meta boxes
        new Configuration();
        new Testing();
    }

    /**
     * Find a model by ID
     *
     * @param int $id Model post ID
     * @return array|null Model data or null if not found
     */
    public function find($id): ?array
    {
        $post = get_post($id);
        
        if (!$post || $post->post_type !== 'mai_model') {
            return null;
        }
        
        $data = [
            'id' => $post->ID,
            'title' => $post->post_title,
        ];
        
        $data['model_id'] = get_post_meta($id, '_mai_model_id', true);
        $data['endpoint'] = get_post_meta($id, '_mai_model_endpoint', true);
        $data['api_key'] = get_post_meta($id, '_mai_model_api_key', true);
        $data['active'] = (bool) get_post_meta($id, '_mai_model_active', true);
        $data['streaming'] = (bool) get_post_meta($id, '_mai_streaming_enabled', true);
        
        return $data;
    }

    /**
     * Find all active models
     *
     * @return array Array of model data
     */
    public function findActive(): array
    {
        // Meta query required for filtering active models (small dataset, acceptable performance)
        $query = new \WP_Query([
            'post_type' => 'mai_model',
            'post_status' => 'publish',
            'posts_per_page' => -1,
            // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
            'meta_key' => '_mai_model_active',
            // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value
            'meta_value' => '1',
            'orderby' => 'title',
            'order' => 'ASC',
            'no_found_rows' => true,
        ]);
        
        $models = [];
        foreach ($query->posts as $post) {
            $model = $this->find($post->ID);
            if ($model) {
                $models[] = $model;
            }
        }
        
        return $models;
    }

    /**
     * Get model settings by ID
     *
     * @param int $id Model post ID
     * @return array|null Model settings or null if not found/invalid
     */
    public function getSettings($id): ?array
    {
        $model = $this->find($id);
        
        if (!$model) {
            return null;
        }
        
        // Validate required fields
        if (empty($model['model_id']) || empty($model['endpoint'])) {
            return null;
        }
        
        return $model;
    }

    /**
     * Check if a model is active
     *
     * @param int $id Model post ID
     * @return bool
     */
    public function isActive($id): bool
    {
        $model = $this->find($id);
        return $model && $model['active'];
    }
}

