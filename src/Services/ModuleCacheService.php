<?php

namespace ModularAI\Services;

/**
 * Module Cache Service
 * Manages caching of module responses using WordPress transients
 */
class ModuleCacheService
{
    /**
     * Generate cache key for a module request
     *
     * @param int $module_id Module ID
     * @param string $query Query string
     * @param int|null $post_id Optional post ID
     * @param bool $streaming Whether streaming is enabled
     * @return string Cache key
     */
    public function generateCacheKey(int $module_id, string $query, ?int $post_id, bool $streaming): string
    {
        $hash_input = $query . ($post_id ? '_' . $post_id : '') . '_s' . ($streaming ? '1' : '0');
        $hash = md5($hash_input);
        
        return "mai_cache_{$module_id}_{$hash}";
    }

    /**
     * Get cached response
     *
     * @param int $module_id Module ID
     * @param string $query Query string
     * @param int|null $post_id Optional post ID
     * @param bool $streaming Whether streaming is enabled
     * @return array|null Cached data or null if not found
     */
    public function get(int $module_id, string $query, ?int $post_id, bool $streaming): ?array
    {
        $cache_key = $this->generateCacheKey($module_id, $query, $post_id, $streaming);
        $cached = get_transient($cache_key);
        
        if ($cached === false) {
            return null;
        }
        
        return $cached;
    }

    /**
     * Store response in cache
     *
     * @param int $module_id Module ID
     * @param string $query Query string
     * @param int|null $post_id Optional post ID
     * @param array $data Data to cache
     * @param int $ttl Time to live in seconds
     * @param bool $streaming Whether streaming is enabled
     * @return bool True on success, false on failure
     */
    public function set(int $module_id, string $query, ?int $post_id, array $data, int $ttl, bool $streaming): bool
    {
        if ($ttl <= 0) {
            return false;
        }
        
        $cache_key = $this->generateCacheKey($module_id, $query, $post_id, $streaming);
        
        return set_transient($cache_key, $data, $ttl);
    }

    /**
     * Clear all cached responses for a specific module
     *
     * @param int $module_id Module ID
     * @return int Number of cache entries cleared
     */
    public function clear(int $module_id): int
    {
        global $wpdb;
        
        $pattern = "mai_cache_{$module_id}_%";
        
        $deleted = $this->deleteTransientByPattern($pattern);
        
        return (int) ($deleted / 2); // Divide by 2 because each transient has a timeout entry
    }

    /**
     * Clear a specific cached response
     *
     * @param int $module_id Module ID
     * @param string $query Query string
     * @param int|null $post_id Optional post ID
     * @param bool $streaming Whether streaming is enabled
     * @return bool True on success, false on failure
     */
    public function clearSpecific(int $module_id, string $query, ?int $post_id, bool $streaming): bool
    {
        $cache_key = $this->generateCacheKey($module_id, $query, $post_id, $streaming);
        
        return delete_transient($cache_key);
    }

    /**
     * Delete transients by pattern
     *
     * @param string $pattern Pattern to delete
     * @return bool True on success, false on failure
     */
    public function deleteTransientByPattern(string $pattern): bool
    {
        global $wpdb;
        
        // Direct query required to delete transients by pattern (no WP API alternative)
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
        return $wpdb->query(
            $wpdb->prepare(
                "DELETE FROM {$wpdb->options} WHERE option_name LIKE %s OR option_name LIKE %s",
                '_transient_' . $pattern,
                '_transient_timeout_' . $pattern
            )
        );
    }
}

