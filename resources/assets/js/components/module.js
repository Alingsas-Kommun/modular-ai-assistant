import { ModuleApiService } from '../services/moduleApiService.js';
import { StreamHandler } from '../streaming/streamHandler.js';
import { WordAnimator } from '../streaming/wordAnimator.js';
import { ContentFormatter } from '../formatters/contentFormatter.js';
import { ClipboardUtil } from '../utilities/clipboard-util.js';

const { __ } = wp.i18n;

document.addEventListener('alpine:init', () => {
    Alpine.data('modularAIModule', (moduleId, query, modal, showCurl, instanceId, postId, streaming) => ({
        // State
        loading: false,
        error: null,
        response: null,
        showModal: false,
        curlPreview: null,
        copiedState: null,
        isStreaming: false,
        markdownEnabled: false,
        outputFormat: 'text',
        isCached: false,
        
        // Animator instance
        animator: null,
        
        /**
         * Initialize component
         */
        async init() {
            // Initialize word animator
            this.animator = new WordAnimator();
            
            // Fetch response for inline mode
            if (!modal) {
                await this.fetchResponse();
            }

            // Watch for modal close to cleanup
            if (modal) {
                this.$watch('showModal', (value) => {
                    if (!value) {
                        this.cleanup();
                    }
                });
            }
        },
        
        /**
         * Cleanup on component destroy
         */
        destroy() {
            this.cleanup();
        },
        
        /**
         * Cleanup resources
         */
        cleanup() {
            if (this.animator) {
                this.animator.stop();
            }
        },
        
        /**
         * Fetch module response
         */
        async fetchResponse() {
            // Reset state
            this.loading = true;
            this.error = null;
            this.response = null;
            this.curlPreview = null;
            this.isStreaming = false;
            
            // Reset animator
            if (this.animator) {
                this.animator.reset();
            }
            
            // Open modal if needed
            if (modal) {
                this.showModal = true;
            }
            
            try {
                // Make API request
                const res = await ModuleApiService.fetchModuleResponse(
                    moduleId,
                    query,
                    showCurl,
                    postId,
                    streaming
                );
                
                // Handle stream or regular response
                if (ModuleApiService.isStreamResponse(res)) {
                    await this.handleStreamingResponse(res);
                } else {
                    await this.handleRegularResponse(res);
                }
                
            } catch (err) {
                this.error = err.message || __('Something went wrong. Please try again later.', 'modular-ai-assistant');
                this.loading = false;
            }
        },
        
        /**
         * Handle streaming response
         * 
         * @param {Response} response - Fetch response
         */
        async handleStreamingResponse(response) {
            this.isStreaming = true;
            let firstChunkReceived = false;
            
            try {
                await StreamHandler.processStream(response, {
                    onMetadata: (event) => {
                        // Store metadata
                        this.markdownEnabled = event.markdown_enabled || false;
                        this.outputFormat = event.output_format || 'text';
                        this.isCached = event.cached || false;
                        
                        if (event.curl_preview) {
                            this.curlPreview = event.curl_preview;
                        }
                    },
                    
                    onChunk: (content) => {
                        // Start animation on first chunk
                        if (!firstChunkReceived) {
                            this.loading = false;
                            firstChunkReceived = true;
                            
                            // Only use animator for non-cached responses
                            if (!this.isCached) {
                                this.animator.start((text) => {
                                    this.response = ContentFormatter.format(
                                        text,
                                        this.markdownEnabled,
                                        this.outputFormat
                                    );
                                });
                            }
                        }
                        
                        // For cached responses, show content immediately without animation
                        if (this.isCached) {
                            this.response = ContentFormatter.format(
                                content,
                                this.markdownEnabled,
                                this.outputFormat
                            );
                        } else {
                            // Add content to animator buffer for non-cached responses
                            this.animator.addText(content);
                        }
                    },
                    
                    onDone: () => {
                        this.isStreaming = false;
                        if (!this.isCached && this.animator) {
                            this.animator.isActive = false;
                        }
                    },
                    
                    onError: (message) => {
                        // Handle streaming error
                        if (!this.isCached && this.animator && (this.animator.getDisplayedText() || this.animator.hasPendingText())) {
                            // Show partial results
                            this.animator.stop();
                            this.error = __('Stream interrupted:', 'modular-ai-assistant') + ' ' + message;
                        } else {
                            this.error = message;
                            this.response = null;
                        }
                        
                        this.isStreaming = false;
                    }
                });
                
            } catch (error) {
                // Handle stream reading error
                if (!this.isCached && this.animator && (this.animator.getDisplayedText() || this.animator.hasPendingText())) {
                    this.animator.stop();
                    this.error = __('Stream interrupted', 'modular-ai-assistant');
                } else {
                    this.error = __('Failed to read stream', 'modular-ai-assistant');
                }
            } finally {
                this.loading = false;
            }
        },
        
        /**
         * Handle regular (non-streaming) response
         * 
         * @param {Response} response - Fetch response
         */
        async handleRegularResponse(response) {
            const data = await response.json();
            
            if (!response.ok) {
                throw new Error(data.message || __('Request failed', 'modular-ai-assistant'));
            }
            
            this.response = data.content;
            
            if (data.curl_preview) {
                this.curlPreview = data.curl_preview;
            }
            
            this.loading = false;
        },
        
        /**
         * Copy analysis to clipboard
         */
        async copyAnalysis() {
            if (!this.response) return;
            
            const text = ContentFormatter.stripHtml(this.response);
            
            await ClipboardUtil.copyWithFeedback(text, (state) => {
                this.copiedState = state ? 'analysis' : null;
            });
        },
        
        /**
         * Copy CURL command to clipboard
         */
        async copyCurl() {
            if (!this.curlPreview) return;
            
            await ClipboardUtil.copyWithFeedback(this.curlPreview, (state) => {
                this.copiedState = state ? 'curl' : null;
            });
        }
    }));
});