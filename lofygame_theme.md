# LofyGame WordPress Theme - Complete File Structure

## Theme Folder Structure
```
lofygame-template/
├── style.css
├── functions.php
├── index.php
├── header.php
├── footer.php
├── single-game.php
├── archive-game.php
├── 404.php
├── blog.php
├── single.php
├── js/
│   └── theme.js
│   └── admin.js
├── images/
│   └── placeholder-game.jpg
│   └── placeholder-blog.jpg
└── template-parts/
│   └── blog-card.php
│   └── game-card.php
│   └── category-card.php
└── assets/
   └── css/
       └── global.css
       └── header.css
       └── footer.css
       └── game-card.css
       └── pagination.css
       └── blog.css
       └── grid.css
       └── categories.css
       └── archive.css
       └── single-game.css
       └── rating.css
       └── utilities.css
       └── responsive.css
└── includes/
    └── theme-setup.php
    └── post-types.php
    └── meta-boxes.php
    └── rating-system.php
    └── query-pagination.php
    └── ajax-handlers.php

    └── seo-functions.php
    └── import-export.php
    └── admin-functions.php
    └── performance.php
    └── performance.php
    └── utility-functions.php
```


'includes/theme-setup.php',           // Theme setup and configuration
        'includes/post-types.php',           // Custom post types
        'includes/meta-boxes.php',           // Meta boxes and custom fields
        'includes/rating-system.php',        // Rating and review system
        'includes/query-pagination.php',     // Query modifications and pagination
        'includes/ajax-handlers.php',        // AJAX functionality
        'includes/seo-functions.php',        // SEO and meta data
        'includes/import-export.php',        // Import/export functionality
        'includes/admin-functions.php',      // Admin customizations
        'includes/performance.php',          // Performance optimizations
        'includes/utility-functions.php'

## 1. style.css - Main Theme Stylesheet
```css
/*!
Theme Name: LofyGame Template
Description: A Poki.com-inspired gaming theme with game embedding functionality
Version: 1.0
Author: Your Name
*/

/* CSS Reset & Base Styles */
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

:root {
    --primary-color: #4f46e5;
    --secondary-color: #06b6d4;
    --accent-color: #f59e0b;
    --text-color: #1f2937;
    --bg-color: #ffffff;
    --card-bg: #f8fafc;
    --border-color: #e5e7eb;
    --shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
    --gradient: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
}

[data-theme="dark"] {
    --text-color: #f3f4f6;
    --bg-color: #111827;
    --card-bg: #1f2937;
    --border-color: #374151;
    --shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.3);
    --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.3);
}

body {
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    background-color: var(--bg-color);
    color: var(--text-color);
    line-height: 1.6;
    transition: all 0.3s ease;
}

/* Header Styles */
.site-header {
    background: var(--gradient);
    padding: 1rem 0;
    box-shadow: var(--shadow);
    position: sticky;
    top: 0;
    z-index: 1000;
}

.header-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 1rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
}

.site-title {
    font-size: 1.8rem;
    font-weight: 700;
    color: white;
    text-decoration: none;
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
}

.header-controls {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.theme-toggle {
    background: rgba(255, 255, 255, 0.2);
    border: none;
    color: white;
    padding: 0.5rem;
    border-radius: 50%;
    cursor: pointer;
    transition: all 0.3s ease;
}

.theme-toggle:hover {
    background: rgba(255, 255, 255, 0.3);
    transform: scale(1.1);
}

/* Navigation */
.header-menu {
    display: flex;
    list-style: none;
    gap: 2rem;
}

.header-menu a {
    color: white;
    text-decoration: none;
    font-weight: 500;
    transition: all 0.3s ease;
}

.header-menu a:hover {
    text-shadow: 0 2px 8px rgba(255, 255, 255, 0.3);
}

/* Filters */
.game-filters {
    max-width: 1200px;
    margin: 2rem auto;
    padding: 0 1rem;
    display: flex;
    gap: 1rem;
    flex-wrap: wrap;
}

.filter-select {
    padding: 0.75rem 1rem;
    border: 2px solid var(--border-color);
    border-radius: 8px;
    background: var(--card-bg);
    color: var(--text-color);
    font-size: 1rem;
    cursor: pointer;
    transition: all 0.3s ease;
}

.filter-select:hover,
.filter-select:focus {
    border-color: var(--primary-color);
    box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
}

/* Game Grid */
.games-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 2rem;
    max-width: 1200px;
    margin: 2rem auto;
    padding: 0 1rem;
}

/* Game Card */
.game-card {
    background: var(--card-bg);
    border-radius: 12px;
    overflow: hidden;
    box-shadow: var(--shadow);
    transition: all 0.3s ease;
    cursor: pointer;
    position: relative;
}

.game-card:hover {
    transform: translateY(-4px);
    box-shadow: var(--shadow-lg);
}

.game-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: var(--gradient);
    transform: scaleX(0);
    transition: transform 0.3s ease;
}

.game-card:hover::before {
    transform: scaleX(1);
}

.game-thumbnail {
    width: 100%;
    height: 200px;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.game-card:hover .game-thumbnail {
    transform: scale(1.05);
}

.game-card-content {
    padding: 1.5rem;
}

.game-title {
    font-size: 1.2rem;
    font-weight: 600;
    margin-bottom: 0.5rem;
    color: var(--text-color);
}

.game-excerpt {
    color: var(--text-color);
    opacity: 0.8;
    font-size: 0.9rem;
    line-height: 1.5;
    margin-bottom: 1rem;
}

.game-meta {
    display: flex;
    gap: 0.5rem;
    flex-wrap: wrap;
}

.game-tag {
    background: var(--primary-color);
    color: white;
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 500;
}

/* Single Game Page */
.single-game-container {
    max-width: 1200px;
    margin: 2rem auto;
    padding: 0 1rem;
}

.game-header {
    text-align: center;
    margin-bottom: 2rem;
}

.game-header h1 {
    font-size: 2.5rem;
    font-weight: 700;
    margin-bottom: 1rem;
    background: var(--gradient);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.game-iframe-container {
    position: relative;
    background: var(--card-bg);
    border-radius: 12px;
    overflow: hidden;
    box-shadow: var(--shadow-lg);
    margin-bottom: 2rem;
}

.game-iframe {
    width: 100%;
    height: 600px;
    border: none;
    display: block;
}

.fullscreen-controls {
    position: absolute;
    top: 1rem;
    right: 1rem;
    z-index: 10;
}

.fullscreen-btn {
    background: rgba(0, 0, 0, 0.7);
    color: white;
    border: none;
    padding: 0.75rem;
    border-radius: 8px;
    cursor: pointer;
    font-size: 1.1rem;
    transition: all 0.3s ease;
}

.fullscreen-btn:hover {
    background: rgba(0, 0, 0, 0.9);
    transform: scale(1.05);
}

.exit-fullscreen {
    position: fixed;
    top: 1rem;
    right: 1rem;
    z-index: 10001;
    background: rgba(220, 38, 38, 0.9);
    color: white;
    border: none;
    padding: 0.75rem 1rem;
    border-radius: 8px;
    cursor: pointer;
    font-size: 1rem;
    font-weight: 600;
    display: none;
}

.game-content {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 2rem;
}

.game-description {
    background: var(--card-bg);
    padding: 2rem;
    border-radius: 12px;
    box-shadow: var(--shadow);
}

.game-description h2 {
    font-size: 1.5rem;
    margin-bottom: 1rem;
    color: var(--primary-color);
}

.how-to-play {
    background: var(--card-bg);
    padding: 2rem;
    border-radius: 12px;
    box-shadow: var(--shadow);
}

.how-to-play h3 {
    font-size: 1.3rem;
    margin-bottom: 1rem;
    color: var(--secondary-color);
}

/* Footer */
.site-footer {
    background: var(--text-color);
    color: var(--bg-color);
    padding: 3rem 0 1rem;
    margin-top: 4rem;
}

.footer-content {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 1rem;
    text-align: center;
}

.footer-menu {
    display: flex;
    justify-content: center;
    gap: 2rem;
    list-style: none;
    margin-bottom: 2rem;
}

.footer-menu a {
    color: var(--bg-color);
    text-decoration: none;
    transition: opacity 0.3s ease;
}

.footer-menu a:hover {
    opacity: 0.8;
}

/* Responsive Design */
@media (max-width: 768px) {
    .header-container {
        flex-direction: column;
        gap: 1rem;
    }
    
    .games-grid {
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1rem;
    }
    
    .game-content {
        grid-template-columns: 1fr;
    }
    
    .game-iframe {
        height: 400px;
    }
    
    .game-header h1 {
        font-size: 2rem;
    }
    
    .header-menu {
        flex-direction: column;
        gap: 1rem;
    }
    
    .footer-menu {
        flex-direction: column;
        gap: 1rem;
    }
    
    .exit-fullscreen {
        display: block !important;
    }
}

/* Loading Animation */
.loading {
    display: inline-block;
    width: 20px;
    height: 20px;
    border: 3px solid var(--border-color);
    border-radius: 50%;
    border-top-color: var(--primary-color);
    animation: spin 1s ease-in-out infinite;
}

@keyframes spin {
    to { transform: rotate(360deg); }
}

/* Accessibility */
.sr-only {
    position: absolute;
    width: 1px;
    height: 1px;
    padding: 0;
    margin: -1px;
    overflow: hidden;
    clip: rect(0, 0, 0, 0);
    white-space: nowrap;
    border: 0;
}

/* Focus styles */
button:focus,
select:focus,
a:focus {
    outline: 2px solid var(--primary-color);
    outline-offset: 2px;
}

/* Fullscreen styles */
.fullscreen-iframe {
    position: fixed !important;
    top: 0 !important;
    left: 0 !important;
    width: 100vw !important;
    height: 100vh !important;
    z-index: 10000 !important;
    background: #000 !important;
}

body.fullscreen-active {
    overflow: hidden;
}
```

## 2. functions.php - Theme Functions
```php
<?php
/**
 * LofyGame Theme Functions
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Theme Setup
 */
function lofygame_setup() {
    // Add theme support
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('html5', array('search-form', 'comment-form', 'comment-list', 'gallery', 'caption'));
    add_theme_support('customize-selective-refresh-widgets');
    
    // Register navigation menus
    register_nav_menus(array(
        'header-menu' => __('Header Menu', 'lofygame'),
        'footer-menu' => __('Footer Menu', 'lofygame'),
    ));
    
    // Add image sizes
    add_image_size('game-thumbnail', 400, 300, true);
}
add_action('after_setup_theme', 'lofygame_setup');

/**
 * Enqueue Scripts and Styles
 */
function lofygame_scripts() {
    wp_enqueue_style('lofygame-style', get_stylesheet_uri(), array(), '1.0.0');
    wp_enqueue_style('google-fonts', 'https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap', array(), null);
    
    wp_enqueue_script('lofygame-script', get_template_directory_uri() . '/js/theme.js', array('jquery'), '1.0.0', true);
    
    // Localize script for AJAX
    wp_localize_script('lofygame-script', 'lofygame_ajax', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('lofygame_nonce')
    ));
}
add_action('wp_enqueue_scripts', 'lofygame_scripts');

/**
 * Register Custom Post Type - Games
 */
function lofygame_register_game_post_type() {
    $labels = array(
        'name' => 'Games',
        'singular_name' => 'Game',
        'menu_name' => 'Games',
        'add_new' => 'Add New Game',
        'add_new_item' => 'Add New Game',
        'edit_item' => 'Edit Game',
        'new_item' => 'New Game',
        'view_item' => 'View Game',
        'search_items' => 'Search Games',
        'not_found' => 'No games found',
        'not_found_in_trash' => 'No games found in trash'
    );
    
    $args = array(
        'labels' => $labels,
        'public' => true,
        'publicly_queryable' => true,
        'show_ui' => true,
        'show_in_menu' => true,
        'query_var' => true,
        'rewrite' => array('slug' => 'games'),
        'capability_type' => 'post',
        'has_archive' => true,
        'hierarchical' => false,
        'menu_position' => 5,
        'menu_icon' => 'dashicons-games',
        'supports' => array('title', 'editor', 'thumbnail', 'excerpt', 'comments'),
        'taxonomies' => array('category', 'post_tag'),
        'show_in_rest' => true
    );
    
    register_post_type('game', $args);
}
add_action('init', 'lofygame_register_game_post_type');

/**
 * Add Custom Meta Boxes
 */
function lofygame_add_game_meta_boxes() {
    add_meta_box(
        'game_details',
        'Game Details',
        'lofygame_game_details_callback',
        'game',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'lofygame_add_game_meta_boxes');

/**
 * Meta Box Callback
 */
function lofygame_game_details_callback($post) {
    wp_nonce_field('lofygame_save_game_details', 'lofygame_game_nonce');
    
    $game_url = get_post_meta($post->ID, '_game_url', true);
    $how_to_play = get_post_meta($post->ID, '_how_to_play', true);
    
    echo '<table class="form-table">';
    echo '<tr>';
    echo '<th><label for="game_url">Game URL</label></th>';
    echo '<td><input type="url" id="game_url" name="game_url" value="' . esc_attr($game_url) . '" class="regular-text" placeholder="https://example.com/game.html" /></td>';
    echo '</tr>';
    echo '<tr>';
    echo '<th><label for="how_to_play">How to Play</label></th>';
    echo '<td><textarea id="how_to_play" name="how_to_play" rows="5" cols="50" class="large-text">' . esc_textarea($how_to_play) . '</textarea></td>';
    echo '</tr>';
    echo '</table>';
}

/**
 * Save Meta Box Data
 */
function lofygame_save_game_details($post_id) {
    if (!isset($_POST['lofygame_game_nonce']) || !wp_verify_nonce($_POST['lofygame_game_nonce'], 'lofygame_save_game_details')) {
        return;
    }
    
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    
    if (isset($_POST['post_type']) && 'game' == $_POST['post_type']) {
        if (!current_user_can('edit_page', $post_id)) {
            return;
        }
    } else {
        if (!current_user_can('edit_post', $post_id)) {
            return;
        }
    }
    
    if (isset($_POST['game_url'])) {
        update_post_meta($post_id, '_game_url', sanitize_url($_POST['game_url']));
    }
    
    if (isset($_POST['how_to_play'])) {
        update_post_meta($post_id, '_how_to_play', sanitize_textarea_field($_POST['how_to_play']));
    }
}
add_action('save_post', 'lofygame_save_game_details');

/**
 * Auto-set Featured Image from Content
 */
function lofygame_auto_set_featured_image($post_id) {
    if (get_post_type($post_id) != 'game') {
        return;
    }
    
    if (has_post_thumbnail($post_id)) {
        return;
    }
    
    $post = get_post($post_id);
    $content = $post->post_content;
    
    preg_match('/<img[^>]+src=[\'"]([^\'"]+)[\'"][^>]*>/i', $content, $matches);
    
    if (!empty($matches[1])) {
        $image_url = $matches[1];
        $upload_dir = wp_upload_dir();
        
        if (strpos($image_url, $upload_dir['baseurl']) !== false) {
            $attachment_id = attachment_url_to_postid($image_url);
            if ($attachment_id) {
                set_post_thumbnail($post_id, $attachment_id);
            }
        }
    }
}
add_action('save_post', 'lofygame_auto_set_featured_image');

/**
 * AJAX Filter Games
 */
function lofygame_filter_games() {
    check_ajax_referer('lofygame_nonce', 'nonce');
    
    $category = sanitize_text_field($_POST['category']);
    $tag = sanitize_text_field($_POST['tag']);
    
    $args = array(
        'post_type' => 'game',
        'posts_per_page' => -1,
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
    } else {
        echo '<p class="no-games">No games found matching your criteria.</p>';
    }
    
    wp_reset_postdata();
    wp_die();
}
add_action('wp_ajax_filter_games', 'lofygame_filter_games');
add_action('wp_ajax_nopriv_filter_games', 'lofygame_filter_games');

/**
 * SEO Optimizations
 */
function lofygame_seo_meta() {
    if (is_singular('game')) {
        global $post;
        
        $game_url = get_post_meta($post->ID, '_game_url', true);
        $excerpt = get_the_excerpt();
        $thumbnail = get_the_post_thumbnail_url($post->ID, 'large');
        
        echo '<meta name="description" content="' . esc_attr($excerpt) . '">' . "\n";
        echo '<meta name="keywords" content="' . esc_attr(lofygame_get_game_keywords($post->ID)) . '">' . "\n";
        
        // Open Graph
        echo '<meta property="og:title" content="' . esc_attr(get_the_title()) . '">' . "\n";
        echo '<meta property="og:description" content="' . esc_attr($excerpt) . '">' . "\n";
        echo '<meta property="og:type" content="game">' . "\n";
        echo '<meta property="og:url" content="' . esc_url(get_permalink()) . '">' . "\n";
        if ($thumbnail) {
            echo '<meta property="og:image" content="' . esc_url($thumbnail) . '">' . "\n";
        }
        
        // Twitter Card
        echo '<meta name="twitter:card" content="summary_large_image">' . "\n";
        echo '<meta name="twitter:title" content="' . esc_attr(get_the_title()) . '">' . "\n";
        echo '<meta name="twitter:description" content="' . esc_attr($excerpt) . '">' . "\n";
        if ($thumbnail) {
            echo '<meta name="twitter:image" content="' . esc_url($thumbnail) . '">' . "\n";
        }
        
        // JSON-LD Schema
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
                'name' => get_bloginfo('name')
            )
        );
        
        if ($thumbnail) {
            $schema['image'] = $thumbnail;
        }
        
        echo '<script type="application/ld+json">' . json_encode($schema) . '</script>' . "\n";
    }
}
add_action('wp_head', 'lofygame_seo_meta');

/**
 * Get Game Keywords
 */
function lofygame_get_game_keywords($post_id) {
    $keywords = array();
    
    $categories = get_the_category($post_id);
    foreach ($categories as $category) {
        $keywords[] = $category->name;
    }
    
    $tags = get_the_tags($post_id);
    if ($tags) {
        foreach ($tags as $tag) {
            $keywords[] = $tag->name;
        }
    }
    
    $keywords[] = 'free online games';
    $keywords[] = 'play games online';
    $keywords[] = 'browser games';
    
    return implode(', ', array_unique($keywords));
}

/**
 * Custom Excerpt Length
 */
function lofygame_excerpt_length($length) {
    return 20;
}
add_filter('excerpt_length', 'lofygame_excerpt_length');

/**
 * Custom Excerpt More
 */
function lofygame_excerpt_more($more) {
    return '...';
}
add_filter('excerpt_more', 'lofygame_excerpt_more');

/**
 * Add Custom CSS Classes to Body
 */
function lofygame_body_classes($classes) {
    if (is_post_type_archive('game') || is_singular('game')) {
        $classes[] = 'games-page';
    }
    return $classes;
}
add_filter('body_class', 'lofygame_body_classes');

/**
 * Register Sidebars
 */
function lofygame_widgets_init() {
    register_sidebar(array(
        'name' => 'Game Sidebar',
        'id' => 'game-sidebar',
        'description' => 'Sidebar for game pages',
        'before_widget' => '<div class="widget %2$s">',
        'after_widget' => '</div>',
        'before_title' => '<h3 class="widget-title">',
        'after_title' => '</h3>',
    ));
}
add_action('widgets_init', 'lofygame_widgets_init');

/**
 * Flush Rewrite Rules on Theme Activation
 */
function lofygame_flush_rewrite_rules() {
    lofygame_register_game_post_type();
    flush_rewrite_rules();
}
add_action('after_switch_theme', 'lofygame_flush_rewrite_rules');

/**
 * Add Sitemap for Games
 */
function lofygame_sitemap_games($provider, $name) {
    if ('games' !== $name) {
        return $provider;
    }
    
    return new WP_Sitemaps_Posts($provider, 'game');
}
add_filter('wp_sitemaps_add_provider', 'lofygame_sitemap_games', 10, 2);

/**
 * Security Headers
 */
function lofygame_security_headers() {
    if (is_singular('game')) {
        header('X-Frame-Options: ALLOWALL');
        header('Content-Security-Policy: frame-src *; frame-ancestors *;');
    }
}
add_action('send_headers', 'lofygame_security_headers');
```

## 3. index.php - Main Template
```php
<?php get_header(); ?>

<main class="site-main">
    <?php if (have_posts()) : ?>
        <section class="games-section">
            <div class="games-grid">
                <?php while (have_posts()) : the_post(); ?>
                    <?php get_template_part('template-parts/game-card'); ?>
                <?php endwhile; ?>
            </div>
        </section>
        
        <?php the_posts_navigation(); ?>
    <?php else : ?>
        <section class="no-content">
            <div class="container">
                <h2>No games found</h2>
                <p>Sorry, no games were found. Please try again later.</p>
            </div>
        </section>
    <?php endif; ?>
</main>

<?php get_footer(); ?>
```

## 4. header.php - Header Template
```php
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="profile" href="https://gmpg.org/xfn/11">
    <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<header class="site-header">
    <div class="header-container">
        <a href="<?php echo esc_url(home_url('/')); ?>" class="site-title">
            <?php bloginfo('name'); ?>
        </a>
        
        <nav class="main-navigation">
            <?php
            wp_nav_menu(array(
                'theme_location' => 'header-menu',
                'menu_class' => 'header-menu',
                'container' => false,
                'fallback_cb' => false,
            ));
            ?>
        </nav>
        
        <div class="header-controls">
            <button class="theme-toggle" id="theme-toggle" aria-label="Toggle dark mode">
                <span class="theme-toggle-icon">🌙</span>
            </button>
        </div>
    </div>
</header>

<?php if (is_home() || is_post_type_archive('game')) : ?>
<section class="game-filters">
    <select id="category-filter" class="filter-select">
        <option value="all">All Categories</option>
        <?php
        $categories = get_categories(array(
            'taxonomy' => 'category',
            'hide_empty' => true,
        ));
        foreach ($categories as $category) {
            echo '<option value="' . esc_attr($category->term_id) . '">' . esc_html($category->name) . '</option>';
        }
        ?>
    </select>
    
    <select id="tag-filter" class="filter-select">
        <option value="all">All Tags</option>
        <?php
        $tags = get_tags(array(
            'hide_empty' => true,
        ));
        foreach ($tags as $tag) {
            echo '<option value="' . esc_attr($tag->term_id) . '">' . esc_html($tag->name) . '</option>';
        }
        ?>
    </select>
</section>
<?php endif; ?>

<div id="content" class="site-content">
```

## 5. footer.php - Footer Template
```php
</div><!-- #content -->

<footer class="site-footer">
    <div class="footer-content">
        <nav class="footer-navigation">
            <?php
            wp_nav_menu(array(
                'theme_location' => 'footer-menu',
                'menu_class' => 'footer-menu',
                'container' => false,
                'fallback_cb' => false,
            ));
            ?>
        </nav>
        
        <div class="footer-info">
            <p>&copy; <?php echo date('Y'); ?> <?php bloginfo('name'); ?>. All rights reserved.</p>
            <p>Play free online games - No downloads required!</p>
        </div>
    </div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
```

## 6. single-game.php - Single Game Template
```php
<?php get_header(); ?>

<main class="site-main">
    <?php while (have_posts()) : the_post(); ?>
        <article class="single-game-container">
            <header class="game-header">
                <h1><?php the_title(); ?></h1>
                <div class="game-meta">
                    <?php
                    $categories = get_the_category();
                    if ($categories) {
                        foreach ($categories as $category) {
                            echo '<span class="game-tag">' . esc_html($category->name) . '</span>';
                        }
                    }
                    
                    $tags = get_the_tags();
                    if ($tags) {
                        foreach ($tags as $tag) {
                            echo '<span class="game-tag">' . esc_html($tag->name) . '</span>';
                        }
                    }
                    ?>
                </div>
            </header>
            
            <?php
            $game_url = get_post_meta(get_the_ID(), '_game_url', true);
            if ($game_url) :
            ?>
            <div class="game-iframe-container">
                <div class="fullscreen-controls">
                    <button class="fullscreen-btn" id="fullscreen-btn" aria-label="Enter fullscreen">
                        ⛶
                    </button>
                </div>
                <iframe 
                    src="<?php echo esc_url($game_url); ?>" 
                    class="game-iframe" 
                    id="game-iframe"
                    frameborder="0" 
                    scrolling="no"
                    allowfullscreen="true"
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                    loading="lazy">
                </iframe>
                <button class="exit-fullscreen" id="exit-fullscreen" aria-label="Exit fullscreen">
                    ✕ Exit Fullscreen
                </button>
            </div>
            <?php endif; ?>
            
            <div class="game-content">
                <div class="game-description">
                    <h2>About This Game</h2>
                    <?php the_content(); ?>
                    
                    <div class="game-stats">
                        <p><strong>Published:</strong> <?php echo get_the_date(); ?></p>
                        <p><strong>Last Updated:</strong> <?php echo get_the_modified_date(); ?></p>
                        <?php if ($categories) : ?>
                            <p><strong>Category:</strong> 
                                <?php foreach ($categories as $category) : ?>
                                    <a href="<?php echo esc_url(get_category_link($category->term_id)); ?>"><?php echo esc_html($category->name); ?></a>
                                <?php endforeach; ?>
                            </p>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="game-sidebar">
                    <?php
                    $how_to_play = get_post_meta(get_the_ID(), '_how_to_play', true);
                    if ($how_to_play) :
                    ?>
                    <div class="how-to-play">
                        <h3>How to Play</h3>
                        <div class="how-to-play-content">
                            <?php echo wpautop(esc_html($how_to_play)); ?>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <?php if (is_active_sidebar('game-sidebar')) : ?>
                        <div class="game-widgets">
                            <?php dynamic_sidebar('game-sidebar'); ?>
                        </div>
                    <?php endif; ?>
                    
                    <div class="related-games">
                        <h3>Related Games</h3>
                        <?php
                        $related_args = array(
                            'post_type' => 'game',
                            'posts_per_page' => 4,
                            'post__not_in' => array(get_the_ID()),
                            'orderby' => 'rand',
                        );
                        
                        if ($categories) {
                            $related_args['category__in'] = wp_list_pluck($categories, 'term_id');
                        }
                        
                        $related_games = new WP_Query($related_args);
                        
                        if ($related_games->have_posts()) :
                            echo '<div class="related-games-grid">';
                            while ($related_games->have_posts()) : $related_games->the_post();
                                ?>
                                <div class="related-game-item">
                                    <a href="<?php the_permalink(); ?>">
                                        <?php if (has_post_thumbnail()) : ?>
                                            <?php the_post_thumbnail('game-thumbnail'); ?>
                                        <?php endif; ?>
                                        <h4><?php the_title(); ?></h4>
                                    </a>
                                </div>
                                <?php
                            endwhile;
                            echo '</div>';
                            wp_reset_postdata();
                        endif;
                        ?>
                    </div>
                </div>
            </div>
            
            <?php
            if (comments_open() || get_comments_number()) :
                comments_template();
            endif;
            ?>
        </article>
    <?php endwhile; ?>
</main>

<?php get_footer(); ?>
```

## 7. archive-game.php - Games Archive Template
```php
<?php get_header(); ?>

<main class="site-main">
    <header class="archive-header">
        <div class="container">
            <h1 class="archive-title">
                <?php
                if (is_category()) {
                    single_cat_title('Games: ');
                } elseif (is_tag()) {
                    single_tag_title('Games Tagged: ');
                } else {
                    echo 'All Games';
                }
                ?>
            </h1>
            <?php
            if (is_category() || is_tag()) {
                the_archive_description('<div class="archive-description">', '</div>');
            }
            ?>
        </div>
    </header>

    <section class="games-section">
        <div class="games-grid" id="games-grid">
            <?php if (have_posts()) : ?>
                <?php while (have_posts()) : the_post(); ?>
                    <?php get_template_part('template-parts/game-card'); ?>
                <?php endwhile; ?>
            <?php else : ?>
                <div class="no-games">
                    <h2>No games found</h2>
                    <p>Sorry, no games were found in this category.</p>
                </div>
            <?php endif; ?>
        </div>
    </section>
    
    <?php
    the_posts_navigation(array(
        'prev_text' => __('Previous Games', 'lofygame'),
        'next_text' => __('More Games', 'lofygame'),
    ));
    ?>
</main>

<?php get_footer(); ?>
```

## 8. 404.php - 404 Error Template
```php
<?php get_header(); ?>

<main class="site-main">
    <section class="error-404 not-found">
        <div class="container">
            <header class="page-header">
                <h1 class="page-title">Game Not Found</h1>
            </header>
            
            <div class="page-content">
                <p>Sorry, the game you're looking for doesn't exist. But don't worry, we have plenty of other amazing games for you to play!</p>
                
                <div class="error-404-actions">
                    <a href="<?php echo esc_url(home_url('/games')); ?>" class="button">Browse All Games</a>
                    <a href="<?php echo esc_url(home_url('/')); ?>" class="button button-secondary">Go Home</a>
                </div>
                
                <div class="popular-games">
                    <h2>Popular Games</h2>
                    <?php
                    $popular_games = new WP_Query(array(
                        'post_type' => 'game',
                        'posts_per_page' => 6,
                        'meta_key' => 'post_views_count',
                        'orderby' => 'meta_value_num',
                        'order' => 'DESC',
                    ));
                    
                    if ($popular_games->have_posts()) :
                        echo '<div class="games-grid">';
                        while ($popular_games->have_posts()) : $popular_games->the_post();
                            get_template_part('template-parts/game-card');
                        endwhile;
                        echo '</div>';
                        wp_reset_postdata();
                    endif;
                    ?>
                </div>
            </div>
        </div>
    </section>
</main>

<?php get_footer(); ?>
```

## 9. js/theme.js - JavaScript Functions
```javascript
(function($) {
    'use strict';
    
    // Theme Toggle
    const themeToggle = document.getElementById('theme-toggle');
    const themeIcon = document.querySelector('.theme-toggle-icon');
    
    // Check for saved theme preference
    const currentTheme = localStorage.getItem('lofygame-theme') || 'light';
    document.documentElement.setAttribute('data-theme', currentTheme);
    
    if (currentTheme === 'dark') {
        themeIcon.textContent = '☀️';
    }
    
    themeToggle.addEventListener('click', function() {
        const currentTheme = document.documentElement.getAttribute('data-theme');
        const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
        
        document.documentElement.setAttribute('data-theme', newTheme);
        localStorage.setItem('lofygame-theme', newTheme);
        
        themeIcon.textContent = newTheme === 'dark' ? '☀️' : '🌙';
    });
    
    // Fullscreen functionality
    const fullscreenBtn = document.getElementById('fullscreen-btn');
    const exitFullscreenBtn = document.getElementById('exit-fullscreen');
    const gameIframe = document.getElementById('game-iframe');
    
    if (fullscreenBtn && gameIframe) {
        fullscreenBtn.addEventListener('click', function() {
            enterFullscreen();
        });
        
        if (exitFullscreenBtn) {
            exitFullscreenBtn.addEventListener('click', function() {
                exitFullscreen();
            });
        }
    }
    
    function enterFullscreen() {
        gameIframe.classList.add('fullscreen-iframe');
        document.body.classList.add('fullscreen-active');
        
        if (exitFullscreenBtn) {
            exitFullscreenBtn.style.display = 'block';
        }
        
        // Hide fullscreen button
        if (fullscreenBtn) {
            fullscreenBtn.style.display = 'none';
        }
    }
    
    function exitFullscreen() {
        gameIframe.classList.remove('fullscreen-iframe');
        document.body.classList.remove('fullscreen-active');
        
        if (exitFullscreenBtn) {
            exitFullscreenBtn.style.display = 'none';
        }
        
        // Show fullscreen button
        if (fullscreenBtn) {
            fullscreenBtn.style.display = 'block';
        }
    }
    
    // ESC key to exit fullscreen
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape' && document.body.classList.contains('fullscreen-active')) {
            exitFullscreen();
        }
    });
    
    // Game filtering
    const categoryFilter = document.getElementById('category-filter');
    const tagFilter = document.getElementById('tag-filter');
    const gamesGrid = document.getElementById('games-grid');
    
    if (categoryFilter && tagFilter && gamesGrid) {
        categoryFilter.addEventListener('change', filterGames);
        tagFilter.addEventListener('change', filterGames);
    }
    
    function filterGames() {
        const category = categoryFilter.value;
        const tag = tagFilter.value;
        
        // Show loading
        gamesGrid.innerHTML = '<div class="loading-container"><div class="loading"></div><p>Loading games...</p></div>';
        
        $.ajax({
            url: lofygame_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'filter_games',
                category: category,
                tag: tag,
                nonce: lofygame_ajax.nonce
            },
            success: function(response) {
                gamesGrid.innerHTML = response;
                
                // Re-initialize hover effects
                initializeGameCards();
            },
            error: function() {
                gamesGrid.innerHTML = '<p class="error">Error loading games. Please try again.</p>';
            }
        });
    }
    
    // Initialize game card hover effects
    function initializeGameCards() {
        const gameCards = document.querySelectorAll('.game-card');
        
        gameCards.forEach(function(card) {
            card.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-4px)';
            });
            
            card.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0)';
            });
        });
    }
    
    // Initialize on page load
    document.addEventListener('DOMContentLoaded', function() {
        initializeGameCards();
    });
    
    // Lazy loading for iframes
    const gameIframes = document.querySelectorAll('.game-iframe');
    
    if ('IntersectionObserver' in window) {
        const iframeObserver = new IntersectionObserver(function(entries) {
            entries.forEach(function(entry) {
                if (entry.isIntersecting) {
                    const iframe = entry.target;
                    if (iframe.dataset.src) {
                        iframe.src = iframe.dataset.src;
                        iframe.removeAttribute('data-src');
                    }
                    iframeObserver.unobserve(iframe);
                }
            });
        });
        
        gameIframes.forEach(function(iframe) {
            iframeObserver.observe(iframe);
        });
    }
    
    // Smooth scrolling for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(function(anchor) {
        anchor.addEventListener('click', function(e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
    
    // Mobile menu toggle (if needed)
    const mobileMenuToggle = document.getElementById('mobile-menu-toggle');
    const headerMenu = document.querySelector('.header-menu');
    
    if (mobileMenuToggle && headerMenu) {
        mobileMenuToggle.addEventListener('click', function() {
            headerMenu.classList.toggle('active');
            this.classList.toggle('active');
        });
    }
    
    // Game view tracking (for SEO and analytics)
    if (document.body.classList.contains('single-game')) {
        const gameId = document.body.getAttribute('data-game-id');
        if (gameId) {
            // Track game view
            $.ajax({
                url: lofygame_ajax.ajax_url,
                type: 'POST',
                data: {
                    action: 'track_game_view',
                    game_id: gameId,
                    nonce: lofygame_ajax.nonce
                }
            });
        }
    }
    
    // Share functionality
    const shareButtons = document.querySelectorAll('.share-button');
    
    shareButtons.forEach(function(button) {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            
            const url = this.getAttribute('data-url');
            const title = this.getAttribute('data-title');
            const platform = this.getAttribute('data-platform');
            
            let shareUrl = '';
            
            switch (platform) {
                case 'facebook':
                    shareUrl = `https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(url)}`;
                    break;
                case 'twitter':
                    shareUrl = `https://twitter.com/intent/tweet?url=${encodeURIComponent(url)}&text=${encodeURIComponent(title)}`;
                    break;
                case 'pinterest':
                    shareUrl = `https://pinterest.com/pin/create/button/?url=${encodeURIComponent(url)}&description=${encodeURIComponent(title)}`;
                    break;
            }
            
            if (shareUrl) {
                window.open(shareUrl, 'share', 'width=600,height=400');
            }
        });
    });
    
})(jQuery);
```

## 10. template-parts/game-card.php - Game Card Template
```php
<article class="game-card">
    <a href="<?php the_permalink(); ?>" class="game-card-link">
        <div class="game-card-image">
            <?php if (has_post_thumbnail()) : ?>
                <?php the_post_thumbnail('game-thumbnail', array('class' => 'game-thumbnail', 'alt' => get_the_title())); ?>
            <?php else : ?>
                <img src="<?php echo get_template_directory_uri(); ?>/images/placeholder-game.jpg" alt="<?php the_title(); ?>" class="game-thumbnail">
            <?php endif; ?>
        </div>
        
        <div class="game-card-content">
            <h3 class="game-title"><?php the_title(); ?></h3>
            <p class="game-excerpt"><?php echo wp_trim_words(get_the_excerpt(), 15); ?></p>
            
            <div class="game-meta">
                <?php
                $categories = get_the_category();
                if ($categories) {
                    foreach (array_slice($categories, 0, 2) as $category) {
                        echo '<span class="game-tag">' . esc_html($category->name) . '</span>';
                    }
                }
                ?>
            </div>
        </div>
    </a>
</article>
```

## 11. Additional SEO Optimization Functions (add to functions.php)
```php
/**
 * Advanced SEO Functions for #1 Google Ranking
 */

// Track game views for popularity
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

// Generate XML sitemap for games
function lofygame_generate_games_sitemap() {
    $games = get_posts(array(
        'post_type' => 'game',
        'posts_per_page' => -1,
        'post_status' => 'publish'
    ));
    
    $sitemap = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
    $sitemap .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
    
    foreach ($games as $game) {
        $sitemap .= '<url>' . "\n";
        $sitemap .= '<loc>' . get_permalink($game->ID) . '</loc>' . "\n";
        $sitemap .= '<lastmod>' . get_the_modified_date('c', $game->ID) . '</lastmod>' . "\n";
        $sitemap .= '<changefreq>weekly</changefreq>' . "\n";
        $sitemap .= '<priority>0.8</priority>' . "\n";
        $sitemap .= '</url>' . "\n";
    }
    
    $sitemap .= '</urlset>';
    
    return $sitemap;
}

// Add robots.txt optimization
function lofygame_robots_txt($output) {
    $output .= "User-agent: *\n";
    $output .= "Allow: /\n";
    $output .= "Sitemap: " . home_url('/sitemap.xml') . "\n";
    return $output;
}
add_filter('robots_txt', 'lofygame_robots_txt');

// Add breadcrumbs
function lofygame_breadcrumbs() {
    if (is_home() || is_front_page()) return;
    
    $breadcrumbs = '<nav class="breadcrumbs" aria-label="Breadcrumb">';
    $breadcrumbs .= '<a href="' . home_url() . '">Home</a>';
    
    if (is_singular('game')) {
        $breadcrumbs .= ' > <a href="' . get_post_type_archive_link('game') . '">Games</a>';
        $breadcrumbs .= ' > <span>' . get_the_title() . '</span>';
    } elseif (is_post_type_archive('game')) {
        $breadcrumbs .= ' > <span>Games</span>';
    }
    
    $breadcrumbs .= '</nav>';
    
    return $breadcrumbs;
}
```

## 12. Performance Optimization (add to functions.php)
```php
/**
 * Performance Optimizations
 */

// Optimize WordPress for speed
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

// Add critical CSS inline
function lofygame_add_critical_css() {
    if (is_singular('game') || is_post_type_archive('game')) {
        echo '<style id="critical-css">';
        echo 'body{margin:0;padding:0;font-family:Inter,sans-serif}';
        echo '.site-header{background:linear-gradient(135deg,#4f46e5,#06b6d4);padding:1rem 0}';
        echo '.game-card{background:#f8fafc;border-radius:12px;transition:transform 0.3s ease}';
        echo '</style>';
    }
}
add_action('wp_head', 'lofygame_add_critical_css', 1);

// Defer non-critical CSS
function lofygame_defer_css($html, $handle) {
    if (is_admin()) return $html;
    
    if ($handle !== 'lofygame-style') {
        $html = str_replace("rel='stylesheet'", "rel='preload' as='style' onload=\"this.onload=null;this.rel='stylesheet'\"", $html);
    }
    
    return $html;
}
add_filter('style_loader_tag', 'lofygame_defer_css', 10, 2);
```

## SEO Recommendations to Rank #1 on Google

### 1. **Content Strategy**
- Create comprehensive game descriptions with 300+ words
- Include "how to play" guides for each game
- Add game reviews and ratings
- Create category pages with detailed descriptions
- Write gaming tips and strategies blog posts

### 2. **Technical SEO**
- Implement lazy loading for iframes
- Use WebP images with fallbacks
- Enable GZIP compression
- Set up proper caching headers
- Implement Service Worker for offline functionality

### 3. **Keywords Strategy**
- Target long-tail keywords: "free online [game name]", "play [game name] online"
- Use location-based keywords: "best online games 2025"
- Include gaming categories: "puzzle games", "action games", "strategy games"
- Target trending game names and genres

### 4. **User Experience**
- Implement infinite scroll for game listings
- Add game ratings and reviews system
- Create user accounts and favorites
- Add social sharing buttons
- Implement game progress saving

### 5. **Performance**
- Use CDN for images and static assets
- Implement database caching
- Optimize images automatically
- Use AMP for mobile pages
- Implement preloading for game iframes

### 6. **Link Building**
- Create gaming guides and tutorials
- Submit to gaming directories
- Guest post on gaming blogs
- Create embeddable game widgets
- Build relationships with game developers

### 7. **Analytics & Monitoring**
- Track Core Web Vitals
- Monitor keyword rankings
- Analyze user behavior
- A/B test game layouts
- Monitor site speed regularly

This theme provides a solid foundation for a gaming website that can compete with Poki.com while being optimized for search engines and user experience.