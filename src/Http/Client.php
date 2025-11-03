<?php

namespace ModularAI\Http;

use ModularAI\Http\Interfaces\HttpClientInterface;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;

use function ModularAI\config;

class Client implements HttpClientInterface
{
    /**
     * Guzzle client instance
     *
     * @var GuzzleClient
     */
    protected $client;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->client = new GuzzleClient([
            'verify' => true,
            'timeout' => 45,
            'connect_timeout' => 10,
        ]);
    }

    /**
     * Make a POST request
     *
     * @param string $url Request URL
     * @param array $data Request payload
     * @param array $headers Request headers
     * @param int $timeout Timeout in seconds
     * @return array|WP_Error Response data or error
     */
    public function post($url, $data = [], $headers = [], $timeout = 45)
    {
        try {
            $response = $this->client->post($url, [
                'json' => $data,
                'headers' => array_merge([
                    'Content-Type' => 'application/json',
                    'User-Agent' => 'ModularAI-WordPress-Plugin/' . config('app.version'),
                ], $headers),
                'timeout' => $timeout,
            ]);
            
            $statusCode = $response->getStatusCode();
            $body = (string) $response->getBody();
            
            if (empty($body)) {
                /* Translators: This is an empty response from the server. */
                return new \WP_Error('mai_empty_response', __('Empty response from server', 'modular-ai'));
            }
            
            $decoded = json_decode($body, true);
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                return new \WP_Error(
                    'mai_json_error',
                    /* Translators: This is a JSON error message. */
                    sprintf(__('JSON error: %s', 'modular-ai'), json_last_error_msg())
                );
            }
            
            return [
                'data' => $decoded,
                'status' => $statusCode,
                'body' => $body,
            ];
            
        } catch (RequestException $e) {
            $statusCode = $e->hasResponse() ? $e->getResponse()->getStatusCode() : 0;
            $message = $e->getMessage();
            
            // Try to get error from response body
            if ($e->hasResponse()) {
                $body = (string) $e->getResponse()->getBody();
                $decoded = json_decode($body, true);
                
                if (json_last_error() === JSON_ERROR_NONE && isset($decoded['error'])) {
                    if (is_string($decoded['error'])) {
                        $message = $decoded['error'];
                    } elseif (isset($decoded['error']['message'])) {
                        $message = $decoded['error']['message'];
                    }
                }
            }
            
            return new \WP_Error(
                'mai_request_error',
                /* Translators: 1: HTTP status code, 2: Error message */
                sprintf(__('Request error (HTTP %1$d): %2$s', 'modular-ai'), $statusCode, $message)
            );
            
        } catch (GuzzleException $e) {
            return new \WP_Error(
                'mai_http_error',
                /* Translators: This is an HTTP error message. */
                sprintf(__('HTTP error: %s', 'modular-ai'), $e->getMessage())
            );
        }
    }

    /**
     * Make a streaming POST request
     *
     * @param string $url Request URL
     * @param array $data Request payload
     * @param array $headers Request headers
     * @param int $timeout Timeout in seconds
     * @return \Generator|\WP_Error Generator yielding SSE chunks or error
     */
    public function stream($url, $data = [], $headers = [], $timeout = 45)
    {
        return $this->streamGenerator($url, $data, $headers, $timeout);
    }

    /**
     * Generator that yields parsed SSE chunks
     *
     * @param string $url Request URL
     * @param array $data Request payload
     * @param array $headers Request headers
     * @param int $timeout Timeout in seconds
     * @return \Generator Yields parsed SSE data chunks
     */
    private function streamGenerator($url, $data, $headers, $timeout)
    {
        try {
            $response = $this->client->post($url, [
                'json' => $data,
                'headers' => array_merge([
                    'Content-Type' => 'application/json',
                    'User-Agent' => 'ModularAI-WordPress-Plugin/' . config('app.version'),
                ], $headers),
                'timeout' => $timeout,
                'stream' => true,
            ]);
            
            $statusCode = $response->getStatusCode();
            
            if ($statusCode < 200 || $statusCode >= 300) {
                $body = (string) $response->getBody();
                $decoded = json_decode($body, true);

                /* Translators: This is a streaming error message. */
                $errorMessage = __('Streaming error', 'modular-ai');
                
                if (json_last_error() === JSON_ERROR_NONE && isset($decoded['error'])) {
                    $errorMessage = is_string($decoded['error']) 
                        ? $decoded['error'] 
                        : ($decoded['error']['message'] ?? $errorMessage);
                }
                
                yield new \WP_Error(
                    'mai_streaming_error',
                    /* Translators: 1: HTTP status code, 2: Error message */
                    sprintf(__('Streaming error (HTTP %1$d): %2$s', 'modular-ai'), $statusCode, $errorMessage)
                );
                return;
            }
            
            // Get stream body
            $body = $response->getBody();
            
            // Read and parse SSE format line by line
            $buffer = '';
            while (!$body->eof()) {
                $chunk = $body->read(1024);
                if ($chunk === false || $chunk === '') {
                    break;
                }
                
                $buffer .= $chunk;
                
                // Process complete lines
                while (($pos = strpos($buffer, "\n")) !== false) {
                    $line = substr($buffer, 0, $pos);
                    $buffer = substr($buffer, $pos + 1);
                    
                    // Trim line
                    $line = trim($line);
                    
                    // Skip empty lines
                    if (empty($line)) {
                        continue;
                    }
                    
                    // Check for SSE data line
                    if (strpos($line, 'data: ') === 0) {
                        $data = substr($line, 6); // Remove "data: " prefix
                        
                        // Check for [DONE] signal
                        if ($data === '[DONE]') {
                            return;
                        }
                        
                        // Try to decode JSON
                        $decoded = json_decode($data, true);
                        
                        if (json_last_error() === JSON_ERROR_NONE) {
                            yield $decoded;
                        }
                    }
                }
            }
            
        } catch (RequestException $e) {
            $statusCode = $e->hasResponse() ? $e->getResponse()->getStatusCode() : 0;
            $message = $e->getMessage();
            
            if ($e->hasResponse()) {
                $body = (string) $e->getResponse()->getBody();
                $decoded = json_decode($body, true);
                
                if (json_last_error() === JSON_ERROR_NONE && isset($decoded['error'])) {
                    if (is_string($decoded['error'])) {
                        $message = $decoded['error'];
                    } elseif (isset($decoded['error']['message'])) {
                        $message = $decoded['error']['message'];
                    }
                }
            }
            
            yield new \WP_Error(
                'mai_request_error',
                /* Translators: 1: HTTP status code, 2: Error message */
                sprintf(__('Request error (HTTP %1$d): %2$s', 'modular-ai'), $statusCode, $message)
            );
            
        } catch (GuzzleException $e) {
            yield new \WP_Error(
                'mai_streaming_error',
                /* Translators: This is a streaming error message. */
                sprintf(__('Streaming error: %s', 'modular-ai'), $e->getMessage())
            );
        }
    }
}

