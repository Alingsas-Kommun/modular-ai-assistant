<?php

namespace ModularAIAssistant\Api\Traits;

use ModularAIAssistant\Entities\ApiKeys\Repository as ApiKeysRepository;

use function ModularAIAssistant\di;

trait ApiAuthentication
{
    /**
     * Authenticate request with API key
     *
     * @param \WP_REST_Request $request
     * @return bool
     */
    protected function authenticateWithApiKey($request): bool
    {
        $api_key = $this->getApiKeyFromRequest($request);
        
        if (!$api_key) {
            return false;
        }
        
        return $this->validateApiKey($api_key);
    }

    /**
     * Get API key from request
     * Checks X-API-Key header first, then query parameter
     *
     * @param \WP_REST_Request $request
     * @return string|null
     */
    protected function getApiKeyFromRequest($request): ?string
    {
        // Check X-API-Key header
        $api_key = $request->get_header('X-API-Key');
        
        if ($api_key) {
            return sanitize_text_field($api_key);
        }
        
        // Check query parameter as fallback
        $api_key = $request->get_param('api_key');
        
        if ($api_key) {
            return sanitize_text_field($api_key);
        }
        
        return null;
    }

    /**
     * Validate API key against repository
     *
     * @param string $key
     * @return bool
     */
    protected function validateApiKey(string $key): bool
    {
        $apiKeysRepository = di(ApiKeysRepository::class);
        
        return $apiKeysRepository->isValid($key);
    }
}

