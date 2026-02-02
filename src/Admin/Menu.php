<?php

namespace ModularAIAssistant\Admin;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Menu
{
    /**
     * Constructor
     *
     * @return void
     */
    public function __construct()
    {
        add_action('admin_menu', [$this, 'registerAdminMenu']);
    }

    /**
     * Register the admin menu
     *
     * @return void
     */
    public function registerAdminMenu()
    {
        add_menu_page(
            __('Modular AI Assistant', 'modular-ai-assistant'),            // Page title
            __('Modular AI Assistant', 'modular-ai-assistant'),            // Menu title
            'manage_options',                               // Capability
            'modular-ai-assistant',                                 // Menu slug
            false,                                          // Callback function
            'dashicons-lightbulb',                          // Icon
            30                                              // Position
        );
    }
}

