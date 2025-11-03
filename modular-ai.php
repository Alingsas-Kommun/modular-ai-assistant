<?php
/**
 * Plugin Name: Modular AI
 * Plugin URI: 
 * Description: WordPress plugin that let's you integrate AI into your wordpress website
 * Version: 1.0.0
 * Author: Adam Alexandersson
 * Author URI: 
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
    exit;
}

// Check if the plugin is loaded correctly
if (!function_exists('add_action')) {
    exit;
}

// Load the autoloader
$autoloader = __DIR__ . '/vendor/autoload.php';
if (file_exists($autoloader)) {
    require_once $autoloader;
} else {
    wp_die('Please run composer install to install the necessary dependencies.');
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