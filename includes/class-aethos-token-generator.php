<?php

/**
 * JWT Token Generator for Widget Authentication
 *
 * @since      1.0.0
 * @package    Aethos_Chat
 * @subpackage Aethos_Chat/includes
 */

class Aethos_Token_Generator {

    /**
     * Generate a JWT token for widget initialization
     *
     * @since    1.0.0
     * @return   string    The generated JWT token
     */
    public function generate_init_token() {
        $shared_secret = get_option('aethos_shared_secret');
        $site_id = get_option('aethos_api_key'); // Using API key as site_id
        
        if (empty($shared_secret) || empty($site_id)) {
            return '';
        }

        $domain = $this->get_current_domain();
        $now = time();

        $header = $this->base64url_encode(json_encode([
            'alg' => 'HS256',
            'typ' => 'JWT'
        ]));

        $payload = $this->base64url_encode(json_encode([
            'site_id' => $site_id,
            'domain'  => $domain,
            'iat'     => $now,
            'exp'     => $now + 300, // 5 minutes
            'nonce'   => wp_create_nonce('aethos_init_' . $now)
        ]));

        $signature = $this->base64url_encode(
            hash_hmac('sha256', "$header.$payload", $shared_secret, true)
        );

        return "$header.$payload.$signature";
    }

    /**
     * Get the current domain
     *
     * @since    1.0.0
     * @return   string    The current domain
     */
    private function get_current_domain() {
        $url = home_url();
        $parsed = parse_url($url);
        return isset($parsed['host']) ? $parsed['host'] : 'localhost';
    }

    /**
     * Base64 URL encode
     *
     * @since    1.0.0
     * @param    string    $data    Data to encode
     * @return   string             Encoded data
     */
    private function base64url_encode($data) {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    /**
     * Base64 URL decode
     *
     * @since    1.0.0
     * @param    string    $data    Data to decode
     * @return   string             Decoded data
     */
    private function base64url_decode($data) {
        return base64_decode(strtr($data, '-_', '+/'));
    }

    /**
     * Verify a JWT token (for testing purposes)
     *
     * @since    1.0.0
     * @param    string    $token    Token to verify
     * @return   array|false         Decoded payload or false on failure
     */
    public function verify_token($token) {
        $shared_secret = get_option('aethos_shared_secret');
        
        if (empty($shared_secret)) {
            return false;
        }

        $parts = explode('.', $token);
        if (count($parts) !== 3) {
            return false;
        }

        list($header, $payload, $signature) = $parts;

        // Verify signature
        $expected_signature = $this->base64url_encode(
            hash_hmac('sha256', "$header.$payload", $shared_secret, true)
        );

        if ($signature !== $expected_signature) {
            return false;
        }

        // Decode payload
        $decoded_payload = json_decode($this->base64url_decode($payload), true);

        // Check expiration
        if (isset($decoded_payload['exp']) && $decoded_payload['exp'] < time()) {
            return false;
        }

        return $decoded_payload;
    }
}
