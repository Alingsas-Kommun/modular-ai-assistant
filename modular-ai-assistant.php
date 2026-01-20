<?php
/**
 * Plugin Name: Modular AI Assistant
 * Plugin URI: 
 * Description: WordPress plugin that lets you integrate AI into your WordPress website
 * Version: 1.0.4
 * Author: Alingsås Kommun
 * Author URI: https://alingsas.se
 * License: GPL v3 or later
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain: modular-ai-assistant
 * Domain Path: /resources/languages
 */

use ModularAIAssistant\Application;
use ModularAIAssistant\Providers\PluginServiceProvider;
use ModularAIAssistant\Providers\AdminServiceProvider;
use ModularAIAssistant\Providers\FrontendServiceProvider;
use ModularAIAssistant\Providers\ApiServiceProvider;

// Check if the plugin is loaded correctly
if (!defined('ABSPATH')) {
    exit;
}

// Check if the plugin is loaded correctly
if (!function_exists('add_action')) {
    exit;
}

// Load the autoloader
// Best practice for dual distribution (WordPress.org + Composer):
// - WordPress.org: Use plugin's own vendor/ directory (included in ZIP)
// - Composer: Use parent project's vendor/ directory (dependencies managed by Composer)
//
// Note: When installed via Composer with composer/installers, the plugin is installed
// in the WordPress plugins directory (e.g., web/app/plugins/modular-ai-assistant/), not in vendor/.
// We detect this by checking if the plugin's own vendor/ directory exists.

// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
// These are file-scoped variables used for bootstrapping, not true global variables.

$pluginAutoloader = __DIR__ . '/vendor/autoload.php';

if (file_exists($pluginAutoloader)) {
    // Standard distribution: Use plugin's own vendor directory
    $autoloader = $pluginAutoloader;
} else {
    // Composer installation: Use parent project's vendor directory
    // Walk up the directory tree to find the project root's vendor/autoload.php
    $currentDir = realpath(__DIR__);
    $maxDepth = 10; // Prevent infinite loops
    $depth = 0;
    $autoloader = null;
    
    while ($depth < $maxDepth && $currentDir !== false) {
        $parentDir = dirname($currentDir);
        
        // Stop if we've reached the filesystem root
        if ($parentDir === $currentDir) {
            break;
        }
        
        $vendorAutoloader = $parentDir . '/vendor/autoload.php';
        if (file_exists($vendorAutoloader)) {
            $autoloader = $vendorAutoloader;
            break;
        }
        
        $currentDir = $parentDir;
        $depth++;
    }
    
    if (!$autoloader || !file_exists($autoloader)) {
        wp_die(
            'Modular AI Assistant: Could not find Composer autoloader. ' .
            'If installed via Composer, ensure dependencies are installed. ' .
            'If installed manually, run "composer install" in the plugin directory.'
        );
    }
}

require_once $autoloader;

// phpcs:enable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound

// Load the textdomain
add_action('init', function() {
    wp_set_script_translations('modular-ai-assistant', 'modular-ai-assistant');
});

// Require helpers
require_once __DIR__ . '/helpers.php';

// Boot the plugin
Application::configure()
    ->withProviders([
        PluginServiceProvider::class,
        AdminServiceProvider::class,
        FrontendServiceProvider::class,
        ApiServiceProvider::class,
    ])
    ->boot(); 