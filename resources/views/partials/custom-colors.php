<?php
/**
 * Custom Colors Inline Styles
 *
 * @var string $primary_color
 * @var string $secondary_color
 */
?>

<style id="modular-ai-custom-colors">
    :root {
        --modular-ai-primary-color: <?php echo esc_attr($primary_color); ?>;
        --modular-ai-secondary-color: <?php echo esc_attr($secondary_color); ?>;
    }
</style>

