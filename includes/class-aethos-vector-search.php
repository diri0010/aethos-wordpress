<?php
/**
 * Vector Search Implementation
 * 
 * Handles similarity search using existing aethos_vectors table.
 * Uses PHP-based cosine similarity calculation.
 * 
 * @package Aethos
 */

class Aethos_Vector_Search {
    
    /**
     * Vector dimensions for OpenAI text-embedding-3-small
     */
    const DIMENSIONS = 1536;
    
    /**
     * Default similarity threshold
     */
    const DEFAULT_THRESHOLD = 0.5;
    
    /**
     * @var string Table name
     */
    private $table_name;
    
    /**
     * Constructor
     */
    public function __construct() {
        global $wpdb;
        $this->table_name = $wpdb->prefix . 'aethos_vectors';
    }
    
    /**
     * Search for similar vectors
     * 
     * @param array $query_embedding The query embedding vector
     * @param int   $limit           Max results to return
     * @param float $threshold       Minimum similarity score (0-1)
     * @return array Array of results with chunk and score
     */
    public function search($query_embedding, $limit = 5, $threshold = null) {
        $threshold = $threshold ?? self::DEFAULT_THRESHOLD;
        
        if (count($query_embedding) !== self::DIMENSIONS) {
            error_log("Aethos: Invalid query embedding dimension: " . count($query_embedding));
            return [];
        }
        
        return $this->php_vector_search($query_embedding, $limit, $threshold);
    }
    
    /**
     * PHP-based cosine similarity search
     * 
     * @param array $query_embedding
     * @param int   $limit
     * @param float $threshold
     * @return array
     */
    private function php_vector_search($query_embedding, $limit, $threshold) {
        global $wpdb;
        
        // Fetch all vectors
        // Note: For production with >10k vectors, this should be optimized 
        // (e.g., pre-fetch only IDs/Embeddings, or use a real vector DB).
        // Current architecture relies on this limitation for simplicity on shared hosts.
        $results = $wpdb->get_results("SELECT id, chunk_text, embedding, post_id, post_url, post_type FROM {$this->table_name}", ARRAY_A);
        
        if (empty($results)) {
            return [];
        }
        
        // Calculate query magnitude once
        $query_magnitude = sqrt(array_sum(array_map(fn($x) => $x * $x, $query_embedding)));
        
        $scored = [];
        foreach ($results as $row) {
            $stored_vector = json_decode($row['embedding'], true);
            
            if (!$stored_vector || !is_array($stored_vector)) continue;
            
            // Calculate stored magnitude
            $stored_magnitude = sqrt(array_sum(array_map(fn($x) => $x * $x, $stored_vector)));
            
            if ($stored_magnitude <= 0) continue;
            
            // Dot Product
            $dot_product = 0;
            foreach ($query_embedding as $i => $val) {
                if (isset($stored_vector[$i])) {
                    $dot_product += $val * $stored_vector[$i];
                }
            }
            
            $similarity = ($query_magnitude * $stored_magnitude) > 0
                ? $dot_product / ($query_magnitude * $stored_magnitude)
                : 0;
            
            if ($similarity >= $threshold) {
                $scored[] = [
                    'chunk_id' => (int) $row['id'], // Using table ID as chunk ID
                    'content' => $row['chunk_text'],
                    'score' => round($similarity, 4),
                    'source' => [
                        'post_id' => (int) $row['post_id'],
                        'url' => $row['post_url'],
                        'type' => $row['post_type']
                    ]
                ];
            }
        }
        
        // Sort by score descending
        usort($scored, fn($a, $b) => $b['score'] <=> $a['score']);
        
        return array_slice($scored, 0, $limit);
    }
}
