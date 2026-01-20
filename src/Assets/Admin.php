<?php

namespace ModularAIAssistant\Assets;

use ModularAIAssistant\Utilities\ViteManifest;
use function ModularAIAssistant\{config, di};

if (! defined('ABSPATH')) {
    exit;
}

class Admin
{
    /**
     * Constructor
     *
     * @return void
     */
    public function __construct()
    {
        add_action('admin_enqueue_scripts', [$this, 'enqueueAdminAssets']);
        add_action('admin_enqueue_scripts', [$this, 'enqueueAdminProfileColors']);
        add_action('admin_enqueue_scripts', [$this, 'enqueueColorPicker']);
    }

    /**
     * Enqueue the admin assets
     *
     * @param string $hook The current admin page hook
     * @return void
     */
    public function enqueueAdminAssets($hook)
    {
        $viteManifest = di(ViteManifest::class);
    
        $adminJs = $viteManifest->getAsset('resources/assets/js/admin.js');
        $adminCss = $viteManifest->getCss('resources/assets/js/admin.js');
    
        if ($adminJs) {
            wp_enqueue_script(
                'modular-ai-assistant-admin',
                config('paths.plugin_url') . 'dist/' . $adminJs,
                ['jquery', 'wp-util', 'wp-i18n'],
                config('app.version'),
                [
                    'strategy'  => 'defer',
                    'in_footer' => true,
                ]
            );
            
            // Add type="module" attribute for ES module support
            add_filter('script_loader_tag', function($tag, $handle) {
                if ($handle === 'modular-ai-assistant-admin') {
                    return str_replace(' src', ' type="module" src', $tag);
                }
                return $tag;
            }, 10, 2);
            
            wp_localize_script('modular-ai-assistant-admin', 'modular_ai_assistant', [
                'restNonce' => wp_create_nonce('wp_rest'),
                'restUrl' => esc_url_raw(rest_url()) . 'modular-ai-assistant/v1',
            ]);

            wp_set_script_translations('modular-ai-assistant-admin', 'modular-ai-assistant', config('paths.plugin_path') . 'resources/languages/');
        }
    
        foreach ($adminCss as $index => $cssFile) {
            wp_enqueue_style(
                'modular-ai-assistant-' . $index,
                config('paths.plugin_url') . 'dist/' . $cssFile,
                [],
                config('app.version'),
            );
        }
    }

    /**
     * Enqueue the admin profile colors
     *
     * @return void
     */
    public function enqueueAdminProfileColors()
    {
        $admin_color = get_user_option('admin_color');
    
        global $_wp_admin_css_colors;
        if (!isset($_wp_admin_css_colors[$admin_color])) {
            return;
        }
    
        $scheme = $_wp_admin_css_colors[$admin_color];
        $colors = $scheme->colors;
        $color_count = count($colors);
        $primary_color = $color_count === 4 ? $colors[2] : $colors[1];
        $secondary_color = $color_count === 4 ? $colors[1] : $colors[2];
        $css_vars = array();
    
        array_push($css_vars, sprintf(
            '--wp-admin-color-primary: %s',
            esc_attr($primary_color)
        ));
    
        array_push($css_vars, sprintf(
            '--wp-admin-color-secondary: %s',
            esc_attr($secondary_color)
        ));
    
        array_push($css_vars, sprintf(
            '--wp-admin-color-primary-light: color-mix(in srgb, %s 10%%, transparent)',
            esc_attr($primary_color)
        ));

        array_push($css_vars, sprintf(
            '--wp-admin-color-primary-dark: color-mix(in srgb, %s 85%%, #000)',
            esc_attr($primary_color)
        ));
    
        array_push($css_vars, sprintf(
            '--wp-admin-color-primary-border: color-mix(in srgb, %s 20%%, transparent)',
            esc_attr($primary_color)
        ));
    
        array_push($css_vars, sprintf(
            '--wp-admin-color-secondary-light: color-mix(in srgb, %s 10%%, transparent)',
            esc_attr($secondary_color)
        ));

        array_push($css_vars, sprintf(
            '--wp-admin-color-secondary-dark: color-mix(in srgb, %s 85%%, #000)',
            esc_attr($secondary_color)
        ));
    
        array_push($css_vars, sprintf(
            '--wp-admin-color-secondary-border: color-mix(in srgb, %s 20%%, transparent)',
            esc_attr($secondary_color)
        ));
    
        foreach ($colors as $index => $color) {
            array_push($css_vars, sprintf(
                '--wp-admin-color-%d: %s',
                (int) $index,
                esc_attr($color)
            ));
        }
    
        $css_output = ':root {' . implode(';', array_map('esc_html', $css_vars)) . '}';
        wp_add_inline_style('wp-admin', $css_output);
    }

    /**
     * Enqueue WordPress color picker on settings page
     *
     * @param string $hook The current admin page hook
     * @return void
     */
    public function enqueueColorPicker($hook)
    {
        // Only load on our settings page
        if ($hook !== 'modular-ai-assistant_page_modular-ai-assistant-settings') {
            return;
        }

        // Enqueue WordPress color picker
        wp_enqueue_style('wp-color-picker');
        wp_enqueue_script('wp-color-picker');

        // Initialize color picker
        wp_add_inline_script('wp-color-picker', '
            jQuery(document).ready(function($) {
                $(".modular-ai-assistant-color-picker").wpColorPicker();
            });
        ');
    }
}

