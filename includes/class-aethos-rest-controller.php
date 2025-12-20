<?php

/**
 * REST API Controller for Widget Endpoints
 *
 * @since      1.0.0
 * @package    Aethos_Chat
 * @subpackage Aethos_Chat/includes
 */

class Aethos_REST_Controller {

    /**
     * Register REST API routes
     *
     * @since    1.0.0
     */
    public function register_routes() {
        
        // Config endpoint - Returns widget settings
        register_rest_route('aethos/v1', '/config', [
            'methods'  => 'GET',
            'callback' => [$this, 'get_config'],
            'permission_callback' => '__return_true' // Public endpoint
        ]);

        // Context endpoint - Performs local RAG vector search
        register_rest_route('aethos/v1', '/context', [
            'methods'  => 'POST',
            'callback' => [$this, 'get_context'],
            'permission_callback' => [$this, 'verify_origin'],
            'args' => [
                'query' => [
                    'required' => false,
                    'type' => 'string',
                    'sanitize_callback' => 'sanitize_text_field'
                ],
                'message' => [
                    'required' => false,
                    'type' => 'string',
                    'sanitize_callback' => 'sanitize_text_field'
                ]
            ]
        ]);

        // Conversations endpoint - Saves chat history locally
        register_rest_route('aethos/v1', '/conversations', [
            'methods'  => 'POST',
            'callback' => [$this, 'save_conversation'],
            'permission_callback' => [$this, 'verify_origin'],
            'args' => [
                'conversation_id' => [
                    'required' => true,
                    'type' => 'string',
                    'sanitize_callback' => 'sanitize_text_field'
                ],
                'visitor_id' => [
                    'required' => true,
                    'type' => 'string',
                    'sanitize_callback' => 'sanitize_text_field'
                ],
                'messages' => [
                    'required' => true,
                    'type' => 'array'
                ]
            ]
        ]);
    }

    /**
     * Verify request origin
     *
     * @param WP_REST_Request $request
     * @return bool
     */
    public function verify_origin($request) {
        return true;
    }

    /**
     * Get widget configuration
     *
     * @since    1.0.0
     * @param    WP_REST_Request    $request    Request object
     * @return   WP_REST_Response               Response object
     */
    public function get_config($request) {
        $config = [
            'theme'           => get_option('aethos_theme', 'light'),
            'position'        => get_option('aethos_widget_position', 'bottom-right'),
            'primary_color'   => get_option('aethos_primary_color', '#0052CC'),
            'welcome_message' => get_option('aethos_greeting_message', 'Hello! How can I help you today?'),
            'avatar_url'      => get_option('aethos_chat_icon', ''),
            'placeholder'     => get_option('aethos_placeholder_text', 'Type your message...'),
            'button_text'     => get_option('aethos_button_text', 'Send'),
            'header_title'    => get_option('aethos_header_title', 'Aethos AI Assistant'),
            'header_subtitle' => get_option('aethos_chat_subtitle', 'Typically replies instantly'),
            'auto_open'       => (bool) get_option('aethos_auto_open', false),
            'auto_open_delay' => (int) get_option('aethos_auto_open_delay', 3)
        ];

        return new WP_REST_Response($config, 200);
    }

    /**
     * Get RAG context from local vector search
     *
     * @since    1.0.0
     * @param    WP_REST_Request    $request    Request object
     * @return   WP_REST_Response               Response object
     */
    /**
     * Get RAG context from local vector search
     *
     * @since    1.0.0
     * @param    WP_REST_Request    $request    Request object
     * @return   WP_REST_Response               Response object
     */
    public function get_context($request) {
        $message = $request->get_param('query') ?: $request->get_param('message');

        if (empty($message)) {
            return new WP_REST_Response([
                'error' => 'query_required',
                'message' => 'Query parameter is required'
            ], 400);
        }

        // Initialize search services
        require_once plugin_dir_path(__FILE__) . 'class-aethos-embeddings.php'; // Load Embeddings Service
        require_once plugin_dir_path(__FILE__) . 'class-aethos-vector-search.php';
        require_once plugin_dir_path(__FILE__) . 'class-aethos-qna.php';

        $embeddings_service = new Aethos_Embeddings();
        $vector_search = new Aethos_Vector_Search();
        $qna = new Aethos_QnA();

        $chunks = [];
        $search_method = 'keyword';
        $query_cached = false;

        try {
            // 1. Get embedding from SaaS Proxy via Service
            $query_embedding = $embeddings_service->generate_embedding($message);

            if ($query_embedding && is_array($query_embedding)) {
                // 2. Perform Vector Search
                $chunks = $vector_search->search($query_embedding, 5, 0.4); // Threshold 0.4
                if (!empty($chunks)) {
                    $search_method = 'vector';
                }
            } else {
                error_log('Aethos: Embedding generation failed, falling back to keyword search');
            }
        } catch (Exception $e) {
            error_log('Aethos: Vector search error: ' . $e->getMessage());
        }

        // Fallback or Augment with Keyword Search if no vector results
        if (empty($chunks)) {
            $chunks = $this->search_vectors_keyword($message);
            $search_method = empty($chunks) ? 'none' : 'keyword';
        }

        // Get Q&A (can also be enhanced with vectors later)
        $qna_entries = $this->search_qna($message, $qna);

        // Format Context
        $formatted_context = $this->format_context_string($chunks, $qna_entries);

        return new WP_REST_Response([
            'context' => $formatted_context,
            'chunks' => $chunks,
            'qna' => $qna_entries,
            'search_method' => $search_method,
            'query_cached' => $query_cached
        ], 200);
    }

    /**
     * Format context string
     */
    private function format_context_string($chunks, $qna_entries) {
        $context = '';
        
        if (!empty($chunks)) {
            $context .= "RELEVANT CONTENT:\n\n";
            foreach ($chunks as $chunk) {
                // Determine title/url from source if available
                $title = $chunk['source']['title'] ?? 'Document';
                $url = $chunk['source']['url'] ?? '';
                $context .= "Source: $title ($url)\n{$chunk['content']}\n\n";
            }
            $context .= "---\n\n";
        }

        if (!empty($qna_entries)) {
            $context .= "RELEVANT Q&A:\n\n";
            foreach ($qna_entries as $entry) {
                $context .= "Q: {$entry['question']}\nA: {$entry['answer']}\n\n";
            }
        }

        return trim($context);
    }

    /**
     * Search vectors via keyword (Fallback)
     */
    private function search_vectors_keyword($query) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'aethos_vectors';

        $results = $wpdb->get_results($wpdb->prepare(
            "SELECT id, chunk_text as content, post_id, post_url, post_type FROM $table_name 
             WHERE chunk_text LIKE %s 
             LIMIT 3",
            '%' . $wpdb->esc_like($query) . '%'
        ), ARRAY_A);

        if (empty($results)) return [];

        // Format to match vector result structure
        return array_map(function($row) {
            return [
                'chunk_id' => (int) $row['id'],
                'content' => $row['content'],
                'score' => 0,
                'source' => [
                    'post_id' => (int) $row['post_id'],
                    'url' => $row['post_url'],
                    'type' => $row['post_type']
                ]
            ];
        }, $results);
    }

    /**
     * Search Q&A for relevant entries
     *
     * @since    1.0.0
     * @param    string         $query    Search query
     * @param    Aethos_QnA     $qna      Q&A instance
     * @return   array                    Relevant Q&A entries
     */
    private function search_qna($query, $qna) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'aethos_qna';

        // Search in questions and answered
        $results = $wpdb->get_results($wpdb->prepare(
            "SELECT question, answer FROM $table_name 
             WHERE (question LIKE %s OR answer LIKE %s)
             AND status = 'published'
             LIMIT 3",
            '%' . $wpdb->esc_like($query) . '%',
            '%' . $wpdb->esc_like($query) . '%'
        ), ARRAY_A);

        return $results ?: [];
    }
}
