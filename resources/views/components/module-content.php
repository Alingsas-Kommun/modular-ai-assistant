<?php
/**
 * Module Content Partial
 * Shared content template for loading, error, and response states
 * Used in both modal and inline display modes
 * 
 * @var bool $show_curl Whether to show the CURL preview
 * @var string $context Display context: 'frontend' or 'editor'
 */

if (! defined('ABSPATH')) {
    exit;
}
?>

<div x-show="loading" class="modular-ai-loading">
    <div class="modular-ai-skeleton"></div>
    <div class="modular-ai-skeleton"></div>
    <div class="modular-ai-skeleton"></div>
    <div class="modular-ai-skeleton"></div>
</div>

<div x-show="error" class="modular-ai-error" x-text="error" x-cloak></div>

<div x-show="response && !loading" class="modular-ai-response" x-html="response" x-cloak></div>

<?php if ($context === 'frontend' && $show_curl): ?>
    <div x-show="curlPreview && !loading" class="modular-ai-curl-container" x-cloak>
        <pre class="modular-ai-curl-command" x-text="curlPreview"></pre>
    </div>
<?php endif; ?>

