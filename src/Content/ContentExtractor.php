<?php

namespace ModularAIAssistant\Content;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

use ModularAIAssistant\Content\Utilities\HtmlProcessor;
use ModularAIAssistant\Content\Utilities\MarkdownConverter;

class ContentExtractor
{
    /**
     * Get content based on prompt type
     *
     * @param string $prompt_type Type of content to extract
     * @param string $custom_text Custom text if prompt_type is 'custom'
     * @param int|null $post_id Optional post ID
     * @return string The content to send to AI
     */
    public static function getContent($prompt_type, $custom_text = '', $post_id = null)
    {
        switch ($prompt_type) {
            case 'page_content':
                return self::getPageContent($post_id);
            case 'page_title':
                return self::getPageTitle($post_id);
            case 'page_excerpt':
                return self::getPageExcerpt($post_id);
            case 'custom':
            default:
                return $custom_text;
        }
    }

    /**
     * Get all text content from the current page (returned as markdown)
     *
     * @param int|null $post_id Optional post ID
     * @return string All text content from the page as markdown
     */
    public static function getPageContent($post_id = null)
    {
        global $post;
        
        // Use provided post_id or global $post
        if ($post_id) {
            $current_post = get_post($post_id);
        } else {
            $current_post = $post;
        }
        
        if (!$current_post) {
            return '';
        }
        
        $content = '';
        
        // Add title
        $content .= get_the_title($current_post) . "\n\n";
        
        // Add excerpt if available
        if (has_excerpt($current_post)) {
            $content .= get_the_excerpt($current_post) . "\n\n";
        }
        
        // Add main content
        $main_content = get_the_content(null, false, $current_post);
        if ($main_content) {
            // Apply WordPress content filters to get rendered HTML
            $main_content = apply_filters('the_content', $main_content); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound
            $content .= $main_content . "\n\n";
        }
        
        // Apply custom HTML content filter (allows third-party plugins to inject content)
        $content = apply_filters('modular_ai_assistant_custom_html_content', $content, $current_post->ID, get_post_type($current_post));
        
        // Initialize adapters manager and auto-load all adapters
        $adaptersManager = new Adapters();
        foreach (Adapters::getAdapterClasses() as $adapterClass) {
            if (class_exists($adapterClass) && $adapterClass::installed()) {
                $adaptersManager->registerAdapter(new $adapterClass());
            }
        }
        
        // Inject content from all registered adapters
        $content = $adaptersManager->injectAll($content, $current_post->ID);
        
        // Clean and prepare HTML for markdown conversion
        $htmlProcessor = new HtmlProcessor();
        $content = $htmlProcessor->cleanHtmlForMarkdown($content);
        
        // Convert HTML to Markdown
        $markdownConverter = new MarkdownConverter();
        $content = $markdownConverter->htmlToMarkdown($content);
        
        return trim($content);
    }

    /**
     * Get page title
     *
     * @param int|null $post_id Optional post ID
     * @return string Page title
     */
    public static function getPageTitle($post_id = null)
    {
        global $post;
        
        // Use provided post_id or global $post
        if ($post_id) {
            $current_post = get_post($post_id);
        } else {
            $current_post = $post;
        }
        
        if (!$current_post) {
            return '';
        }
        
        return get_the_title($current_post);
    }

    /**
     * Get page excerpt
     *
     * @param int|null $post_id Optional post ID
     * @return string Page excerpt
     */
    public static function getPageExcerpt($post_id = null)
    {
        global $post;
        
        // Use provided post_id or global $post
        if ($post_id) {
            $current_post = get_post($post_id);
        } else {
            $current_post = $post;
        }
        
        if (!$current_post) {
            return '';
        }
        
        if (has_excerpt($current_post)) {
            return get_the_excerpt($current_post);
        }
        
        // If no excerpt, generate one from content
        $content = get_the_content(null, false, $current_post);
        if ($content) {
            $content = wp_strip_all_tags($content);
            return wp_trim_words($content, 55, '...');
        }
        
        return '';
    }
}

