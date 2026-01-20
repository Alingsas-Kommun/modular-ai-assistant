<?php

namespace ModularAIAssistant\Assets;

use ModularAIAssistant\Utilities\ViteManifest;
use ModularAIAssistant\Utilities\Template;

use function ModularAIAssistant\{config, di, getSetting};

if (! defined('ABSPATH')) {
    exit;
}

class Frontend
{
    /**
     * Constructor
     *
     * @return void
     */
    public function __construct()
    {
        add_action('wp_enqueue_scripts', [$this, 'enqueueFrontendAssets']);
        add_action('wp_head', [$this, 'injectCustomColors'], 100);
    }

    /**
     * Enqueue the frontend assets
     *
     * @return void
     */
    public function enqueueFrontendAssets()
    {
        $viteManifest = di(ViteManifest::class);
    
        $frontendJs = $viteManifest->getAsset('resources/assets/js/frontend.js');
        $frontendCss = $viteManifest->getCss('resources/assets/js/frontend.js');
    
        if ($frontendJs) {
            wp_enqueue_script(
                'modular-ai-assistant-frontend',
                config('paths.plugin_url') . 'dist/' . $frontendJs,
                [],
                config('app.version'),
                [
                    'strategy'  => 'defer',
                    'in_footer' => true,
                ]
            );
            
            // Add type="module" attribute
            add_filter('script_loader_tag', function($tag, $handle) {
                if ($handle === 'modular-ai-assistant-frontend') {
                    return str_replace(' src', ' type="module" src', $tag);
                }
                return $tag;
            }, 10, 2);
            
            wp_localize_script('modular-ai-assistant-frontend', 'modular_ai_assistant', [
                'restNonce' => wp_create_nonce('wp_rest'),
                'restUrl' => esc_url_raw(rest_url()) . 'modular-ai-assistant/v1',
            ]);

            wp_set_script_translations('modular-ai-assistant-frontend', 'modular-ai-assistant', config('paths.plugin_path') . 'resources/languages/');
        }
    
        foreach ($frontendCss as $index => $cssFile) {
            wp_enqueue_style(
                'modular-ai-assistant-frontend-' . $index,
                config('paths.plugin_url') . 'dist/' . $cssFile,
                [],
                config('app.version'),
            );
        }
    }

    /**
     * Inject custom colors as CSS variables
     *
     * @return void
     */
    public function injectCustomColors()
    {
        $primary_color = getSetting('primary_color', '#9333ea');
        $secondary_color = getSetting('secondary_color', '#3b82f6');

        if (!$primary_color || !$secondary_color) {
            return;
        }

        Template::load('partials/custom-colors', [
            'primary_color' => $primary_color,
            'secondary_color' => $secondary_color,
        ]);
    }
}

