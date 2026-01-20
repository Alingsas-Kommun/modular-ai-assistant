<?php

namespace ModularAIAssistant\Api\Endpoints;

use ModularAIAssistant\Api\Abstracts\Endpoint;
use ModularAIAssistant\Utilities\Template;

class ModuleTemplate extends Endpoint
{
    /**
     * Endpoint route
     *
     * @var string
     */
    protected $endpoint = '/template/module';

    /**
     * HTTP methods
     *
     * @var string
     */
    protected $methods = 'GET';

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
        'instance_id' => [
            'required' => true,
            'type' => 'string',
            'sanitize_callback' => 'sanitize_text_field',
        ],
        'post_id' => [
            'required' => false,
            'type' => 'integer',
            'sanitize_callback' => 'absint',
            'default' => null,
        ],
    ];

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Check permissions for the endpoint
     * Internal use only - requires logged-in user
     *
     * @param \WP_REST_Request $request
     * @return bool
     */
    public function checkPermissions($request)
    {
        // Only allow logged-in users (admin context)
        return $this->isUserLoggedIn();
    }

    /**
     * Handle the request - Get module template HTML
     *
     * @param \WP_REST_Request $request
     * @return \WP_REST_Response|\WP_Error
     */
    public function handleRequest($request)
    {
        $module_id = $request->get_param('module_id');
        $instance_id = $request->get_param('instance_id');
        $post_id = $request->get_param('post_id');
        
        // Render module template in modal mode without trigger button
        $html = Template::get('components/module', [
            'module_id' => $module_id,
            'query' => '',
            'show_curl' => true,
            'instance_id' => $instance_id,
            'modal' => true,
            'hide_trigger' => true,
            'modal_title' => __('AI Content Analysis', 'modular-ai-assistant'),
            'button_text' => '',
            'button_class' => '',
            'post_id' => $post_id,
            'context' => 'editor',
        ]);
        
        return rest_ensure_response([
            'success' => true,
            'html' => $html,
        ]);
    }
}

