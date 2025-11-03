<?php

if (!defined('ABSPATH')) {
    exit;
}

return [
    'name' => 'modular-ai',
    'version' => function_exists('ModularAI\get_plugin_version') ? \ModularAI\get_plugin_version() : '1.0.0',
]; 