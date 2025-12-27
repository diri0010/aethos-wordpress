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
                ],
                'duration' => [
                    'required' => false,
                    'type' => 'integer',
                    'default' => 0
                ]
            ]
        ]);

        // Feedback endpoint - Handles upvote/downvote/copy from widget
        register_rest_route('aethos/v1', '/feedback', [
            'methods'  => 'POST',
            'callback' => [$this, 'handle_feedback'],
            'permission_callback' => [$this, 'verify_origin'],
            'args' => [
                'action' => [
                    'required' => true,
                    'type' => 'string',
                    'enum' => ['upvote', 'downvote', 'copy'],
                    'sanitize_callback' => 'sanitize_text_field'
                ],
                'visitor_id' => [
                    'required' => true,
                    'type' => 'string',
                    'sanitize_callback' => 'sanitize_text_field'
                ],
                'conversation_id' => [
                    'required' => false,
                    'type' => 'string',
                    'sanitize_callback' => 'sanitize_text_field'
                ],
                'message_id' => [
                    'required' => false,
                    'type' => 'string',
                    'sanitize_callback' => 'sanitize_text_field'
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
        $current_page_url = $request->get_param('currentPageUrl'); // NEW: Current page URL

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
        $current_page_context = '';

        // NEW: Get current page context if URL provided
        error_log("Aethos: get_context called with currentPageUrl: " . ($current_page_url ?: '(empty)'));
        
        if (!empty($current_page_url)) {
            $current_page_chunks = $vector_search->get_by_url($current_page_url, 3);
            error_log("Aethos: Found " . count($current_page_chunks) . " chunks for URL: " . $current_page_url);
            
            if (!empty($current_page_chunks)) {
                $current_page_context = "CURRENT PAGE CONTENT (User is viewing this page):\n\n";
                foreach ($current_page_chunks as $chunk) {
                    $current_page_context .= $chunk['chunk_text'] . "\n\n";
                }
                $current_page_context .= "---\n\n";
            }
        }

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

        // Format Context - prepend current page context
        $formatted_context = $current_page_context . $this->format_context_string($chunks, $qna_entries);

        // Build sources array for citation display - include all chunks used for context
        $sources = [];
        $seen_urls = []; // Track unique sources
        
        foreach ($chunks as $chunk) {
            // Include sources with score >= 0.4 (matching search threshold)
            if (isset($chunk['score']) && $chunk['score'] >= 0.4) {
                $post_id = $chunk['source']['post_id'] ?? null;
                $url = $chunk['source']['url'] ?? '';
                
                // Avoid duplicate sources
                if (!empty($url) && in_array($url, $seen_urls)) {
                    continue;
                }
                $seen_urls[] = $url;
                
                $sources[] = [
                    'title' => $post_id ? get_the_title($post_id) : 'Unknown Source',
                    'url' => $url,
                    'score' => $chunk['score'],
                    'type' => $chunk['source']['type'] ?? 'page'
                ];
            }
        }
        
        // NOTE: We no longer automatically add "This Page" as a source.
        // Current page context is still sent to AI, but sources only come from 
        // actual RAG chunks. If the current page content was used, it will appear
        // in the RAG results with its proper title and URL.

        return new WP_REST_Response([
            'context' => $formatted_context,
            'chunks' => $chunks,
            'qna' => $qna_entries,
            'sources' => $sources,
            'search_method' => $search_method,
            'query_cached' => $query_cached,
            'has_current_page_context' => !empty($current_page_context)
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

    /**
     * Save conversation to local database
     *
     * @since    1.0.0
     * @param    WP_REST_Request    $request    Request object
     * @return   WP_REST_Response               Response object
     */
    public function save_conversation($request) {
        $conversation_id = $request->get_param('conversation_id');
        $visitor_id = $request->get_param('visitor_id');
        $messages = $request->get_param('messages');
        $duration = $request->get_param('duration') ?: 0;
        $is_new_conversation = $request->get_param('is_new_conversation');

        if (empty($messages) || !is_array($messages)) {
            return new WP_REST_Response([
                'error' => 'invalid_messages',
                'message' => 'Messages array is required'
            ], 400);
        }
        
        // Debug: Log received messages with sources
        error_log('[Aethos save_conversation] Received ' . count($messages) . ' messages');
        foreach ($messages as $idx => $msg) {
            $has_sources = isset($msg['sources']) && is_array($msg['sources']) ? count($msg['sources']) : 0;
            error_log("[Aethos save_conversation] Msg $idx: role=" . ($msg['role'] ?? 'unknown') . ", sources_count=$has_sources");
        }

        // Check if conversation logging is enabled (default: enabled)
        $log_option = get_option('aethos_log_conversations', 'not_set');
        $log_enabled = ($log_option === 'not_set') ? true : (bool)$log_option;
        if (!$log_enabled) {
            return new WP_REST_Response([
                'success' => true,
                'message' => 'Logging disabled'
            ], 200);
        }

        global $wpdb;
        $table_name = $wpdb->prefix . 'aethos_conversations';

        // Extract first user message for snippet
        $first_message = '';
        foreach ($messages as $msg) {
            if (isset($msg['role']) && $msg['role'] === 'user' && empty($first_message)) {
                $first_message = isset($msg['content']) ? sanitize_text_field($msg['content']) : '';
            }
        }

        // Check if a conversation with this session_id already exists
        // This ensures all messages in the same session are grouped together
        $session_id = sanitize_text_field($conversation_id);
        $existing = $wpdb->get_row($wpdb->prepare(
            "SELECT id FROM $table_name WHERE session_id = %s LIMIT 1",
            $session_id
        ));

        if ($existing) {
            // UPDATE existing session's conversation
            $wpdb->update(
                $table_name,
                [
                    'messages' => maybe_serialize($messages),
                    'message_count' => count($messages),
                    'duration' => intval($duration),
                    'first_message' => $first_message,
                    'updated_at' => current_time('mysql')
                ],
                ['id' => $existing->id],
                ['%s', '%d', '%d', '%s', '%s'],
                ['%d']
            );
            $log_id = $existing->id;
        } else {
            // INSERT new session conversation
            $wpdb->insert(
                $table_name,
                [
                    'user_id' => sanitize_text_field($visitor_id),
                    'session_id' => $session_id,
                    'user_ip' => sanitize_text_field($_SERVER['REMOTE_ADDR'] ?? ''),
                    'messages' => maybe_serialize($messages),
                    'message_count' => count($messages),
                    'duration' => intval($duration),
                    'first_message' => $first_message,
                    'topics' => '',
                    'feedback_score' => null,
                    'created_at' => current_time('mysql'),
                    'updated_at' => current_time('mysql')
                ],
                ['%s', '%s', '%s', '%s', '%d', '%d', '%s', '%s', '%s', '%s', '%s']
            );
            $log_id = $wpdb->insert_id;
        }

        if ($log_id) {
            return new WP_REST_Response([
                'success' => true,
                'conversation_id' => $log_id
            ], 200);
        }

        return new WP_REST_Response([
            'error' => 'save_failed',
            'message' => 'Failed to save conversation'
        ], 500);
    }

    /**
     * Handle feedback from widget (upvote/downvote/copy)
     *
     * @since    1.0.0
     * @param    WP_REST_Request    $request    Request object
     * @return   WP_REST_Response               Response object
     */
    public function handle_feedback($request) {
        $action = $request->get_param('action');
        $conversation_id = $request->get_param('conversation_id');
        $message_id = $request->get_param('message_id');
        $visitor_id = $request->get_param('visitor_id');

        if (empty($action) || empty($visitor_id)) {
            return new WP_REST_Response([
                'error' => 'missing_params',
                'message' => 'Action and visitor_id are required'
            ], 400);
        }

        global $wpdb;
        $table_name = $wpdb->prefix . 'aethos_conversations';

        // Map action to feedback score
        $feedback_score = null;
        switch ($action) {
            case 'upvote':
                $feedback_score = 5;
                break;
            case 'downvote':
                $feedback_score = 1;
                break;
            case 'copy':
                // Just log the event, no score change
                return new WP_REST_Response([
                    'success' => true,
                    'action' => 'copy_logged'
                ], 200);
        }

        if ($feedback_score !== null) {
            // Find conversation by session_id (conversation_id from widget)
            $conversation = $wpdb->get_row($wpdb->prepare(
                "SELECT id, messages FROM $table_name WHERE session_id = %s LIMIT 1",
                sanitize_text_field($conversation_id)
            ));

            if ($conversation) {
                // Update per-message feedback in messages array
                $messages = maybe_unserialize($conversation->messages);
                if (is_array($messages) && isset($messages[intval($message_id)])) {
                    $messages[intval($message_id)]['feedback'] = $action;
                }

                $wpdb->update(
                    $table_name,
                    [
                        'messages' => maybe_serialize($messages),
                        'feedback_score' => $feedback_score,
                        'updated_at' => current_time('mysql')
                    ],
                    ['id' => $conversation->id],
                    ['%s', '%f', '%s'],
                    ['%d']
                );

                return new WP_REST_Response([
                    'success' => true,
                    'feedback_score' => $feedback_score,
                    'message_id' => $message_id
                ], 200);
            }
        }

        // Return 200 with error message - not 404 which confuses browser
        return new WP_REST_Response([
            'success' => false,
            'error' => 'conversation_not_found',
            'message' => 'No conversation found for this visitor'
        ], 200);
    }
}

// Self-register REST routes on rest_api_init
// This is a backup in case the loader pattern doesn't work
add_action('rest_api_init', function() {
    $controller = new Aethos_REST_Controller();
    $controller->register_routes();
});
