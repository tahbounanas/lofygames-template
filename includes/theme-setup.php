<?php
/**
 * Theme Setup and Configuration
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
        'blog-menu' => __('Blog Menu', 'lofygame'),
    ));
    
    // Add image sizes
    add_image_size('game-thumbnail', 400, 400, true); // Square thumbnails
    add_image_size('blog-thumbnail', 600, 400, true);
    add_image_size('category-thumbnail', 300, 200, true); // Category images
}

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
    
    register_sidebar(array(
        'name' => 'Blog Sidebar',
        'id' => 'blog-sidebar',
        'description' => 'Sidebar for blog pages',
        'before_widget' => '<div class="widget %2$s">',
        'after_widget' => '</div>',
        'before_title' => '<h3 class="widget-title">',
        'after_title' => '</h3>',
    ));
}
add_action('widgets_init', 'lofygame_widgets_init');

/**
 * Add Custom CSS Classes to Body
 */
function lofygame_body_classes($classes) {
    if (is_post_type_archive('game') || is_singular('game')) {
        $classes[] = 'games-page';
    }
    
    if (is_singular('game')) {
        $classes[] = 'single-game';
        $classes[] = 'game-id-' . get_the_ID();
    }
    
    if (is_page_template('blog.php') || is_singular('post')) {
        $classes[] = 'blog-page';
    }
    
    return $classes;
}
add_filter('body_class', 'lofygame_body_classes');

/**
 * Fallback Menu for Header
 */
function lofygame_fallback_menu() {
    echo '<ul class="header-menu">';
    echo '<li><a href="' . home_url('/') . '">Home</a></li>';
    echo '<li><a href="' . get_post_type_archive_link('game') . '">Games</a></li>';
    echo '<li><a href="' . home_url('/blog') . '">Blog</a></li>';
    echo '</ul>';
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
 * Flush Rewrite Rules on Theme Activation
 */
function lofygame_flush_rewrite_rules() {
    lofygame_register_game_post_type();
    flush_rewrite_rules();
}
add_action('after_switch_theme', 'lofygame_flush_rewrite_rules');

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

/**
 * Fix pagination 404 errors
 */
function lofygame_fix_pagination_404() {
    if (is_404() && is_paged()) {
        global $wp_query;
        $wp_query->is_404 = false;
        status_header(200);
    }
}
add_action('template_redirect', 'lofygame_fix_pagination_404');

/**
 * Add pagination support
 */
function lofygame_pagination_support() {
    add_theme_support('pagination');
}
add_action('after_setup_theme', 'lofygame_pagination_support');
?>