<?php
/**
 * Admin Functions and Customizations
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Add Admin Columns for Games
 */
function lofygame_game_columns($columns) {
    $columns['game_url'] = __('Game URL');
    $columns['rating'] = __('Rating');
    $columns['views'] = __('Views');
    return $columns;
}
add_filter('manage_game_posts_columns', 'lofygame_game_columns');

function lofygame_game_column_content($column, $post_id) {
    switch ($column) {
        case 'game_url':
            $game_url = get_post_meta($post_id, '_game_url', true);
            if ($game_url) {
                echo '<a href="' . esc_url($game_url) . '" target="_blank">View Game</a>';
            } else {
                echo '<span style="color: #dc3232;">No URL Set</span>';
            }
            break;
            
        case 'rating':
            $avg_rating = get_post_meta($post_id, '_game_avg_rating', true);
            $rating_count = get_post_meta($post_id, '_game_rating_count', true);
            $enable_user_ratings = get_post_meta($post_id, '_enable_user_ratings', true);
            
            if (empty($avg_rating)) $avg_rating = 4.5;
            if (empty($rating_count)) $rating_count = 127;
            
            echo '<div style="display: flex; align-items: center; gap: 8px;">';
            
            // Stars
            echo '<span style="color: #ffa500; font-size: 14px;">';
            for ($i = 1; $i <= 5; $i++) {
                echo ($i <= floor($avg_rating)) ? '★' : '☆';
            }
            echo '</span>';
            
            // Rating info
            echo '<div style="font-size: 12px;">';
            echo '<div style="font-weight: bold;">' . $avg_rating . '/5</div>';
            echo '<div style="color: #666;">(' . number_format($rating_count) . ')</div>';
            echo '</div>';
            
            // Status indicators
            if ($enable_user_ratings) {
                echo '<span style="color: #28a745; font-size: 12px;" title="User ratings enabled">👥</span>';
            }
            
            echo '</div>';
            
            // Rich snippets status
            $has_structured_data = !empty($avg_rating) && !empty($rating_count);
            if ($has_structured_data) {
                echo '<div style="font-size: 11px; color: #28a745; margin-top: 2px;">✅ Rich Snippets Ready</div>';
            } else {
                echo '<div style="font-size: 11px; color: #dc3545; margin-top: 2px;">❌ Missing Rating Data</div>';
            }
            break;
            
        case 'views':
            $views = get_post_meta($post_id, 'post_views_count', true);
            echo $views ? number_format($views) : '0';
            break;
    }
}
add_action('manage_game_posts_custom_column', 'lofygame_game_column_content', 10, 2);

/**
 * Make Admin Columns Sortable
 */
function lofygame_sortable_columns($columns) {
    $columns['views'] = 'views';
    $columns['rating'] = 'game_avg_rating';
    return $columns;
}
add_filter('manage_edit-game_sortable_columns', 'lofygame_sortable_columns');

/**
 * Handle Rating Column Sorting
 */
function lofygame_rating_column_orderby($query) {
    if (!is_admin() || !$query->is_main_query()) {
        return;
    }
    
    if ('game_avg_rating' === $query->get('orderby')) {
        $query->set('meta_key', '_game_avg_rating');
        $query->set('orderby', 'meta_value_num');
    }
}
add_action('pre_get_posts', 'lofygame_rating_column_orderby');

/**
 * Custom Post States for Admin
 */
function lofygame_display_post_states($post_states, $post) {
    if ($post->post_type === 'game') {
        $game_url = get_post_meta($post->ID, '_game_url', true);
        if (empty($game_url)) {
            $post_states['no-game-url'] = __('No Game URL');
        }
    }
    return $post_states;
}
add_filter('display_post_states', 'lofygame_display_post_states', 10, 2);

/**
 * Increase Games Per Page in Admin Table
 */
function lofygame_admin_games_per_page($per_page, $post_type) {
    if ($post_type === 'game') {
        return 100; // Change this number to show more/fewer games
    }
    return $per_page;
}
add_filter('edit_posts_per_page', 'lofygame_admin_games_per_page', 10, 2);

/**
 * Set games per page for admin only
 */
function lofygame_set_admin_games_per_page($query) {
    if (is_admin() && $query->is_main_query()) {
        if (isset($_GET['post_type']) && $_GET['post_type'] === 'game') {
            $query->set('posts_per_page', 100); // Show 100 games per page in admin
        }
    }
}
add_action('pre_get_posts', 'lofygame_set_admin_games_per_page');

/**
 * Add Screen Options for Games Per Page
 */
function lofygame_admin_screen_options() {
    $screen = get_current_screen();
    
    if ($screen && $screen->post_type === 'game') {
        add_screen_option('per_page', array(
            'label' => 'Games per page',
            'default' => 100,
            'option' => 'edit_game_per_page'
        ));
    }
}
add_action('load-edit.php', 'lofygame_admin_screen_options');

/**
 * Save Screen Options for Games Per Page
 */
function lofygame_save_screen_options($status, $option, $value) {
    if ($option === 'edit_game_per_page') {
        return $value;
    }
    return $status;
}
add_filter('set-screen-option', 'lofygame_save_screen_options', 10, 3);

/**
 * Add rating validation notices
 */
function lofygame_rating_validation_notices() {
    global $post;
    
    if ($post && $post->post_type === 'game') {
        $avg_rating = get_post_meta($post->ID, '_game_avg_rating', true);
        $rating_count = get_post_meta($post->ID, '_game_rating_count', true);
        
        if (empty($avg_rating) || empty($rating_count)) {
            echo '<div class="notice notice-warning">';
            echo '<p><strong>⚠️ SEO Notice:</strong> This game is missing rating data needed for Google rich snippets. ';
            echo 'Add ratings in the "Game Rating & Reviews" section to improve search visibility.</p>';
            echo '</div>';
        } else {
            echo '<div class="notice notice-success">';
            echo '<p><strong>✅ SEO Ready:</strong> This game has rating data and will display stars in Google search results. ';
            echo '<a href="https://search.google.com/test/rich-results?url=' . urlencode(get_permalink($post->ID)) . '" target="_blank">Test Rich Snippets</a></p>';
            echo '</div>';
        }
    }
}
add_action('edit_form_after_title', 'lofygame_rating_validation_notices');

/**
 * Admin dashboard widget for ratings overview
 */
function lofygame_add_ratings_dashboard_widget() {
    wp_add_dashboard_widget(
        'lofygame_ratings_overview',
        '⭐ Game Ratings Overview',
        'lofygame_ratings_dashboard_widget_content'
    );
}
add_action('wp_dashboard_setup', 'lofygame_add_ratings_dashboard_widget');

function lofygame_ratings_dashboard_widget_content() {
    global $wpdb;
    
    // Get rating statistics
    $total_games = wp_count_posts('game')->publish;
    
    $ratings_query = $wpdb->prepare("
        SELECT 
            AVG(CAST(meta_value AS DECIMAL(3,1))) as avg_rating,
            COUNT(*) as rated_games
        FROM {$wpdb->postmeta} pm
        INNER JOIN {$wpdb->posts} p ON pm.post_id = p.ID
        WHERE pm.meta_key = '_game_avg_rating' 
        AND p.post_type = 'game' 
        AND p.post_status = 'publish'
        AND pm.meta_value != ''
    ");
    
    $ratings_data = $wpdb->get_row($ratings_query);
    
    $total_reviews_query = $wpdb->prepare("
        SELECT SUM(CAST(meta_value AS UNSIGNED)) as total_reviews
        FROM {$wpdb->postmeta} pm
        INNER JOIN {$wpdb->posts} p ON pm.post_id = p.ID
        WHERE pm.meta_key = '_game_rating_count' 
        AND p.post_type = 'game' 
        AND p.post_status = 'publish'
        AND pm.meta_value != ''
    ");
    
    $total_reviews = $wpdb->get_var($total_reviews_query);
    
    // Get top rated games
    $top_games_query = $wpdb->prepare("
        SELECT p.ID, p.post_title, pm1.meta_value as rating, pm2.meta_value as count
        FROM {$wpdb->posts} p
        INNER JOIN {$wpdb->postmeta} pm1 ON p.ID = pm1.post_id AND pm1.meta_key = '_game_avg_rating'
        INNER JOIN {$wpdb->postmeta} pm2 ON p.ID = pm2.post_id AND pm2.meta_key = '_game_rating_count'
        WHERE p.post_type = 'game' 
        AND p.post_status = 'publish'
        AND pm1.meta_value != ''
        ORDER BY CAST(pm1.meta_value AS DECIMAL(3,1)) DESC, CAST(pm2.meta_value AS UNSIGNED) DESC
        LIMIT 5
    ");
    
    $top_games = $wpdb->get_results($top_games_query);
    
    echo '<div class="ratings-dashboard-widget">';
    
    // Statistics
    echo '<div class="ratings-stats" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(120px, 1fr)); gap: 1rem; margin-bottom: 1.5rem;">';
    
    echo '<div class="stat-card" style="text-align: center; padding: 1rem; background: #f8f9fa; border-radius: 8px;">';
    echo '<div style="font-size: 1.5rem; font-weight: bold; color: #0073aa;">' . $total_games . '</div>';
    echo '<div style="font-size: 0.9rem; color: #666;">Total Games</div>';
    echo '</div>';
    
    echo '<div class="stat-card" style="text-align: center; padding: 1rem; background: #f8f9fa; border-radius: 8px;">';
    echo '<div style="font-size: 1.5rem; font-weight: bold; color: #ffa500;">' . ($ratings_data->avg_rating ? round($ratings_data->avg_rating, 1) : '0') . '</div>';
    echo '<div style="font-size: 0.9rem; color: #666;">Avg Rating</div>';
    echo '</div>';
    
    echo '<div class="stat-card" style="text-align: center; padding: 1rem; background: #f8f9fa; border-radius: 8px;">';
    echo '<div style="font-size: 1.5rem; font-weight: bold; color: #28a745;">' . number_format($total_reviews ?: 0) . '</div>';
    echo '<div style="font-size: 0.9rem; color: #666;">Total Reviews</div>';
    echo '</div>';
    
    echo '<div class="stat-card" style="text-align: center; padding: 1rem; background: #f8f9fa; border-radius: 8px;">';
    echo '<div style="font-size: 1.5rem; font-weight: bold; color: #dc3545;">' . ($ratings_data->rated_games ?: 0) . '</div>';
    echo '<div style="font-size: 0.9rem; color: #666;">Rated Games</div>';
    echo '</div>';
    
    echo '</div>';
    
    // Top rated games
    if ($top_games) {
        echo '<h4 style="margin-bottom: 1rem;">🏆 Top Rated Games</h4>';
        echo '<table class="widefat" style="font-size: 0.9rem;">';
        echo '<thead><tr><th>Game</th><th>Rating</th><th>Reviews</th><th>Actions</th></tr></thead>';
        echo '<tbody>';
        
        foreach ($top_games as $game) {
            $stars = str_repeat('★', floor($game->rating)) . str_repeat('☆', 5 - floor($game->rating));
            echo '<tr>';
            echo '<td><strong>' . esc_html($game->post_title) . '</strong></td>';
            echo '<td><span style="color: #ffa500;">' . $stars . '</span> ' . $game->rating . '</td>';
            echo '<td>' . number_format($game->count) . '</td>';
            echo '<td><a href="' . get_edit_post_link($game->ID) . '" class="button button-small">Edit</a></td>';
            echo '</tr>';
        }
        
        echo '</tbody></table>';
    }
    
    // Quick actions
    echo '<div style="margin-top: 1.5rem; text-align: center;">';
    echo '<a href="' . admin_url('edit.php?post_type=game') . '" class="button button-primary">Manage Games</a> ';
    echo '<a href="https://search.google.com/test/rich-results" target="_blank" class="button">Test Rich Snippets</a>';
    echo '</div>';
    
    echo '</div>';
}

/**
 * Add Rich Snippets Testing Helper
 */
function lofygame_add_rich_snippets_tools() {
    if (current_user_can('manage_options')) {
        add_action('admin_bar_menu', function($wp_admin_bar) {
            if (is_singular('game')) {
                $wp_admin_bar->add_node(array(
                    'id' => 'test-rich-snippets',
                    'title' => '🔍 Test Rich Snippets',
                    'href' => 'https://search.google.com/test/rich-results?url=' . urlencode(get_permalink()),
                    'meta' => array('target' => '_blank')
                ));
            }
        }, 100);
    }
}
add_action('init', 'lofygame_add_rich_snippets_tools');

/**
 * Handle quick pagination changes
 */
function lofygame_handle_pagination_change() {
    if (isset($_GET['games_per_page']) && is_admin()) {
        $per_page = intval($_GET['games_per_page']);
        if ($per_page > 0 && $per_page <= 2000) {
            update_user_meta(get_current_user_id(), 'edit_game_per_page', $per_page);
        }
        
        // Redirect to clean URL
        $redirect_url = remove_query_arg('games_per_page');
        wp_redirect($redirect_url);
        exit;
    }
}
add_action('admin_init', 'lofygame_handle_pagination_change');

/**
 * Add admin notice about pagination
 */
function lofygame_admin_pagination_notice() {
    $screen = get_current_screen();
    
    if ($screen && $screen->post_type === 'game' && $screen->base === 'edit') {
        $total_games = wp_count_posts('game')->publish;
        $per_page = get_user_meta(get_current_user_id(), 'edit_game_per_page', true) ?: 20;
        
        if ($total_games > $per_page) {
            echo '<div class="notice notice-info is-dismissible">';
            echo '<p><strong>💡 Tip:</strong> You have ' . $total_games . ' games. ';
            echo 'Showing ' . $per_page . ' per page. ';
            echo 'Use <strong>Screen Options</strong> (top right) to show more games per page.</p>';
            echo '</div>';
        }
    }
}
add_action('admin_notices', 'lofygame_admin_pagination_notice');
?>