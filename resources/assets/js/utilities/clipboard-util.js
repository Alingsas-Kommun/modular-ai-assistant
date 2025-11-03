/**
 * Clipboard Utilities
 * Handles copying text to clipboard with feedback
 */
export class ClipboardUtil {
    /**
     * Copy text to clipboard
     * 
     * @param {string} text - Text to copy
     * @returns {Promise<boolean>} Success status
     */
    static async copyText(text) {
        if (!text) {
            return false;
        }
        
        try {
            await navigator.clipboard.writeText(text);
            return true;
        } catch (error) {
            console.error('Failed to copy to clipboard:', error);
            return false;
        }
    }
    
    /**
     * Copy with temporary state feedback
     * 
     * @param {string} text - Text to copy
     * @param {Function} onSuccess - Callback on success
     * @param {number} duration - Duration to show success state (ms)
     * @returns {Promise<boolean>}
     */
    static async copyWithFeedback(text, onSuccess, duration = 2000) {
        const success = await this.copyText(text);
        
        if (success && onSuccess) {
            onSuccess();
            
            // Reset state after duration
            if (duration > 0) {
                setTimeout(() => {
                    onSuccess(null);
                }, duration);
            }
        }
        
        return success;
    }
}

