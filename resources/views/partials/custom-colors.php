<?php
/**
 * Custom Colors Inline Styles
 *
 * @var string $primary_color
 * @var string $secondary_color
**/

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
?>

<style id="modular-ai-assistant-custom-colors">
    :root {
        --modular-ai-assistant-primary-color: <?php echo esc_attr($primary_color); ?>;
        --modular-ai-assistant-secondary-color: <?php echo esc_attr($secondary_color); ?>;
    }
</style>

