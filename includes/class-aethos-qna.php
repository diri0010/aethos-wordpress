<?php

/**
 * Q&A Management Class
 *
 * Handles CRUD operations for the chatbot's knowledge base Q&A entries
 *
 * @since      1.2.0
 * @package    Aethos_Chat
 * @subpackage Aethos_Chat/includes
 */

class Aethos_QnA {

    /**
     * Get the Q&A table name
     */
    private function get_table_name() {
        global $wpdb;
        return $wpdb->prefix . 'aethos_qna';
    }

    /**
     * Get all Q&A entries with optional filters
     */
    public function get_qna_list( $args = array() ) {
        global $wpdb;
        $table = $this->get_table_name();

        $defaults = array(
            'search' => '',
            'category' => '',
            'priority' => '',
            'status' => '',
            'is_ai_generated' => null,
            'is_accepted' => null,
            'limit' => 100,
            'offset' => 0,
            'orderby' => 'created_at',
            'order' => 'DESC'
        );

        $args = wp_parse_args( $args, $defaults );

        $where = array( '1=1' );
        $where_values = array();

        if ( ! empty( $args['search'] ) ) {
            $where[] = '(question LIKE %s OR answer LIKE %s)';
            $search_term = '%' . $wpdb->esc_like( $args['search'] ) . '%';
            $where_values[] = $search_term;
            $where_values[] = $search_term;
        }

        if ( ! empty( $args['category'] ) ) {
            $where[] = 'category = %s';
            $where_values[] = $args['category'];
        }

        if ( ! empty( $args['priority'] ) ) {
            $where[] = 'priority = %s';
            $where_values[] = $args['priority'];
        }

        if ( ! empty( $args['status'] ) ) {
            $where[] = 'status = %s';
            $where_values[] = $args['status'];
        }

        if ( $args['is_ai_generated'] !== null ) {
            $where[] = 'is_ai_generated = %d';
            $where_values[] = (int) $args['is_ai_generated'];
        }

        if ( $args['is_accepted'] !== null ) {
            $where[] = 'is_accepted = %d';
            $where_values[] = (int) $args['is_accepted'];
        }

        $where_clause = implode( ' AND ', $where );

        $orderby = sanitize_sql_orderby( $args['orderby'] . ' ' . $args['order'] );

        $sql = "SELECT * FROM $table WHERE $where_clause ORDER BY $orderby LIMIT %d OFFSET %d";
        $where_values[] = $args['limit'];
        $where_values[] = $args['offset'];

        if ( ! empty( $where_values ) ) {
            $sql = $wpdb->prepare( $sql, $where_values );
        }

        return $wpdb->get_results( $sql );
    }

    /**
     * Get total count of Q&A entries
     */
    public function get_qna_count( $args = array() ) {
        global $wpdb;
        $table = $this->get_table_name();

        $where = array( '1=1' );
        $where_values = array();

        if ( ! empty( $args['search'] ) ) {
            $where[] = '(question LIKE %s OR answer LIKE %s)';
            $search_term = '%' . $wpdb->esc_like( $args['search'] ) . '%';
            $where_values[] = $search_term;
            $where_values[] = $search_term;
        }

        if ( ! empty( $args['category'] ) ) {
            $where[] = 'category = %s';
            $where_values[] = $args['category'];
        }

        if ( ! empty( $args['status'] ) ) {
            $where[] = 'status = %s';
            $where_values[] = $args['status'];
        }

        if ( isset( $args['is_ai_generated'] ) && $args['is_ai_generated'] !== null ) {
            $where[] = 'is_ai_generated = %d';
            $where_values[] = (int) $args['is_ai_generated'];
        }

        $where_clause = implode( ' AND ', $where );

        $sql = "SELECT COUNT(*) FROM $table WHERE $where_clause";

        if ( ! empty( $where_values ) ) {
            $sql = $wpdb->prepare( $sql, $where_values );
        }

        return (int) $wpdb->get_var( $sql );
    }

    /**
     * Get a single Q&A entry by ID
     */
    public function get_qna( $id ) {
        global $wpdb;
        $table = $this->get_table_name();

        return $wpdb->get_row( $wpdb->prepare(
            "SELECT * FROM $table WHERE id = %d",
            $id
        ) );
    }

    /**
     * Add a new Q&A entry
     */
    public function add_qna( $data ) {
        global $wpdb;
        $table = $this->get_table_name();

        $defaults = array(
            'question' => '',
            'answer' => '',
            'category' => 'General',
            'priority' => 'normal',
            'status' => 'draft',
            'source' => null,
            'is_ai_generated' => 0,
            'is_accepted' => 0
        );

        $data = wp_parse_args( $data, $defaults );

        $result = $wpdb->insert(
            $table,
            array(
                'question' => sanitize_textarea_field( $data['question'] ),
                'answer' => wp_kses_post( $data['answer'] ),
                'category' => sanitize_text_field( $data['category'] ),
                'priority' => sanitize_text_field( $data['priority'] ),
                'status' => sanitize_text_field( $data['status'] ),
                'source' => sanitize_text_field( $data['source'] ),
                'is_ai_generated' => (int) $data['is_ai_generated'],
                'is_accepted' => (int) $data['is_accepted']
            ),
            array( '%s', '%s', '%s', '%s', '%s', '%s', '%d', '%d' )
        );

        if ( $result ) {
            return $wpdb->insert_id;
        }

        return false;
    }

    /**
     * Update an existing Q&A entry
     */
    public function update_qna( $id, $data ) {
        global $wpdb;
        $table = $this->get_table_name();

        $update_data = array();
        $format = array();

        if ( isset( $data['question'] ) ) {
            $update_data['question'] = sanitize_textarea_field( $data['question'] );
            $format[] = '%s';
        }

        if ( isset( $data['answer'] ) ) {
            $update_data['answer'] = wp_kses_post( $data['answer'] );
            $format[] = '%s';
        }

        if ( isset( $data['category'] ) ) {
            $update_data['category'] = sanitize_text_field( $data['category'] );
            $format[] = '%s';
        }

        if ( isset( $data['priority'] ) ) {
            $update_data['priority'] = sanitize_text_field( $data['priority'] );
            $format[] = '%s';
        }

        if ( isset( $data['status'] ) ) {
            $update_data['status'] = sanitize_text_field( $data['status'] );
            $format[] = '%s';
        }

        if ( isset( $data['source'] ) ) {
            $update_data['source'] = sanitize_text_field( $data['source'] );
            $format[] = '%s';
        }

        if ( isset( $data['is_accepted'] ) ) {
            $update_data['is_accepted'] = (int) $data['is_accepted'];
            $format[] = '%d';
        }

        if ( empty( $update_data ) ) {
            return false;
        }

        return $wpdb->update(
            $table,
            $update_data,
            array( 'id' => $id ),
            $format,
            array( '%d' )
        );
    }

    /**
     * Delete a Q&A entry
     */
    public function delete_qna( $id ) {
        global $wpdb;
        $table = $this->get_table_name();

        return $wpdb->delete(
            $table,
            array( 'id' => $id ),
            array( '%d' )
        );
    }

    /**
     * Bulk delete Q&A entries
     */
    public function bulk_delete_qna( $ids ) {
        global $wpdb;
        $table = $this->get_table_name();

        if ( empty( $ids ) || ! is_array( $ids ) ) {
            return false;
        }

        $ids = array_map( 'absint', $ids );
        $placeholders = implode( ',', array_fill( 0, count( $ids ), '%d' ) );

        return $wpdb->query( $wpdb->prepare(
            "DELETE FROM $table WHERE id IN ($placeholders)",
            $ids
        ) );
    }

    /**
     * Get all unique categories
     */
    public function get_categories() {
        global $wpdb;
        $table = $this->get_table_name();

        return $wpdb->get_col( "SELECT DISTINCT category FROM $table WHERE category IS NOT NULL ORDER BY category" );
    }

    /**
     * Accept an AI-generated suggestion
     */
    public function accept_suggestion( $id ) {
        return $this->update_qna( $id, array(
            'is_accepted' => 1,
            'status' => 'published'
        ) );
    }

    /**
     * Generate AI suggestions from WordPress content
     */
    public function generate_ai_suggestions() {
        // Get enabled content sources
        $kb_pages = get_option( 'aethos_kb_pages', true );
        $kb_posts = get_option( 'aethos_kb_posts', true );
        $kb_custom_post_types = get_option( 'aethos_kb_custom_post_types', array() );

        $post_types = array();
        if ( $kb_pages ) $post_types[] = 'page';
        if ( $kb_posts ) $post_types[] = 'post';
        if ( ! empty( $kb_custom_post_types ) ) {
            $post_types = array_merge( $post_types, $kb_custom_post_types );
        }

        if ( empty( $post_types ) ) {
            return array();
        }

        // Get recent posts from enabled content types
        $posts = get_posts( array(
            'post_type' => $post_types,
            'posts_per_page' => 10,
            'post_status' => 'publish',
            'orderby' => 'modified',
            'order' => 'DESC'
        ) );

        $suggestions = array();

        foreach ( $posts as $post ) {
            // Generate sample Q&A based on post title and excerpt
            $question = 'What is ' . get_the_title( $post ) . '?';
            $answer = $post->post_excerpt ? $post->post_excerpt : wp_trim_words( $post->post_content, 50 );
            
            // Check if this suggestion already exists
            $existing = $this->get_qna_list( array(
                'search' => $question,
                'is_ai_generated' => 1,
                'limit' => 1
            ) );

            if ( empty( $existing ) ) {
                $suggestions[] = array(
                    'question' => $question,
                    'answer' => $answer,
                    'source' => get_the_title( $post ),
                    'category' => get_post_type( $post ) === 'page' ? 'General' : ucfirst( get_post_type( $post ) ),
                    'is_ai_generated' => 1,
                    'is_accepted' => 0,
                    'status' => 'draft'
                );
            }
        }

        // Save suggestions to database
        foreach ( $suggestions as $suggestion ) {
            $this->add_qna( $suggestion );
        }

        return $suggestions;
    }

    /**
     * Sync content - regenerate AI suggestions
     */
    public function sync_content() {
        // Delete old unaccepted AI suggestions
        global $wpdb;
        $table = $this->get_table_name();
        
        $wpdb->query( "DELETE FROM $table WHERE is_ai_generated = 1 AND is_accepted = 0" );

        // Generate new suggestions
        return $this->generate_ai_suggestions();
    }
}

