<?php

namespace ModularAIAssistant\Utilities;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

use function ModularAIAssistant\config;

class Template
{
    /**
     * Load a template file with parameters
     *
     * @param string $template_path Relative path to template (without .php extension)
     * @param array $args Arguments to pass to template
     * @return void
     */
    public static function load($template_path, $args = [])
    {
        $full_path = config('paths.plugin_path') . 'resources/views/' . $template_path . '.php';
        
        if (!file_exists($full_path)) {
            return;
        }
        
        // Use a closure to create isolated scope with named parameters
        $render = function($__template_file, $__template_args) {
            extract($__template_args, EXTR_SKIP);
            unset($__template_args);
            include $__template_file;
        };
        
        $render($full_path, $args);
    }

    /**
     * Get the output of a template file
     *
     * @param string $template_path Relative path to template (without .php extension)
     * @param array $args Arguments to pass to template
     * @return string
     */
    public static function get($template_path, $args = [])
    {
        ob_start();
        self::load($template_path, $args);
        return ob_get_clean();
    }

    /**
     * Check if a template file exists
     *
     * @param string $template_path Relative path to template (without .php extension)
     * @return bool
     */
    public static function exists($template_path)
    {
        $full_path = config('paths.plugin_path') . 'resources/views/' . $template_path . '.php';
        return file_exists($full_path);
    }

    /**
     * Get the full path to a template file
     *
     * @param string $template_path Relative path to template (without .php extension)
     * @return string
     */
    public static function path($template_path)
    {
        return config('paths.plugin_path') . 'resources/views/' . $template_path . '.php';
    }
}

