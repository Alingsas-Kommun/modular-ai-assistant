<?php

namespace ModularAIAssistant\Api\Endpoints;

use ModularAIAssistant\Api\Abstracts\Endpoint;
use ModularAIAssistant\Entities\Modules\Repository as ModulesRepository;
use ModularAIAssistant\Services\ModuleRunner;
use ModularAIAssistant\Services\ModuleCacheService;

use function ModularAIAssistant\di;

class RunModule extends Endpoint
{
    /**
     * Modules repository
     *
     * @var ModulesRepository
     */
    protected $modulesRepository;

    /**
     * Module runner service
     *
     * @var ModuleRunner
     */
    protected $moduleRunner;

    /**
     * Module cache service
     *
     * @var ModuleCacheService
     */
    protected $cacheService;

    /**
     * Endpoint route
     *
     * @var string
     */
    protected $endpoint = '/run';

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
        'module_id' => [
            'required' => true,
            'type' => 'integer',
            'sanitize_callback' => 'absint',
        ],
        'query' => [
            'required' => true,
            'type' => 'string',
            'sanitize_callback' => 'sanitize_text_field',
            'default' => '',
        ],
        'post_id' => [
            'required' => false,
            'type' => 'integer',
            'sanitize_callback' => 'absint',
            'default' => null,
        ],
        'show_curl' => [
            'required' => false,
            'type' => 'boolean',
            'sanitize_callback' => 'rest_sanitize_boolean',
            'default' => false,
        ],
        'streaming' => [
            'required' => false,
            'type' => 'boolean',
            'sanitize_callback' => 'rest_sanitize_boolean',
            'default' => null,
        ],
    ];

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->modulesRepository = di(ModulesRepository::class);
        $this->moduleRunner = di(ModuleRunner::class);
        $this->cacheService = di(ModuleCacheService::class);
        
        parent::__construct();
    }

    /**
     * Check permissions for the endpoint
     *
     * @param \WP_REST_Request $request
     * @return bool|\WP_Error
     */
    public function checkPermissions($request)
    {
        $module_id = $request->get_param('module_id');
        
        if ($this->authenticateWithApiKey($request)) {
            if (!$module_id || !$this->modulesRepository->isPublic($module_id)) {
                return $this->error(
                    'mai_module_not_found',
                    __('Module not found or not publicly accessible', 'modular-ai-assistant'),
                    404
                );
            }
            return true;
        }
        
        if ($this->isUserLoggedIn() && $this->userCan('edit_posts')) {
            return true;
        }
        
        if ($module_id && $this->modulesRepository->isPublic($module_id)) {
            return true;
        }
        
        // No valid authentication method found
        return $this->error(
            'mai_unauthorized',
            __('You do not have permission to access this module', 'modular-ai-assistant'),
            403
        );
    }

    /**
     * Handle the request - Run AI module
     *
     * @param \WP_REST_Request $request
     * @return \WP_REST_Response|\WP_Error
     */
    public function handleRequest($request)
    {
        $module_id = $request->get_param('module_id');
        $query = $request->get_param('query');
        $post_id = $request->get_param('post_id');
        $show_curl = $request->get_param('show_curl');
        $streaming_override = $request->get_param('streaming');
        
        // Get module configuration
        $module = $this->modulesRepository->find($module_id);
        
        if (!$module) {
            return new \WP_Error(
                'mai_module_not_found',
                __('Module not found', 'modular-ai-assistant'),
                ['status' => 404]
            );
        }
        
        // Try to serve from cache first
        $cachedResponse = $this->tryServeFromCache($module_id, $query, $post_id, $module, $streaming_override);
        if ($cachedResponse !== null) {
            return $cachedResponse;
        }
        
        // Run the module (ModuleRunner handles streaming resolution)
        $result = $this->moduleRunner->run($module_id, $query, $post_id, $show_curl, $streaming_override);
        
        if (!$result['success']) {
            return new \WP_Error(
                $result['error']['code'],
                $result['error']['message'],
                ['status' => $result['error']['status']]
            );
        }
        
        // Use streaming state from result (already resolved by ModuleRunner)
        $use_streaming = $result['streaming'];
        
        // Handle streaming response
        if ($use_streaming && isset($result['generator'])) {
            $this->handleStreamingResponse($result, $module, $query, $post_id, $use_streaming);
            exit;
        }
        
        // Handle and cache non-streaming response
        $this->handleResponse($result, $module, $module_id, $query, $post_id, $use_streaming);
        
        return rest_ensure_response($result);
    }

    /**
     * Try to serve response from cache
     *
     * @param int $module_id Module ID
     * @param string $query Query string
     * @param int|null $post_id Post ID
     * @param array $module Module configuration
     * @param bool|null $streaming_override Streaming override parameter
     * @return \WP_REST_Response|null Response if cache hit, null if cache miss
     */
    private function tryServeFromCache(int $module_id, string $query, ?int $post_id, array $module, $streaming_override)
    {
        $cache_ttl = isset($module['cache_ttl']) ? (int) $module['cache_ttl'] : 0;
        
        // Skip cache check if TTL is not set
        if ($cache_ttl <= 0) {
            return null;
        }
        
        // Resolve streaming to determine cache key (use same logic as ModuleRunner)
        $model = $this->modulesRepository->getModel($module_id);
        $resolved_streaming = $this->moduleRunner->resolveStreamingSetting($module, $model, $streaming_override);
        
        $cached = $this->cacheService->get($module_id, $query, $post_id, $resolved_streaming);
        
        if ($cached === null) {
            return null; // Cache miss
        }
        
        // Cache hit - serve cached response
        if (isset($cached['streaming']) && $cached['streaming']) {
            // Stream cached response and exit
            $this->serveCachedStreamingResponse($cached);
            exit;
        }
        
        // Return cached JSON response
        return rest_ensure_response($cached);
    }

    /**
     * Handle streaming response with caching
     *
     * @param array $result Result from module runner
     * @param array $module Module configuration
     * @param string $query Query string
     * @param int|null $post_id Post ID
     * @param bool $streaming Whether streaming is enabled
     * @return void
     */
    private function handleStreamingResponse(array $result, array $module, string $query, ?int $post_id, bool $streaming): void
    {
        $cache_ttl = isset($module['cache_ttl']) ? (int) $module['cache_ttl'] : 0;
        $this->streamSSEResponse($result, $cache_ttl, $query, $post_id, $streaming);
    }

    /**
     * Cache non-streaming response if caching is enabled
     *
     * @param array $result Result from module runner
     * @param array $module Module configuration
     * @param int $module_id Module ID
     * @param string $query Query string
     * @param int|null $post_id Post ID
     * @param bool $streaming Whether streaming is enabled
     * @return void
     */
    private function handleResponse(array $result, array $module, int $module_id, string $query, ?int $post_id, bool $streaming): void
    {
        $cache_ttl = isset($module['cache_ttl']) ? (int) $module['cache_ttl'] : 0;
        
        if ($cache_ttl <= 0) {
            return; // Caching disabled
        }
        
        $cache_data = $this->buildCacheData($result, $module_id, false);
        $this->cacheService->set($module_id, $query, $post_id, $cache_data, $cache_ttl, $streaming);
    }

    /**
     * Build cache data structure
     *
     * @param array|string $content Content data (array for regular, string for streaming)
     * @param int $module_id Module ID
     * @param bool $is_streaming Whether this is streaming data
     * @param array|null $metadata Optional metadata for streaming
     * @return array Cache data structure
     */
    private function buildCacheData($content, int $module_id, bool $is_streaming = false, ?array $metadata = null): array
    {
        if ($is_streaming) {
            $cache_data = [
                'content' => $content,
                'module_id' => $module_id,
                'streaming' => true,
                'metadata' => [
                    'markdown_enabled' => $metadata['markdown_enabled'] ?? false,
                    'output_format' => $metadata['output_format'] ?? 'text',
                ],
            ];
            
            if (!empty($metadata['curl_preview'])) {
                $cache_data['metadata']['curl_preview'] = $metadata['curl_preview'];
            }
            
            return $cache_data;
        }
        
        // Non-streaming
        $cache_data = [
            'success' => $content['success'],
            'content' => $content['content'],
            'module_id' => $module_id,
            'streaming' => false,
            'format' => $content['format'],
        ];
        
        if (isset($content['curl_preview'])) {
            $cache_data['curl_preview'] = $content['curl_preview'];
        }
        
        return $cache_data;
    }

    /**
     * Initialize SSE headers and output settings
     *
     * @return void
     */
    private function initializeSSEHeaders(): void
    {
        // Disable output buffering
        if (ob_get_level()) {
            ob_end_clean();
        }
        
        // Set SSE headers
        header('Content-Type: text/event-stream');
        header('Cache-Control: no-cache');
        header('Connection: keep-alive');
        header('X-Accel-Buffering: no'); // Disable nginx buffering
        
        // Increase execution time for streaming (if allowed by host)
        if (function_exists('set_time_limit')) {
            // phpcs:ignore Squiz.PHP.DiscouragedFunctions.Discouraged -- Required for long-running AI streaming responses
            @set_time_limit(300);
        }
    }

    /**
     * Stream SSE response to client
     *
     * @param array $result Result array with generator and metadata
     * @param int $cache_ttl Cache TTL in seconds (0 = no cache)
     * @param string $query Query string for cache key
     * @param int|null $post_id Post ID for cache key
     * @param bool $streaming Whether streaming is enabled
     * @return void
     */
    private function streamSSEResponse(array $result, int $cache_ttl = 0, string $query = '', ?int $post_id = null, bool $streaming = true): void
    {
        $this->initializeSSEHeaders();
        
        // Build and send metadata
        $curl_preview = null;
        if ($result['show_curl']) {
            $curl_preview = $this->moduleRunner->generateCurlPreview(
                $result['model'],
                $result['messages']
            );
        }
        
        $metadata = $this->buildSSEMetadata(
            $result['module'],
            $result['module_id'],
            false,
            $curl_preview
        );
        
        $this->sendSSEEvent($metadata);
        
        // Process generator and send chunks
        $generator = $result['generator'];
        
        // Accumulate full response for caching
        $full_content = '';
        
        try {
            foreach ($generator as $chunk) {
                // Check if chunk is an error
                if (is_wp_error($chunk)) {
                    $this->sendSSEEvent([
                        'type' => 'error',
                        'code' => $chunk->get_error_code(),
                        'message' => $chunk->get_error_message(),
                    ]);
                    break;
                }
                
                // Extract content from OpenAI/Gemini format
                $content = '';
                if (isset($chunk['choices'][0]['delta']['content'])) {
                    $content = $chunk['choices'][0]['delta']['content'];
                }
                
                // Send content chunk if not empty
                if (!empty($content)) {
                    $full_content .= $content;
                    
                    $this->sendSSEEvent([
                        'type' => 'chunk',
                        'content' => $content,
                    ]);
                }
                
                // Check for finish reason
                if (isset($chunk['choices'][0]['delta']['finish_reason']) && 
                    $chunk['choices'][0]['delta']['finish_reason'] === 'stop') {
                    break;
                }
            }
            
            // Send done event
            $this->sendSSEEvent([
                'type' => 'done',
            ]);
            
            // Cache the streamed response if caching is enabled
            $this->cacheStreamingResponse($full_content, $metadata, $result['module_id'], $query, $post_id, $cache_ttl, $streaming);
            
        } catch (\Exception $e) {
            // Send error event if streaming fails
            $this->sendSSEEvent([
                'type' => 'error',
                'code' => 'mai_streaming_exception',
                'message' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Cache streaming response if TTL is set
     *
     * @param string $content Full accumulated content
     * @param array $metadata Metadata from streaming
     * @param int $module_id Module ID
     * @param string $query Query string
     * @param int|null $post_id Post ID
     * @param int $cache_ttl Cache TTL
     * @param bool $streaming Whether streaming is enabled
     * @return void
     */
    private function cacheStreamingResponse(string $content, array $metadata, int $module_id, string $query, ?int $post_id, int $cache_ttl, bool $streaming): void
    {
        if ($cache_ttl <= 0 || empty($content)) {
            return; // Caching disabled or no content
        }
        
        $cache_data = $this->buildCacheData($content, $module_id, true, $metadata);
        $this->cacheService->set($module_id, $query, $post_id, $cache_data, $cache_ttl, $streaming);
    }

    /**
     * Serve cached streaming response instantly
     *
     * @param array $cached_data Cached response data
     * @return void
     */
    private function serveCachedStreamingResponse(array $cached_data): void
    {
        $this->initializeSSEHeaders();
        
        // Build module data from cached metadata
        $module = [
            'markdown_enabled' => $cached_data['metadata']['markdown_enabled'] ?? false,
            'output' => $cached_data['metadata']['output_format'] ?? 'text',
        ];
        
        $curl_preview = $cached_data['metadata']['curl_preview'] ?? null;
        
        // Build and send metadata with cached flag
        $metadata = $this->buildSSEMetadata(
            $module,
            $cached_data['module_id'],
            true,
            $curl_preview
        );
        
        $this->sendSSEEvent($metadata);
        
        // Send entire content in one chunk (no delays)
        $this->sendSSEEvent([
            'type' => 'chunk',
            'content' => $cached_data['content'],
        ]);
        
        // Send done event
        $this->sendSSEEvent([
            'type' => 'done',
        ]);
    }

    /**
     * Build SSE metadata structure
     *
     * @param array $module Module configuration
     * @param int $module_id Module ID
     * @param bool $cached Whether response is cached
     * @param string|null $curl_preview Optional CURL preview
     * @return array Metadata array
     */
    private function buildSSEMetadata(array $module, int $module_id, bool $cached = false, ?string $curl_preview = null): array
    {
        $metadata = [
            'type' => 'metadata',
            'module_id' => $module_id,
            'markdown_enabled' => $module['markdown_enabled'] ?? false,
            'output_format' => $module['output'] ?? 'text',
        ];
        
        if ($cached) {
            $metadata['cached'] = true;
        }
        
        if ($curl_preview) {
            $metadata['curl_preview'] = $curl_preview;
        }
        
        return $metadata;
    }

    /**
     * Send a single SSE event
     *
     * @param array $data Event data
     * @return void
     */
    private function sendSSEEvent(array $data): void
    {
        echo 'data: ' . wp_json_encode($data) . "\n\n";
        
        if (ob_get_level()) {
            ob_flush();
        }
        flush();
    }
}
