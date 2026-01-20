<?php

if (!defined('ABSPATH')) {
    exit;
}

return [
    'name' => 'modular-ai-assistant',
    'version' => function_exists('ModularAIAssistant\get_plugin_version') ? \ModularAIAssistant\get_plugin_version() : '1.0.0',
]; 