<?php

namespace ModularAIAssistant\Http\Interfaces;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

interface AiClientInterface
{
    /**
     * Send chat request to AI model
     *
     * @param array $model Model settings (endpoint, api_key, model_id)
     * @param array $messages Messages array
     * @param bool $streaming Whether to use streaming
     * @return array|\WP_Error Response data or error
     */
    public function chat($model, $messages, $streaming = false);

    /**
     * Test connection to AI model
     *
     * @param array $model Model settings (endpoint, api_key, model_id)
     * @return array|\WP_Error Test result or error
     */
    public function testConnection($model);
}

