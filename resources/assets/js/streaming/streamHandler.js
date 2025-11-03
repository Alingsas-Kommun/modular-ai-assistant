const { __ } = wp.i18n;

/**
 * SSE Stream Handler
 * Handles Server-Sent Events streaming and parsing
 */
export class StreamHandler {
    /**
     * Process SSE stream
     * 
     * @param {Response} response - Fetch response with SSE stream
     * @param {Object} callbacks - Event callbacks
     * @param {Function} callbacks.onMetadata - Called when metadata is received
     * @param {Function} callbacks.onChunk - Called when content chunk is received
     * @param {Function} callbacks.onDone - Called when stream is complete
     * @param {Function} callbacks.onError - Called on error
     * @returns {Promise<void>}
     */
    static async processStream(response, callbacks) {
        const reader = response.body.getReader();
        const decoder = new TextDecoder();
        
        let buffer = '';
        
        try {
            while (true) {
                const {value, done} = await reader.read();
                
                if (done) {
                    break;
                }
                
                buffer += decoder.decode(value, {stream: true});
                
                // Process complete SSE messages
                const lines = buffer.split('\n');
                buffer = lines.pop() || ''; // Keep incomplete line in buffer
                
                for (const line of lines) {
                    if (line.startsWith('data: ')) {
                        const data = line.substring(6);
                        
                        try {
                            const event = JSON.parse(data);
                            
                            switch (event.type) {
                                case 'metadata':
                                    if (callbacks.onMetadata) {
                                        callbacks.onMetadata(event);
                                    }
                                    break;
                                    
                                case 'chunk':
                                    if (callbacks.onChunk) {
                                        callbacks.onChunk(event.content);
                                    }
                                    break;
                                    
                                case 'done':
                                    if (callbacks.onDone) {
                                        callbacks.onDone();
                                    }
                                    return; // Exit the function
                                    
                                case 'error':
                                    if (callbacks.onError) {
                                        callbacks.onError(event.message, event.code);
                                    }
                                    return; // Exit the function
                            }
                        } catch (parseError) {
                            console.error('Failed to parse SSE event:', parseError);
                        }
                    }
                }
            }
        } catch (streamError) {
            if (callbacks.onError) {
                callbacks.onError(__('Failed to read stream', 'modular-ai'));
            }
            throw streamError;
        }
    }
}

