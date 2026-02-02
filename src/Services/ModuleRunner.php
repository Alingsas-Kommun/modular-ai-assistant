<?php

namespace ModularAIAssistant\Services;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

use ModularAIAssistant\Entities\Modules\Repository as ModulesRepository;
use ModularAIAssistant\Http\Clients\OpenAIClient;
use ModularAIAssistant\Content\ContentExtractor;
use ModularAIAssistant\Utilities\Markdown;

use function ModularAIAssistant\di;

class ModuleRunner
{
    /**
     * Modules repository
     *
     * @var ModulesRepository
     */
    protected $modulesRepository;

    /**
     * OpenAI client
     *
     * @var OpenAIClient
     */
    protected $openAIClient;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->modulesRepository = di(ModulesRepository::class);
        $this->openAIClient = di(OpenAIClient::class);
    }

    /**
     * Run a module and return the processed response
     *
     * @param int $module_id Module post ID
     * @param string $query Optional custom query/content
     * @param int|null $post_id Optional post ID for context
     * @param bool $show_curl Whether to include CURL preview (only for logged-in users)
     * @param bool|null $streaming_override Optional streaming override
     * @return array Result array with success status and data
     */
    public function run(int $module_id, string $query = '', ?int $post_id = null, bool $show_curl = false, $streaming_override = null): array
    {
        // Get module settings
        $module = $this->modulesRepository->find($module_id);
        if (!$module) {
            return $this->error('mai_module_not_found', __('Module not found', 'modular-ai-assistant'), 404);
        }

        // Get associated model
        $model = $this->modulesRepository->getModel($module_id);

        if (!$model) {
            return $this->error(
                'mai_model_not_found',
                __('Model not found. Please ensure a model is selected for this module.', 'modular-ai-assistant'),
                400
            );
        }

        if (!$model['active']) {
            return $this->error(
                'mai_model_inactive',
                /* Translators: 1: Model title */
                sprintf(__('Model "%s" is inactive. Please activate it in the Models settings.', 'modular-ai-assistant'), $model['title'] ?? 'Unknown'),
                400
            );
        }

        // Determine user content
        $user_content = $this->getUserContent($module, $query, $post_id);

        if (empty($user_content)) {
            return $this->error('mai_no_content', __('No question or content provided', 'modular-ai-assistant'), 400);
        }

        // Build messages array
        $messages = $this->buildMessages($module, $user_content);

        // Resolve streaming setting with proper hierarchy
        $use_streaming = $this->resolveStreamingSetting($module, $model, $streaming_override);

        // Call AI API with resolved streaming setting
        $response = $this->openAIClient->chat($model, $messages, $use_streaming);

        if (is_wp_error($response)) {
            return $this->error(
                $response->get_error_code(),
                $response->get_error_message(),
                500
            );
        }

        // Handle streaming response
        if ($response['streaming'] && isset($response['generator'])) {
            return [
                'success' => true,
                'streaming' => true,
                'generator' => $response['generator'],
                'module_id' => $module_id,
                'module' => $module,
                'model' => $model,
                'messages' => $messages,
                'show_curl' => $show_curl,
            ];
        }

        // Process and format response for non-streaming
        $formatted_response = $this->formatResponse($response['text'], $module);

        $result = [
            'success' => true,
            'content' => $formatted_response['content'],
            'module_id' => $module_id,
            'streaming' => false,
            'format' => $formatted_response['format'],
        ];

        // Add CURL preview if requested and user is logged in
        if ($show_curl) {
            $result['curl_preview'] = $this->generateCurlPreview($model, $messages);
        }

        return $result;
    }

    /**
     * Get user content for the module
     *
     * @param array $module Module data
     * @param string $query Custom query
     * @param int|null $post_id Post ID for context
     * @return string
     */
    protected function getUserContent(array $module, string $query, ?int $post_id): string
    {
        // Use custom query if provided
        if (!empty($query)) {
            return $query;
        }

        // Try to get content based on module settings
        $user_content = ContentExtractor::getContent($module['user_prompt_type'], $module['user'], $post_id);

        // Fallback to page content if custom is empty
        if (empty($user_content) && $module['user_prompt_type'] === 'custom') {
            $user_content = ContentExtractor::getContent('page_content', '', $post_id);
        }

        // Final fallback
        if (empty($user_content)) {
            $user_content = __('Analyze the content on this page.', 'modular-ai-assistant');
        }

        return $user_content;
    }

    /**
     * Build messages array for AI API
     *
     * @param array $module Module data
     * @param string $user_content User content
     * @return array
     */
    protected function buildMessages(array $module, string $user_content): array
    {
        $messages = [];

        if (!empty($module['system'])) {
            $messages[] = [
                'role' => 'system',
                'content' => $module['system'],
            ];
        }

        $messages[] = [
            'role' => 'user',
            'content' => $user_content,
        ];

        return $messages;
    }

    /**
     * Format AI response based on module settings
     *
     * @param string $text AI response text
     * @param array $module Module data
     * @return array Formatted response with content and format
     */
    protected function formatResponse(string $text, array $module): array
    {
        // Convert markdown to HTML if markdown is enabled
        if (isset($module['markdown_enabled']) && $module['markdown_enabled']) {
            $text = Markdown::toHtml($text);
        } else {
            // Always convert markdown lists to plain text for better line breaks
            $text = Markdown::convertListsToPlainText($text);
        }

        // Handle output format
        if (isset($module['output']) && $module['output'] === 'html') {
            $content = wp_kses_post($text);
        } else {
            $content = nl2br(esc_html(wp_strip_all_tags($text)));
        }

        return [
            'content' => $content,
            'format' => $module['output'],
        ];
    }

    /**
     * Resolve streaming setting based on hierarchy
     *
     * Priority: API/Shortcode > Module > Model
     *
     * @param array $module Module configuration
     * @param array|null $model Model configuration
     * @param bool|null $request_override Streaming override from API/shortcode
     * @return bool Whether to use streaming
     */
    public function resolveStreamingSetting(array $module, ?array $model, $request_override = null): bool
    {
        // 1. API/Shortcode override (highest priority)
        if ($request_override !== null) {
            return (bool) $request_override;
        }
        
        // 2. Module-level override
        $module_streaming = $module['streaming_override'] ?? 'model_default';
        
        if ($module_streaming === 'enabled') {
            return true;
        }
        
        if ($module_streaming === 'disabled') {
            return false;
        }
        
        // 3. Fall back to model default (lowest priority)
        return isset($model['streaming']) ? (bool) $model['streaming'] : false;
    }

    /**
     * Create error response
     *
     * @param string $code Error code
     * @param string $message Error message
     * @param int $status HTTP status code
     * @return array
     */
    protected function error(string $code, string $message, int $status): array
    {
        return [
            'success' => false,
            'error' => [
                'code' => $code,
                'message' => $message,
                'status' => $status,
            ],
        ];
    }

    /**
     * Generate CURL command preview for debugging
     *
     * @param array $model Model data with endpoint, api_key, and model_id
     * @param array $messages Messages array for the API call
     * @return string Formatted CURL command
     */
    public function generateCurlPreview(array $model, array $messages): string
    {
        $endpoint = $model['endpoint'] ?? '';
        $model_id = $model['model_id'] ?? '';
        
        $curl_command = 'curl -X POST "' . esc_html($endpoint) . "\" \\\n";
        $curl_command .= "  -H \"Content-Type: application/json\" \\\n";
        $curl_command .= "  -H \"Authorization: Bearer [API_KEY]\" \\\n";
        $curl_command .= "  -d '{\n";
        $curl_command .= '    "model": "' . esc_html($model_id) . "\",\n";
        $curl_command .= '    "messages": ' . wp_json_encode($messages, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . ",\n";
        $curl_command .= "    \"stream\": false\n";
        $curl_command .= "  }'";
        
        return $curl_command;
    }
}

