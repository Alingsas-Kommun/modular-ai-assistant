/**
 * Word Animator
 * Handles smooth word-by-word animation for streaming text
 */
export class WordAnimator {
    constructor() {
        this.displayedText = '';
        this.pendingText = '';
        this.animationInterval = null;
        this.isActive = false;
        this.onUpdate = null;
        this.wordsPerSecond = 25; // 25 words per second
    }
    
    /**
     * Start the animation
     * 
     * @param {Function} onUpdate - Callback when text is updated
     */
    start(onUpdate) {
        this.onUpdate = onUpdate;
        this.isActive = true;
        
        // Clear any existing animation
        if (this.animationInterval) {
            clearInterval(this.animationInterval);
        }
        
        // Animate words at 40ms per word (25 words per second)
        this.animationInterval = setInterval(() => {
            if (this.pendingText.length === 0) {
                // Nothing to animate
                if (!this.isActive) {
                    // Animation is done
                    clearInterval(this.animationInterval);
                    this.animationInterval = null;
                }
                return;
            }
            
            // Extract next word or chunk from pending text
            const match = this.pendingText.match(/^(\s*\S+\s*)/);
            if (match) {
                const word = match[1];
                this.displayedText += word;
                this.pendingText = this.pendingText.substring(word.length);
                
                // Notify update
                if (this.onUpdate) {
                    this.onUpdate(this.displayedText);
                }
            } else {
                // Add remaining text if no word pattern matches
                this.displayedText += this.pendingText;
                this.pendingText = '';
                
                if (this.onUpdate) {
                    this.onUpdate(this.displayedText);
                }
            }
        }, 1000 / this.wordsPerSecond); // Convert words per second to milliseconds
    }
    
    /**
     * Add text to the pending buffer
     * 
     * @param {string} text - Text to add
     */
    addText(text) {
        this.pendingText += text;
    }
    
    /**
     * Stop the animation and flush remaining text
     */
    stop() {
        this.isActive = false;
        
        if (this.animationInterval) {
            clearInterval(this.animationInterval);
            this.animationInterval = null;
        }
        
        // Flush any remaining pending text
        if (this.pendingText.length > 0) {
            this.displayedText += this.pendingText;
            this.pendingText = '';
            
            if (this.onUpdate) {
                this.onUpdate(this.displayedText);
            }
        }
    }
    
    /**
     * Reset the animator
     */
    reset() {
        this.stop();
        this.displayedText = '';
        this.pendingText = '';
    }
    
    /**
     * Get the displayed text
     * 
     * @returns {string}
     */
    getDisplayedText() {
        return this.displayedText;
    }
    
    /**
     * Check if there's pending text
     * 
     * @returns {boolean}
     */
    hasPendingText() {
        return this.pendingText.length > 0;
    }
}

