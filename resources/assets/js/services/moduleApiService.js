/**
 * Module API Service
 * Handles all API communication for module execution
 */
export class ModuleApiService {
    /**
     * Fetch module response
     * 
     * @param {number} moduleId - Module ID
     * @param {string} query - Query string
     * @param {boolean} showCurl - Whether to show CURL preview
     * @param {number|null} postId - Optional post ID
     * @param {boolean|null} streaming - Streaming override
     * @returns {Promise<Response>}
     */
    static async fetchModuleResponse(moduleId, query, showCurl, postId = null, streaming = null) {
        const body = {
            module_id: moduleId,
            query: query,
            show_curl: showCurl
        };
        
        if (postId) {
            body.post_id = postId;
        }
        
        if (streaming !== null) {
            body.streaming = streaming;
        }
        
        const response = await fetch(modular_ai.restUrl + '/run', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-WP-Nonce': modular_ai.restNonce
            },
            body: JSON.stringify(body)
        });

        return response;
    }
    
    /**
     * Check if response is SSE stream
     * 
     * @param {Response} response - Fetch response
     * @returns {boolean}
     */
    static isStreamResponse(response) {
        const contentType = response.headers.get('Content-Type');
        return contentType && contentType.includes('text/event-stream');
    }
}

