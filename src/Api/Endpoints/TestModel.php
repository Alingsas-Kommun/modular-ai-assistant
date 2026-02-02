<?php

namespace ModularAIAssistant\Api\Endpoints;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

use ModularAIAssistant\Api\Abstracts\Endpoint;
use ModularAIAssistant\Entities\Models\Repository;
use ModularAIAssistant\Http\Clients\OpenAIClient;

use function ModularAIAssistant\di;

class TestModel extends Endpoint
{
    /**
     * Models repository
     *
     * @var Repository
     */
    protected $modelsRepository;

    /**
     * OpenAI client
     *
     * @var OpenAIClient
     */
    protected $openAIClient;

    /**
     * Endpoint route
     *
     * @var string
     */
    protected $endpoint = '/test-model';

    /**
     * HTTP methods
     *
     * @var string
     */
    protected $methods = 'POST';

    /**
     * Route arguments
     *
     * @var array
     */
    protected $args = [
        'model_id' => [
            'required' => true,
            'type' => 'integer',
            'sanitize_callback' => 'absint',
        ],
    ];

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->modelsRepository = di(Repository::class);
        $this->openAIClient = di(OpenAIClient::class);
        
        parent::__construct();
    }

    /**
     * Check permissions for the endpoint
     * Internal use only - requires edit_posts capability
     *
     * @param \WP_REST_Request $request
     * @return bool
     */
    public function checkPermissions($request)
    {
        // Only allow users who can edit posts
        return $this->userCan('edit_posts');
    }

    /**
     * Handle the request - Test AI model connection
     *
     * @param \WP_REST_Request $request
     * @return \WP_REST_Response|\WP_Error
     */
    public function handleRequest($request)
    {
        $model_id = $request->get_param('model_id');
        
        // Get model data
        $model = $this->modelsRepository->find($model_id);
        
        if (!$model) {
            return new \WP_Error(
                'mai_model_not_found',
                __('Model not found', 'modular-ai-assistant'),
                ['status' => 404]
            );
        }
        
        // Validate required fields
        if (empty($model['model_id'])) {
            return new \WP_Error(
                'mai_model_id_missing',
                __('Model name missing', 'modular-ai-assistant'),
                ['status' => 400]
            );
        }
        
        if (empty($model['endpoint'])) {
            return new \WP_Error(
                'mai_endpoint_missing',
                __('Endpoint URL missing', 'modular-ai-assistant'),
                ['status' => 400]
            );
        }
        
        if (empty($model['api_key'])) {
            return new \WP_Error(
                'mai_api_key_missing',
                __('API key missing', 'modular-ai-assistant'),
                ['status' => 400]
            );
        }
        
        // Test the connection
        $result = $this->openAIClient->testConnection($model);
        
        if (is_wp_error($result)) {
            return $result;
        }
        
        return rest_ensure_response([
            'success' => true,
            'message' => $result['message'],
            'response_text' => $result['response_text'],
            'status' => $result['status'],
        ]);
    }
}

