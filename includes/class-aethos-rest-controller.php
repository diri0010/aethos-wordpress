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
                    'required' => true,
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
    public function get_context($request) {
        $query = $request->get_param('query');

        if (empty($query)) {
            return new WP_REST_Response([
                'error' => 'Query is required'
            ], 400);
        }

        // Perform vector search using existing vector storage class
        require_once plugin_dir_path(__FILE__) . 'class-aethos-vector-storage.php';
        require_once plugin_dir_path(__FILE__) . 'class-aethos-qna.php';

        $vector_storage = new Aethos_Vector_Storage();
        $qna = new Aethos_QnA();

        // Get relevant document chunks (simplified - you may want to implement actual vector similarity)
        $context_chunks = $this->search_vectors($query, $vector_storage);
        
        // Get relevant Q&A entries
        $qna_entries = $this->search_qna($query, $qna);

        // Combine into context string
        $context = '';
        
        if (!empty($context_chunks)) {
            $context .= "Relevant content:\n\n";
            foreach ($context_chunks as $chunk) {
                $context .= $chunk['content'] . "\n\n";
            }
        }

        if (!empty($qna_entries)) {
            $context .= "Q&A:\n\n";
            foreach ($qna_entries as $entry) {
                $context .= "Q: " . $entry['question'] . "\n";
                $context .= "A: " . $entry['answer'] . "\n\n";
            }
        }

        return new WP_REST_Response([
            'context' => trim($context)
        ], 200);
    }

    /**
     * Save conversation to local database
     *
     * @since    1.0.0
     * @param    WP_REST_Request    $request    Request object
     * @return   WP_REST_Response               Response object
     */
    public function save_conversation($request) {
        global $wpdb;

        $conversation_id = sanitize_text_field($request->get_param('conversation_id'));
        $visitor_id = sanitize_text_field($request->get_param('visitor_id'));
        $messages = $request->get_param('messages');
        $metadata = $request->get_param('metadata');

        // Sanitize messages
        $sanitized_messages = [];
        foreach ($messages as $message) {
            $sanitized_messages[] = [
                'role' => sanitize_text_field($message['role']),
                'content' => sanitize_textarea_field($message['content']),
                'timestamp' => isset($message['timestamp']) ? intval($message['timestamp']) : time()
            ];
        }

        // Use existing conversation history class
        require_once plugin_dir_path(__FILE__) . 'class-aethos-conversation-history.php';
        $conversation_history = new Aethos_Conversation_History();

        // Save or update conversation
        $table_name = $wpdb->prefix . 'aethos_conversations';
        
        $existing = $wpdb->get_row($wpdb->prepare(
            "SELECT id FROM $table_name WHERE conversation_id = %s",
            $conversation_id
        ));

        if ($existing) {
            // Update existing conversation
            $wpdb->update(
                $table_name,
                [
                    'messages' => wp_json_encode($sanitized_messages),
                    'metadata' => wp_json_encode($metadata),
                    'updated_at' => current_time('mysql')
                ],
                ['conversation_id' => $conversation_id],
                ['%s', '%s', '%s'],
                ['%s']
            );
        } else {
            // Insert new conversation
            $wpdb->insert(
                $table_name,
                [
                    'conversation_id' => $conversation_id,
                    'visitor_id' => $visitor_id,
                    'messages' => wp_json_encode($sanitized_messages),
                    'metadata' => wp_json_encode($metadata),
                    'created_at' => current_time('mysql'),
                    'updated_at' => current_time('mysql')
                ],
                ['%s', '%s', '%s', '%s', '%s', '%s']
            );
        }

        return new WP_REST_Response(['saved' => true], 200);
    }

    /**
     * Verify request origin
     *
     * @since    1.0.0
     * @param    WP_REST_Request    $request    Request object
     * @return   bool                           True if origin is valid
     */
    public function verify_origin($request) {
        $origin = $request->get_header('origin');
        
        if (empty($origin)) {
            return true; // Allow requests without origin (same-origin)
        }

        $site_url = home_url();
        $site_domain = parse_url($site_url, PHP_URL_HOST);
        $origin_domain = parse_url($origin, PHP_URL_HOST);

        return $origin_domain === $site_domain;
    }

    /**
     * Search vectors for relevant content
     *
     * @since    1.0.0
     * @param    string                    $query            Search query
     * @param    Aethos_Vector_Storage     $vector_storage   Vector storage instance
     * @return   array                                       Relevant chunks
     */
    private function search_vectors($query, $vector_storage) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'aethos_vectors';

        // Simple keyword search (in production, use actual vector similarity)
        $results = $wpdb->get_results($wpdb->prepare(
            "SELECT chunk_text as content FROM $table_name 
             WHERE chunk_text LIKE %s 
             LIMIT 5",
            '%' . $wpdb->esc_like($query) . '%'
        ), ARRAY_A);

        return $results ?: [];
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

        // Search in questions and answers
        $results = $wpdb->get_results($wpdb->prepare(
            "SELECT question, answer FROM $table_name 
             WHERE (question LIKE %s OR answer LIKE %s)
             AND status = 'active'
             LIMIT 3",
            '%' . $wpdb->esc_like($query) . '%',
            '%' . $wpdb->esc_like($query) . '%'
        ), ARRAY_A);

        return $results ?: [];
    }
}
