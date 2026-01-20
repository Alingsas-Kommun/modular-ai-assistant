<?php

namespace ModularAIAssistant\Shortcodes;

use ModularAIAssistant\Utilities\Template;

use function ModularAIAssistant\getSetting;

class ModularAIAssistant
{
    /**
     * Constructor
     */
    public function __construct()
    {
        add_shortcode('modular-ai-assistant', [$this, 'render']);
    }

    /**
     * Render the shortcode
     * 
     * This is a thin wrapper that invokes the module system.
     * Modules can also be executed from admin UI, API, etc.
     *
     * @param array $atts Shortcode attributes
     * @return string Rendered output
     */
    public function render($atts)
    {
        if (! getSetting('enable_shortcode', true)) {
            return '';
        }
        
        $atts = array_change_key_case((array) $atts, CASE_LOWER);
        
        $id = isset($atts['id']) ? absint($atts['id']) : 0;
        $query = isset($atts['q']) ? sanitize_text_field($atts['q']) : '';
        $show_curl = isset($atts['show_curl']) && $atts['show_curl'];
        $modal = isset($atts['modal']) && $atts['modal'];
        $button_text = isset($atts['button_text']) ? sanitize_text_field($atts['button_text']) : __('Show AI Response', 'modular-ai-assistant');
        $button_class = isset($atts['button_class']) ? sanitize_text_field($atts['button_class']) : 'modular-ai-assistant-modal-btn';
        
        // Streaming override parameter
        $streaming = null;
        if (isset($atts['streaming'])) {
            $streaming = filter_var($atts['streaming'], FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
        }
        
        if (!$id) {
            return '';
        }
        
        $instance_id = 'modular-ai-assistant-' . uniqid();
        
        return Template::get('components/module', [
            'module_id' => $id,
            'query' => $query,
            'show_curl' => $show_curl,
            'instance_id' => $instance_id,
            'modal' => $modal,
            'button_text' => $button_text,
            'button_class' => $button_class,
            'streaming' => $streaming,
        ]);
    }
}

