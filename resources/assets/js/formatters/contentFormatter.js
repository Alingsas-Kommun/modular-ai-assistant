/**
 * Content Formatter
 * Handles content formatting including markdown and HTML escaping
 */
export class ContentFormatter {
    /**
     * Format content based on settings
     * 
     * @param {string} text - Text to format
     * @param {boolean} markdownEnabled - Whether markdown is enabled
     * @param {string} outputFormat - Output format ('text' or 'html')
     * @returns {string} Formatted content
     */
    static format(text, markdownEnabled = false, outputFormat = 'text') {
        if (!text) {
            return '';
        }
        
        // Apply markdown if enabled
        if (markdownEnabled && typeof marked !== 'undefined') {
            try {
                // Configure marked for better incomplete markdown handling
                marked.setOptions({
                    breaks: true,
                    gfm: true,
                    silent: true // Don't throw on errors
                });
                
                return marked.parse(text);
            } catch (e) {
                console.error('Markdown parsing error:', e);
                return this.escapeHtml(text).replace(/\n/g, '<br>');
            }
        }
        
        // For HTML output, return as-is (already sanitized on server)
        if (outputFormat === 'html') {
            return text;
        }
        
        // Plain text with line breaks
        return this.escapeHtml(text).replace(/\n/g, '<br>');
    }
    
    /**
     * Escape HTML special characters
     * 
     * @param {string} text - Text to escape
     * @returns {string} Escaped text
     */
    static escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
    
    /**
     * Strip HTML tags from text
     * 
     * @param {string} html - HTML string
     * @returns {string} Plain text
     */
    static stripHtml(html) {
        const tempDiv = document.createElement('div');
        tempDiv.innerHTML = html;
        return tempDiv.textContent || tempDiv.innerText || '';
    }
}

