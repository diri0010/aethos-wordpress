<?php

/**
 * Caching Layer for Aethos Chat
 *
 * Handles caching of API responses and configuration data
 *
 * @since      1.0.0
 * @package    Aethos_Chat
 * @subpackage Aethos_Chat/includes
 */

class Aethos_Cache {

    /**
     * Cache prefix
     *
     * @var string
     */
    private $prefix = 'aethos_cache_';

    /**
     * Default TTL (Time To Live) in seconds
     *
     * @var int
     */
    private $default_ttl = 300; // 5 minutes

    /**
     * Set a cached value
     *
     * @param string $key Cache key
     * @param mixed  $value Value to cache
     * @param int    $ttl Time to live in seconds (optional)
     * @return bool Success status
     */
    public function set( $key, $value, $ttl = null ) {
        if ( ! get_option( 'aethos_cache_enabled', true ) ) {
            return false;
        }

        $ttl = $ttl ? $ttl : $this->default_ttl;
        $cache_key = $this->prefix . md5( $key );

        return set_transient( $cache_key, $value, $ttl );
    }

    /**
     * Get a cached value
     *
     * @param string $key Cache key
     * @param mixed  $default Default value if not found
     * @return mixed Cached value or default
     */
    public function get( $key, $default = null ) {
        if ( ! get_option( 'aethos_cache_enabled', true ) ) {
            return $default;
        }

        $cache_key = $this->prefix . md5( $key );
        $value = get_transient( $cache_key );

        return $value !== false ? $value : $default;
    }

    /**
     * Delete a cached value
     *
     * @param string $key Cache key
     * @return bool Success status
     */
    public function delete( $key ) {
        $cache_key = $this->prefix . md5( $key );
        return delete_transient( $cache_key );
    }

    /**
     * Clear all Aethos caches
     *
     * @return int Number of cache entries deleted
     */
    public function clear_all() {
        global $wpdb;

        $result = $wpdb->query(
            $wpdb->prepare(
                "DELETE FROM {$wpdb->options} 
                WHERE option_name LIKE %s 
                OR option_name LIKE %s",
                '_transient_' . $this->prefix . '%',
                '_transient_timeout_' . $this->prefix . '%'
            )
        );

        return $result;
    }

    /**
     * Check if a key exists in cache
     *
     * @param string $key Cache key
     * @return bool True if exists, false otherwise
     */
    public function has( $key ) {
        if ( ! get_option( 'aethos_cache_enabled', true ) ) {
            return false;
        }

        $cache_key = $this->prefix . md5( $key );
        return get_transient( $cache_key ) !== false;
    }

    /**
     * Remember a value - get from cache or execute callback and cache result
     *
     * @param string   $key Cache key
     * @param callable $callback Callback to execute if not cached
     * @param int      $ttl Time to live in seconds (optional)
     * @return mixed Cached or computed value
     */
    public function remember( $key, $callback, $ttl = null ) {
        $value = $this->get( $key );

        if ( $value !== null ) {
            return $value;
        }

        $value = call_user_func( $callback );
        $this->set( $key, $value, $ttl );

        return $value;
    }
}
