<?php

/**
 * Security Layer for Aethos Chat
 *
 * Handles security operations including nonce verification, sanitization, and rate limiting
 *
 * @since      1.0.0
 * @package    Aethos_Chat
 * @subpackage Aethos_Chat/includes
 */

class Aethos_Security {

    /**
     * Verify nonce for AJAX requests
     *
     * @param string $nonce Nonce to verify
     * @param string $action Action name
     * @return bool True if valid, false otherwise
     */
    public static function verify_nonce( $nonce, $action = 'aethos_chat_nonce' ) {
        return wp_verify_nonce( $nonce, $action );
    }

    /**
     * Sanitize chat message
     *
     * @param string $message Message to sanitize
     * @return string Sanitized message
     */
    public static function sanitize_message( $message ) {
        // Remove HTML tags
        $message = wp_strip_all_tags( $message );
        
        // Sanitize text
        $message = sanitize_textarea_field( $message );
        
        // Limit length
        $max_length = 2000;
        if ( strlen( $message ) > $max_length ) {
            $message = substr( $message, 0, $max_length );
        }

        return $message;
    }

    /**
     * Escape output for safe display
     *
     * @param string $text Text to escape
     * @return string Escaped text
     */
    public static function escape_output( $text ) {
        return esc_html( $text );
    }

    /**
     * Generate secure random token
     *
     * @param int $length Token length
     * @return string Random token
     */
    public static function generate_token( $length = 32 ) {
        return bin2hex( random_bytes( $length / 2 ) );
    }

    /**
     * Check if request is from valid origin
     *
     * @return bool True if valid, false otherwise
     */
    public static function check_origin() {
        if ( ! isset( $_SERVER['HTTP_REFERER'] ) ) {
            return false;
        }

        $referer = wp_parse_url( $_SERVER['HTTP_REFERER'] );
        $site_url = wp_parse_url( home_url() );

        return $referer['host'] === $site_url['host'];
    }

    /**
     * Sanitize API key
     *
     * @param string $api_key API key to sanitize
     * @return string Sanitized API key
     */
    public static function sanitize_api_key( $api_key ) {
        // Remove whitespace
        $api_key = trim( $api_key );
        
        // Only allow alphanumeric and dashes
        $api_key = preg_replace( '/[^a-zA-Z0-9\-_]/', '', $api_key );

        return $api_key;
    }

    /**
     * Encrypt sensitive data
     *
     * @param string $data Data to encrypt
     * @return string Encrypted data
     */
    public static function encrypt( $data ) {
        if ( ! function_exists( 'openssl_encrypt' ) ) {
            return base64_encode( $data );
        }

        $key = self::get_encryption_key();
        $iv = openssl_random_pseudo_bytes( 16 );
        
        $encrypted = openssl_encrypt( $data, 'AES-256-CBC', $key, 0, $iv );
        
        return base64_encode( $iv . $encrypted );
    }

    /**
     * Decrypt sensitive data
     *
     * @param string $data Encrypted data
     * @return string Decrypted data
     */
    public static function decrypt( $data ) {
        if ( ! function_exists( 'openssl_decrypt' ) ) {
            return base64_decode( $data );
        }

        $key = self::get_encryption_key();
        $data = base64_decode( $data );
        
        $iv = substr( $data, 0, 16 );
        $encrypted = substr( $data, 16 );
        
        return openssl_decrypt( $encrypted, 'AES-256-CBC', $key, 0, $iv );
    }

    /**
     * Get encryption key
     *
     * @return string Encryption key
     */
    private static function get_encryption_key() {
        if ( defined( 'AETHOS_ENCRYPTION_KEY' ) ) {
            return AETHOS_ENCRYPTION_KEY;
        }

        // Use WordPress auth key as fallback
        if ( defined( 'AUTH_KEY' ) ) {
            return AUTH_KEY;
        }

        // Last resort - generate and store
        $key = get_option( 'aethos_encryption_key' );
        if ( ! $key ) {
            $key = self::generate_token( 64 );
            update_option( 'aethos_encryption_key', $key );
        }

        return $key;
    }

    /**
     * Validate message content for potential threats
     *
     * @param string $message Message to validate
     * @return bool|WP_Error True if safe, WP_Error if threat detected
     */
    public static function validate_message( $message ) {
        // Check for SQL injection patterns
        $sql_patterns = array( 'SELECT', 'INSERT', 'UPDATE', 'DELETE', 'DROP', 'UNION' );
        foreach ( $sql_patterns as $pattern ) {
            if ( stripos( $message, $pattern ) !== false ) {
                return new WP_Error( 'security_threat', 'Potential SQL injection detected' );
            }
        }

        // Check for XSS patterns
        if ( preg_match( '/<script|javascript:|onerror=|onload=/i', $message ) ) {
            return new WP_Error( 'security_threat', 'Potential XSS detected' );
        }

        return true;
    }
}
