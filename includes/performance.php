<?php
/**
 * Performance Optimizations
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Performance Optimizations
 */
function lofygame_optimize_wp() {
    // Remove unnecessary WordPress features
    remove_action('wp_head', 'wp_generator');
    remove_action('wp_head', 'wlwmanifest_link');
    remove_action('wp_head', 'rsd_link');
    remove_action('wp_head', 'wp_shortlink_wp_head');
    
    // Disable emoji scripts
    remove_action('wp_head', 'print_emoji_detection_script', 7);
    remove_action('wp_print_styles', 'print_emoji_styles');
    
    // Remove query strings from static resources
    add_filter('script_loader_src', 'lofygame_remove_script_version', 15, 1);
    add_filter('style_loader_src', 'lofygame_remove_script_version', 15, 1);
}
add_action('init', 'lofygame_optimize_wp');

function lofygame_remove_script_version($src) {
    $parts = explode('?ver', $src);
    return $parts[0];
}

/**
 * Add Critical CSS Inline
 */
function lofygame_add_critical_css() {
    if (is_singular('game') || is_post_type_archive('game') || is_page_template('blog.php')) {
        echo '<style id="critical-css">';
        echo 'body{margin:0;padding:0;font-family:Inter,sans-serif}';
        echo '.site-header{background:linear-gradient(135deg,#4f46e5,#06b6d4);padding:1rem 0}';
        echo '.game-card{background:#f8fafc;border-radius:12px;transition:transform 0.3s ease}';
        echo '.games-grid-12-col{display:grid;grid-template-columns:repeat(12,1fr);gap:0.75rem}';
        echo '</style>';
    }
}
add_action('wp_head', 'lofygame_add_critical_css', 1);

/**
 * Clean up old ratings periodically
 */
function lofygame_cleanup_old_ratings() {
    // Clean up user ratings older than 30 days
    $user_ratings = get_option('lofygame_user_ratings', array());
    $thirty_days_ago = time() - (30 * 24 * 60 * 60);
    $cleaned = 0;
    
    foreach ($user_ratings as $key => $rating_data) {
        if (isset($rating_data['timestamp']) && $rating_data['timestamp'] < $thirty_days_ago) {
            unset($user_ratings[$key]);
            $cleaned++;
        }
    }
    
    if ($cleaned > 0) {
        update_option('lofygame_user_ratings', $user_ratings);
        error_log("LofyGame: Cleaned up {$cleaned} old rating records");
    }
}

// Schedule cleanup to run daily
if (!wp_next_scheduled('lofygame_cleanup_ratings')) {
    wp_schedule_event(time(), 'daily', 'lofygame_cleanup_ratings');
}
add_action('lofygame_cleanup_ratings', 'lofygame_cleanup_old_ratings');

/**
 * Optimize database queries for games
 */
function lofygame_optimize_game_queries($query) {
    if (!is_admin() && $query->is_main_query()) {
        if (is_post_type_archive('game') || is_category() || is_tag()) {
            // Only select necessary fields for listings
            $query->set('fields', 'ids');
            $query->set('no_found_rows', false); // We need found_rows for pagination
        }
    }
}
// Uncomment if you want to optimize queries (may require template adjustments)
// add_action('pre_get_posts', 'lofygame_optimize_game_queries');

/**
 * Preload critical resources
 */
function lofygame_preload_resources() {
    if (is_singular('game') || is_post_type_archive('game')) {
        echo '<link rel="preload" href="' . get_template_directory_uri() . '/js/theme.js" as="script">';
        echo '<link rel="preconnect" href="https://fonts.googleapis.com">';
        echo '<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>';
    }
}
add_action('wp_head', 'lofygame_preload_resources', 1);

/**
 * Defer non-critical JavaScript
 */
function lofygame_defer_scripts($tag, $handle, $src) {
    $defer_scripts = array('lofygame-script');
    
    if (in_array($handle, $defer_scripts)) {
        return str_replace('<script ', '<script defer ', $tag);
    }
    
    return $tag;
}
add_filter('script_loader_tag', 'lofygame_defer_scripts', 10, 3);

/**
 * Optimize images for games
 */
function lofygame_optimize_images() {
    // Add lazy loading to images
    add_filter('wp_lazy_loading_enabled', '__return_true');
    
    // Set default image quality
    add_filter('jpeg_quality', function() {
        return 85;
    });
}
add_action('init', 'lofygame_optimize_images');

/**
 * Cache game ratings for better performance
 */
function lofygame_cache_game_ratings() {
    $cache_key = 'lofygame_rating_stats';
    $cached_stats = wp_cache_get($cache_key);
    
    if (false === $cached_stats) {
        global $wpdb;
        
        $stats = $wpdb->get_row($wpdb->prepare("
            SELECT 
                COUNT(*) as total_games,
                AVG(CAST(pm1.meta_value AS DECIMAL(3,1))) as avg_rating,
                SUM(CAST(pm2.meta_value AS UNSIGNED)) as total_reviews
            FROM {$wpdb->posts} p
            LEFT JOIN {$wpdb->postmeta} pm1 ON p.ID = pm1.post_id AND pm1.meta_key = '_game_avg_rating'
            LEFT JOIN {$wpdb->postmeta} pm2 ON p.ID = pm2.post_id AND pm2.meta_key = '_game_rating_count'
            WHERE p.post_type = 'game' AND p.post_status = 'publish'
        "));
        
        wp_cache_set($cache_key, $stats, '', 3600); // Cache for 1 hour
        return $stats;
    }
    
    return $cached_stats;
}

/**
 * Invalidate rating cache when games are updated
 */
function lofygame_invalidate_rating_cache($post_id) {
    if (get_post_type($post_id) === 'game') {
        wp_cache_delete('lofygame_rating_stats');
    }
}
add_action('save_post', 'lofygame_invalidate_rating_cache');
add_action('delete_post', 'lofygame_invalidate_rating_cache');
?>