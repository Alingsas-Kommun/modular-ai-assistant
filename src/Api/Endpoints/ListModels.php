<?php

namespace ModularAI\Api\Endpoints;

use ModularAI\Api\Abstracts\Endpoint;
use ModularAI\Entities\Models\Repository as ModelsRepository;

use function ModularAI\di;

class ListModels extends Endpoint
{
    /**
     * Models repository
     *
     * @var ModelsRepository
     */
    protected $modelsRepository;

    /**
     * Endpoint route
     *
     * @var string
     */
    protected $endpoint = '/models';

    /**
     * HTTP methods
     *
     * @var string
     */
    protected $methods = 'GET';

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->modelsRepository = di(ModelsRepository::class);
        
        parent::__construct();
    }

    /**
     * Check permissions for the endpoint
     * Requires valid API key
     *
     * @param \WP_REST_Request $request
     * @return bool|\WP_Error
     */
    public function checkPermissions($request)
    {
        if ($this->authenticateWithApiKey($request)) {
            return true;
        }
        
        return $this->error(
            'mai_unauthorized',
            __('Valid API key required. Please provide a valid API key via X-API-Key header or api_key parameter.', 'modular-ai'),
            401
        );
    }

    /**
     * Handle the request - List all active models
     *
     * @param \WP_REST_Request $request
     * @return \WP_REST_Response|\WP_Error
     */
    public function handleRequest($request)
    {
        // Get all active models
        $models = $this->modelsRepository->findActive();
        
        // Sanitize data - remove sensitive information
        $sanitized_models = array_map(function($model) {
            return [
                'id' => $model['id'],
                'title' => $model['title'],
                'model_id' => $model['model_id'],
                'streaming' => $model['streaming'],
                'active' => $model['active'],
            ];
        }, $models);
        
        return $this->success([
            'data' => $sanitized_models,
            'count' => count($sanitized_models),
        ]);
    }
}

