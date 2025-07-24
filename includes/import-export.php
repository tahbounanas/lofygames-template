<?php
/**
 * Import/Export Functionality
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

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
    </div>
    <?php
}

/**
 * Handle Game Export
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
    
    // CSV Headers with all fields
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
 * Handle Game Import
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
                update_post_meta($post_id, '_how_to_play', sanitize_textarea_field($how_to_play));
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
 * Display Import Results
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
        }
        
        if ($imported > 0) {
            echo '<p><a href="' . admin_url('edit-tags.php?taxonomy=category') . '" class="button button-primary">View Categories</a></p>';
        }
        
        echo '</div>';
    }
}
add_action('admin_notices', 'lofygame_import_admin_notices');
?>