<?php

/**
 * Embeddings API Client Class
 *
 * Handles embedding generation via Aethos SaaS backend
 *
 * @since      1.0.0
 * @package    Aethos_Chat
 * @subpackage Aethos_Chat/includes
 */

class Aethos_Embeddings {

    /**
     * API client instance
     */
    private $api_client;

    /**
     * Constructor
     */
    /**
     * Constructor
     */
    public function __construct() {
        // No dependencies needed for now
    }

    /**
     * Generate embedding for single text
     *
     * @param string $text Text to embed
     * @return array|false Embedding vector or false on failure
     */
    public function generate_embedding($text) {
        $api_key = get_option('aethos_api_key');
        $saas_url = get_option('aethos_saas_url', 'http://localhost:3000');
        
        if (empty($api_key)) {
            aethos_log('Embeddings: No API key configured');
            return false;
        }

        $response = wp_remote_post($saas_url . '/api/embeddings/query', [
            'headers' => [
                'Content-Type' => 'application/json',
                'x-api-key' => $api_key,
            ],
            'body' => wp_json_encode(['text' => $text]),
            'timeout' => 20, // Increased timeout for AI generation
        ]);
        
        if (is_wp_error($response)) {
            aethos_log('Embeddings: Request failed - ' . $response->get_error_message());
            return false;
        }
        
        $status_code = wp_remote_retrieve_response_code($response);
        $body = json_decode(wp_remote_retrieve_body($response), true);
        
        if ($status_code !== 200) {
            aethos_log('Embeddings: API error ' . $status_code . ' - ' . ($body['message'] ?? 'Unknown error'));
            return false;
        }
        
        if (isset($body['embedding']) && is_array($body['embedding'])) {
            // Optional: Log usage or cache status
            return $body['embedding'];
        }
        
        return false;
    }

    /**
     * Generate embeddings for multiple texts (batch)
     * 
     * Note: Current SaaS endpoint handles single requests.
     * We loop here. Future optimization: Batch endpoint on SaaS.
     *
     * @param array $texts Array of texts to embed
     * @return array|false Array of embedding vectors or false on failure
     */
    public function batch_generate_embeddings($texts) {
        if (empty($texts) || !is_array($texts)) {
            return false;
        }

        $embeddings = [];
        
        foreach ($texts as $text) {
            // Add small delay to prevent client-side overwhelming if many requests
            // But PHP is synchronous so it's fine.
            $embedding = $this->generate_embedding($text);
            
            if ($embedding === false) {
                aethos_log('Embeddings: Failed to generate embedding in batch');
                // Return what we have or fail completely?
                // Failing completely ensures integrity.
                return false;
            }
            
            $embeddings[] = $embedding;
        }

        return $embeddings;
    }

    /**
     * Validate embedding format
     *
     * @param mixed $embedding Embedding to validate
     * @return bool True if valid, false otherwise
     */
    public function validate_embedding($embedding) {
        if (!is_array($embedding)) {
            return false;
        }

        // Check dimension (1536 for text-embedding-3-small)
        // We allow some flexibility if model changes, but 1536 is standard.
        if (count($embedding) !== 1536) {
           return false;
        }

        return true;
    }

    /**
     * Log embedding usage locally
     * (Deprecated: Usage is now tracked on SaaS)
     */
    private function log_usage($usage) {
        // No-op
    }

    /**
     * Get usage stats
     * (Deprecated)
     */
    public function get_usage_stats() {
        return [];
    }

    /**
     * Reset usage stats
     */
    public function reset_usage_stats() {
        // No-op
    }
}
