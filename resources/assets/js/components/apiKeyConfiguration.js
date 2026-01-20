const { __ } = wp.i18n;

document.addEventListener('alpine:init', () => {
    Alpine.data('apiKeyConfiguration', (postId) => ({
        copied: false,
        regenerating: false,
        postId: postId,
        
        async copyToClipboard() {
            const input = this.$refs.apiKeyInput;
            
            if (input && navigator.clipboard) {
                try {
                    await navigator.clipboard.writeText(input.value);
                    this.showCopied();
                } catch (err) {
                    console.error(__('Failed to copy:', 'modular-ai-assistant'), err);
                    alert(__('Failed to copy to clipboard', 'modular-ai-assistant'));
                }
            }
        },
        
        showCopied() {
            this.copied = true;
            setTimeout(() => {
                this.copied = false;
            }, 2000);
        },
        
        regenerateKey() {
            if (this.regenerating) {
                return;
            }
            
            // Confirm before regenerating
            if (!confirm(__('Are you sure you want to regenerate this API key? The old key will no longer work.', 'modular-ai-assistant'))) {
                return;
            }
            
            this.regenerating = true;
            
            // Simulate a brief delay for better UX
            setTimeout(() => {
                try {
                    // Generate a new secure API key
                    const newKey = this.generateSecureKey();
                    
                    // Update the input field with the new key
                    const input = this.$refs.apiKeyInput;
                    if (input) {
                        input.value = newKey;
                        
                        // Mark the field as dirty so WordPress knows to save it
                        input.dispatchEvent(new Event('input', { bubbles: true }));
                        input.dispatchEvent(new Event('change', { bubbles: true }));
                    }
                    
                    // Show copied state briefly to indicate success
                    this.copied = true;
                    setTimeout(() => {
                        this.copied = false;
                    }, 2000);
                } catch (err) {
                    console.error(__('Failed to regenerate API key:', 'modular-ai-assistant'), err);
                    alert(__('Failed to regenerate API key') + ': ' + err.message);
                } finally {
                    this.regenerating = false;
                }
            }, 300);
        },
        
        generateSecureKey() {
            // Generate a secure random key similar to WordPress wp_generate_password
            const prefix = 'mai_';
            const length = 32;
            const charset = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
            
            // Use crypto.getRandomValues for cryptographically secure random values
            const randomValues = new Uint8Array(length);
            crypto.getRandomValues(randomValues);
            
            let key = prefix;
            for (let i = 0; i < length; i++) {
                key += charset[randomValues[i] % charset.length];
            }
            
            return key;
        }
    }));
});

