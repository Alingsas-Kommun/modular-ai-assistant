<?php

namespace ModularAI\Api\Endpoints;

use ModularAI\Api\Abstracts\Endpoint;
use ModularAI\Entities\Modules\Repository as ModulesRepository;

use function ModularAI\di;

class ListModules extends Endpoint
{
    /**
     * Modules repository
     *
     * @var ModulesRepository
     */
    protected $modulesRepository;

    /**
     * Endpoint route
     *
     * @var string
     */
    protected $endpoint = '/modules';

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
        $this->modulesRepository = di(ModulesRepository::class);
        
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
     * Handle the request - List all public modules
     *
     * @param \WP_REST_Request $request
     * @return \WP_REST_Response|\WP_Error
     */
    public function handleRequest($request)
    {
        // Get all public modules from repository
        $modules = $this->modulesRepository->findPublic();
        
        // Sanitize data - only return basic, non-sensitive information
        $sanitized_modules = array_map(function($module) {
            return [
                'id' => $module['id'],
                'title' => $module['title'],
                'model_id' => $module['model_ref'],
                'output' => $module['output'],
                'markdown_enabled' => $module['markdown_enabled'],
                'public' => $module['public'],
            ];
        }, $modules);
        
        return $this->success([
            'data' => $sanitized_modules,
            'count' => count($sanitized_modules),
        ]);
    }
}

