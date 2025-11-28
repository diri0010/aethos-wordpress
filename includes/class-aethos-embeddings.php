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
    public function __construct() {
        $this->api_client = new Aethos_API_Client();
    }

    /**
     * Generate embedding for single text
     *
     * @param string $text Text to embed
     * @param string $model Embedding model (default: text-embedding-3-small)
     * @return array|false Embedding vector or false on failure
     */
    public function generate_embedding($text, $model = 'text-embedding-3-small') {
        try {
            $result = $this->batch_generate_embeddings(array($text), $model);
            
            if ($result && isset($result[0])) {
                return $result[0];
            }
        } catch (Exception $e) {
            throw $e; // Rethrow to let caller handle
        }
        
        return false;
    }

    /**
     * Generate embeddings for multiple texts (batch)
     *
     * @param array  $texts Array of texts to embed
     * @param string $model Embedding model
     * @return array|false Array of embedding vectors or false on failure
     */
    public function batch_generate_embeddings($texts, $model = 'text-embedding-3-small') {
        if (empty($texts) || !is_array($texts)) {
            return false;
        }

        // Limit batch size
        if (count($texts) > 100) {
            error_log('Aethos Embeddings: Batch size exceeds 100, splitting into multiple requests');
            return $this->batch_generate_large($texts, $model);
        }

        try {
            // Call Aethos SaaS backend
            $endpoint = $this->api_client->get_api_endpoint() . '/api/embeddings';
            $response = $this->api_client->make_request($endpoint, array(
                'texts' => $texts,
                'model' => $model
            ));

            if (is_wp_error($response)) {
                $error_data = $response->get_error_data();
                $status = isset($error_data['status']) ? $error_data['status'] : 'unknown';
                throw new Exception('API request failed (' . $status . '): ' . $response->get_error_message());
            }

            if (!$response || !isset($response['success']) || !$response['success']) {
                $error_msg = isset($response['error']) ? $response['error'] : 'Unknown error';
                throw new Exception('API returned error: ' . $error_msg);
            }

            if (!isset($response['embeddings']) || !is_array($response['embeddings'])) {
                throw new Exception('Invalid response format: embeddings missing');
            }

            // Log usage locally (optional)
            if (isset($response['usage'])) {
                $this->log_usage($response['usage']);
            }

            return $response['embeddings'];

        } catch (Exception $e) {
            error_log('Aethos Embeddings: Exception - ' . $e->getMessage());
            throw $e; // Rethrow to let caller handle
        }
    }

    /**
     * Handle large batch by splitting into chunks
     *
     * @param array  $texts Array of texts
     * @param string $model Embedding model
     * @return array|false Combined embeddings or false on failure
     */
    private function batch_generate_large($texts, $model) {
        $all_embeddings = array();
        $chunks = array_chunk($texts, 100);

        foreach ($chunks as $chunk) {
            $embeddings = $this->batch_generate_embeddings($chunk, $model);
            
            if ($embeddings === false) {
                error_log('Aethos Embeddings: Failed to process chunk');
                return false;
            }

            $all_embeddings = array_merge($all_embeddings, $embeddings);
        }

        return $all_embeddings;
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

        // Check if it's a numeric array
        foreach ($embedding as $value) {
            if (!is_numeric($value)) {
                return false;
            }
        }

        // Check dimension (1536 for text-embedding-3-small, 3072 for large)
        $count = count($embedding);
        if ($count !== 1536 && $count !== 3072) {
            return false;
        }

        return true;
    }

    /**
     * Log embedding usage locally
     *
     * @param array $usage Usage data from API
     */
    private function log_usage($usage) {
        if (!isset($usage['total_tokens'])) {
            return;
        }

        $current_usage = get_option('aethos_embedding_usage', array(
            'total_tokens' => 0,
            'total_requests' => 0,
            'total_cost' => 0
        ));

        $current_usage['total_tokens'] += $usage['total_tokens'];
        $current_usage['total_requests'] += 1;
        
        if (isset($usage['cost'])) {
            $current_usage['total_cost'] += $usage['cost'];
        }

        update_option('aethos_embedding_usage', $current_usage);
    }

    /**
     * Get local usage statistics
     *
     * @return array Usage statistics
     */
    public function get_usage_stats() {
        return get_option('aethos_embedding_usage', array(
            'total_tokens' => 0,
            'total_requests' => 0,
            'total_cost' => 0
        ));
    }

    /**
     * Reset local usage statistics
     */
    public function reset_usage_stats() {
        delete_option('aethos_embedding_usage');
    }
}
