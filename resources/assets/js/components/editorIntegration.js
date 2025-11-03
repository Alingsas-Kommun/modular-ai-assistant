const { __ } = wp.i18n;

// Editor integration for admin
document.addEventListener('DOMContentLoaded', () => {
    const analyzeButtons = document.querySelectorAll('.modular-ai-analyze-btn');
    
    analyzeButtons.forEach(button => {
        button.addEventListener('click', async function() {
            const postId = this.getAttribute('data-post-id');
            const select = document.getElementById(`modular-ai-module-selector-${postId}`);
            const moduleId = select ? select.value : null;
            
            if (!moduleId) {
                alert(__('Please select a module', 'modular-ai'));
                return;
            }
            
            // Get modal container
            let modalContainer = document.getElementById(`modular-ai-editor-modal-${postId}`);
            if (!modalContainer) {
                return;
            }
            
            const instanceId = `editor-${postId}-${Date.now()}`;
            
            // Show loading state
            modalContainer.style.display = 'block';
            
            try {
                // Fetch the module template from the API
                const params = new URLSearchParams({
                    module_id: parseInt(moduleId),
                    instance_id: instanceId,
                    post_id: parseInt(postId)
                });
                
                const response = await fetch(window.modular_ai.restUrl + '/template/module?' + params, {
                    method: 'GET',
                    headers: {
                        'X-WP-Nonce': window.modular_ai.restNonce
                    }
                });
                
                if (!response.ok) {
                    throw new Error(__('Failed to load template', 'modular-ai'));
                }
                
                const data = await response.json();
                
                if (data.success && data.html) {
                    // Insert the template HTML
                    modalContainer.innerHTML = data.html;
                    
                    // Initialize Alpine on the new content
                    if (window.Alpine) {
                        window.Alpine.initTree(modalContainer);
                        
                        // Trigger the fetchResponse method to load data
                        setTimeout(() => {
                            const alpineElement = modalContainer.querySelector('[x-data]');
                            if (alpineElement) {
                                const alpineData = window.Alpine.$data(alpineElement);
                                if (alpineData && alpineData.fetchResponse) {
                                    alpineData.fetchResponse();
                                }
                            }
                        }, 100);
                    }
                } else {
                    throw new Error(__('Invalid template response', 'modular-ai'));
                }
                
            } catch (error) {
                console.error(__('Error loading module template:', 'modular-ai'), error);
                modalContainer.innerHTML = `
                    <div style="padding: 20px; color: #d63638;">
                        ${__('Error loading template:', 'modular-ai')} ${error.message}
                    </div>
                `;
            }
        });
    });
});

