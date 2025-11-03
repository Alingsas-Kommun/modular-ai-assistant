<?php

namespace ModularAI\Admin;

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
            __('Modular AI', 'modular-ai'),            // Page title
            __('Modular AI', 'modular-ai'),            // Menu title
            'manage_options',                               // Capability
            'modular-ai',                                 // Menu slug
            false,                                          // Callback function
            'dashicons-lightbulb',                          // Icon
            30                                              // Position
        );
    }
}

