<?php

namespace ModularAI\Entities\Modules;

use ModularAI\Entities\Modules\MetaBoxes\Configuration;
use ModularAI\Entities\Modules\MetaBoxes\Access;
use ModularAI\Entities\Modules\MetaBoxes\Usage;
use ModularAI\Entities\Models\Repository as ModelsRepository;

use function ModularAI\di;

class Repository
{
    /**
     * Models repository instance
     *
     * @var ModelsRepository
     */
    protected $modelsRepository;

    /**
     * Constructor - Initializes PostType and MetaBoxes
     *
     * @return void
     */
    public function __construct()
    {
        // Register post type
        new Module();
        
        // Register meta boxes
        new Configuration();
        new Access();
        new Usage();
        
        // Get models repository instance
        $this->modelsRepository = di(ModelsRepository::class);
    }

    /**
     * Find a module by ID
     *
     * @param int $id Module post ID
     * @return array|null Module data or null if not found
     */
    public function find($id): ?array
    {
        $post = get_post($id);
        
        if (!$post || $post->post_type !== 'mai_module') {
            return null;
        }
        
        $data = [
            'id' => $post->ID,
            'title' => $post->post_title,
        ];
        
        // Fallback: manually load known meta fields if auto-loading fails
        $data['model_ref'] = (int) get_post_meta($id, '_mai_model_ref', true);
        $data['system'] = get_post_meta($id, '_mai_system', true);
        $data['user'] = get_post_meta($id, '_mai_user', true);
        $data['user_prompt_type'] = get_post_meta($id, '_mai_user_prompt_type', true) ?: 'custom';
        $data['output'] = get_post_meta($id, '_mai_output', true) ?: 'plain';
        $data['cache_ttl'] = (int) get_post_meta($id, '_mai_cache_ttl', true);
        $data['markdown_enabled'] = (bool) get_post_meta($id, '_mai_markdown_enabled', true);
        $data['public'] = (bool) get_post_meta($id, '_mai_public', true);
        $data['editor_analysis_enabled'] = (bool) get_post_meta($id, '_mai_editor_analysis_enabled', true);
        $data['streaming_override'] = get_post_meta($id, '_mai_streaming_override', true) ?: 'model_default';
        
        return $data;
    }

    /**
     * Get module settings by ID
     *
     * @param int $id Module post ID
     * @return array|null Module settings or null if not found
     */
    public function getSettings($id): ?array
    {
        return $this->find($id);
    }

    /**
     * Get associated model for a module
     *
     * @param int $module_id Module post ID
     * @return array|null Model data or null if not found
     */
    public function getModel($module_id): ?array
    {
        $module = $this->find($module_id);
        
        if (!$module) {
            return null;
        }
        
        // Check if model_ref exists and is valid (> 0)
        if (!isset($module['model_ref']) || $module['model_ref'] <= 0) {
            return null;
        }
        
        return $this->modelsRepository->getSettings($module['model_ref']);
    }

    /**
     * Find modules by meta key
     *
     * @param string $meta_key Meta key to search for
     * @param string $meta_value Meta value to match (default: '1')
     * @param string $compare Comparison operator (default: '=')
     * @return array Array of module data
     */
    protected function findByMeta(string $meta_key, string $meta_value = '1', string $compare = '='): array
    {
        // Meta query required for filtering modules (small dataset, acceptable performance)
        $query = new \WP_Query([
            'post_type' => 'mai_module',
            'post_status' => 'publish',
            'posts_per_page' => -1,
            // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
            'meta_query' => [
                [
                    'key' => $meta_key,
                    'value' => $meta_value,
                    'compare' => $compare,
                ],
            ],
            'orderby' => 'title',
            'order' => 'ASC',
            'no_found_rows' => true,
        ]);
        
        $modules = [];
        foreach ($query->posts as $post) {
            $module = $this->find($post->ID);
            if ($module) {
                $modules[] = $module;
            }
        }
        
        return $modules;
    }

    /**
     * Find all public modules
     *
     * @return array Array of module data
     */
    public function findPublic(): array
    {
        return $this->findByMeta('_mai_public');
    }

    /**
     * Check if a module is publicly accessible
     *
     * @param int $id Module post ID
     * @return bool
     */
    public function isPublic($id): bool
    {
        $module = $this->find($id);
        
        return $module && $module['public'];
    }

    /**
     * Find all modules with editor analysis enabled
     *
     * @return array Array of module data
     */
    public function findWithEditorAnalysis(): array
    {
        return $this->findByMeta('_mai_editor_analysis_enabled');
    }
}

