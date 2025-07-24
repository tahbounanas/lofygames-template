<?php
/**
 * Query Modifications and Pagination
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * FIXED: Proper pagination handling for home page and archive pages
 */
function lofygame_fix_pagination($query) {
    if (!is_admin() && $query->is_main_query()) {
        // Home page - show only games with proper pagination
        if (is_home() && !is_front_page()) {
            $query->set('post_type', array('game'));
            $query->set('posts_per_page', 204); // 12 per row × 17 rows
        }
        
        // Front page (if set as homepage)
        if (is_front_page() && !is_page()) {
            $query->set('post_type', array('game'));
            $query->set('posts_per_page', 204);
        }
        
        // Games archive page
        if (is_post_type_archive('game')) {
            $query->set('posts_per_page', 204);
        }
        
        // Category and tag archives for games
        if (is_category() || is_tag()) {
            $query->set('post_type', array('game'));
            $query->set('posts_per_page', 204);
        }
    }
}
add_action('pre_get_posts', 'lofygame_fix_pagination');

/**
 * Enhanced pagination function with modern design
 */
function lofygame_modern_pagination($query = null) {
    global $wp_query;
    
    if (!$query) {
        $query = $wp_query;
    }
    
    $total_pages = $query->max_num_pages;
    $current_page = max(1, get_query_var('paged'));
    
    if ($total_pages <= 1) {
        return;
    }
    
    echo '<div class="modern-pagination">';
    echo '<div class="pagination-info">';
    echo '<span class="page-info">Page ' . $current_page . ' of ' . $total_pages . '</span>';
    echo '<span class="total-games">' . $query->found_posts . ' games total</span>';
    echo '</div>';
    
    echo '<div class="pagination-controls">';
    
    // Previous button
    if ($current_page > 1) {
        echo '<a href="' . get_pagenum_link($current_page - 1) . '" class="pagination-btn prev-btn">';
        echo '<span class="btn-icon">←</span> Previous';
        echo '</a>';
    }
    
    // Page numbers
    echo '<div class="page-numbers-container">';
    
    // First page
    if ($current_page > 3) {
        echo '<a href="' . get_pagenum_link(1) . '" class="page-number">1</a>';
        if ($current_page > 4) {
            echo '<span class="page-dots">…</span>';
        }
    }
    
    // Pages around current
    for ($i = max(1, $current_page - 2); $i <= min($total_pages, $current_page + 2); $i++) {
        if ($i == $current_page) {
            echo '<span class="page-number current">' . $i . '</span>';
        } else {
            echo '<a href="' . get_pagenum_link($i) . '" class="page-number">' . $i . '</a>';
        }
    }
    
    // Last page
    if ($current_page < $total_pages - 2) {
        if ($current_page < $total_pages - 3) {
            echo '<span class="page-dots">…</span>';
        }
        echo '<a href="' . get_pagenum_link($total_pages) . '" class="page-number">' . $total_pages . '</a>';
    }
    
    echo '</div>';
    
    // Next button
    if ($current_page < $total_pages) {
        echo '<a href="' . get_pagenum_link($current_page + 1) . '" class="pagination-btn next-btn">';
        echo 'Next <span class="btn-icon">→</span>';
        echo '</a>';
    }
    
    echo '</div>';
    echo '</div>';
}

/**
 * Ensure pagination works for custom post types
 */
function lofygame_custom_post_type_pagination($query) {
    if (!is_admin() && $query->is_main_query()) {
        // Ensure paged parameter is recognized
        if (isset($query->query_vars['paged']) && $query->query_vars['paged'] > 1) {
            $query->is_paged = true;
        }
    }
}
add_action('parse_query', 'lofygame_custom_post_type_pagination');

/**
 * Add rating data to the game query for frontend
 */
function lofygame_add_rating_data_to_games($query) {
    if (!is_admin() && $query->is_main_query()) {
        if (is_post_type_archive('game') || is_category() || is_tag()) {
            // You can add meta_query here if needed to filter by ratings
        }
    }
}
add_action('pre_get_posts', 'lofygame_add_rating_data_to_games');

/**
 * Get top categories with game counts
 */
function lofygame_get_top_categories($limit = 8) {
    return get_categories(array(
        'taxonomy' => 'category',
        'orderby' => 'count',
        'order' => 'DESC',
        'number' => $limit,
        'hide_empty' => true,
    ));
}

/**
 * Get categories excluding current category
 */
function lofygame_get_other_categories($current_category_id = null, $limit = 8) {
    $args = array(
        'taxonomy' => 'category',
        'orderby' => 'count',
        'order' => 'DESC',
        'number' => $limit + 1, // Get one extra in case we need to exclude current
        'hide_empty' => true,
    );
    
    if ($current_category_id) {
        $args['exclude'] = array($current_category_id);
    }
    
    $categories = get_categories($args);
    
    // If we have the extra one, remove it
    if (count($categories) > $limit) {
        array_pop($categories);
    }
    
    return $categories;
}
?>