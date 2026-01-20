<?php

namespace ModularAIAssistant;

use ModularAIAssistant\Utilities\Container;

if (! defined('ABSPATH')) {
    exit;
}

/**
 * Get configuration value
 *
 * @param string $key Configuration key in dot notation (e.g., 'app.name')
 * @param mixed $default Default value if key not found
 * @return mixed
 */
function config(string $key, $default = null)
{
    static $configs = [];

    $parts = explode('.', $key, 2);
    $file = $parts[0];
    $item = $parts[1] ?? null;
    $configPath = __DIR__ . "/config/{$file}.php";

    if (!isset($configs[$file])) {
        if (file_exists($configPath)) {
            $configs[$file] = require $configPath;
        } else {
            $configs[$file] = [];
        }
    }

    if ($item === null) {
        return $configs[$file];
    }
    
    return $configs[$file][$item] ?? $default;
}

/**
 * Get instance from dependency injection container
 *
 * @param string $class Class name to resolve
 * @return mixed
 */
function di(string $class)
{
    return Container::get($class);
}

/**
 * Get plugin version
 *
 * @return string
 */
function get_plugin_version(): string
{
    static $version = null;
    
    if ($version === null) {
        $plugin_data = \get_plugin_data(config('paths.plugin_path') . 'modular-ai-assistant.php');
        $version = $plugin_data['Version'];
    }
    
    return $version;
}

/**
 * Get plugin setting with automatic _mai_ prefix
 *
 * @param string $key Setting key without prefix (e.g., 'enable_shortcode')
 * @param mixed $default Default value if setting not found
 * @return mixed
 */
function getSetting(string $key, $default = null)
{
    return \get_option('_mai_' . $key, $default);
}

/**
 * Update plugin setting with automatic _mai_ prefix
 *
 * @param string $key Setting key without prefix (e.g., 'enable_shortcode')
 * @param mixed $value Value to update
 * @return bool
 */
function updateSetting(string $key, $value): bool
{
    return \update_option('_mai_' . $key, $value);
}
