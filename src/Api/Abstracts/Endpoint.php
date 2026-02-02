<?php

namespace ModularAIAssistant\Api\Abstracts;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

use ModularAIAssistant\Api\Traits\ApiAuthentication;

abstract class Endpoint
{
    use ApiAuthentication;

    /**
     * API namespace (base)
     *
     * @var string
     */
    protected $namespace = 'modular-ai-assistant';

    /**
     * API version
     *
     * @var string
     */
    protected $version = 'v1';

    /**
     * Endpoint route
     *
     * @var string
     */
    protected $endpoint = '';

    /**
     * HTTP methods
     *
     * @var string|array
     */
    protected $methods = 'GET';

    /**
     * Route arguments
     *
     * @var array
     */
    protected $args = [];

    /**
     * Constructor - Registers routes on rest_api_init
     */
    public function __construct()
    {
        add_action('rest_api_init', [$this, 'registerRoutes']);
    }

    /**
     * Register REST API routes
     * Automatically registers using class properties
     *
     * @return void
     */
    final public function registerRoutes()
    {
        $full_namespace = $this->namespace . '/' . $this->version;
        
        register_rest_route($full_namespace, $this->endpoint, [
            'methods' => $this->methods,
            'callback' => [$this, 'handleRequest'],
            'permission_callback' => [$this, 'checkPermissions'],
            'args' => $this->args,
        ]);
    }

    /**
     * Handle the request
     * Must be implemented by child classes
     *
     * @param \WP_REST_Request $request
     * @return \WP_REST_Response|\WP_Error
     */
    abstract public function handleRequest($request);

    /**
     * Check permissions for the endpoint
     * Must be implemented by child classes
     *
     * @param \WP_REST_Request $request
     * @return bool|\WP_Error
     */
    abstract public function checkPermissions($request);

    /**
     * Create a standardized error response
     *
     * @param string $code Error code
     * @param string $message Error message
     * @param int $status HTTP status code
     * @return \WP_Error
     */
    protected function error(string $code, string $message, int $status = 400): \WP_Error
    {
        return new \WP_Error($code, $message, ['status' => $status]);
    }

    /**
     * Create a standardized success response
     *
     * @param array $data Response data
     * @param int $status HTTP status code
     * @return \WP_REST_Response
     */
    protected function success(array $data, int $status = 200): \WP_REST_Response
    {
        return rest_ensure_response(array_merge(['success' => true], $data));
    }

    /**
     * Validate required parameters
     *
     * @param \WP_REST_Request $request
     * @param array $required_params List of required parameter names
     * @return \WP_Error|true Returns true if valid, WP_Error if not
     */
    protected function validateRequired($request, array $required_params)
    {
        foreach ($required_params as $param) {
            $value = $request->get_param($param);
            
            if (empty($value) && $value !== '0' && $value !== 0) {
                return $this->error(
                    'mai_missing_parameter',
                    /* Translators: 1: Parameter name */
                    sprintf(__('Missing required parameter: %s', 'modular-ai-assistant'), $param),
                    400
                );
            }
        }
        
        return true;
    }

    /**
     * Check if user is logged in (for internal endpoints)
     *
     * @return bool
     */
    protected function isUserLoggedIn(): bool
    {
        return is_user_logged_in();
    }

    /**
     * Check if user has capability (for internal endpoints)
     *
     * @param string $capability
     * @return bool
     */
    protected function userCan(string $capability): bool
    {
        return current_user_can($capability);
    }
}

