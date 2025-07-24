<?php
/**
 * Utility Functions and Helpers
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Enhanced Featured Image Function
 */
function lofygame_get_featured_image_or_first($post_id = null) {
    if (!$post_id) {
        $post_id = get_the_ID();
    }
    
    // Check if has featured image
    if (has_post_thumbnail($post_id)) {
        return get_the_post_thumbnail($post_id, 'game-thumbnail');
    }
    
    // Get first image from content
    $post = get_post($post_id);
    $content = $post->post_content;
    
    preg_match('/<img[^>]+src=[\'"]([^\'"]+)[\'"][^>]*>/i', $content, $matches);
    
    if (!empty($matches[1])) {
        return '<img src="' . esc_url($matches[1]) . '" alt="' . esc_attr(get_the_title($post_id)) . '" class="game-thumbnail">';
    }
    
    // Return placeholder
    return '<img src="' . get_template_directory_uri() . '/images/placeholder-game.jpg" alt="' . esc_attr(get_the_title($post_id)) . '" class="game-thumbnail">';
}

/**
 * Add Breadcrumbs
 */
function lofygame_breadcrumbs() {
    if (is_home() || is_front_page()) return '';
    
    $breadcrumbs = '<nav class="breadcrumbs" aria-label="Breadcrumb">';
    $breadcrumbs .= '<a href="' . home_url() . '">Home</a>';
    
    if (is_singular('game')) {
        $breadcrumbs .= ' > <a href="' . get_post_type_archive_link('game') . '">Games</a>';
        $breadcrumbs .= ' > <span>' . get_the_title() . '</span>';
    } elseif (is_post_type_archive('game')) {
        $breadcrumbs .= ' > <span>Games</span>';
    } elseif (is_singular('post')) {
        $breadcrumbs .= ' > <a href="' . home_url('/blog') . '">Blog</a>';
        $breadcrumbs .= ' > <span>' . get_the_title() . '</span>';
    } elseif (is_page_template('blog.php')) {
        $breadcrumbs .= ' > <span>Blog</span>';
    }
    
    $breadcrumbs .= '</nav>';
    
    return $breadcrumbs;
}

/**
 * Get rating statistics for admin
 */
function lofygame_get_rating_statistics() {
    global $wpdb;
    
    $stats = array(
        'total_ratings' => 0,
        'average_rating' => 0,
        'rating_distribution' => array(
            5 => 0,
            4 => 0,
            3 => 0,
            2 => 0,
            1 => 0
        )
    );
    
    $games = get_posts(array(
        'post_type' => 'game',
        'posts_per_page' => -1,
        'meta_key' => '_user_ratings',
        'post_status' => 'publish'
    ));
    
    foreach ($games as $game) {
        $user_ratings = get_post_meta($game->ID, '_user_ratings', true);
        
        if (is_array($user_ratings)) {
            foreach ($user_ratings as $rating_data) {
                $rating = round($rating_data['rating']);
                $stats['total_ratings']++;
                $stats['rating_distribution'][$rating]++;
            }
        }
    }
    
    // Calculate average
    if ($stats['total_ratings'] > 0) {
        $total_score = 0;
        foreach ($stats['rating_distribution'] as $rating => $count) {
            $total_score += $rating * $count;
        }
        $stats['average_rating'] = round($total_score / $stats['total_ratings'], 2);
    }
    
    return $stats;
}

/**
 * Display user rating in single game template
 */
function lofygame_display_user_rating_section($game_id) {
    $enable_ratings = get_post_meta($game_id, '_enable_user_ratings', true);
    
    if (!$enable_ratings) {
        return '';
    }
    
    $user_has_rated = lofygame_user_has_rated_game($game_id);
    $user_rating = lofygame_get_user_game_rating($game_id);
    
    ob_start();
    ?>
    <div class="user-rating-widget" data-game-id="<?php echo esc_attr($game_id); ?>">
        <h3>Rate This Game</h3>
        
        <?php if ($user_has_rated) : ?>
            <div class="already-rated">
                <p>You rated this game <?php echo $user_rating['rating']; ?> stars</p>
                <div class="rating-stars-display">
                    <?php for ($i = 1; $i <= 5; $i++) : ?>
                        <span class="star <?php echo ($i <= $user_rating['rating']) ? 'filled' : 'empty'; ?>">★</span>
                    <?php endfor; ?>
                </div>
            </div>
        <?php else : ?>
            <div class="rating-input-wrapper">
                <p>Click on a star to rate this game:</p>
                <div class="rating-input" id="user-rating-input">
                    <?php for ($i = 1; $i <= 5; $i++) : ?>
                        <span class="rating-star-input" data-rating="<?php echo $i; ?>" tabindex="0" role="button" aria-label="Rate <?php echo $i; ?> stars">★</span>
                    <?php endfor; ?>
                </div>
                <button class="rating-submit" id="submit-rating" disabled>Submit Rating</button>
                <p class="rating-feedback" id="rating-feedback" style="display: none;"></p>
            </div>
        <?php endif; ?>
    </div>
    <?php
    
    return ob_get_clean();
}

/**
 * Shortcode for displaying rating widget anywhere
 */
function lofygame_rating_widget_shortcode($atts) {
    $atts = shortcode_atts(array(
        'game_id' => get_the_ID()
    ), $atts);
    
    return lofygame_display_user_rating_section($atts['game_id']);
}
add_shortcode('game_rating', 'lofygame_rating_widget_shortcode');

/**
 * Format game dimensions for display
 */
function lofygame_format_game_dimensions($width, $height) {
    if (empty($width)) $width = 800;
    if (empty($height)) $height = 600;
    
    return $width . ' × ' . $height . 'px';
}

/**
 * Get game difficulty level based on categories/tags
 */
function lofygame_get_game_difficulty($post_id) {
    $categories = get_the_category($post_id);
    $tags = get_the_tags($post_id);
    
    $all_terms = array();
    
    if ($categories) {
        foreach ($categories as $category) {
            $all_terms[] = strtolower($category->name);
        }
    }
    
    if ($tags) {
        foreach ($tags as $tag) {
            $all_terms[] = strtolower($tag->name);
        }
    }
    
    $difficulty_keywords = array(
        'easy' => array('easy', 'simple', 'casual', 'beginner', 'kids'),
        'medium' => array('medium', 'normal', 'standard', 'intermediate'),
        'hard' => array('hard', 'difficult', 'challenging', 'expert', 'advanced')
    );
    
    foreach ($difficulty_keywords as $level => $keywords) {
        foreach ($keywords as $keyword) {
            if (in_array($keyword, $all_terms)) {
                return ucfirst($level);
            }
        }
    }
    
    return 'Medium'; // Default difficulty
}

/**
 * Get estimated play time for a game
 */
function lofygame_get_estimated_play_time($post_id) {
    $categories = get_the_category($post_id);
    
    if ($categories) {
        $category_name = strtolower($categories[0]->name);
        
        $time_estimates = array(
            'puzzle' => '10-30 minutes',
            'arcade' => '5-15 minutes',
            'action' => '15-45 minutes',
            'adventure' => '30-90 minutes',
            'strategy' => '20-60 minutes',
            'simulation' => '30-120 minutes',
            'rpg' => '60+ minutes'
        );
        
        foreach ($time_estimates as $type => $time) {
            if (strpos($category_name, $type) !== false) {
                return $time;
            }
        }
    }
    
    return '15-30 minutes'; // Default estimate
}

/**
 * Check if game is mobile-friendly
 */
function lofygame_is_mobile_friendly($post_id) {
    $width = get_post_meta($post_id, '_game_width', true);
    $height = get_post_meta($post_id, '_game_height', true);
    $tags = get_the_tags($post_id);
    
    // Check dimensions
    if ($width && $height) {
        if ($width <= 480 && $height <= 800) {
            return true;
        }
    }
    
    // Check tags for mobile keywords
    if ($tags) {
        $mobile_keywords = array('mobile', 'mobile-friendly', 'touch', 'responsive');
        foreach ($tags as $tag) {
            if (in_array(strtolower($tag->name), $mobile_keywords)) {
                return true;
            }
        }
    }
    
    return false;
}

/**
 * Generate game meta description for SEO
 */
function lofygame_generate_game_meta_description($post_id) {
    $title = get_the_title($post_id);
    $excerpt = get_the_excerpt($post_id);
    $categories = get_the_category($post_id);
    $avg_rating = get_post_meta($post_id, '_game_avg_rating', true);
    $rating_count = get_post_meta($post_id, '_game_rating_count', true);
    
    if (empty($avg_rating)) $avg_rating = 4.5;
    if (empty($rating_count)) $rating_count = 127;
    
    $category_name = $categories ? $categories[0]->name : 'online';
    
    if ($excerpt) {
        $description = $excerpt;
    } else {
        $description = "Play {$title} online for free. An exciting {$category_name} game that you can enjoy instantly in your browser.";
    }
    
    $description .= " Rated {$avg_rating}/5 stars by {$rating_count} players. No downloads required!";
    
    return wp_trim_words($description, 25);
}

/**
 * Get similar games based on categories and tags
 */
function lofygame_get_similar_games($post_id, $limit = 6) {
    $categories = get_the_category($post_id);
    $tags = get_the_tags($post_id);
    
    $args = array(
        'post_type' => 'game',
        'posts_per_page' => $limit,
        'post__not_in' => array($post_id),
        'orderby' => 'rand'
    );
    
    if ($categories) {
        $args['category__in'] = wp_list_pluck($categories, 'term_id');
    } elseif ($tags) {
        $args['tag__in'] = wp_list_pluck($tags, 'term_id');
    }
    
    return new WP_Query($args);
}

/**
 * Generate structured data for game
 */
function lofygame_generate_game_structured_data($post_id) {
    $title = get_the_title($post_id);
    $description = get_the_excerpt($post_id);
    $thumbnail = get_the_post_thumbnail_url($post_id, 'large');
    $avg_rating = get_post_meta($post_id, '_game_avg_rating', true);
    $rating_count = get_post_meta($post_id, '_game_rating_count', true);
    
    if (empty($avg_rating)) $avg_rating = 4.5;
    if (empty($rating_count)) $rating_count = 127;
    
    $structured_data = array(
        '@context' => 'https://schema.org',
        '@type' => 'Game',
        'name' => $title,
        'description' => $description,
        'url' => get_permalink($post_id),
        'datePublished' => get_the_date('c', $post_id),
        'aggregateRating' => array(
            '@type' => 'AggregateRating',
            'ratingValue' => $avg_rating,
            'ratingCount' => $rating_count,
            'bestRating' => 5,
            'worstRating' => 1
        ),
        'offers' => array(
            '@type' => 'Offer',
            'price' => '0',
            'priceCurrency' => 'USD'
        )
    );
    
    if ($thumbnail) {
        $structured_data['image'] = $thumbnail;
    }
    
    return json_encode($structured_data);
}

/**
 * Sanitize game URL for iframe embedding
 */
function lofygame_sanitize_game_url($url) {
    // Basic URL validation
    if (!filter_var($url, FILTER_VALIDATE_URL)) {
        return false;
    }
    
    // Check if URL is from allowed domains (add your trusted domains)
    $allowed_domains = array(
        'html5.gamemonetize.co',
        'games.crazygames.com',
        'cdn.htmlgames.com',
        // Add more trusted game hosting domains
    );
    
    $parsed_url = parse_url($url);
    $domain = $parsed_url['host'];
    
    foreach ($allowed_domains as $allowed_domain) {
        if (strpos($domain, $allowed_domain) !== false) {
            return esc_url($url);
        }
    }
    
    // If domain is not in allowed list, return false or log for review
    error_log("LofyGame: Potentially unsafe game URL blocked: " . $url);
    return false;
}

/**
 * Get game statistics for admin
 */
function lofygame_get_game_statistics() {
    $stats = array(
        'total_games' => 0,
        'published_games' => 0,
        'draft_games' => 0,
        'games_with_urls' => 0,
        'games_with_ratings' => 0,
        'total_views' => 0,
        'categories_count' => 0,
        'tags_count' => 0
    );
    
    // Get game counts
    $game_counts = wp_count_posts('game');
    $stats['total_games'] = $game_counts->publish + $game_counts->draft;
    $stats['published_games'] = $game_counts->publish;
    $stats['draft_games'] = $game_counts->draft;
    
    // Count games with URLs
    global $wpdb;
    $stats['games_with_urls'] = $wpdb->get_var($wpdb->prepare("
        SELECT COUNT(DISTINCT post_id) 
        FROM {$wpdb->postmeta} pm
        INNER JOIN {$wpdb->posts} p ON pm.post_id = p.ID
        WHERE pm.meta_key = '_game_url' 
        AND pm.meta_value != '' 
        AND p.post_type = 'game' 
        AND p.post_status = 'publish'
    "));
    
    // Count games with ratings
    $stats['games_with_ratings'] = $wpdb->get_var($wpdb->prepare("
        SELECT COUNT(DISTINCT post_id) 
        FROM {$wpdb->postmeta} pm
        INNER JOIN {$wpdb->posts} p ON pm.post_id = p.ID
        WHERE pm.meta_key = '_game_avg_rating' 
        AND pm.meta_value != '' 
        AND p.post_type = 'game' 
        AND p.post_status = 'publish'
    "));
    
    // Total views
    $stats['total_views'] = $wpdb->get_var($wpdb->prepare("
        SELECT SUM(CAST(meta_value AS UNSIGNED)) 
        FROM {$wpdb->postmeta} pm
        INNER JOIN {$wpdb->posts} p ON pm.post_id = p.ID
        WHERE pm.meta_key = 'post_views_count' 
        AND p.post_type = 'game' 
        AND p.post_status = 'publish'
    ")) ?: 0;
    
    // Categories and tags count
    $stats['categories_count'] = wp_count_terms('category', array('hide_empty' => false));
    $stats['tags_count'] = wp_count_terms('post_tag', array('hide_empty' => false));
    
    return $stats;
}
?>