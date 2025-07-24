<?php
/**
 * AJAX Handlers
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * AJAX Filter Games with Pagination Support
 */
function lofygame_filter_games() {
    check_ajax_referer('lofygame_nonce', 'nonce');
    
    $category = sanitize_text_field($_POST['category']);
    $tag = sanitize_text_field($_POST['tag']);
    $paged = isset($_POST['paged']) ? intval($_POST['paged']) : 1;
    
    $args = array(
        'post_type' => 'game',
        'posts_per_page' => 204, // 12 per row × 17 rows
        'paged' => $paged,
        'post_status' => 'publish'
    );
    
    if (!empty($category) && $category != 'all') {
        $args['cat'] = $category;
    }
    
    if (!empty($tag) && $tag != 'all') {
        $args['tag_id'] = $tag;
    }
    
    $games = new WP_Query($args);
    
    if ($games->have_posts()) {
        while ($games->have_posts()) {
            $games->the_post();
            get_template_part('template-parts/game-card');
        }
        
        // Add pagination info for AJAX
        if ($games->max_num_pages > 1) {
            echo '<div class="ajax-pagination-info" data-current="' . $paged . '" data-total="' . $games->max_num_pages . '"></div>';
        }
    } else {
        echo '<div class="no-games"><h2>No games found</h2><p>No games found matching your criteria.</p></div>';
    }
    
    wp_reset_postdata();
    wp_die();
}
add_action('wp_ajax_filter_games', 'lofygame_filter_games');
add_action('wp_ajax_nopriv_filter_games', 'lofygame_filter_games');

/**
 * Track Game Views for SEO and Analytics
 */
function lofygame_track_game_view() {
    check_ajax_referer('lofygame_nonce', 'nonce');
    
    $game_id = intval($_POST['game_id']);
    $views = get_post_meta($game_id, 'post_views_count', true);
    $views = $views ? $views + 1 : 1;
    
    update_post_meta($game_id, 'post_views_count', $views);
    
    wp_die();
}
add_action('wp_ajax_track_game_view', 'lofygame_track_game_view');
add_action('wp_ajax_nopriv_track_game_view', 'lofygame_track_game_view');

/**
 * AJAX endpoint to check if user has rated
 */
function lofygame_check_user_rating() {
    check_ajax_referer('lofygame_nonce', 'nonce');
    
    $game_id = isset($_POST['game_id']) ? intval($_POST['game_id']) : 0;
    
    if (!$game_id) {
        wp_send_json_error('Invalid game ID');
        return;
    }
    
    $user_rating = lofygame_get_user_game_rating($game_id);
    
    if ($user_rating) {
        wp_send_json_success(array(
            'has_rated' => true,
            'rating' => $user_rating['rating'],
            'timestamp' => $user_rating['timestamp']
        ));
    } else {
        wp_send_json_success(array(
            'has_rated' => false
        ));
    }
}
add_action('wp_ajax_check_user_rating', 'lofygame_check_user_rating');
add_action('wp_ajax_nopriv_check_user_rating', 'lofygame_check_user_rating');

/**
 * Handle quick edit rating AJAX
 */
function lofygame_handle_quick_edit_rating() {
    check_ajax_referer('lofygame_quick_edit', 'nonce');
    
    if (!current_user_can('edit_posts')) {
        wp_send_json_error('Insufficient permissions');
        return;
    }
    
    $post_id = intval($_POST['post_id']);
    $rating = floatval($_POST['rating']);
    $count = intval($_POST['count']);
    
    if (!$post_id || $rating < 1 || $rating > 5 || $count < 1) {
        wp_send_json_error('Invalid data');
        return;
    }
    
    update_post_meta($post_id, '_game_avg_rating', $rating);
    update_post_meta($post_id, '_game_rating_count', $count);
    
    wp_send_json_success('Rating updated successfully');
}
add_action('wp_ajax_lofygame_quick_edit_rating', 'lofygame_handle_quick_edit_rating');
?>