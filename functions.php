<?php
/**
 * LofyGame Theme Functions - Main File
 * This file loads all modular function files
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Define theme constants
define('LOFYGAME_THEME_VERSION', '2.0');
define('LOFYGAME_THEME_PATH', get_template_directory());
define('LOFYGAME_THEME_URL', get_template_directory_uri());

/**
 * Load all function modules
 */
function lofygame_load_modules() {
    $modules = array(
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
        'includes/utility-functions.php',    // Utility and helper functions
    );

    foreach ($modules as $module) {
        $file_path = LOFYGAME_THEME_PATH . '/' . $module;
        if (file_exists($file_path)) {
            require_once $file_path;
        }
    }
}

// Load all modules
lofygame_load_modules();

/**
 * Theme initialization
 */
function lofygame_init() {
    // Initialize theme features
    if (function_exists('lofygame_setup')) {
        lofygame_setup();
    }
    
    // Initialize rating system
    if (function_exists('lofygame_initialize_game_ratings')) {
        lofygame_initialize_game_ratings();
    }
    
    // Register post types
    if (function_exists('lofygame_register_game_post_type')) {
        lofygame_register_game_post_type();
    }
}
add_action('after_setup_theme', 'lofygame_init');

/**
 * Enqueue scripts and styles
 */
function lofygame_scripts() {
    wp_enqueue_style('lofygame-style', get_stylesheet_uri(), array(), LOFYGAME_THEME_VERSION);
    wp_enqueue_style('google-fonts', 'https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap', array(), null);
    
    wp_enqueue_script('lofygame-script', LOFYGAME_THEME_URL . '/js/theme.js', array('jquery'), LOFYGAME_THEME_VERSION, true);
    
    // Localize script for AJAX
    wp_localize_script('lofygame-script', 'lofygame_ajax', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('lofygame_nonce')
    ));
}
add_action('wp_enqueue_scripts', 'lofygame_scripts');
?>