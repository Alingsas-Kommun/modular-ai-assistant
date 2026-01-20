<?php

namespace ModularAIAssistant\Http\Interfaces;

interface HttpClientInterface
{
    /**
     * Make a POST request
     *
     * @param string $url Request URL
     * @param array $data Request payload
     * @param array $headers Request headers
     * @param int $timeout Timeout in seconds
     * @return array|\WP_Error Response data or error
     */
    public function post($url, $data = [], $headers = [], $timeout = 45);

    /**
     * Make a streaming POST request
     *
     * @param string $url Request URL
     * @param array $data Request payload
     * @param array $headers Request headers
     * @param int $timeout Timeout in seconds
     * @return array|\WP_Error Response data or error
     */
    public function stream($url, $data = [], $headers = [], $timeout = 45);
}

