<?php

namespace ModularAIAssistant\Http\Clients;

use ModularAIAssistant\Http\Client;
use ModularAIAssistant\Http\Interfaces\AiClientInterface;

use function ModularAIAssistant\di;

class OpenAIClient implements AiClientInterface
{
    /**
     * HTTP Client instance
     *
     * @var Client
     */
    protected $httpClient;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->httpClient = di(Client::class);
    }

    /**
     * Send chat request to OpenAI-compatible API
     *
     * @param array $model Model settings
     * @param array $messages Messages array
     * @param bool $streaming Whether to use streaming
     * @return array|\WP_Error Response data or error
     */
    public function chat($model, $messages, $streaming = false)
    {
        // Validate model settings
        if (empty($model['endpoint'])) {
            return new \WP_Error('mai_no_endpoint', __('Endpoint missing', 'modular-ai-assistant'));
        }
        
        if (empty($model['api_key'])) {
            return new \WP_Error('mai_no_key', __('API key missing', 'modular-ai-assistant'));
        }
        
        if (empty($model['model_id'])) {
            return new \WP_Error('mai_no_model', __('Model ID missing', 'modular-ai-assistant'));
        }
        
        // Build payload
        $payload = [
            'model' => $model['model_id'],
            'messages' => $messages,
            'stream' => $streaming,
        ];
        
        // Build headers
        $headers = [
            'Authorization' => 'Bearer ' . $model['api_key'],
        ];
        
        // Make request
        if ($streaming) {
            $generator = $this->httpClient->stream($model['endpoint'], $payload, $headers);
            
            // Return generator directly for streaming
            return [
                'streaming' => true,
                'generator' => $generator,
            ];
        } else {
            $response = $this->httpClient->post($model['endpoint'], $payload, $headers);
            
            if (is_wp_error($response)) {
                return $response;
            }
            
            // Handle standard response
            $data = $response['data'];
            
            // Extract text content from OpenAI response format
            $textContent = '';
            if (isset($data['choices'][0]['message']['content'])) {
                $textContent = $data['choices'][0]['message']['content'];
            }
            
            return [
                'streaming' => false,
                'text' => $textContent,
                'raw' => $data,
                'status' => $response['status'],
            ];
        }
    }

    /**
     * Test connection to model
     *
     * @param array $model Model settings
     * @return array|\WP_Error Test result or error
     */
    public function testConnection($model)
    {
        // Validate inputs
        if (empty($model['endpoint'])) {
            return new \WP_Error('mai_no_endpoint', __('Endpoint missing', 'modular-ai-assistant'));
        }
        
        if (empty($model['api_key'])) {
            return new \WP_Error('mai_no_key', __('API key missing', 'modular-ai-assistant'));
        }
        
        if (empty($model['model_id'])) {
            return new \WP_Error('mai_no_model', __('Model name missing', 'modular-ai-assistant'));
        }
        
        // Create test message
        $currentLocale = get_locale();
        
        /* Translators: This is a test message to verify the AI model is working correctly.*/
        $testMessage = sprintf(__('Please respond in %s language. This is a test to verify the AI model is working correctly.', 'modular-ai-assistant'), $currentLocale);
        
        $messages = [
            [
                'role' => 'user',
                'content' => $testMessage,
            ],
        ];
        
        // Test the connection
        $result = $this->chat($model, $messages, false);
        
        if (is_wp_error($result)) {
            return $result;
        }
        
        return [
            'success' => true,
            'message' => __('Model connection successful!', 'modular-ai-assistant'),
            'response_text' => $result['text'],
            'status' => $result['status'],
            'raw_response' => $result['raw'],
        ];
    }
}

