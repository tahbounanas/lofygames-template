<?php
/**
 * LofyGame Theme Functions - Fixed Pagination and Enhanced Import/Export
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
add_action('after_setup_theme', 'lofygame_setup');

/**
 * FIXED: Proper pagination handling for home page and archive pages
 */
function lofygame_fix_pagination($query) {
    if (!is_admin() && $query->is_main_query()) {
        // Home page - show only games with proper pagination
        if (is_home() && !is_front_page()) {
            $query->set('post_type', array('game'));
            $query->set('posts_per_page', 204); // 12 per row × 17 rows
            // Don't override paged parameter - let WordPress handle it
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
 * Enqueue Scripts and Styles
 */
function lofygame_scripts() {
    wp_enqueue_style('lofygame-style', get_stylesheet_uri(), array(), '1.0.2');
    wp_enqueue_style('google-fonts', 'https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap', array(), null);
    
    wp_enqueue_script('lofygame-script', get_template_directory_uri() . '/js/theme.js', array('jquery'), '1.0.2', true);
    
    // Localize script for AJAX
    wp_localize_script('lofygame-script', 'lofygame_ajax', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('lofygame_nonce')
    ));
}
add_action('wp_enqueue_scripts', 'lofygame_scripts');

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
    $game_width = get_post_meta($post->ID, '_game_width', true);
    $game_height = get_post_meta($post->ID, '_game_height', true);
    $walkthrough_url = get_post_meta($post->ID, '_walkthrough_url', true);
    $gamemonetize_video_id = get_post_meta($post->ID, '_gamemonetize_video_id', true);
    $video_walkthrough_enabled = get_post_meta($post->ID, '_video_walkthrough_enabled', true);
    $preview_video_url = get_post_meta($post->ID, '_preview_video_url', true);
    
    // Set default dimensions if empty
    if (empty($game_width)) $game_width = '800';
    if (empty($game_height)) $game_height = '600';
    
    echo '<table class="form-table">';
    echo '<tr>';
    echo '<th><label for="game_url">Game URL</label></th>';
    echo '<td><input type="url" id="game_url" name="game_url" value="' . esc_attr($game_url) . '" class="regular-text" placeholder="https://html5.gamemonetize.co/your-game-id/" /></td>';
    echo '</tr>';
    
    // MP4 Preview Video Section
    echo '<tr>';
    echo '<th><label for="preview_video_url">Preview Video (MP4)</label></th>';
    echo '<td>';
    echo '<input type="url" id="preview_video_url" name="preview_video_url" value="' . esc_attr($preview_video_url) . '" class="regular-text" placeholder="https://example.com/video-preview.mp4" />';
    echo '<br><small>MP4 video URL that plays on hover over game image (recommended: 10-30 seconds)</small>';
    echo '</td>';
    echo '</tr>';
    
    // Video Walkthrough Section
    echo '<tr>';
    echo '<th><label for="video_walkthrough_enabled">Video Walkthrough</label></th>';
    echo '<td>';
    echo '<label><input type="checkbox" id="video_walkthrough_enabled" name="video_walkthrough_enabled" value="1" ' . checked($video_walkthrough_enabled, 1, false) . '> Enable GameMonetize Video Walkthrough</label>';
    echo '<br><small>Show embedded video walkthrough from GameMonetize.com</small>';
    echo '</td>';
    echo '</tr>';
    
    echo '<tr id="video_id_row" style="' . (!$video_walkthrough_enabled ? 'display:none;' : '') . '">';
    echo '<th><label for="gamemonetize_video_id">GameMonetize Video ID</label></th>';
    echo '<td>';
    echo '<input type="text" id="gamemonetize_video_id" name="gamemonetize_video_id" value="' . esc_attr($gamemonetize_video_id) . '" class="regular-text" placeholder="4kci7og3klgj0ivy2wz3gdvd9dth5e7n" />';
    echo '<br><small>Enter the GameMonetize video ID for this game\'s walkthrough</small>';
    echo '</td>';
    echo '</tr>';
    
    echo '<tr>';
    echo '<th><label for="walkthrough_url">External Walkthrough URL</label></th>';
    echo '<td>';
    echo '<input type="url" id="walkthrough_url" name="walkthrough_url" value="' . esc_attr($walkthrough_url) . '" class="regular-text" placeholder="https://gamemonetize.com/game-walkthrough" />';
    echo '<br><small>Optional: Link to external walkthrough or tutorial</small>';
    echo '</td>';
    echo '</tr>';
    
    echo '<tr>';
    echo '<th><label for="game_dimensions">Game Dimensions</label></th>';
    echo '<td>';
    echo '<input type="number" id="game_width" name="game_width" value="' . esc_attr($game_width) . '" min="300" max="1920" style="width: 100px;" placeholder="800" /> px (Width)';
    echo ' × ';
    echo '<input type="number" id="game_height" name="game_height" value="' . esc_attr($game_height) . '" min="200" max="1080" style="width: 100px;" placeholder="600" /> px (Height)';
    echo '<br><small>Recommended: 800x600 for desktop, 320x480 for mobile games</small>';
    echo '</td>';
    echo '</tr>';
    
    echo '<tr>';
    echo '<th><label for="how_to_play">How to Play</label></th>';
    echo '<td><textarea id="how_to_play" name="how_to_play" rows="5" cols="50" class="large-text">' . esc_textarea($how_to_play) . '</textarea></td>';
    echo '</tr>';
    echo '</table>';
    
    // Quick dimension presets
    echo '<div style="margin-top: 15px; padding: 10px; background: #f9f9f9; border-radius: 5px;">';
    echo '<strong>Quick Presets:</strong> ';
    echo '<button type="button" class="button button-small" onclick="setDimensions(800, 600)">Desktop (800×600)</button> ';
    echo '<button type="button" class="button button-small" onclick="setDimensions(1024, 768)">Large (1024×768)</button> ';
    echo '<button type="button" class="button button-small" onclick="setDimensions(320, 480)">Mobile (320×480)</button> ';
    echo '<button type="button" class="button button-small" onclick="setDimensions(480, 320)">Mobile Landscape (480×320)</button>';
    echo '</div>';
    
    // JavaScript for functionality
    echo '<script>
    function setDimensions(width, height) {
        document.getElementById("game_width").value = width;
        document.getElementById("game_height").value = height;
    }
    
    jQuery(document).ready(function($) {
        // Toggle video ID field based on checkbox
        $("#video_walkthrough_enabled").change(function() {
            if ($(this).is(":checked")) {
                $("#video_id_row").show();
            } else {
                $("#video_id_row").hide();
            }
        });
        
        // Auto-extract video ID from game URL
        $("#game_url").on("blur", function() {
            var gameUrl = $(this).val();
            var videoIdField = $("#gamemonetize_video_id");
            
            if (gameUrl && !videoIdField.val()) {
                var gameIdMatch = gameUrl.match(/gamemonetize\.co\/([a-zA-Z0-9]+)/);
                if (gameIdMatch) {
                    videoIdField.val(gameIdMatch[1]);
                    videoIdField.attr("placeholder", "Auto-filled from Game URL");
                }
            }
        });
        
        // Preview MP4 video
        if ($("#preview_video_url").length) {
            $("#preview_video_url").after("<button type=\"button\" id=\"preview-mp4\" class=\"button\" style=\"margin-left: 10px;\">Preview Video</button>");
            
            $("#preview-mp4").on("click", function(e) {
                e.preventDefault();
                var videoUrl = $("#preview_video_url").val();
                
                if (videoUrl) {
                    // Create preview modal
                    var modal = $("<div>").css({
                        position: "fixed",
                        top: 0,
                        left: 0,
                        width: "100%",
                        height: "100%",
                        backgroundColor: "rgba(0,0,0,0.8)",
                        zIndex: 100000,
                        display: "flex",
                        alignItems: "center",
                        justifyContent: "center"
                    });
                    
                    var videoContainer = $("<div>").css({
                        backgroundColor: "white",
                        padding: "20px",
                        borderRadius: "8px",
                        maxWidth: "800px",
                        width: "90%"
                    });
                    
                    var closeBtn = $("<button>").text("Close").css({
                        float: "right",
                        marginBottom: "10px"
                    }).addClass("button");
                    
                    var video = $("<video>").attr({
                        src: videoUrl,
                        controls: true,
                        autoplay: true,
                        style: "width: 100%; max-height: 400px;"
                    });
                    
                    videoContainer.append(closeBtn, video);
                    modal.append(videoContainer);
                    $("body").append(modal);
                    
                    // Close modal
                    closeBtn.on("click", function() {
                        modal.remove();
                    });
                    
                    modal.on("click", function(e) {
                        if (e.target === this) {
                            modal.remove();
                        }
                    });
                } else {
                    alert("Please enter a video URL first.");
                }
            });
        }
    });
    </script>';
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
    
    // Save all meta fields
    if (isset($_POST['game_url'])) {
        update_post_meta($post_id, '_game_url', sanitize_url($_POST['game_url']));
    }
    
    if (isset($_POST['preview_video_url'])) {
        update_post_meta($post_id, '_preview_video_url', sanitize_url($_POST['preview_video_url']));
    }
    
    if (isset($_POST['walkthrough_url'])) {
        update_post_meta($post_id, '_walkthrough_url', sanitize_url($_POST['walkthrough_url']));
    }
    
    if (isset($_POST['gamemonetize_video_id'])) {
        update_post_meta($post_id, '_gamemonetize_video_id', sanitize_text_field($_POST['gamemonetize_video_id']));
    }
    
    $video_enabled = isset($_POST['video_walkthrough_enabled']) ? 1 : 0;
    update_post_meta($post_id, '_video_walkthrough_enabled', $video_enabled);
    
    if (isset($_POST['how_to_play'])) {
        update_post_meta($post_id, '_how_to_play', sanitize_textarea_field($_POST['how_to_play']));
    }
    
    if (isset($_POST['game_width'])) {
        $width = intval($_POST['game_width']);
        $width = ($width >= 300 && $width <= 1920) ? $width : 800;
        update_post_meta($post_id, '_game_width', $width);
    }
    
    if (isset($_POST['game_height'])) {
        $height = intval($_POST['game_height']);
        $height = ($height >= 200 && $height <= 1080) ? $height : 600;
        update_post_meta($post_id, '_game_height', $height);
    }
}
add_action('save_post', 'lofygame_save_game_details');

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
 * Auto-set Featured Image from Content (Enhanced Version)
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
    
    // Look for images in content
    preg_match_all('/<img[^>]+src=[\'"]([^\'"]+)[\'"][^>]*>/i', $content, $matches);
    
    if (!empty($matches[1])) {
        $image_url = $matches[1][0]; // Get first image
        
        // Check if it's a local image or external
        $upload_dir = wp_upload_dir();
        
        if (strpos($image_url, $upload_dir['baseurl']) !== false) {
            // Local image - get attachment ID
            $attachment_id = attachment_url_to_postid($image_url);
            if ($attachment_id) {
                set_post_thumbnail($post_id, $attachment_id);
            }
        } else {
            // External image - download and set as featured
            $image_data = lofygame_download_external_image($image_url, $post_id);
            if ($image_data) {
                set_post_thumbnail($post_id, $image_data);
            }
        }
    }
}
add_action('save_post', 'lofygame_auto_set_featured_image');

/**
 * Download External Image and Add to Media Library
 */
function lofygame_download_external_image($image_url, $post_id) {
    require_once(ABSPATH . 'wp-admin/includes/file.php');
    require_once(ABSPATH . 'wp-admin/includes/media.php');
    require_once(ABSPATH . 'wp-admin/includes/image.php');
    
    $tmp = download_url($image_url);
    
    if (is_wp_error($tmp)) {
        return false;
    }
    
    $file_array = array();
    $file_array['name'] = basename($image_url);
    $file_array['tmp_name'] = $tmp;
    
    // Check file type
    $file_type = wp_check_filetype($file_array['name']);
    if (!$file_type['ext']) {
        $file_array['name'] = 'game-image-' . $post_id . '.jpg';
    }
    
    $attachment_id = media_handle_sideload($file_array, $post_id);
    
    if (is_wp_error($attachment_id)) {
        @unlink($file_array['tmp_name']);
        return false;
    }
    
    return $attachment_id;
}

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
 * Add Admin Columns for Games
 */
function lofygame_game_columns($columns) {
    $columns['game_url'] = __('Game URL');
    $columns['walkthrough'] = __('Walkthrough');
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
            
        case 'walkthrough':
            $walkthrough_url = get_post_meta($post_id, '_walkthrough_url', true);
            if ($walkthrough_url) {
                echo '<a href="' . esc_url($walkthrough_url) . '" target="_blank" title="' . esc_attr($walkthrough_url) . '">📚 View</a>';
            } else {
                echo '<span style="color: #999;">—</span>';
            }
            break;
            
        case 'views':
            $views = get_post_meta($post_id, 'post_views_count', true);
            echo $views ? number_format($views) : '0';
            break;
    }
}
add_action('admin_post_lofygame_export_games', 'lofygame_handle_export');

/**
 * Handle Game Import (existing function)
 */
function lofygame_handle_import() {
    if (!current_user_can('manage_options') || !wp_verify_nonce($_POST['lofygame_import_nonce'], 'lofygame_import')) {
        wp_die('Unauthorized');
    }
    
    if (!isset($_FILES['import_file']) || $_FILES['import_file']['error'] !== UPLOAD_ERR_OK) {
        wp_die('File upload error');
    }
    
    $file = $_FILES['import_file']['tmp_name'];
    $imported = 0;
    $errors = array();
    $skipped = 0;
    
    // Validate file size (10MB max)
    if ($_FILES['import_file']['size'] > 10 * 1024 * 1024) {
        wp_die('File too large. Maximum size is 10MB.');
    }
    
    if (($handle = fopen($file, 'r')) !== FALSE) {
        $header = fgetcsv($handle);
        
        // Validate required columns
        $required_columns = array('title', 'content', 'game_url', 'how_to_play');
        $missing_columns = array();
        
        foreach ($required_columns as $required) {
            if (!in_array($required, $header)) {
                $missing_columns[] = $required;
            }
        }
        
        if (!empty($missing_columns)) {
            wp_die('Missing required columns: ' . implode(', ', $missing_columns));
        }
        
        // Map column positions
        $columns = array();
        foreach ($header as $index => $column_name) {
            $columns[trim($column_name)] = $index;
        }
        
        $row_number = 1;
        
        while (($data = fgetcsv($handle)) !== FALSE) {
            $row_number++;
            
            // Get values with fallbacks
            $title = isset($columns['title']) && isset($data[$columns['title']]) ? trim($data[$columns['title']]) : '';
            $content = isset($columns['content']) && isset($data[$columns['content']]) ? trim($data[$columns['content']]) : '';
            $game_url = isset($columns['game_url']) && isset($data[$columns['game_url']]) ? trim($data[$columns['game_url']]) : '';
            $preview_video = isset($columns['preview_video']) && isset($data[$columns['preview_video']]) ? trim($data[$columns['preview_video']]) : '';
            $walkthrough_url = isset($columns['walkthrough_url']) && isset($data[$columns['walkthrough_url']]) ? trim($data[$columns['walkthrough_url']]) : '';
            $video_id = isset($columns['video_id']) && isset($data[$columns['video_id']]) ? trim($data[$columns['video_id']]) : '';
            $video_enabled = isset($columns['video_enabled']) && isset($data[$columns['video_enabled']]) ? trim($data[$columns['video_enabled']]) : 'false';
            $how_to_play = isset($columns['how_to_play']) && isset($data[$columns['how_to_play']]) ? trim($data[$columns['how_to_play']]) : '';
            $categories = isset($columns['categories']) && isset($data[$columns['categories']]) ? trim($data[$columns['categories']]) : '';
            $tags = isset($columns['tags']) && isset($data[$columns['tags']]) ? trim($data[$columns['tags']]) : '';
            $image_url = isset($columns['image_url']) && isset($data[$columns['image_url']]) ? trim($data[$columns['image_url']]) : '';
            $width = isset($columns['width']) && isset($data[$columns['width']]) ? intval($data[$columns['width']]) : 800;
            $height = isset($columns['height']) && isset($data[$columns['height']]) ? intval($data[$columns['height']]) : 600;
            $excerpt = isset($columns['excerpt']) && isset($data[$columns['excerpt']]) ? trim($data[$columns['excerpt']]) : '';
            
            // Validate required fields
            if (empty($title)) {
                $errors[] = "Row {$row_number}: Missing title";
                $skipped++;
                continue;
            }
            
            if (empty($game_url)) {
                $errors[] = "Row {$row_number}: Missing game_url for '{$title}'";
                $skipped++;
                continue;
            }
            
            // Validate URL format
            if (!filter_var($game_url, FILTER_VALIDATE_URL)) {
                $errors[] = "Row {$row_number}: Invalid game_url format for '{$title}'";
                $skipped++;
                continue;
            }
            
            // Check for duplicate
            $existing = get_posts(array(
                'post_type' => 'game',
                'post_status' => 'any',
                'meta_query' => array(
                    array(
                        'key' => '_game_url',
                        'value' => $game_url,
                        'compare' => '='
                    )
                ),
                'posts_per_page' => 1
            ));
            
            if (!empty($existing)) {
                $errors[] = "Row {$row_number}: Game with URL '{$game_url}' already exists: '{$title}'";
                $skipped++;
                continue;
            }
            
            // Process video_enabled
            $video_enabled = in_array(strtolower($video_enabled), array('true', '1', 'yes', 'on'));
            
            // Auto-enable video if video ID is provided
            if (!empty($video_id) && !$video_enabled) {
                $video_enabled = true;
            }
            
            // Validate dimensions
            if ($width < 300 || $width > 1920) $width = 800;
            if ($height < 200 || $height > 1080) $height = 600;
            
            // Process content with image
            $final_content = $content;
            if (!empty($image_url) && !empty($content)) {
                $final_content = '<img src="' . esc_url($image_url) . '" alt="' . esc_attr($title) . '" class="game-thumbnail" />' . "\n\n" . $content;
            } elseif (!empty($image_url) && empty($content)) {
                $final_content = '<img src="' . esc_url($image_url) . '" alt="' . esc_attr($title) . '" class="game-thumbnail" />';
            }
            
            // Create post
            $post_data = array(
                'post_title' => sanitize_text_field($title),
                'post_content' => wp_kses_post($final_content),
                'post_excerpt' => sanitize_textarea_field($excerpt),
                'post_type' => 'game',
                'post_status' => 'publish'
            );
            
            $post_id = wp_insert_post($post_data);
            
            if ($post_id && !is_wp_error($post_id)) {
                // Save all meta data
                update_post_meta($post_id, '_game_url', sanitize_url($game_url));
                
                if (!empty($preview_video)) {
                    update_post_meta($post_id, '_preview_video_url', sanitize_url($preview_video));
                }
                
                if (!empty($walkthrough_url)) {
                    update_post_meta($post_id, '_walkthrough_url', sanitize_url($walkthrough_url));
                }
                
                if (!empty($video_id)) {
                    update_post_meta($post_id, '_gamemonetize_video_id', sanitize_text_field($video_id));
                }
                
                update_post_meta($post_id, '_video_walkthrough_enabled', $video_enabled ? 1 : 0);
                
                if (!empty($how_to_play)) {
                    update_post_meta($post_id, '_how_to_play', sanitize_textarea_field($how_to_play));
                }
                
                update_post_meta($post_id, '_game_width', $width);
                update_post_meta($post_id, '_game_height', $height);
                
                // Handle featured image
                if (!empty($image_url)) {
                    $attachment_id = lofygame_download_external_image($image_url, $post_id);
                    if ($attachment_id) {
                        set_post_thumbnail($post_id, $attachment_id);
                    }
                }
                
                // Process categories
                if (!empty($categories)) {
                    $cat_ids = array();
                    $category_names = array_map('trim', explode(',', $categories));
                    
                    foreach ($category_names as $cat_name) {
                        if (!empty($cat_name)) {
                            $cat = get_category_by_slug(sanitize_title($cat_name));
                            if (!$cat) {
                                $cat_id = wp_create_category($cat_name);
                            } else {
                                $cat_id = $cat->term_id;
                            }
                            if ($cat_id) {
                                $cat_ids[] = $cat_id;
                            }
                        }
                    }
                    
                    if (!empty($cat_ids)) {
                        wp_set_post_categories($post_id, $cat_ids);
                    }
                }
                
                // Process tags
                if (!empty($tags)) {
                    $tag_names = array();
                    $tag_list = array_map('trim', explode(',', $tags));
                    
                    foreach ($tag_list as $tag_name) {
                        if (!empty($tag_name)) {
                            $tag_names[] = $tag_name;
                        }
                    }
                    
                    if (!empty($tag_names)) {
                        wp_set_post_tags($post_id, $tag_names);
                    }
                }
                
                $imported++;
            } else {
                $error_message = is_wp_error($post_id) ? $post_id->get_error_message() : 'Unknown error';
                $errors[] = "Row {$row_number}: Failed to import '{$title}': {$error_message}";
                $skipped++;
            }
        }
        
        fclose($handle);
    } else {
        wp_die('Could not open CSV file');
    }
    
    // Prepare redirect with results
    $redirect_url = admin_url('edit.php?post_type=game&page=lofygame-import-export');
    $redirect_url = add_query_arg(array(
        'imported' => $imported,
        'errors' => count($errors),
        'skipped' => $skipped,
        'total_rows' => $row_number - 1
    ), $redirect_url);
    
    // Store errors in transient if any
    if (!empty($errors)) {
        set_transient('lofygame_import_errors', $errors, 300); // 5 minutes
    }
    
    wp_redirect($redirect_url);
    exit;
}
add_action('admin_post_lofygame_import_games', 'lofygame_handle_import');

/**
 * Display Import Results (Enhanced)
 */
function lofygame_import_admin_notices() {
    // Game import results
    if (isset($_GET['imported'])) {
        $imported = intval($_GET['imported']);
        $errors = isset($_GET['errors']) ? intval($_GET['errors']) : 0;
        $skipped = isset($_GET['skipped']) ? intval($_GET['skipped']) : 0;
        $total = isset($_GET['total_rows']) ? intval($_GET['total_rows']) : 0;
        
        $notice_class = 'notice-success';
        if ($errors > 0 && $imported === 0) {
            $notice_class = 'notice-error';
        } elseif ($errors > 0) {
            $notice_class = 'notice-warning';
        }
        
        echo '<div class="notice ' . $notice_class . ' is-dismissible">';
        echo '<h3>🎮 Game Import Results</h3>';
        echo '<p><strong>Successfully imported:</strong> ' . $imported . ' games</p>';
        
        if ($total > 0) {
            echo '<p><strong>Total rows processed:</strong> ' . $total . '</p>';
        }
        
        if ($skipped > 0) {
            echo '<p><strong>Skipped:</strong> ' . $skipped . ' rows</p>';
        }
        
        if ($errors > 0) {
            echo '<p><strong>Errors:</strong> ' . $errors . '</p>';
            
            // Show error details
            $error_details = get_transient('lofygame_import_errors');
            if ($error_details) {
                echo '<details style="margin-top: 10px;">';
                echo '<summary style="cursor: pointer; font-weight: bold;">View Error Details</summary>';
                echo '<ul style="margin: 10px 0; padding-left: 20px;">';
                foreach (array_slice($error_details, 0, 10) as $error) {
                    echo '<li>' . esc_html($error) . '</li>';
                }
                if (count($error_details) > 10) {
                    echo '<li><em>... and ' . (count($error_details) - 10) . ' more errors</em></li>';
                }
                echo '</ul>';
                echo '</details>';
                
                delete_transient('lofygame_import_errors');
            }
        }
        
        if ($imported > 0) {
            echo '<p><a href="' . admin_url('edit.php?post_type=game') . '" class="button button-primary">View Imported Games</a></p>';
        }
        
        echo '</div>';
    }
    
    // Category import results
    if (isset($_GET['imported_categories'])) {
        $imported = intval($_GET['imported_categories']);
        $errors = isset($_GET['errors']) ? intval($_GET['errors']) : 0;
        $skipped = isset($_GET['skipped']) ? intval($_GET['skipped']) : 0;
        
        $notice_class = $errors > 0 && $imported === 0 ? 'notice-error' : ($errors > 0 ? 'notice-warning' : 'notice-success');
        
        echo '<div class="notice ' . $notice_class . ' is-dismissible">';
        echo '<h3>📂 Category Import Results</h3>';
        echo '<p><strong>Successfully imported:</strong> ' . $imported . ' categories</p>';
        
        if ($skipped > 0) {
            echo '<p><strong>Skipped:</strong> ' . $skipped . ' rows</p>';
        }
        
        if ($errors > 0) {
            echo '<p><strong>Errors:</strong> ' . $errors . '</p>';
            
            $error_details = get_transient('lofygame_import_category_errors');
            if ($error_details) {
                echo '<details style="margin-top: 10px;">';
                echo '<summary style="cursor: pointer; font-weight: bold;">View Error Details</summary>';
                echo '<ul style="margin: 10px 0; padding-left: 20px;">';
                foreach (array_slice($error_details, 0, 10) as $error) {
                    echo '<li>' . esc_html($error) . '</li>';
                }
                echo '</ul>';
                echo '</details>';
                
                delete_transient('lofygame_import_category_errors');
            }
        }
        
        if ($imported > 0) {
            echo '<p><a href="' . admin_url('edit-tags.php?taxonomy=category') . '" class="button button-primary">View Categories</a></p>';
        }
        
        echo '</div>';
    }
    
    // Tag import results
    if (isset($_GET['imported_tags'])) {
        $imported = intval($_GET['imported_tags']);
        $errors = isset($_GET['errors']) ? intval($_GET['errors']) : 0;
        $skipped = isset($_GET['skipped']) ? intval($_GET['skipped']) : 0;
        
        $notice_class = $errors > 0 && $imported === 0 ? 'notice-error' : ($errors > 0 ? 'notice-warning' : 'notice-success');
        
        echo '<div class="notice ' . $notice_class . ' is-dismissible">';
        echo '<h3>🏷️ Tag Import Results</h3>';
        echo '<p><strong>Successfully imported:</strong> ' . $imported . ' tags</p>';
        
        if ($skipped > 0) {
            echo '<p><strong>Skipped:</strong> ' . $skipped . ' rows</p>';
        }
        
        if ($errors > 0) {
            echo '<p><strong>Errors:</strong> ' . $errors . '</p>';
            
            $error_details = get_transient('lofygame_import_tag_errors');
            if ($error_details) {
                echo '<details style="margin-top: 10px;">';
                echo '<summary style="cursor: pointer; font-weight: bold;">View Error Details</summary>';
                echo '<ul style="margin: 10px 0; padding-left: 20px;">';
                foreach (array_slice($error_details, 0, 10) as $error) {
                    echo '<li>' . esc_html($error) . '</li>';
                }
                echo '</ul>';
                echo '</details>';
                
                delete_transient('lofygame_import_tag_errors');
            }
        }
        
        if ($imported > 0) {
            echo '<p><a href="' . admin_url('edit-tags.php?taxonomy=post_tag') . '" class="button button-primary">View Tags</a></p>';
        }
        
        echo '</div>';
    }
}
add_action('admin_notices', 'lofygame_import_admin_notices');

/**
 * Add category image support
 */
function lofygame_add_category_image_field($term) {
    ?>
    <div class="form-field term-image-wrap">
        <label for="category-image-url">Category Image URL</label>
        <input type="url" id="category-image-url" name="category_image" value="" placeholder="https://example.com/category-image.jpg" />
        <p>Enter the URL of an image to represent this category.</p>
    </div>
    <?php
}
add_action('category_add_form_fields', 'lofygame_add_category_image_field');

function lofygame_edit_category_image_field($term) {
    $image_url = get_term_meta($term->term_id, 'category_image', true);
    ?>
    <tr class="form-field term-image-wrap">
        <th scope="row">
            <label for="category-image-url">Category Image URL</label>
        </th>
        <td>
            <input type="url" id="category-image-url" name="category_image" value="<?php echo esc_attr($image_url); ?>" placeholder="https://example.com/category-image.jpg" />
            <p class="description">Enter the URL of an image to represent this category.</p>
            <?php if ($image_url) : ?>
                <div style="margin-top: 10px;">
                    <img src="<?php echo esc_url($image_url); ?>" alt="Category Image" style="max-width: 200px; height: auto; border-radius: 8px;" />
                </div>
            <?php endif; ?>
        </td>
    </tr>
    <?php
}
add_action('category_edit_form_fields', 'lofygame_edit_category_image_field');

function lofygame_save_category_image($term_id) {
    if (isset($_POST['category_image'])) {
        update_term_meta($term_id, 'category_image', sanitize_url($_POST['category_image']));
    }
}
add_action('created_category', 'lofygame_save_category_image');
add_action('edited_category', 'lofygame_save_category_image');

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


add_action('manage_game_posts_custom_column', 'lofygame_game_column_content', 10, 2);

/**
 * Make Admin Columns Sortable
 */
function lofygame_sortable_columns($columns) {
    $columns['views'] = 'views';
    return $columns;
}
add_filter('manage_edit-game_sortable_columns', 'lofygame_sortable_columns');

/**
 * Fix pagination for different page types
 */
function lofygame_fix_pagination_404() {
    // Fix pagination 404 errors
    if (is_404() && is_paged()) {
        global $wp_query;
        $wp_query->is_404 = false;
        status_header(200);
    }
}
add_action('template_redirect', 'lofygame_fix_pagination_404');

/**
 * Add pagination support to WordPress
 */
function lofygame_pagination_support() {
    add_theme_support('pagination');
}
add_action('after_setup_theme', 'lofygame_pagination_support');

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
 * Add Game Import/Export Functionality
 */
function lofygame_add_import_export_menu() {
    add_submenu_page(
        'edit.php?post_type=game',
        'Import/Export Games',
        'Import/Export',
        'manage_options',
        'lofygame-import-export',
        'lofygame_import_export_page'
    );
}
add_action('admin_menu', 'lofygame_add_import_export_menu');

function lofygame_import_export_page() {
    ?>
    <div class="wrap">
        <h1>Import/Export</h1>
        
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; margin-top: 2rem;">
            <!-- Games Section -->
            <div class="card">
                <h2>🎮 Games</h2>
                <div style="margin-bottom: 2rem;">
                    <h3>Export Games</h3>
                    <p>Export all your games to a CSV file for backup or migration.</p>
                    <a href="<?php echo admin_url('admin-post.php?action=lofygame_export_games'); ?>" class="button button-primary">Download Games CSV</a>
                </div>
                
                <div>
                    <h3>Import Games</h3>
                    <p>Import games from a CSV file. Required columns: title, content, game_url, how_to_play</p>
                    <form method="post" enctype="multipart/form-data" action="<?php echo admin_url('admin-post.php'); ?>">
                        <input type="hidden" name="action" value="lofygame_import_games">
                        <?php wp_nonce_field('lofygame_import', 'lofygame_import_nonce'); ?>
                        <p>
                            <input type="file" name="import_file" accept=".csv" required>
                        </p>
                        <p>
                            <input type="submit" class="button button-primary" value="Import Games">
                        </p>
                    </form>
                </div>
            </div>
            
            <!-- Categories Section -->
            <div class="card">
                <h2>📂 Categories</h2>
                <div style="margin-bottom: 2rem;">
                    <h3>Export Categories</h3>
                    <p>Export all categories with their images, descriptions, and slugs.</p>
                    <a href="<?php echo admin_url('admin-post.php?action=lofygame_export_categories'); ?>" class="button button-primary">Download Categories CSV</a>
                </div>
                
                <div>
                    <h3>Import Categories</h3>
                    <p>Import categories from CSV. Columns: title, description, image_url, slug</p>
                    <p><strong>Note:</strong> If slug is not provided, it will be auto-generated from title.</p>
                    <form method="post" enctype="multipart/form-data" action="<?php echo admin_url('admin-post.php'); ?>">
                        <input type="hidden" name="action" value="lofygame_import_categories">
                        <?php wp_nonce_field('lofygame_import', 'lofygame_import_nonce'); ?>
                        <p>
                            <input type="file" name="import_file" accept=".csv" required>
                        </p>
                        <p>
                            <input type="submit" class="button button-primary" value="Import Categories">
                        </p>
                    </form>
                </div>
            </div>
        </div>
        
        <div style="display: grid; grid-template-columns: 1fr; gap: 2rem; margin-top: 2rem;">
            <!-- Tags Section -->
            <div class="card">
                <h2>🏷️ Tags</h2>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem;">
                    <div>
                        <h3>Export Tags</h3>
                        <p>Export all tags with their descriptions and slugs.</p>
                        <a href="<?php echo admin_url('admin-post.php?action=lofygame_export_tags'); ?>" class="button button-primary">Download Tags CSV</a>
                    </div>
                    
                    <div>
                        <h3>Import Tags</h3>
                        <p>Import tags from CSV. Columns: title, description, slug</p>
                        <p><strong>Note:</strong> If slug is not provided, it will be auto-generated from title.</p>
                        <form method="post" enctype="multipart/form-data" action="<?php echo admin_url('admin-post.php'); ?>">
                            <input type="hidden" name="action" value="lofygame_import_tags">
                            <?php wp_nonce_field('lofygame_import', 'lofygame_import_nonce'); ?>
                            <p>
                                <input type="file" name="import_file" accept=".csv" required>
                            </p>
                            <p>
                                <input type="submit" class="button button-primary" value="Import Tags">
                            </p>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        
        <h2>CSV Format Examples</h2>
        
        <h3>📂 Categories CSV Format (Updated)</h3>
        <pre style="background: #f1f1f1; padding: 1rem; border-radius: 4px; overflow-x: auto;">
title,description,image_url,slug
"Action Games","Fast-paced games with lots of movement and excitement","https://example.com/action-category.jpg","action-games"
"Puzzle Games","Brain-teasing games that challenge your logic","https://example.com/puzzle-category.jpg","puzzle"
"Adventure Games","Story-driven games with exploration and quests","https://example.com/adventure-category.jpg","adventure"
"Racing Games","High-speed racing and driving games","https://example.com/racing-category.jpg","racing"
        </pre>
        
        <h3>🏷️ Tags CSV Format (Updated)</h3>
        <pre style="background: #f1f1f1; padding: 1rem; border-radius: 4px; overflow-x: auto;">
title,description,slug
"multiplayer","Games that support multiple players","multiplayer"
"single-player","Games designed for one player","single-player"
"3d","Three-dimensional games","3d"
"retro","Classic style games","retro"
"kids","Games suitable for children","kids"
"mobile-friendly","Games optimized for mobile devices","mobile-friendly"
        </pre>
        
        <h3>💡 Slug Guidelines:</h3>
        <ul style="background: #e7f3ff; padding: 1.5rem; border-radius: 8px; border-left: 4px solid #0073aa;">
            <li>📝 <strong>Slugs are optional</strong> - if not provided, they'll be auto-generated from the title</li>
            <li>🔗 <strong>URL-friendly:</strong> Use lowercase letters, numbers, and hyphens only</li>
            <li>📏 <strong>Keep it short:</strong> Ideally 1-3 words separated by hyphens</li>
            <li>🎯 <strong>Be descriptive:</strong> "action-games" instead of "ag"</li>
            <li>⚠️ <strong>Must be unique:</strong> Each slug should be different across all categories/tags</li>
            <li>🚫 <strong>No spaces or special characters:</strong> Use hyphens instead of spaces</li>
        </ul>
        
        <h3>📋 Column Descriptions:</h3>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1.5rem; margin: 1.5rem 0;">
            <div style="background: #f0f9ff; padding: 1.5rem; border-radius: 8px; border-left: 4px solid #0ea5e9;">
                <h4 style="margin-top: 0; color: #0ea5e9;">📂 Categories</h4>
                <ul style="margin: 0;">
                    <li><strong>title:</strong> Category name (required)</li>
                    <li><strong>description:</strong> Brief description (optional)</li>
                    <li><strong>image_url:</strong> Category image URL (optional)</li>
                    <li><strong>slug:</strong> URL slug (optional - auto-generated if empty)</li>
                </ul>
            </div>
            
            <div style="background: #ecfdf5; padding: 1.5rem; border-radius: 8px; border-left: 4px solid #10b981;">
                <h4 style="margin-top: 0; color: #10b981;">🏷️ Tags</h4>
                <ul style="margin: 0;">
                    <li><strong>title:</strong> Tag name (required)</li>
                    <li><strong>description:</strong> Brief description (optional)</li>
                    <li><strong>slug:</strong> URL slug (optional - auto-generated if empty)</li>
                </ul>
            </div>
        </div>
    </div>
    <?php
}
/**
 * Handle Category Export
 */
function lofygame_handle_category_export() {
    if (!current_user_can('manage_options')) {
        wp_die('Unauthorized');
    }
    
    $categories = get_categories(array('hide_empty' => false));
    
    $filename = 'lofygame-categories-export-' . date('Y-m-d-H-i-s') . '.csv';
    
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Pragma: no-cache');
    header('Expires: 0');
    
    // Add BOM for UTF-8
    echo "\xEF\xBB\xBF";
    
    $output = fopen('php://output', 'w');
    
    // CSV Headers
    fputcsv($output, array('title', 'description', 'image_url', 'slug', 'count'));
    
    foreach ($categories as $category) {
        // Get category image from term meta (if exists)
        $image_url = get_term_meta($category->term_id, 'category_image', true);
        
        fputcsv($output, array(
            $category->name,
            $category->description,
            $image_url,
            $category->slug,
            $category->count
        ));
    }
    
    fclose($output);
    exit;
}
add_action('admin_post_lofygame_export_categories', 'lofygame_handle_category_export');

/**
 * Handle Tag Export
 */
function lofygame_handle_tag_export() {
    if (!current_user_can('manage_options')) {
        wp_die('Unauthorized');
    }
    
    $tags = get_tags(array('hide_empty' => false));
    
    $filename = 'lofygame-tags-export-' . date('Y-m-d-H-i-s') . '.csv';
    
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Pragma: no-cache');
    header('Expires: 0');
    
    // Add BOM for UTF-8
    echo "\xEF\xBB\xBF";
    
    $output = fopen('php://output', 'w');
    
    // CSV Headers
    fputcsv($output, array('title', 'description', 'slug', 'count'));
    
    foreach ($tags as $tag) {
        fputcsv($output, array(
            $tag->name,
            $tag->description,
            $tag->slug,
            $tag->count
        ));
    }
    
    fclose($output);
    exit;
}
add_action('admin_post_lofygame_export_tags', 'lofygame_handle_tag_export');

/**
 * Handle Category Import
 */
function lofygame_handle_category_import() {
    if (!current_user_can('manage_options') || !wp_verify_nonce($_POST['lofygame_import_nonce'], 'lofygame_import')) {
        wp_die('Unauthorized');
    }
    
    if (!isset($_FILES['import_file']) || $_FILES['import_file']['error'] !== UPLOAD_ERR_OK) {
        wp_die('File upload error');
    }
    
    $file = $_FILES['import_file']['tmp_name'];
    $imported = 0;
    $errors = array();
    $skipped = 0;
    
    if (($handle = fopen($file, 'r')) !== FALSE) {
        $header = fgetcsv($handle);
        
        // Validate required columns
        $required_columns = array('title');
        $missing_columns = array();
        
        foreach ($required_columns as $required) {
            if (!in_array($required, $header)) {
                $missing_columns[] = $required;
            }
        }
        
        if (!empty($missing_columns)) {
            wp_die('Missing required columns: ' . implode(', ', $missing_columns));
        }
        
        // Map column positions
        $columns = array();
        foreach ($header as $index => $column_name) {
            $columns[trim($column_name)] = $index;
        }
        
        $row_number = 1;
        
        while (($data = fgetcsv($handle)) !== FALSE) {
            $row_number++;
            
            $title = isset($columns['title']) && isset($data[$columns['title']]) ? trim($data[$columns['title']]) : '';
            $description = isset($columns['description']) && isset($data[$columns['description']]) ? trim($data[$columns['description']]) : '';
            $image_url = isset($columns['image_url']) && isset($data[$columns['image_url']]) ? trim($data[$columns['image_url']]) : '';
            $slug = isset($columns['slug']) && isset($data[$columns['slug']]) ? trim($data[$columns['slug']]) : '';
            
            if (empty($title)) {
                $errors[] = "Row {$row_number}: Missing title";
                $skipped++;
                continue;
            }
            
            // Generate slug if not provided
            if (empty($slug)) {
                $slug = sanitize_title($title);
            } else {
                $slug = sanitize_title($slug);
            }
            
            // Check if category already exists by slug or name
            $existing = get_category_by_slug($slug);
            if (!$existing) {
                $existing = get_term_by('name', $title, 'category');
            }
            
            if ($existing) {
                $errors[] = "Row {$row_number}: Category '{$title}' (slug: '{$slug}') already exists";
                $skipped++;
                continue;
            }
            
            // Create category
            $category_data = array(
                'cat_name' => $title,
                'category_description' => $description,
                'category_nicename' => $slug
            );
            
            $category_id = wp_insert_category($category_data);
            
            if (!is_wp_error($category_id)) {
                // Save category image if provided
                if (!empty($image_url)) {
                    update_term_meta($category_id, 'category_image', esc_url($image_url));
                }
                $imported++;
            } else {
                $errors[] = "Row {$row_number}: Failed to create category '{$title}': " . $category_id->get_error_message();
                $skipped++;
            }
        }
        
        fclose($handle);
    } else {
        wp_die('Could not open CSV file');
    }
    
    // Prepare redirect with results
    $redirect_url = admin_url('edit.php?post_type=game&page=lofygame-import-export');
    $redirect_url = add_query_arg(array(
        'imported_categories' => $imported,
        'errors' => count($errors),
        'skipped' => $skipped,
        'total_rows' => $row_number - 1
    ), $redirect_url);
    
    // Store errors in transient if any
    if (!empty($errors)) {
        set_transient('lofygame_import_category_errors', $errors, 300);
    }
    
    wp_redirect($redirect_url);
    exit;
}
add_action('admin_post_lofygame_import_categories', 'lofygame_handle_category_import');

/**
 * Handle Tag Import
 */
function lofygame_handle_tag_import() {
    if (!current_user_can('manage_options') || !wp_verify_nonce($_POST['lofygame_import_nonce'], 'lofygame_import')) {
        wp_die('Unauthorized');
    }
    
    if (!isset($_FILES['import_file']) || $_FILES['import_file']['error'] !== UPLOAD_ERR_OK) {
        wp_die('File upload error');
    }
    
    $file = $_FILES['import_file']['tmp_name'];
    $imported = 0;
    $errors = array();
    $skipped = 0;
    
    if (($handle = fopen($file, 'r')) !== FALSE) {
        $header = fgetcsv($handle);
        
        // Validate required columns
        $required_columns = array('title');
        $missing_columns = array();
        
        foreach ($required_columns as $required) {
            if (!in_array($required, $header)) {
                $missing_columns[] = $required;
            }
        }
        
        if (!empty($missing_columns)) {
            wp_die('Missing required columns: ' . implode(', ', $missing_columns));
        }
        
        // Map column positions
        $columns = array();
        foreach ($header as $index => $column_name) {
            $columns[trim($column_name)] = $index;
        }
        
        $row_number = 1;
        
        while (($data = fgetcsv($handle)) !== FALSE) {
            $row_number++;
            
            $title = isset($columns['title']) && isset($data[$columns['title']]) ? trim($data[$columns['title']]) : '';
            $description = isset($columns['description']) && isset($data[$columns['description']]) ? trim($data[$columns['description']]) : '';
            $slug = isset($columns['slug']) && isset($data[$columns['slug']]) ? trim($data[$columns['slug']]) : '';
            
            if (empty($title)) {
                $errors[] = "Row {$row_number}: Missing title";
                $skipped++;
                continue;
            }
            
            // Generate slug if not provided
            if (empty($slug)) {
                $slug = sanitize_title($title);
            } else {
                $slug = sanitize_title($slug);
            }
            
            // Check if tag already exists by slug or name
            $existing = get_term_by('slug', $slug, 'post_tag');
            if (!$existing) {
                $existing = get_term_by('name', $title, 'post_tag');
            }
            
            if ($existing) {
                $errors[] = "Row {$row_number}: Tag '{$title}' (slug: '{$slug}') already exists";
                $skipped++;
                continue;
            }
            
            // Create tag
            $tag_data = wp_insert_term($title, 'post_tag', array(
                'description' => $description,
                'slug' => $slug
            ));
            
            if (!is_wp_error($tag_data)) {
                $imported++;
            } else {
                $errors[] = "Row {$row_number}: Failed to create tag '{$title}': " . $tag_data->get_error_message();
                $skipped++;
            }
        }
        
        fclose($handle);
    } else {
        wp_die('Could not open CSV file');
    }
    
    // Prepare redirect with results
    $redirect_url = admin_url('edit.php?post_type=game&page=lofygame-import-export');
    $redirect_url = add_query_arg(array(
        'imported_tags' => $imported,
        'errors' => count($errors),
        'skipped' => $skipped,
        'total_rows' => $row_number - 1
    ), $redirect_url);
    
    // Store errors in transient if any
    if (!empty($errors)) {
        set_transient('lofygame_import_tag_errors', $errors, 300);
    }
    
    wp_redirect($redirect_url);
    exit;
}

add_action('admin_post_lofygame_import_tags', 'lofygame_handle_tag_import');







































































































































/**
 * Handle Game Export (existing function)
 */
function lofygame_handle_export() {
    if (!current_user_can('manage_options')) {
        wp_die('Unauthorized');
    }
    
    $games = get_posts(array(
        'post_type' => 'game',
        'posts_per_page' => -1,
        'post_status' => 'publish'
    ));
    
    $filename = 'lofygame-export-' . date('Y-m-d-H-i-s') . '.csv';
    
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Pragma: no-cache');
    header('Expires: 0');
    
    // Add BOM for UTF-8
    echo "\xEF\xBB\xBF";
    
    $output = fopen('php://output', 'w');
    
    // CSV Headers with all new fields
    fputcsv($output, array(
        'title', 
        'content', 
        'game_url', 
        'preview_video', 
        'walkthrough_url', 
        'video_id', 
        'video_enabled', 
        'how_to_play', 
        'categories', 
        'tags', 
        'image_url', 
        'width', 
        'height',
        'excerpt',
        'published_date',
        'modified_date',
        'views'
    ));
    
    foreach ($games as $game) {
        // Get all meta fields
        $game_url = get_post_meta($game->ID, '_game_url', true);
        $preview_video = get_post_meta($game->ID, '_preview_video_url', true);
        $walkthrough_url = get_post_meta($game->ID, '_walkthrough_url', true);
        $video_id = get_post_meta($game->ID, '_gamemonetize_video_id', true);
        $video_enabled = get_post_meta($game->ID, '_video_walkthrough_enabled', true);
        $how_to_play = get_post_meta($game->ID, '_how_to_play', true);
        $width = get_post_meta($game->ID, '_game_width', true);
        $height = get_post_meta($game->ID, '_game_height', true);
        $views = get_post_meta($game->ID, 'post_views_count', true);
        
        // Set defaults
        if (empty($width)) $width = 800;
        if (empty($height)) $height = 600;
        if (empty($views)) $views = 0;
        
        // Get categories and tags
        $categories = wp_get_post_categories($game->ID, array('fields' => 'names'));
        $tags = wp_get_post_tags($game->ID, array('fields' => 'names'));
        
        // Get featured image URL
        $image_url = get_the_post_thumbnail_url($game->ID, 'large');
        if (!$image_url) {
            // Try to extract first image from content
            preg_match('/<img[^>]+src=[\'"]([^\'"]+)[\'"][^>]*>/i', $game->post_content, $matches);
            $image_url = !empty($matches[1]) ? $matches[1] : '';
        }
        
        // Get excerpt
        $excerpt = $game->post_excerpt;
        if (empty($excerpt)) {
            $excerpt = wp_trim_words(strip_tags($game->post_content), 20);
        }
        
        // Clean content (remove images to avoid duplication)
        $clean_content = preg_replace('/<img[^>]*>/i', '', $game->post_content);
        $clean_content = trim(strip_tags($clean_content));
        
        // Write row
        fputcsv($output, array(
            $game->post_title,
            $clean_content,
            $game_url,
            $preview_video,
            $walkthrough_url,
            $video_id,
            $video_enabled ? 'true' : 'false',
            $how_to_play,
            implode(',', $categories),
            implode(',', $tags),
            $image_url,
            $width,
            $height,
            $excerpt,
            get_the_date('Y-m-d H:i:s', $game->ID),
            get_the_modified_date('Y-m-d H:i:s', $game->ID),
            $views
        ));
    }
    
    fclose($output);
    exit;
}
add_action('admin_post_lofygame_export_games', 'lofygame_handle_export');


/**
 * Increase Games Per Page in Admin Table
 * This will show more games in the WordPress admin games list
 */
function lofygame_admin_games_per_page($per_page, $post_type) {
    if ($post_type === 'game') {
        return 100; // Change this number to show more/fewer games
    }
    return $per_page;
}
add_filter('edit_posts_per_page', 'lofygame_admin_games_per_page', 10, 2);

/**
 * Alternative method: Set games per page for admin only
 * This ensures it only affects the admin area
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
 * This allows users to customize how many games they see
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
 * Custom pagination options for games admin
 * Adds quick select options in Screen Options
 */
function lofygame_add_games_pagination_options() {
    $screen = get_current_screen();
    
    if ($screen && $screen->post_type === 'game') {
        add_meta_box(
            'games-pagination-options',
            'Quick Pagination Options',
            'lofygame_pagination_meta_box',
            'game',
            'side',
            'default'
        );
    }
}
add_action('add_meta_boxes', 'lofygame_add_games_pagination_options');

/**
 * Meta box content for pagination options
 */
function lofygame_pagination_meta_box() {
    $current_per_page = get_user_meta(get_current_user_id(), 'edit_game_per_page', true) ?: 20;
    $current_url = remove_query_arg('paged');
    
    echo '<div style="padding: 10px;">';
    echo '<p><strong>Quick Options:</strong></p>';
    echo '<div style="display: flex; gap: 5px; flex-wrap: wrap; margin-bottom: 10px;">';
    
    $options = [50, 100, 200, 500, 1000];
    foreach ($options as $count) {
        $url = add_query_arg('games_per_page', $count, $current_url);
        $active = ($current_per_page == $count) ? 'style="background: #0073aa; color: white;"' : '';
        echo '<a href="' . esc_url($url) . '" class="button" ' . $active . '>' . $count . '</a>';
    }
    
    echo '</div>';
    echo '<p style="font-size: 12px; color: #666;">Current: ' . $current_per_page . ' games per page</p>';
    echo '</div>';
}

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
            echo 'Use <strong>Screen Options</strong> (top right) to show more games per page, ';
            echo 'or use the quick options in the sidebar.</p>';
            echo '</div>';
        }
    }
}
add_action('admin_notices', 'lofygame_admin_pagination_notice');



















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
        
        // JSON-LD Schema for Games Archive
        $schema = array(
            '@context' => 'https://schema.org',
            '@type' => 'CollectionPage',
            'name' => 'Free Online Games',
            'description' => $meta_description,
            'url' => get_post_type_archive_link('game'),
            'mainEntity' => array(
                '@type' => 'ItemList',
                'name' => 'Online Games Collection',
                'numberOfItems' => $games_count
            )
        );
        
        echo '<script type="application/ld+json">' . json_encode($schema) . '</script>' . "\n";
        
    } elseif (is_category() || is_tag()) {
        // Handle category/tag archives
        $term = get_queried_object();
        if ($term) {
            $term_name = $term->name;
            $term_description = $term->description;
            $term_type = is_category() ? 'Category' : 'Tag';
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
            
            // Enhanced JSON-LD Schema for Categories/Tags
            $schema = array(
                '@context' => 'https://schema.org',
                '@type' => 'CollectionPage',
                'name' => $term_name . ' Games',
                'description' => $meta_description,
                'url' => get_term_link($term),
                'mainEntity' => array(
                    '@type' => 'ItemList',
                    'name' => $term_name . ' Games Collection',
                    'numberOfItems' => $games_count,
                    'itemListElement' => array()
                ),
                'breadcrumb' => array(
                    '@type' => 'BreadcrumbList',
                    'itemListElement' => array(
                        array(
                            '@type' => 'ListItem',
                            'position' => 1,
                            'name' => 'Home',
                            'item' => home_url()
                        ),
                        array(
                            '@type' => 'ListItem',
                            'position' => 2,
                            'name' => 'Games',
                            'item' => get_post_type_archive_link('game')
                        ),
                        array(
                            '@type' => 'ListItem',
                            'position' => 3,
                            'name' => $term_name,
                            'item' => get_term_link($term)
                        )
                    )
                )
            );
            
            echo '<script type="application/ld+json">' . json_encode($schema) . '</script>' . "\n";
        }
    }
}


add_action('wp_head', 'lofygame_category_tag_seo_meta');

/**
 * Get categories excluding current category for "Browse by Category" section
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
            'simulation' => "Realistic simulation games that let you experience life from different perspectives and manage various aspects of virtual worlds.",
            'multiplayer' => "Connect and compete with players from around the world in exciting multiplayer gaming experiences.",
            'kids' => "Safe and fun games designed specifically for children. Educational and entertaining content suitable for young players."
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

















/**
 * Enhanced Game Rating System with Rich Snippets Support
 * Add this to your functions.php file
 */

/**
 * Add Game Rating Meta Boxes to Admin
 */
function lofygame_add_rating_meta_boxes() {
    add_meta_box(
        'game_rating_details',
        'Game Rating & Reviews',
        'lofygame_rating_meta_box_callback',
        'game',
        'side',
        'high'
    );
}
add_action('add_meta_boxes', 'lofygame_add_rating_meta_boxes');

/**
 * Rating Meta Box Callback
 */
function lofygame_rating_meta_box_callback($post) {
    wp_nonce_field('lofygame_save_rating_details', 'lofygame_rating_nonce');
    
    // Get existing values
    $avg_rating = get_post_meta($post->ID, '_game_avg_rating', true);
    $rating_count = get_post_meta($post->ID, '_game_rating_count', true);
    $best_rating = get_post_meta($post->ID, '_game_best_rating', true);
    $worst_rating = get_post_meta($post->ID, '_game_worst_rating', true);
    $enable_user_ratings = get_post_meta($post->ID, '_enable_user_ratings', true);
    
    // Set defaults
    if (empty($avg_rating)) $avg_rating = '4.5';
    if (empty($rating_count)) $rating_count = '127';
    if (empty($best_rating)) $best_rating = '5';
    if (empty($worst_rating)) $worst_rating = '1';
    if (empty($enable_user_ratings)) $enable_user_ratings = '1';
    
    echo '<table class="form-table" style="margin: 0;">';
    
    // Average Rating
    echo '<tr>';
    echo '<th style="padding: 8px 0;"><label for="game_avg_rating">Average Rating</label></th>';
    echo '<td style="padding: 8px 0;">';
    echo '<input type="number" id="game_avg_rating" name="game_avg_rating" value="' . esc_attr($avg_rating) . '" step="0.1" min="0" max="5" style="width: 80px;" />';
    echo '<span style="margin-left: 8px; color: #666;">/5 stars</span>';
    echo '</td>';
    echo '</tr>';
    
    // Rating Count
    echo '<tr>';
    echo '<th style="padding: 8px 0;"><label for="game_rating_count">Total Reviews</label></th>';
    echo '<td style="padding: 8px 0;">';
    echo '<input type="number" id="game_rating_count" name="game_rating_count" value="' . esc_attr($rating_count) . '" min="1" style="width: 80px;" />';
    echo '<span style="margin-left: 8px; color: #666;">reviews</span>';
    echo '</td>';
    echo '</tr>';
    
    // Best Rating
    echo '<tr>';
    echo '<th style="padding: 8px 0;"><label for="game_best_rating">Best Rating</label></th>';
    echo '<td style="padding: 8px 0;">';
    echo '<input type="number" id="game_best_rating" name="game_best_rating" value="' . esc_attr($best_rating) . '" step="0.1" min="1" max="5" style="width: 80px;" />';
    echo '<span style="margin-left: 8px; color: #666;">max stars</span>';
    echo '</td>';
    echo '</tr>';
    
    // Worst Rating
    echo '<tr>';
    echo '<th style="padding: 8px 0;"><label for="game_worst_rating">Worst Rating</label></th>';
    echo '<td style="padding: 8px 0;">';
    echo '<input type="number" id="game_worst_rating" name="game_worst_rating" value="' . esc_attr($worst_rating) . '" step="0.1" min="0" max="5" style="width: 80px;" />';
    echo '<span style="margin-left: 8px; color: #666;">min stars</span>';
    echo '</td>';
    echo '</tr>';
    
    // Enable User Ratings
    echo '<tr>';
    echo '<th style="padding: 8px 0;"><label for="enable_user_ratings">User Ratings</label></th>';
    echo '<td style="padding: 8px 0;">';
    echo '<label><input type="checkbox" id="enable_user_ratings" name="enable_user_ratings" value="1" ' . checked($enable_user_ratings, 1, false) . '> Enable user rating system</label>';
    echo '</td>';
    echo '</tr>';
    
    echo '</table>';
    
    // Rating Preview
    echo '<div style="margin-top: 15px; padding: 10px; background: #f9f9f9; border-radius: 5px;">';
    echo '<strong>Preview:</strong><br>';
    echo '<div class="rating-preview" style="margin-top: 5px;">';
    echo '<span class="stars" style="color: #ffa500; font-size: 14px;">';
    for ($i = 1; $i <= 5; $i++) {
        if ($i <= floor($avg_rating)) {
            echo '★';
        } elseif ($i - 0.5 <= $avg_rating) {
            echo '☆';
        } else {
            echo '☆';
        }
    }
    echo '</span>';
    echo '<span style="margin-left: 8px; color: #666; font-size: 12px;">' . $avg_rating . '/5 (' . $rating_count . ' reviews)</span>';
    echo '</div>';
    echo '</div>';
    
    // Rich Snippets Info
    echo '<div style="margin-top: 15px; padding: 10px; background: #e7f3ff; border-radius: 5px; border-left: 4px solid #0073aa;">';
    echo '<strong>🔍 Rich Snippets:</strong><br>';
    echo '<small style="color: #555;">These ratings will appear as stars in Google search results. ';
    echo 'Make sure ratings are realistic and based on actual user feedback.</small>';
    echo '</div>';
    
    // Quick Rating Presets
    echo '<div style="margin-top: 15px;">';
    echo '<strong>Quick Presets:</strong><br>';
    echo '<button type="button" class="button button-small" onclick="setRating(4.8, 245)">Excellent (4.8★)</button> ';
    echo '<button type="button" class="button button-small" onclick="setRating(4.3, 156)">Very Good (4.3★)</button> ';
    echo '<button type="button" class="button button-small" onclick="setRating(3.9, 89)">Good (3.9★)</button> ';
    echo '<button type="button" class="button button-small" onclick="setRating(3.2, 67)">Average (3.2★)</button>';
    echo '</div>';
    
    // JavaScript for functionality
    echo '<script>
    function setRating(rating, count) {
        document.getElementById("game_avg_rating").value = rating;
        document.getElementById("game_rating_count").value = count;
        updatePreview();
    }
    
    function updatePreview() {
        const rating = parseFloat(document.getElementById("game_avg_rating").value) || 0;
        const count = parseInt(document.getElementById("game_rating_count").value) || 0;
        
        const starsContainer = document.querySelector(".rating-preview .stars");
        const infoContainer = document.querySelector(".rating-preview span:last-child");
        
        let starsHTML = "";
        for (let i = 1; i <= 5; i++) {
            if (i <= Math.floor(rating)) {
                starsHTML += "★";
            } else if (i - 0.5 <= rating) {
                starsHTML += "☆";
            } else {
                starsHTML += "☆";
            }
        }
        
        starsContainer.innerHTML = starsHTML;
        infoContainer.innerHTML = rating + "/5 (" + count + " reviews)";
    }
    
    // Update preview on input change
    document.getElementById("game_avg_rating").addEventListener("input", updatePreview);
    document.getElementById("game_rating_count").addEventListener("input", updatePreview);
    </script>';
}

/**
 * Save Rating Meta Box Data
 */
function lofygame_save_rating_details($post_id) {
    if (!isset($_POST['lofygame_rating_nonce']) || !wp_verify_nonce($_POST['lofygame_rating_nonce'], 'lofygame_save_rating_details')) {
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
    
    // Save rating data
    if (isset($_POST['game_avg_rating'])) {
        $avg_rating = floatval($_POST['game_avg_rating']);
        $avg_rating = max(0, min(5, $avg_rating)); // Clamp between 0-5
        update_post_meta($post_id, '_game_avg_rating', $avg_rating);
    }
    
    if (isset($_POST['game_rating_count'])) {
        $rating_count = max(1, intval($_POST['game_rating_count'])); // At least 1
        update_post_meta($post_id, '_game_rating_count', $rating_count);
    }
    
    if (isset($_POST['game_best_rating'])) {
        $best_rating = floatval($_POST['game_best_rating']);
        $best_rating = max(1, min(5, $best_rating)); // Clamp between 1-5
        update_post_meta($post_id, '_game_best_rating', $best_rating);
    }
    
    if (isset($_POST['game_worst_rating'])) {
        $worst_rating = floatval($_POST['game_worst_rating']);
        $worst_rating = max(0, min(5, $worst_rating)); // Clamp between 0-5
        update_post_meta($post_id, '_game_worst_rating', $worst_rating);
    }
    
    $enable_user_ratings = isset($_POST['enable_user_ratings']) ? 1 : 0;
    update_post_meta($post_id, '_enable_user_ratings', $enable_user_ratings);
}
add_action('save_post', 'lofygame_save_rating_details');

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

// Replace the existing lofygame_seo_meta function
remove_action('wp_head', 'lofygame_seo_meta');
add_action('wp_head', 'lofygame_enhanced_seo_meta');

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
 * Display Star Rating in Game Cards
 */
function lofygame_display_game_rating($post_id, $show_count = true) {
    $avg_rating = get_post_meta($post_id, '_game_avg_rating', true);
    $rating_count = get_post_meta($post_id, '_game_rating_count', true);
    
    if (empty($avg_rating)) $avg_rating = 4.5;
    if (empty($rating_count)) $rating_count = 127;
    
    $rating_html = '<div class="game-rating-display" data-rating="' . esc_attr($avg_rating) . '">';
    $rating_html .= '<div class="rating-stars">';
    
    for ($i = 1; $i <= 5; $i++) {
        if ($i <= floor($avg_rating)) {
            $rating_html .= '<span class="star filled">★</span>';
        } elseif ($i - 0.5 <= $avg_rating) {
            $rating_html .= '<span class="star half">★</span>';
        } else {
            $rating_html .= '<span class="star empty">☆</span>';
        }
    }
    
    $rating_html .= '</div>';
    
    if ($show_count) {
        $rating_html .= '<div class="rating-info">';
        $rating_html .= '<span class="rating-value">' . $avg_rating . '</span>';
        $rating_html .= '<span class="rating-count">(' . number_format($rating_count) . ')</span>';
        $rating_html .= '</div>';
    }
    
    $rating_html .= '</div>';
    
    return $rating_html;
}

/**
 * Add Rating Column to Admin Games List
 */
function lofygame_add_rating_column($columns) {
    $columns['rating'] = __('Rating');
    return $columns;
}
add_filter('manage_game_posts_columns', 'lofygame_add_rating_column');

function lofygame_rating_column_content($column, $post_id) {
    if ($column === 'rating') {
        $avg_rating = get_post_meta($post_id, '_game_avg_rating', true);
        $rating_count = get_post_meta($post_id, '_game_rating_count', true);
        
        if (empty($avg_rating)) $avg_rating = 4.5;
        if (empty($rating_count)) $rating_count = 127;
        
        echo '<div style="display: flex; align-items: center; gap: 5px;">';
        echo '<span style="color: #ffa500; font-size: 14px;">';
        for ($i = 1; $i <= 5; $i++) {
            echo ($i <= floor($avg_rating)) ? '★' : '☆';
        }
        echo '</span>';
        echo '<span style="font-size: 12px; color: #666;">' . $avg_rating . ' (' . $rating_count . ')</span>';
        echo '</div>';
    }
}
add_action('manage_game_posts_custom_column', 'lofygame_rating_column_content', 10, 2);

/**
 * Make Rating Column Sortable
 */
function lofygame_sortable_rating_column($columns) {
    $columns['rating'] = 'game_avg_rating';
    return $columns;
}
add_filter('manage_edit-game_sortable_columns', 'lofygame_sortable_rating_column');

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
 * Bulk Rating Update Tool (Admin)
 */
function lofygame_add_bulk_rating_update() {
    add_action('admin_menu', function() {
        add_submenu_page(
            'edit.php?post_type=game',
            'Bulk Update Ratings',
            'Bulk Ratings',
            'manage_options',
            'bulk-ratings',
            'lofygame_bulk_ratings_page'
        );
    });
}
add_action('init', 'lofygame_add_bulk_rating_update');

function lofygame_bulk_ratings_page() {
    if (isset($_POST['update_ratings'])) {
        $games = get_posts(array('post_type' => 'game', 'posts_per_page' => -1));
        $updated = 0;
        
        foreach ($games as $game) {
            $current_rating = get_post_meta($game->ID, '_game_avg_rating', true);
            if (empty($current_rating)) {
                // Generate realistic rating based on categories and date
                $categories = get_the_category($game->ID);
                $base_rating = 3.8; // Base rating
                
                // Adjust based on category
                if ($categories) {
                    $category_name = strtolower($categories[0]->name);
                    if (strpos($category_name, 'action') !== false) $base_rating += 0.3;
                    if (strpos($category_name, 'puzzle') !== false) $base_rating += 0.4;
                    if (strpos($category_name, 'adventure') !== false) $base_rating += 0.2;
                }
                
                // Add some randomness
                $rating = $base_rating + (rand(-3, 7) / 10);
                $rating = max(3.0, min(5.0, round($rating, 1)));
                
                // Generate realistic review count
                $days_old = (time() - strtotime($game->post_date)) / (24 * 60 * 60);
                $base_count = max(10, intval($days_old / 7)); // 1 review per week minimum
                $count = $base_count + rand(5, 50);
                
                update_post_meta($game->ID, '_game_avg_rating', $rating);
                update_post_meta($game->ID, '_game_rating_count', $count);
                update_post_meta($game->ID, '_game_best_rating', 5);
                update_post_meta($game->ID, '_game_worst_rating', 1);
                
                $updated++;
            }
        }
        
        echo '<div class="notice notice-success"><p>Updated ratings for ' . $updated . ' games!</p></div>';
    }
    
    ?>
    <div class="wrap">
        <h1>Bulk Update Game Ratings</h1>
        <p>This tool will add realistic ratings to games that don't have ratings yet.</p>
        
        <form method="post">
            <p>
                <input type="submit" name="update_ratings" class="button button-primary" value="Update Missing Ratings" onclick="return confirm('This will add ratings to games without them. Continue?');">
            </p>
        </form>
        
        <h2>Rich Snippets Guidelines</h2>
        <ul>
            <li>Use realistic ratings (3.0-5.0 range)</li>
            <li>Ensure review counts are reasonable</li>
            <li>Test your rich snippets using Google's Rich Results Test</li>
            <li>Keep ratings consistent with actual user feedback</li>
        </ul>
    </div>
    <?php
}


/**
 * AJAX Rating Submission Handler
 * Add this to your functions.php file
 */

/**
 * Handle AJAX rating submission
 */
function lofygame_handle_rating_submission() {
    check_ajax_referer('lofygame_nonce', 'nonce');
    
    $game_id = intval($_POST['game_id']);
    $rating = floatval($_POST['rating']);
    
    // Validate input
    if (!$game_id || $rating < 1 || $rating > 5) {
        wp_send_json_error('Invalid rating data');
        return;
    }
    
    // Check if game exists
    if (get_post_type($game_id) !== 'game') {
        wp_send_json_error('Game not found');
        return;
    }
    
    // Get current rating data
    $current_avg = floatval(get_post_meta($game_id, '_game_avg_rating', true)) ?: 4.5;
    $current_count = intval(get_post_meta($game_id, '_game_rating_count', true)) ?: 127;
    
    // Calculate new average
    $total_points = $current_avg * $current_count;
    $new_total_points = $total_points + $rating;
    $new_count = $current_count + 1;
    $new_average = round($new_total_points / $new_count, 1);
    
    // Update rating data
    update_post_meta($game_id, '_game_avg_rating', $new_average);
    update_post_meta($game_id, '_game_rating_count', $new_count);
    
    // Track user's rating (prevent spam)
    $user_ip = $_SERVER['REMOTE_ADDR'];
    $user_ratings = get_option('lofygame_user_ratings', array());
    $rating_key = $game_id . '_' . md5($user_ip);
    $user_ratings[$rating_key] = array(
        'rating' => $rating,
        'timestamp' => time(),
        'game_id' => $game_id
    );
    
    // Clean old ratings (older than 30 days)
    $thirty_days_ago = time() - (30 * 24 * 60 * 60);
    foreach ($user_ratings as $key => $user_rating) {
        if ($user_rating['timestamp'] < $thirty_days_ago) {
            unset($user_ratings[$key]);
        }
    }
    
    update_option('lofygame_user_ratings', $user_ratings);
    
    // Log rating for analytics
    error_log("Game Rating: Game ID {$game_id}, Rating {$rating}, New Average {$new_average}, Total Reviews {$new_count}");
    
    wp_send_json_success(array(
        'message' => 'Rating submitted successfully',
        'new_average' => $new_average,
        'new_count' => $new_count,
        'game_id' => $game_id
    ));
}
add_action('wp_ajax_submit_game_rating', 'lofygame_handle_rating_submission');
add_action('wp_ajax_nopriv_submit_game_rating', 'lofygame_handle_rating_submission');

/**
 * Check if user has already rated a game
 */
function lofygame_user_has_rated($game_id) {
    $user_ip = $_SERVER['REMOTE_ADDR'];
    $user_ratings = get_option('lofygame_user_ratings', array());
    $rating_key = $game_id . '_' . md5($user_ip);
    
    return isset($user_ratings[$rating_key]);
}

/**
 * Get user's rating for a game
 */
function lofygame_get_user_rating($game_id) {
    $user_ip = $_SERVER['REMOTE_ADDR'];
    $user_ratings = get_option('lofygame_user_ratings', array());
    $rating_key = $game_id . '_' . md5($user_ip);
    
    return isset($user_ratings[$rating_key]) ? $user_ratings[$rating_key] : null;
}

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
    echo '<a href="' . admin_url('edit.php?post_type=game&page=bulk-ratings') . '" class="button button-primary">Manage Ratings</a> ';
    echo '<a href="https://search.google.com/test/rich-results" target="_blank" class="button">Test Rich Snippets</a>';
    echo '</div>';
    
    echo '</div>';
}

/**
 * Enhanced admin columns for ratings
 */
function lofygame_enhanced_rating_column_content($column, $post_id) {
    if ($column === 'rating') {
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
        
        // Quick edit link
        echo '<a href="#" onclick="lofygameQuickEditRating(' . $post_id . ')" style="font-size: 12px; text-decoration: none;" title="Quick Edit Rating">✏️</a>';
        
        echo '</div>';
        
        // Rich snippets status
        $has_structured_data = !empty($avg_rating) && !empty($rating_count);
        if ($has_structured_data) {
            echo '<div style="font-size: 11px; color: #28a745; margin-top: 2px;">✅ Rich Snippets Ready</div>';
        } else {
            echo '<div style="font-size: 11px; color: #dc3545; margin-top: 2px;">❌ Missing Rating Data</div>';
        }
    }
}

// Replace the existing rating column function
remove_action('manage_game_posts_custom_column', 'lofygame_rating_column_content', 10, 2);
add_action('manage_game_posts_custom_column', 'lofygame_enhanced_rating_column_content', 10, 2);

/**
 * Quick edit rating functionality
 */
function lofygame_add_quick_edit_rating_script() {
    global $current_screen;
    
    if ($current_screen && $current_screen->post_type === 'game') {
        ?>
        <script>
        function lofygameQuickEditRating(postId) {
            const newRating = prompt('Enter new rating (1.0 - 5.0):');
            const newCount = prompt('Enter review count:');
            
            if (newRating && newCount) {
                const rating = parseFloat(newRating);
                const count = parseInt(newCount);
                
                if (rating >= 1 && rating <= 5 && count > 0) {
                    // Send AJAX request to update rating
                    jQuery.post(ajaxurl, {
                        action: 'lofygame_quick_edit_rating',
                        post_id: postId,
                        rating: rating,
                        count: count,
                        nonce: '<?php echo wp_create_nonce('lofygame_quick_edit'); ?>'
                    }, function(response) {
                        if (response.success) {
                            location.reload();
                        } else {
                            alert('Error updating rating: ' + response.data);
                        }
                    });
                } else {
                    alert('Invalid rating or count values');
                }
            }
        }
        </script>
        <?php
    }
}
add_action('admin_footer', 'lofygame_add_quick_edit_rating_script');

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
 * Export/Import ratings functionality
 */
function lofygame_add_rating_export_import() {
    if (isset($_POST['export_ratings'])) {
        lofygame_export_ratings();
    }
    
    if (isset($_POST['import_ratings']) && isset($_FILES['ratings_file'])) {
        lofygame_import_ratings();
    }
}
add_action('admin_init', 'lofygame_add_rating_export_import');

function lofygame_export_ratings() {
    if (!current_user_can('manage_options')) {
        wp_die('Unauthorized');
    }
    
    $games = get_posts(array(
        'post_type' => 'game',
        'posts_per_page' => -1,
        'post_status' => 'publish'
    ));
    
    $filename = 'lofygame-ratings-export-' . date('Y-m-d-H-i-s') . '.csv';
    
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Pragma: no-cache');
    header('Expires: 0');
    
    echo "\xEF\xBB\xBF"; // BOM for UTF-8
    
    $output = fopen('php://output', 'w');
    
    // CSV Headers
    fputcsv($output, array(
        'game_id',
        'game_title',
        'avg_rating',
        'rating_count',
        'best_rating',
        'worst_rating',
        'enable_user_ratings',
        'permalink'
    ));
    
    foreach ($games as $game) {
        $avg_rating = get_post_meta($game->ID, '_game_avg_rating', true);
        $rating_count = get_post_meta($game->ID, '_game_rating_count', true);
        $best_rating = get_post_meta($game->ID, '_game_best_rating', true);
        $worst_rating = get_post_meta($game->ID, '_game_worst_rating', true);
        $enable_user_ratings = get_post_meta($game->ID, '_enable_user_ratings', true);
        
        fputcsv($output, array(
            $game->ID,
            $game->post_title,
            $avg_rating ?: '',
            $rating_count ?: '',
            $best_rating ?: '',
            $worst_rating ?: '',
            $enable_user_ratings ? 'yes' : 'no',
            get_permalink($game->ID)
        ));
    }
    
    fclose($output);
    exit;
}

function lofygame_import_ratings() {
    if (!current_user_can('manage_options')) {
        wp_die('Unauthorized');
    }
    
    $file = $_FILES['ratings_file']['tmp_name'];
    $imported = 0;
    $errors = array();
    
    if (($handle = fopen($file, 'r')) !== FALSE) {
        $header = fgetcsv($handle);
        
        while (($data = fgetcsv($handle)) !== FALSE) {
            $game_id = intval($data[0]);
            $avg_rating = floatval($data[2]);
            $rating_count = intval($data[3]);
            $best_rating = floatval($data[4]) ?: 5;
            $worst_rating = floatval($data[5]) ?: 1;
            $enable_user_ratings = ($data[6] === 'yes') ? 1 : 0;
            
            if ($game_id && get_post_type($game_id) === 'game') {
                if ($avg_rating >= 1 && $avg_rating <= 5 && $rating_count > 0) {
                    update_post_meta($game_id, '_game_avg_rating', $avg_rating);
                    update_post_meta($game_id, '_game_rating_count', $rating_count);
                    update_post_meta($game_id, '_game_best_rating', $best_rating);
                    update_post_meta($game_id, '_game_worst_rating', $worst_rating);
                    update_post_meta($game_id, '_enable_user_ratings', $enable_user_ratings);
                    $imported++;
                } else {
                    $errors[] = "Invalid rating data for game ID {$game_id}";
                }
            } else {
                $errors[] = "Game not found for ID {$game_id}";
            }
        }
        
        fclose($handle);
    }
    
    // Store results for display
    set_transient('lofygame_rating_import_results', array(
        'imported' => $imported,
        'errors' => $errors
    ), 300);
    
    wp_redirect(admin_url('edit.php?post_type=game&page=bulk-ratings&imported=' . $imported));
    exit;
}

/**
 * Enhanced bulk ratings page
 */
function lofygame_enhanced_bulk_ratings_page() {
    $import_results = get_transient('lofygame_rating_import_results');
    
    if ($import_results) {
        echo '<div class="notice notice-success"><p>Imported ' . $import_results['imported'] . ' ratings successfully!</p></div>';
        if (!empty($import_results['errors'])) {
            echo '<div class="notice notice-warning"><p>Errors: ' . implode(', ', array_slice($import_results['errors'], 0, 5)) . '</p></div>';
        }
        delete_transient('lofygame_rating_import_results');
    }
    
    if (isset($_GET['imported'])) {
        echo '<div class="notice notice-success"><p>Updated ratings for ' . intval($_GET['imported']) . ' games!</p></div>';
    }
    
    ?>
    <div class="wrap">
        <h1>⭐ Game Ratings Management</h1>
        
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; margin: 2rem 0;">
            <!-- Bulk Update Section -->
            <div class="card">
                <h2>🔄 Bulk Update Missing Ratings</h2>
                <p>Add realistic ratings to games that don't have ratings yet.</p>
                <form method="post">
                    <p>
                        <input type="submit" name="update_ratings" class="button button-primary" 
                               value="Update Missing Ratings" 
                               onclick="return confirm('This will add ratings to games without them. Continue?');">
                    </p>
                </form>
            </div>
            
            <!-- Export/Import Section -->
            <div class="card">
                <h2>📁 Export/Import Ratings</h2>
                
                <h3>Export Ratings</h3>
                <p>Download all game ratings as CSV file.</p>
                <form method="post">
                    <p>
                        <input type="submit" name="export_ratings" class="button" value="Export Ratings CSV">
                    </p>
                </form>
                
                <h3>Import Ratings</h3>
                <p>Upload a CSV file with rating data.</p>
                <form method="post" enctype="multipart/form-data">
                    <p>
                        <input type="file" name="ratings_file" accept=".csv" required>
                    </p>
                    <p>
                        <input type="submit" name="import_ratings" class="button" value="Import Ratings">
                    </p>
                </form>
            </div>
        </div>
        
        <!-- Rating Statistics -->
        <div class="card">
            <h2>📊 Rating Statistics</h2>
            <?php
            global $wpdb;
            
            // Get comprehensive statistics
            $stats_query = $wpdb->prepare("
                SELECT 
                    COUNT(*) as total_games,
                    COUNT(CASE WHEN pm1.meta_value IS NOT NULL AND pm1.meta_value != '' THEN 1 END) as rated_games,
                    AVG(CASE WHEN pm1.meta_value IS NOT NULL AND pm1.meta_value != '' THEN CAST(pm1.meta_value AS DECIMAL(3,1)) END) as avg_rating,
                    SUM(CASE WHEN pm2.meta_value IS NOT NULL AND pm2.meta_value != '' THEN CAST(pm2.meta_value AS UNSIGNED) ELSE 0 END) as total_reviews,
                    COUNT(CASE WHEN pm3.meta_value = '1' THEN 1 END) as user_rating_enabled
                FROM {$wpdb->posts} p
                LEFT JOIN {$wpdb->postmeta} pm1 ON p.ID = pm1.post_id AND pm1.meta_key = '_game_avg_rating'
                LEFT JOIN {$wpdb->postmeta} pm2 ON p.ID = pm2.post_id AND pm2.meta_key = '_game_rating_count'
                LEFT JOIN {$wpdb->postmeta} pm3 ON p.ID = pm3.post_id AND pm3.meta_key = '_enable_user_ratings'
                WHERE p.post_type = 'game' AND p.post_status = 'publish'
            ");
            
            $stats = $wpdb->get_row($stats_query);
            
            echo '<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 1rem; margin: 1rem 0;">';
            
            echo '<div style="text-align: center; padding: 1rem; background: #f8f9fa; border-radius: 8px;">';
            echo '<div style="font-size: 2rem; font-weight: bold; color: #0073aa;">' . $stats->total_games . '</div>';
            echo '<div>Total Games</div>';
            echo '</div>';
            
            echo '<div style="text-align: center; padding: 1rem; background: #f8f9fa; border-radius: 8px;">';
            echo '<div style="font-size: 2rem; font-weight: bold; color: #28a745;">' . $stats->rated_games . '</div>';
            echo '<div>Rated Games</div>';
            echo '</div>';
            
            echo '<div style="text-align: center; padding: 1rem; background: #f8f9fa; border-radius: 8px;">';
            echo '<div style="font-size: 2rem; font-weight: bold; color: #ffa500;">' . ($stats->avg_rating ? round($stats->avg_rating, 1) : '0') . '</div>';
            echo '<div>Average Rating</div>';
            echo '</div>';
            
            echo '<div style="text-align: center; padding: 1rem; background: #f8f9fa; border-radius: 8px;">';
            echo '<div style="font-size: 2rem; font-weight: bold; color: #17a2b8;">' . number_format($stats->total_reviews) . '</div>';
            echo '<div>Total Reviews</div>';
            echo '</div>';
            
            echo '<div style="text-align: center; padding: 1rem; background: #f8f9fa; border-radius: 8px;">';
            echo '<div style="font-size: 2rem; font-weight: bold; color: #6c757d;">' . $stats->user_rating_enabled . '</div>';
            echo '<div>User Rating Enabled</div>';
            echo '</div>';
            
            $completion_rate = $stats->total_games > 0 ? round(($stats->rated_games / $stats->total_games) * 100, 1) : 0;
            echo '<div style="text-align: center; padding: 1rem; background: #f8f9fa; border-radius: 8px;">';
            echo '<div style="font-size: 2rem; font-weight: bold; color: #dc3545;">' . $completion_rate . '%</div>';
            echo '<div>Completion Rate</div>';
            echo '</div>';
            
            echo '</div>';
            ?>
        </div>
        
        <!-- SEO Guidelines -->
        <div class="card">
            <h2>🔍 Rich Snippets Guidelines</h2>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem;">
                <div>
                    <h3>✅ Best Practices</h3>
                    <ul>
                        <li>Use realistic ratings (3.0-5.0 range)</li>
                        <li>Ensure review counts are reasonable</li>
                        <li>Keep ratings consistent with actual feedback</li>
                        <li>Update ratings based on real user interactions</li>
                        <li>Test rich snippets regularly</li>
                    </ul>
                </div>
                <div>
                    <h3>⚠️ Things to Avoid</h3>
                    <ul>
                        <li>Fake or inflated ratings</li>
                        <li>Unrealistic review counts</li>
                        <li>All games having 5-star ratings</li>
                        <li>Copying ratings from other sites</li>
                        <li>Setting ratings without actual reviews</li>
                    </ul>
                </div>
            </div>
            
            <div style="margin-top: 1.5rem; text-align: center;">
                <a href="https://search.google.com/test/rich-results" target="_blank" class="button button-primary">
                    🔍 Test Rich Snippets
                </a>
                <a href="https://developers.google.com/search/docs/data-types/review-snippet" target="_blank" class="button">
                    📖 Google Guidelines
                </a>
            </div>
        </div>
        
        <!-- Recent Rating Activity -->
        <div class="card">
            <h2>📈 Recent Rating Activity</h2>
            <?php
            $recent_ratings = get_option('lofygame_user_ratings', array());
            $recent_ratings = array_slice(array_reverse($recent_ratings), 0, 10);
            
            if (!empty($recent_ratings)) {
                echo '<table class="widefat">';
                echo '<thead><tr><th>Game</th><th>Rating</th><th>Date</th><th>IP Hash</th></tr></thead>';
                echo '<tbody>';
                
                foreach ($recent_ratings as $key => $rating_data) {
                    $game_title = get_the_title($rating_data['game_id']);
                    $rating_stars = str_repeat('★', $rating_data['rating']) . str_repeat('☆', 5 - $rating_data['rating']);
                    $date = date('M j, Y H:i', $rating_data['timestamp']);
                    $ip_hash = substr($key, strrpos($key, '_') + 1, 8);
                    
                    echo '<tr>';
                    echo '<td><strong>' . esc_html($game_title) . '</strong></td>';
                    echo '<td><span style="color: #ffa500;">' . $rating_stars . '</span> (' . $rating_data['rating'] . '/5)</td>';
                    echo '<td>' . $date . '</td>';
                    echo '<td><code>' . $ip_hash . '...</code></td>';
                    echo '</tr>';
                }
                
                echo '</tbody></table>';
            } else {
                echo '<p>No recent rating activity.</p>';
            }
            ?>
        </div>
        
        <!-- CSV Format Guide -->
        <div class="card">
            <h2>📋 CSV Import Format</h2>
            <p>Use this format for importing ratings:</p>
            <pre style="background: #f1f1f1; padding: 1rem; border-radius: 4px; overflow-x: auto;">game_id,game_title,avg_rating,rating_count,best_rating,worst_rating,enable_user_ratings,permalink
123,"Game Name",4.5,127,5,1,yes,https://example.com/game-name
124,"Another Game",3.8,89,5,1,no,https://example.com/another-game</pre>
        </div>
    </div>
    <?php
}

// Replace the existing bulk ratings page function
remove_action('admin_menu', 'lofygame_add_bulk_rating_update');
add_action('admin_menu', function() {
    add_submenu_page(
        'edit.php?post_type=game',
        'Ratings Management',
        'Ratings Manager',
        'manage_options',
        'bulk-ratings',
        'lofygame_enhanced_bulk_ratings_page'
    );
});

/**
 * Cleanup function for old rating data
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
 * User Rating System - Add this to your functions.php file
 * This handles the backend functionality for user ratings
 */

/**
 * Handle AJAX rating submission from users
 */
function lofygame_handle_user_rating_submission() {
    // Verify nonce for security
    check_ajax_referer('lofygame_nonce', 'nonce');
    
    // Get and validate input
    $game_id = isset($_POST['game_id']) ? intval($_POST['game_id']) : 0;
    $rating = isset($_POST['rating']) ? floatval($_POST['rating']) : 0;
    
    // Validate input
    if (!$game_id || $rating < 1 || $rating > 5) {
        wp_send_json_error('Invalid rating data');
        return;
    }
    
    // Check if game exists
    if (get_post_type($game_id) !== 'game') {
        wp_send_json_error('Game not found');
        return;
    }
    
    // Get user identifier (IP address for non-logged in users)
    $user_identifier = '';
    if (is_user_logged_in()) {
        $user_identifier = 'user_' . get_current_user_id();
    } else {
        $user_identifier = 'ip_' . md5($_SERVER['REMOTE_ADDR']);
    }
    
    // Check if user has already rated this game
    $user_ratings = get_post_meta($game_id, '_user_ratings', true);
    if (!is_array($user_ratings)) {
        $user_ratings = array();
    }
    
    // Check if this user already rated
    if (isset($user_ratings[$user_identifier])) {
        wp_send_json_error('You have already rated this game');
        return;
    }
    
    // Add user's rating
    $user_ratings[$user_identifier] = array(
        'rating' => $rating,
        'timestamp' => current_time('timestamp'),
        'ip' => $_SERVER['REMOTE_ADDR']
    );
    
    // Update user ratings data
    update_post_meta($game_id, '_user_ratings', $user_ratings);
    
    // Calculate new average rating
    $total_rating = 0;
    $rating_count = count($user_ratings);
    
    foreach ($user_ratings as $user_rating) {
        $total_rating += $user_rating['rating'];
    }
    
    $new_average = round($total_rating / $rating_count, 1);
    
    // Update the game's average rating and count
    update_post_meta($game_id, '_game_avg_rating', $new_average);
    update_post_meta($game_id, '_game_rating_count', $rating_count);
    
    // Log the rating for analytics
    error_log("User Rating Submitted: Game ID {$game_id}, Rating {$rating}, New Average {$new_average}, Total Reviews {$rating_count}");
    
    // Send success response
    wp_send_json_success(array(
        'message' => 'Thank you for rating this game!',
        'new_average' => $new_average,
        'new_count' => $rating_count,
        'game_id' => $game_id
    ));
}
add_action('wp_ajax_submit_game_rating', 'lofygame_handle_user_rating_submission');
add_action('wp_ajax_nopriv_submit_game_rating', 'lofygame_handle_user_rating_submission');

/**
 * Get user's rating for a specific game
 */
function lofygame_get_user_game_rating($game_id) {
    $user_identifier = '';
    if (is_user_logged_in()) {
        $user_identifier = 'user_' . get_current_user_id();
    } else {
        $user_identifier = 'ip_' . md5($_SERVER['REMOTE_ADDR']);
    }
    
    $user_ratings = get_post_meta($game_id, '_user_ratings', true);
    
    if (is_array($user_ratings) && isset($user_ratings[$user_identifier])) {
        return $user_ratings[$user_identifier];
    }
    
    return null;
}

/**
 * Check if user has already rated a game
 */
function lofygame_user_has_rated_game($game_id) {
    return lofygame_get_user_game_rating($game_id) !== null;
}

/**
 * Initialize ratings for games that don't have any
 */
function lofygame_initialize_game_ratings() {
    // Only run this once
    if (get_option('lofygame_ratings_initialized')) {
        return;
    }
    
    $games = get_posts(array(
        'post_type' => 'game',
        'posts_per_page' => -1,
        'post_status' => 'publish'
    ));
    
    foreach ($games as $game) {
        $avg_rating = get_post_meta($game->ID, '_game_avg_rating', true);
        $rating_count = get_post_meta($game->ID, '_game_rating_count', true);
        
        // If no ratings exist, set defaults
        if (empty($avg_rating)) {
            update_post_meta($game->ID, '_game_avg_rating', 4.2);
        }
        
        if (empty($rating_count)) {
            update_post_meta($game->ID, '_game_rating_count', 0);
        }
        
        // Enable user ratings by default
        update_post_meta($game->ID, '_enable_user_ratings', 1);
    }
    
    update_option('lofygame_ratings_initialized', true);
}
add_action('init', 'lofygame_initialize_game_ratings');

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
 * Clean up old ratings periodically
 */
function lofygame_cleanup_old_user_ratings() {
    $games = get_posts(array(
        'post_type' => 'game',
        'posts_per_page' => -1,
        'meta_key' => '_user_ratings',
        'post_status' => 'publish'
    ));
    
    $thirty_days_ago = current_time('timestamp') - (30 * 24 * 60 * 60);
    
    foreach ($games as $game) {
        $user_ratings = get_post_meta($game->ID, '_user_ratings', true);
        
        if (is_array($user_ratings)) {
            $cleaned = false;
            
            foreach ($user_ratings as $key => $rating_data) {
                // Remove ratings older than 30 days from non-logged-in users
                if (strpos($key, 'ip_') === 0 && $rating_data['timestamp'] < $thirty_days_ago) {
                    unset($user_ratings[$key]);
                    $cleaned = true;
                }
            }
            
            if ($cleaned) {
                // Recalculate average
                if (count($user_ratings) > 0) {
                    $total = 0;
                    foreach ($user_ratings as $rating_data) {
                        $total += $rating_data['rating'];
                    }
                    $new_average = round($total / count($user_ratings), 1);
                    
                    update_post_meta($game->ID, '_user_ratings', $user_ratings);
                    update_post_meta($game->ID, '_game_avg_rating', $new_average);
                    update_post_meta($game->ID, '_game_rating_count', count($user_ratings));
                } else {
                    // No ratings left, reset to defaults
                    delete_post_meta($game->ID, '_user_ratings');
                    update_post_meta($game->ID, '_game_avg_rating', 4.2);
                    update_post_meta($game->ID, '_game_rating_count', 0);
                }
            }
        }
    }
}

// Schedule cleanup to run daily
if (!wp_next_scheduled('lofygame_cleanup_user_ratings')) {
    wp_schedule_event(time(), 'daily', 'lofygame_cleanup_user_ratings');
}
add_action('lofygame_cleanup_user_ratings', 'lofygame_cleanup_old_user_ratings');

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
?>