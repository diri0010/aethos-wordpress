<?php

/**
 * Q&A Management AJAX Handlers
 *
 * @since      1.2.0
 * @package    Aethos_Chat
 * @subpackage Aethos_Chat/includes
 */

class Aethos_QnA_Ajax {

    private $qna;

    public function __construct() {
        require_once plugin_dir_path( __FILE__ ) . 'class-aethos-qna.php';
        $this->qna = new Aethos_QnA();
        
        $this->register_ajax_handlers();
    }

    /**
     * Register AJAX handlers
     */
    private function register_ajax_handlers() {
        add_action( 'wp_ajax_aethos_get_ai_suggestions', array( $this, 'get_ai_suggestions' ) );
        add_action( 'wp_ajax_aethos_accept_suggestion', array( $this, 'accept_suggestion' ) );
        add_action( 'wp_ajax_aethos_get_qna_list', array( $this, 'get_qna_list' ) );
        add_action( 'wp_ajax_aethos_add_qna', array( $this, 'add_qna' ) );
        add_action( 'wp_ajax_aethos_update_qna', array( $this, 'update_qna' ) );
        add_action( 'wp_ajax_aethos_delete_qna', array( $this, 'delete_qna' ) );
        add_action( 'wp_ajax_aethos_bulk_delete_qna', array( $this, 'bulk_delete_qna' ) );
        add_action( 'wp_ajax_aethos_sync_content', array( $this, 'sync_content' ) );
        add_action( 'wp_ajax_aethos_get_qna_categories', array( $this, 'get_categories' ) );
    }

    /**
     * Get AI-generated suggestions
     */
    public function get_ai_suggestions() {
        check_ajax_referer( 'aethos_admin_nonce', 'nonce' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => 'Unauthorized' ), 403 );
        }

        $suggestions = $this->qna->get_qna_list( array(
            'is_ai_generated' => 1,
            'is_accepted' => 0,
            'limit' => 50
        ) );

        wp_send_json_success( array( 'suggestions' => $suggestions ) );
    }

    /**
     * Accept an AI suggestion
     */
    public function accept_suggestion() {
        check_ajax_referer( 'aethos_admin_nonce', 'nonce' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => 'Unauthorized' ), 403 );
        }

        $id = isset( $_POST['id'] ) ? absint( $_POST['id'] ) : 0;

        if ( ! $id ) {
            wp_send_json_error( array( 'message' => 'Invalid ID' ) );
        }

        $result = $this->qna->accept_suggestion( $id );

        if ( $result !== false ) {
            wp_send_json_success( array( 'message' => 'Suggestion accepted' ) );
        } else {
            wp_send_json_error( array( 'message' => 'Failed to accept suggestion' ) );
        }
    }

    /**
     * Get Q&A list
     */
    public function get_qna_list() {
        check_ajax_referer( 'aethos_admin_nonce', 'nonce' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => 'Unauthorized' ), 403 );
        }

        $args = array(
            'search' => isset( $_POST['search'] ) ? sanitize_text_field( $_POST['search'] ) : '',
            'category' => isset( $_POST['category'] ) ? sanitize_text_field( $_POST['category'] ) : '',
            'priority' => isset( $_POST['priority'] ) ? sanitize_text_field( $_POST['priority'] ) : '',
            'status' => isset( $_POST['status'] ) ? sanitize_text_field( $_POST['status'] ) : '',
            'is_accepted' => 1, // Only show accepted/manual entries in knowledge base
            'limit' => 100
        );

        $qna_list = $this->qna->get_qna_list( $args );
        $total = $this->qna->get_qna_count( $args );

        wp_send_json_success( array(
            'qna_list' => $qna_list,
            'total' => $total
        ) );
    }

    /**
     * Add new Q&A
     */
    public function add_qna() {
        check_ajax_referer( 'aethos_admin_nonce', 'nonce' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => 'Unauthorized' ), 403 );
        }

        $question = isset( $_POST['question'] ) ? sanitize_textarea_field( $_POST['question'] ) : '';
        $answer = isset( $_POST['answer'] ) ? wp_kses_post( $_POST['answer'] ) : '';
        $category = isset( $_POST['category'] ) ? sanitize_text_field( $_POST['category'] ) : 'General';
        $priority = isset( $_POST['priority'] ) ? sanitize_text_field( $_POST['priority'] ) : 'normal';
        $status = isset( $_POST['status'] ) ? sanitize_text_field( $_POST['status'] ) : 'draft';

        if ( empty( $question ) || empty( $answer ) ) {
            wp_send_json_error( array( 'message' => 'Question and answer are required' ) );
        }

        $id = $this->qna->add_qna( array(
            'question' => $question,
            'answer' => $answer,
            'category' => $category,
            'priority' => $priority,
            'status' => $status,
            'is_ai_generated' => 0,
            'is_accepted' => 1
        ) );

        if ( $id ) {
            wp_send_json_success( array(
                'message' => 'Q&A added successfully',
                'id' => $id
            ) );
        } else {
            wp_send_json_error( array( 'message' => 'Failed to add Q&A' ) );
        }
    }

    /**
     * Update existing Q&A
     */
    public function update_qna() {
        check_ajax_referer( 'aethos_admin_nonce', 'nonce' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => 'Unauthorized' ), 403 );
        }

        $id = isset( $_POST['id'] ) ? absint( $_POST['id'] ) : 0;

        if ( ! $id ) {
            wp_send_json_error( array( 'message' => 'Invalid ID' ) );
        }

        $data = array();

        if ( isset( $_POST['question'] ) ) {
            $data['question'] = sanitize_textarea_field( $_POST['question'] );
        }

        if ( isset( $_POST['answer'] ) ) {
            $data['answer'] = wp_kses_post( $_POST['answer'] );
        }

        if ( isset( $_POST['category'] ) ) {
            $data['category'] = sanitize_text_field( $_POST['category'] );
        }

        if ( isset( $_POST['priority'] ) ) {
            $data['priority'] = sanitize_text_field( $_POST['priority'] );
        }

        if ( isset( $_POST['status'] ) ) {
            $data['status'] = sanitize_text_field( $_POST['status'] );
        }

        $result = $this->qna->update_qna( $id, $data );

        if ( $result !== false ) {
            wp_send_json_success( array( 'message' => 'Q&A updated successfully' ) );
        } else {
            wp_send_json_error( array( 'message' => 'Failed to update Q&A' ) );
        }
    }

    /**
     * Delete Q&A
     */
    public function delete_qna() {
        check_ajax_referer( 'aethos_admin_nonce', 'nonce' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => 'Unauthorized' ), 403 );
        }

        $id = isset( $_POST['id'] ) ? absint( $_POST['id'] ) : 0;

        if ( ! $id ) {
            wp_send_json_error( array( 'message' => 'Invalid ID' ) );
        }

        $result = $this->qna->delete_qna( $id );

        if ( $result !== false ) {
            wp_send_json_success( array( 'message' => 'Q&A deleted successfully' ) );
        } else {
            wp_send_json_error( array( 'message' => 'Failed to delete Q&A' ) );
        }
    }

    /**
     * Bulk delete Q&A
     */
    public function bulk_delete_qna() {
        check_ajax_referer( 'aethos_admin_nonce', 'nonce' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => 'Unauthorized' ), 403 );
        }

        $ids = isset( $_POST['ids'] ) ? array_map( 'absint', $_POST['ids'] ) : array();

        if ( empty( $ids ) ) {
            wp_send_json_error( array( 'message' => 'No IDs provided' ) );
        }

        $result = $this->qna->bulk_delete_qna( $ids );

        if ( $result !== false ) {
            wp_send_json_success( array(
                'message' => 'Q&A entries deleted successfully',
                'count' => $result
            ) );
        } else {
            wp_send_json_error( array( 'message' => 'Failed to delete Q&A entries' ) );
        }
    }

    /**
     * Sync content and generate AI suggestions
     */
    public function sync_content() {
        check_ajax_referer( 'aethos_admin_nonce', 'nonce' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => 'Unauthorized' ), 403 );
        }

        $suggestions = $this->qna->sync_content();

        wp_send_json_success( array(
            'message' => 'Content synced successfully',
            'count' => count( $suggestions ),
            'suggestions' => $suggestions
        ) );
    }

    /**
     * Get all categories
     */
    public function get_categories() {
        check_ajax_referer( 'aethos_admin_nonce', 'nonce' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => 'Unauthorized' ), 403 );
        }

        $categories = $this->qna->get_categories();

        wp_send_json_success( array( 'categories' => $categories ) );
    }
}

// Initialize AJAX handlers
new Aethos_QnA_Ajax();

