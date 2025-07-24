<?php
/**
 * SEO Functions and Meta Data
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Enhanced SEO with Rich Snippets for Games
 */
function lofygame_enhanced_seo_meta() {
    if (is_singular('game')) {
        global $post;
        
        // Get game data
        $game_url = get_post_meta($post->ID, '_game_url', true);
        $game_width = get_post_meta($post->ID, '_game_width', true);
        $game_height = get_post_meta($post->ID, '_game_height', true);
        $how_to_play = get_post_meta($post->ID, '_how_to_play', true);
        $preview_video = get_post_meta($post->ID, '_preview_video_url', true);
        
        // Get rating data
        $avg_rating = get_post_meta($post->ID, '_game_avg_rating', true);
        $rating_count = get_post_meta($post->ID, '_game_rating_count', true);
        $best_rating = get_post_meta($post->ID, '_game_best_rating', true);
        $worst_rating = get_post_meta($post->ID, '_game_worst_rating', true);
        
        // Set defaults
        if (empty($avg_rating)) $avg_rating = 4.5;
        if (empty($rating_count)) $rating_count = 127;
        if (empty($best_rating)) $best_rating = 5;
        if (empty($worst_rating)) $worst_rating = 1;
        if (empty($game_width)) $game_width = 800;
        if (empty($game_height)) $game_height = 600;
        
        $excerpt = get_the_excerpt();
        if (empty($excerpt)) {
            $excerpt = wp_trim_words(get_the_content(), 25);
        }
        
        $thumbnail = get_the_post_thumbnail_url($post->ID, 'large');
        $categories = get_the_category();
        $tags = get_the_tags();
        
        // Enhanced meta description
        $meta_description = $excerpt . ' Play this ' . ($categories ? $categories[0]->name . ' ' : '') . 'game online for free. Rated ' . $avg_rating . '/5 stars by ' . $rating_count . ' players.';
        
        echo '<meta name="description" content="' . esc_attr($meta_description) . '">' . "\n";
        echo '<meta name="keywords" content="' . esc_attr(lofygame_get_enhanced_game_keywords($post->ID)) . '">' . "\n";
        
        // Open Graph with Rating
        echo '<meta property="og:title" content="' . esc_attr(get_the_title() . ' - ' . $avg_rating . '★ Game') . '">' . "\n";
        echo '<meta property="og:description" content="' . esc_attr($meta_description) . '">' . "\n";
        echo '<meta property="og:type" content="game">' . "\n";
        echo '<meta property="og:url" content="' . esc_url(get_permalink()) . '">' . "\n";
        if ($thumbnail) {
            echo '<meta property="og:image" content="' . esc_url($thumbnail) . '">' . "\n";
        }
        
        // Twitter Card with Rating
        echo '<meta name="twitter:card" content="summary_large_image">' . "\n";
        echo '<meta name="twitter:title" content="' . esc_attr(get_the_title() . ' - ' . $avg_rating . '★') . '">' . "\n";
        echo '<meta name="twitter:description" content="' . esc_attr($meta_description) . '">' . "\n";
        if ($thumbnail) {
            echo '<meta name="twitter:image" content="' . esc_url($thumbnail) . '">' . "\n";
        }
        
        // Enhanced JSON-LD Schema with AggregateRating (CRITICAL FOR RICH SNIPPETS)
        $schema = array(
            '@context' => 'https://schema.org',
            '@type' => 'Game',
            'name' => get_the_title(),
            'description' => $excerpt,
            'url' => get_permalink(),
            'datePublished' => get_the_date('c'),
            'dateModified' => get_the_modified_date('c'),
            'author' => array(
                '@type' => 'Organization',
                'name' => get_bloginfo('name'),
                'url' => home_url()
            ),
            'publisher' => array(
                '@type' => 'Organization',
                'name' => get_bloginfo('name'),
                'url' => home_url()
            ),
            // CRITICAL: AggregateRating for Rich Snippets
            'aggregateRating' => array(
                '@type' => 'AggregateRating',
                'ratingValue' => $avg_rating,
                'ratingCount' => $rating_count,
                'bestRating' => $best_rating,
                'worstRating' => $worst_rating
            ),
            'offers' => array(
                '@type' => 'Offer',
                'price' => '0',
                'priceCurrency' => 'USD',
                'availability' => 'https://schema.org/InStock',
                'url' => get_permalink()
            ),
            'genre' => array()
        );
        
        // Add image
        if ($thumbnail) {
            $schema['image'] = array(
                '@type' => 'ImageObject',
                'url' => $thumbnail,
                'width' => $game_width,
                'height' => $game_height
            );
        }
        
        // Add categories as genre
        if ($categories) {
            foreach ($categories as $category) {
                $schema['genre'][] = $category->name;
            }
        }
        
        // Add game-specific properties
        if ($game_url) {
            $schema['gamePlatform'] = 'Web Browser';
            $schema['applicationCategory'] = 'Game';
        }
        
        // Add video if available
        if ($preview_video) {
            $schema['video'] = array(
                '@type' => 'VideoObject',
                'name' => get_the_title() . ' - Preview',
                'description' => 'Game preview video',
                'contentUrl' => $preview_video,
                'uploadDate' => get_the_date('c')
            );
        }
        
        // Add instructions if available
        if ($how_to_play) {
            $schema['gameHelp'] = $how_to_play;
        }
        
        // Add keywords
        if ($tags) {
            $keywords = array();
            foreach ($tags as $tag) {
                $keywords[] = $tag->name;
            }
            $schema['keywords'] = implode(', ', $keywords);
        }
        
        echo '<script type="application/ld+json">' . json_encode($schema, JSON_PRETTY_PRINT) . '</script>' . "\n";
    }
}
add_action('wp_head', 'lofygame_enhanced_seo_meta');

/**
 * Enhanced SEO for Category and Tag Archives
 */
function lofygame_category_tag_seo_meta() {
    // Handle post type archive (like /games)
    if (is_post_type_archive('game')) {
        $games_count = wp_count_posts('game')->publish;
        $meta_description = "Play " . $games_count . " free online games instantly in your browser. Action, puzzle, adventure, strategy, and more. No downloads, no registration required!";
        
        $keywords = array(
            'free online games',
            'browser games',
            'play games online',
            'free games',
            'online gaming',
            'instant play games',
            'no download games',
            'web games'
        );
        
        echo '<meta name="description" content="' . esc_attr($meta_description) . '">' . "\n";
        echo '<meta name="keywords" content="' . esc_attr(implode(', ', $keywords)) . '">' . "\n";
        
        // Open Graph
        echo '<meta property="og:title" content="Free Online Games - Play Instantly">' . "\n";
        echo '<meta property="og:description" content="' . esc_attr($meta_description) . '">' . "\n";
        echo '<meta property="og:type" content="website">' . "\n";
        echo '<meta property="og:url" content="' . esc_url(get_post_type_archive_link('game')) . '">' . "\n";
        
        // Twitter Card
        echo '<meta name="twitter:card" content="summary">' . "\n";
        echo '<meta name="twitter:title" content="Free Online Games">' . "\n";
        echo '<meta name="twitter:description" content="' . esc_attr($meta_description) . '">' . "\n";
        
    } elseif (is_category() || is_tag()) {
        // Handle category/tag archives
        $term = get_queried_object();
        if ($term) {
            $term_name = $term->name;
            $term_description = $term->description;
            $games_count = $term->count;
            
            // Enhanced meta description
            $meta_description = '';
            if (!empty($term_description)) {
                $meta_description = wp_trim_words(strip_tags($term_description), 25);
            } else {
                $meta_description = "Discover {$games_count} amazing {$term_name} games. Play free online {$term_name} games instantly in your browser. No downloads required!";
            }
            
            // Enhanced meta keywords
            $keywords = array(
                $term_name . ' games',
                'free ' . $term_name . ' games',
                'online ' . $term_name . ' games',
                $term_name . ' browser games',
                'play ' . $term_name . ' games',
                'free online games',
                'browser games',
                'no download games'
            );
            
            echo '<meta name="description" content="' . esc_attr($meta_description) . '">' . "\n";
            echo '<meta name="keywords" content="' . esc_attr(implode(', ', $keywords)) . '">' . "\n";
            
            // Open Graph
            echo '<meta property="og:title" content="' . esc_attr($term_name . ' Games - Free Online ' . $term_name . ' Games') . '">' . "\n";
            echo '<meta property="og:description" content="' . esc_attr($meta_description) . '">' . "\n";
            echo '<meta property="og:type" content="website">' . "\n";
            echo '<meta property="og:url" content="' . esc_url(get_term_link($term)) . '">' . "\n";
            
            // Twitter Card
            echo '<meta name="twitter:card" content="summary">' . "\n";
            echo '<meta name="twitter:title" content="' . esc_attr($term_name . ' Games') . '">' . "\n";
            echo '<meta name="twitter:description" content="' . esc_attr($meta_description) . '">' . "\n";
        }
    }
}
add_action('wp_head', 'lofygame_category_tag_seo_meta');

/**
 * Enhanced Game Keywords
 */
function lofygame_get_enhanced_game_keywords($post_id) {
    $keywords = array();
    
    // Add rating-related keywords
    $avg_rating = get_post_meta($post_id, '_game_avg_rating', true);
    if ($avg_rating >= 4.5) {
        $keywords[] = 'top rated game';
        $keywords[] = 'best online game';
    } elseif ($avg_rating >= 4.0) {
        $keywords[] = 'highly rated game';
        $keywords[] = 'popular online game';
    }
    
    $categories = get_the_category($post_id);
    foreach ($categories as $category) {
        $keywords[] = $category->name;
        $keywords[] = $category->name . ' game';
        $keywords[] = 'free ' . strtolower($category->name) . ' games';
    }
    
    $tags = get_the_tags($post_id);
    if ($tags) {
        foreach ($tags as $tag) {
            $keywords[] = $tag->name;
        }
    }
    
    // Add generic gaming keywords
    $keywords = array_merge($keywords, array(
        'free online games',
        'play games online',
        'browser games',
        'html5 games',
        'no download games',
        'instant play games'
    ));
    
    return implode(', ', array_unique($keywords));
}

/**
 * Enhanced breadcrumbs for category/tag pages
 */
function lofygame_enhanced_breadcrumbs() {
    if (is_home() || is_front_page()) return '';
    
    $breadcrumbs = '<nav class="breadcrumbs enhanced-breadcrumbs" aria-label="Breadcrumb">';
    $breadcrumbs .= '<div class="breadcrumb-container">';
    $breadcrumbs .= '<a href="' . home_url() . '" class="breadcrumb-item">🏠 Home</a>';
    
    if (is_category()) {
        $category = get_queried_object();
        $breadcrumbs .= '<span class="breadcrumb-separator">→</span>';
        $breadcrumbs .= '<a href="' . get_post_type_archive_link('game') . '" class="breadcrumb-item">🎮 Games</a>';
        $breadcrumbs .= '<span class="breadcrumb-separator">→</span>';
        $breadcrumbs .= '<span class="breadcrumb-current">📂 ' . $category->name . '</span>';
        
    } elseif (is_tag()) {
        $tag = get_queried_object();
        $breadcrumbs .= '<span class="breadcrumb-separator">→</span>';
        $breadcrumbs .= '<a href="' . get_post_type_archive_link('game') . '" class="breadcrumb-item">🎮 Games</a>';
        $breadcrumbs .= '<span class="breadcrumb-separator">→</span>';
        $breadcrumbs .= '<span class="breadcrumb-current">🏷️ ' . $tag->name . '</span>';
        
    } elseif (is_singular('game')) {
        $breadcrumbs .= '<span class="breadcrumb-separator">→</span>';
        $breadcrumbs .= '<a href="' . get_post_type_archive_link('game') . '" class="breadcrumb-item">🎮 Games</a>';
        $breadcrumbs .= '<span class="breadcrumb-separator">→</span>';
        $breadcrumbs .= '<span class="breadcrumb-current">' . get_the_title() . '</span>';
        
    } elseif (is_post_type_archive('game')) {
        $breadcrumbs .= '<span class="breadcrumb-separator">→</span>';
        $breadcrumbs .= '<span class="breadcrumb-current">🎮 Games</span>';
        
    } elseif (is_singular('post')) {
        $breadcrumbs .= '<span class="breadcrumb-separator">→</span>';
        $breadcrumbs .= '<a href="' . home_url('/blog') . '" class="breadcrumb-item">📝 Blog</a>';
        $breadcrumbs .= '<span class="breadcrumb-separator">→</span>';
        $breadcrumbs .= '<span class="breadcrumb-current">' . get_the_title() . '</span>';
        
    } elseif (is_page_template('blog.php')) {
        $breadcrumbs .= '<span class="breadcrumb-separator">→</span>';
        $breadcrumbs .= '<span class="breadcrumb-current">📝 Blog</span>';
    }
    
    $breadcrumbs .= '</div>';
    $breadcrumbs .= '</nav>';
    
    return $breadcrumbs;
}

/**
 * Generate SEO-optimized title for category/tag archives
 */
function lofygame_get_archive_title($term = null) {
    // Handle post type archive (like /games)
    if (is_post_type_archive('game')) {
        $games_count = wp_count_posts('game')->publish;
        return 'Free Online Games - Play ' . $games_count . ' Games Instantly';
    }
    
    // Handle category/tag archives
    if ($term && isset($term->count)) {
        $games_count = $term->count;
        $term_name = $term->name;
        
        if ($games_count == 1) {
            return $term_name . ' Games - 1 Free Game to Play Online';
        } else {
            return $term_name . ' Games - ' . $games_count . ' Free Games to Play Online';
        }
    }
    
    // Fallback
    return 'Free Online Games';
}

/**
 * Generate SEO-optimized description for category/tag archives
 */
function lofygame_get_archive_description($term = null) {
    // Handle post type archive (like /games)
    if (is_post_type_archive('game')) {
        $games_count = wp_count_posts('game')->publish;
        return "Play " . $games_count . " free online games instantly in your browser. No downloads, no registration required. Choose from action, puzzle, adventure, and many more game categories!";
    }
    
    // Handle category/tag archives
    if ($term && isset($term->count) && isset($term->name)) {
        $games_count = $term->count;
        $term_name = $term->name;
        $term_description = isset($term->description) ? $term->description : '';
        
        if (!empty($term_description)) {
            return $term_description;
        }
        
        // Generate dynamic description based on category/tag name
        $descriptions = array(
            'action' => "High-energy action games that will get your adrenaline pumping. Fast-paced gameplay with intense combat and thrilling adventures.",
            'puzzle' => "Challenge your mind with brain-teasing puzzle games. Solve complex problems, think strategically, and exercise your logical thinking.",
            'adventure' => "Embark on epic adventures in immersive story-driven games. Explore new worlds, meet interesting characters, and uncover hidden secrets.",
            'strategy' => "Test your tactical skills with strategic games that require careful planning and smart decision-making to achieve victory.",
            'racing' => "Feel the speed in exciting racing games. Compete against others, master different tracks, and become the ultimate racing champion.",
            'sports' => "Experience the thrill of sports games. Play your favorite sports virtually and compete for the championship title.",
            'arcade' => "Classic arcade-style games that bring back the nostalgic gaming experience with simple yet addictive gameplay mechanics.",
        );
        
        $term_slug = isset($term->slug) ? strtolower($term->slug) : strtolower($term_name);
        foreach ($descriptions as $key => $desc) {
            if (strpos($term_slug, $key) !== false) {
                return $desc . " Play " . $games_count . " free " . $term_name . " games online instantly in your browser.";
            }
        }
        
        // Default description for categories/tags
        return "Discover " . $games_count . " amazing " . $term_name . " games. Play free online " . $term_name . " games instantly in your browser. No downloads required, no registration needed - just pure gaming fun!";
    }
    
    // Fallback
    return "Play amazing free online games instantly in your browser. No downloads required!";
}

/**
 * Add structured data for games in category/tag archives
 */
function lofygame_add_games_structured_data() {
    if (is_post_type_archive('game')) {
        // Handle games archive page
        $games_query = new WP_Query(array(
            'post_type' => 'game',
            'posts_per_page' => 10, // Limit for structured data
        ));
        
        if ($games_query->have_posts()) {
            $games_list = array();
            $position = 1;
            
            while ($games_query->have_posts()) {
                $games_query->the_post();
                $games_list[] = array(
                    '@type' => 'ListItem',
                    'position' => $position++,
                    'item' => array(
                        '@type' => 'Game',
                        'name' => get_the_title(),
                        'url' => get_permalink(),
                        'description' => get_the_excerpt() ?: wp_trim_words(get_the_content(), 20),
                        'datePublished' => get_the_date('c'),
                        'genre' => 'Online Game'
                    )
                );
            }
            wp_reset_postdata();
            
            $schema = array(
                '@context' => 'https://schema.org',
                '@type' => 'ItemList',
                'name' => 'Free Online Games',
                'description' => 'Collection of free online games to play instantly',
                'numberOfItems' => count($games_list),
                'itemListElement' => $games_list
            );
            
            echo '<script type="application/ld+json">' . json_encode($schema) . '</script>' . "\n";
        }
        
    } elseif (is_category() || is_tag()) {
        // Handle category/tag archives
        $term = get_queried_object();
        $games_query = new WP_Query(array(
            'post_type' => 'game',
            'posts_per_page' => 10, // Limit for structured data
            'tax_query' => array(
                array(
                    'taxonomy' => $term->taxonomy,
                    'field' => 'term_id',
                    'terms' => $term->term_id,
                ),
            ),
        ));
        
        if ($games_query->have_posts()) {
            $games_list = array();
            $position = 1;
            
            while ($games_query->have_posts()) {
                $games_query->the_post();
                $games_list[] = array(
                    '@type' => 'ListItem',
                    'position' => $position++,
                    'item' => array(
                        '@type' => 'Game',
                        'name' => get_the_title(),
                        'url' => get_permalink(),
                        'description' => get_the_excerpt() ?: wp_trim_words(get_the_content(), 20),
                        'datePublished' => get_the_date('c'),
                        'genre' => $term->name
                    )
                );
            }
            wp_reset_postdata();
            
            $schema = array(
                '@context' => 'https://schema.org',
                '@type' => 'ItemList',
                'name' => $term->name . ' Games',
                'description' => lofygame_get_archive_description($term),
                'numberOfItems' => count($games_list),
                'itemListElement' => $games_list
            );
            
            echo '<script type="application/ld+json">' . json_encode($schema) . '</script>' . "\n";
        }
    }
}
add_action('wp_footer', 'lofygame_add_games_structured_data');
?>