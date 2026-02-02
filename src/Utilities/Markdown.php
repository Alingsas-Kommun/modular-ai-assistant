<?php

namespace ModularAIAssistant\Utilities;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

use FastVolt\Helper\Markdown as FastvoltMarkdown;

class Markdown
{
    /**
     * Convert markdown to HTML
     *
     * @param string $text Markdown text
     * @return string HTML output
     */
    public static function toHtml($text)
    {
        if (empty($text)) {
            return '';
        }
        
        try {
            // Initialize markdown object
            $markdown = new FastvoltMarkdown();
            
            // Set markdown content
            $markdown->setContent($text);
            
            // Compile as raw HTML
            return $markdown->toHtml();
        } catch (\Exception $e) {
            // If markdown parsing fails, return the original text
            return $text;
        }
    }

    /**
     * Convert markdown lists to plain text with line breaks
     *
     * @param string $text Raw text with markdown lists
     * @return string Text with lists converted to plain text
     */
    public static function convertListsToPlainText($text)
    {
        // Convert unordered list items (-, *, +) to plain text with line breaks
        $text = preg_replace('/^[\s]*[-*+]\s+(.+)$/m', '$1', $text);
        
        // Convert ordered list items (1., 2., etc.) to plain text with line breaks
        $text = preg_replace('/^[\s]*\d+\.\s+(.+)$/m', '$1', $text);
        
        return $text;
    }
}

