<?php
/**
 * Plugin Name: Modular AI
 * Plugin URI: 
 * Description: WordPress plugin that let's you integrate AI into your wordpress website
 * Version: 1.0.2
 * Author: Alingsås Kommun
 * Author URI: https://alingsas.se
 * License: GPL v3 or later
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain: modular-ai
 * Domain Path: /resources/languages
 */

use ModularAI\Application;
use ModularAI\Providers\PluginServiceProvider;
use ModularAI\Providers\AdminServiceProvider;
use ModularAI\Providers\FrontendServiceProvider;
use ModularAI\Providers\ApiServiceProvider;

// Check if the plugin is loaded correctly
if (!defined('ABSPATH')) {
    throw new \Exception('Modular AI can only be loaded within the context of WordPress (ABSPATH not defined).');
}

// Check if the plugin is loaded correctly
if (!function_exists('add_action')) {
    throw new \Exception('Modular AI can only be loaded within the context of WordPress (add_action function not found).');
}

// Load the autoloader
if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    require_once __DIR__ . '/vendor/autoload.php';
}

// Load helper functions
if(file_exists(__DIR__ . '/helpers.php')) {
    require_once __DIR__ . '/helpers.php';
}

// Load the textdomain
add_action('init', function() {
    wp_set_script_translations('modular-ai', 'modular-ai');
});

// Boot the plugin
Application::configure()
    ->withProviders([
        PluginServiceProvider::class,
        AdminServiceProvider::class,
        FrontendServiceProvider::class,
        ApiServiceProvider::class,
    ])
    ->boot(); 