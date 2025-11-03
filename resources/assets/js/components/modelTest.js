import Alpine from 'alpinejs';

Alpine.data('modelTest', () => ({
    loading: false,
    result: null,
    resultType: 'success',

    async testModel(postId) {
        this.loading = true;
        this.result = null;

        try {
            const response = await fetch(`${window.modular_ai.restUrl}/test-model`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-WP-Nonce': window.modular_ai.restNonce,
                },
                body: JSON.stringify({
                    model_id: postId,
                }),
            });

            const data = await response.json();

            if (data.success) {
                this.resultType = 'success';
                let message = `<p><strong>✓ ${data.message}</strong></p>`;
                
                if (data.response_text) {
                    message += `<p><strong>${this.__('Response:', 'modular-ai')}</strong> ${this.escapeHtml(data.response_text)}</p>`;
                }
                
                this.result = message;
            } else {
                this.resultType = 'error';
                const errorMessage = data.message || data.data?.message || this.__('Unknown error', 'modular-ai');
                this.result = `<p><strong>${this.__('Error:', 'modular-ai')}</strong> ${this.escapeHtml(errorMessage)}</p>`;
            }
        } catch (error) {
            this.resultType = 'error';
            this.result = `<p><strong>${this.__('Error:', 'modular-ai')}</strong> ${this.__('Network error. Please try again.', 'modular-ai')}</p>`;
        } finally {
            this.loading = false;
        }
    },

    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    },

    __(text, domain) {
        const { __ } = wp.i18n;

        return __(text, domain);
    }
}));

