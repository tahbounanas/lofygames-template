<?php
/**
 * Rating and Review System
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Use if (!function_exists()) to prevent redeclaration errors if this function is also defined elsewhere.
if (!function_exists('lofygame_add_rating_meta_boxes')) {
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
}

// Use if (!function_exists()) to prevent redeclaration errors if this function is also defined elsewhere.
if (!function_exists('lofygame_rating_meta_box_callback')) {
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
}

// Use if (!function_exists()) to prevent redeclaration errors if this function is also defined elsewhere.
if (!function_exists('lofygame_save_rating_details')) {
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
}

// Use if (!function_exists()) to prevent redeclaration errors if this function is also defined elsewhere.
if (!function_exists('lofygame_handle_user_rating_submission')) {
    /**
     * Handle AJAX rating submission from users
     */
    function lofygame_handle_user_rating_submission() {
        check_ajax_referer('lofygame_nonce', 'nonce');
        
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

        // Check if user ratings are enabled for this game
        $enable_user_ratings = get_post_meta($game_id, '_enable_user_ratings', true);
        if (!$enable_user_ratings) {
            wp_send_json_error('User ratings are disabled for this game.');
            return;
        }
        
        // Get user identifier
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
        
        foreach ($user_ratings as $user_rating_data) {
            $total_rating += $user_rating_data['rating'];
        }
        
        $new_average = round($total_rating / $rating_count, 1);
        
        // Update the game's aggregate rating and count
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
}

// Use if (!function_exists()) to prevent redeclaration errors if this function is also defined elsewhere.
if (!function_exists('lofygame_get_user_game_rating')) {
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
}

// Use if (!function_exists()) to prevent redeclaration errors if this function is also defined elsewhere.
if (!function_exists('lofygame_user_has_rated_game')) {
    /**
     * Check if user has already rated a game
     */
    function lofygame_user_has_rated_game($game_id) {
        return lofygame_get_user_game_rating($game_id) !== null;
    }
}

// Use if (!function_exists()) to prevent redeclaration errors if this function is also defined elsewhere.
if (!function_exists('lofygame_initialize_game_ratings')) {
    /**
     * Initialize ratings for games that don't have any on first run
     */
    function lofygame_initialize_game_ratings() {
        // Only run this once on theme activation or first load
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
            if (empty($avg_rating) && empty($rating_count)) {
                update_post_meta($game->ID, '_game_avg_rating', 4.2); // Default average
                update_post_meta($game->ID, '_game_rating_count', 0);  // Start with 0 user reviews
                update_post_meta($game->ID, '_game_best_rating', 5);
                update_post_meta($game->ID, '_game_worst_rating', 1);
            }
            
            // Ensure user ratings are enabled by default if not set
            if (get_post_meta($game->ID, '_enable_user_ratings', true) === '') {
                update_post_meta($game->ID, '_enable_user_ratings', 1);
            }

            // Initialize _user_ratings meta if it doesn't exist
            if (get_post_meta($game->ID, '_user_ratings', true) === '') {
                update_post_meta($game->ID, '_user_ratings', []);
            }
        }
        
        update_option('lofygame_ratings_initialized', true);
    }
    add_action('init', 'lofygame_initialize_game_ratings');
}

// Use if (!function_exists()) to prevent redeclaration errors if this function is also defined elsewhere.
if (!function_exists('lofygame_display_game_rating')) {
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
}

// Use if (!function_exists()) to prevent redeclaration errors if this function is also defined elsewhere.
if (!function_exists('lofygame_cleanup_old_user_ratings')) {
    /**
     * Clean up old user ratings periodically
     * Removes IP-based ratings older than 30 days to prevent meta bloat
     */
    function lofygame_cleanup_old_user_ratings() {
        $games = get_posts(array(
            'post_type' => 'game',
            'posts_per_page' => -1,
            'meta_key' => '_user_ratings', // Only get games that have user ratings
            'post_status' => 'publish',
            'fields' => 'ids' // Only retrieve IDs for efficiency
        ));
        
        $thirty_days_ago = current_time('timestamp') - (30 * 24 * 60 * 60);
        
        foreach ($games as $game_id) { // Changed $game to $game_id for consistency with fields => 'ids'
            $user_ratings = get_post_meta($game_id, '_user_ratings', true);
            
            if (is_array($user_ratings) && !empty($user_ratings)) {
                $cleaned = false;
                $new_user_ratings = $user_ratings; // Create a copy to modify

                foreach ($new_user_ratings as $key => $rating_data) {
                    // Remove ratings older than 30 days from non-logged-in users (IP-based)
                    if (strpos($key, 'ip_') === 0 && isset($rating_data['timestamp']) && $rating_data['timestamp'] < $thirty_days_ago) {
                        unset($new_user_ratings[$key]);
                        $cleaned = true;
                    }
                }
                
                if ($cleaned) {
                    // Recalculate average and count based on remaining ratings
                    if (count($new_user_ratings) > 0) {
                        $total = 0;
                        foreach ($new_user_ratings as $rating_data) {
                            $total += $rating_data['rating'];
                        }
                        $new_average = round($total / count($new_user_ratings), 1);
                        $new_count = count($new_user_ratings);
                        
                        update_post_meta($game_id, '_user_ratings', $new_user_ratings);
                        update_post_meta($game_id, '_game_avg_rating', $new_average);
                        update_post_meta($game_id, '_game_rating_count', $new_count);
                    } else {
                        // No ratings left, reset to initial defaults
                        delete_post_meta($game_id, '_user_ratings');
                        update_post_meta($game_id, '_game_avg_rating', 4.2); // Reset to a sensible default
                        update_post_meta($game_id, '_game_rating_count', 0);
                    }
                }
            }
        }
        error_log("LofyGame: Completed cleanup of old user ratings.");
    }

    // Schedule cleanup to run daily
    if (!wp_next_scheduled('lofygame_cleanup_user_ratings')) {
        wp_schedule_event(time(), 'daily', 'lofygame_cleanup_user_ratings');
    }
    add_action('lofygame_cleanup_user_ratings', 'lofygame_cleanup_old_user_ratings');
}

// Use if (!function_exists()) to prevent redeclaration errors if this function is also defined elsewhere.
if (!function_exists('lofygame_add_rating_column')) {
    /**
     * Add Rating Column to Admin Games List
     */
    function lofygame_add_rating_column($columns) {
        $columns['rating'] = __('Rating');
        return $columns;
    }
    add_filter('manage_game_posts_columns', 'lofygame_add_rating_column');
}

// Use if (!function_exists()) to prevent redeclaration errors if this function is also defined elsewhere.
if (!function_exists('lofygame_rating_column_content')) {
    /**
     * Content for Rating Column in Admin Games List
     */
    function lofygame_rating_column_content($column, $post_id) {
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
    add_action('manage_game_posts_custom_column', 'lofygame_rating_column_content', 10, 2);
}

// Use if (!function_exists()) to prevent redeclaration errors if this function is also defined elsewhere.
if (!function_exists('lofygame_sortable_rating_column')) {
    /**
     * Make Rating Column Sortable
     */
    function lofygame_sortable_rating_column($columns) {
        $columns['rating'] = 'game_avg_rating';
        return $columns;
    }
    add_filter('manage_edit-game_sortable_columns', 'lofygame_sortable_rating_column');
}

// Use if (!function_exists()) to prevent redeclaration errors if this function is also defined elsewhere.


// Use if (!function_exists()) to prevent redeclaration errors if this function is also defined elsewhere.
if (!function_exists('lofygame_add_quick_edit_rating_script')) {
    /**
     * Quick edit rating functionality (Admin)
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
}

// Use if (!function_exists()) to prevent redeclaration errors if this function is also defined elsewhere.



// Use if (!function_exists()) to prevent redeclaration errors if this function is also defined elsewhere.
if (!function_exists('lofygame_add_bulk_rating_update')) {
    /**
     * Bulk Rating Update Tool (Admin Page)
     */
    function lofygame_add_bulk_rating_update() {
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
    }
    add_action('init', 'lofygame_add_bulk_rating_update');
}

// Use if (!function_exists()) to prevent redeclaration errors if this function is also defined elsewhere.
if (!function_exists('lofygame_enhanced_bulk_ratings_page')) {
    /**
     * Enhanced bulk ratings page content
     */
    function lofygame_enhanced_bulk_ratings_page() {
        // Handle bulk update action
        if (isset($_POST['update_ratings'])) {
            $games = get_posts(array('post_type' => 'game', 'posts_per_page' => -1));
            $updated = 0;

            foreach ($games as $game) {
                $current_rating = get_post_meta($game->ID, '_game_avg_rating', true);
                if (empty($current_rating) || get_post_meta($game->ID, '_game_rating_count', true) === '') {
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
                    update_post_meta($game->ID, '_enable_user_ratings', 1); // Enable user ratings by default

                    $updated++;
                }
            }

            echo '<div class="notice notice-success"><p>Updated ratings for ' . $updated . ' games!</p></div>';
        }

        // Handle import action
        if (isset($_POST['import_ratings']) && isset($_FILES['ratings_file'])) {
            lofygame_import_ratings();
        }

        $import_results = get_transient('lofygame_rating_import_results');

        if ($import_results) {
            echo '<div class="notice notice-success"><p>Imported ' . $import_results['imported'] . ' ratings successfully!</p></div>';
            if (!empty($import_results['errors'])) {
                echo '<div class="notice notice-warning"><p>Errors: ' . implode(', ', array_slice($import_results['errors'], 0, 5)) . '</p></div>';
            }
            delete_transient('lofygame_rating_import_results');
        }
        
        ?>
        <div class="wrap">
            <h1>⭐ Game Ratings Management</h1>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; margin: 2rem 0;">
                <!-- Bulk Update Section -->
                <div class="card">
                    <h2>🔄 Bulk Update Missing Ratings</h2>
                    <p>Add realistic ratings to games that don't have ratings yet. This will not overwrite existing ratings.</p>
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
                    <p>Upload a CSV file with rating data. Existing ratings for matching game IDs will be overwritten.</p>
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
                // Retrieve recent user ratings from post meta
                $recent_ratings_data = [];
                $all_games = get_posts([
                    'post_type' => 'game',
                    'posts_per_page' => -1,
                    'fields' => 'ids', // Only get post IDs for efficiency
                ]);

                foreach ($all_games as $game_id) {
                    $user_ratings = get_post_meta($game_id, '_user_ratings', true);
                    if (is_array($user_ratings)) {
                        foreach ($user_ratings as $identifier => $rating_data) {
                            $recent_ratings_data[] = [
                                'game_id' => $game_id,
                                'rating' => $rating_data['rating'],
                                'timestamp' => $rating_data['timestamp'],
                                'identifier_hash' => substr($identifier, strrpos($identifier, '_') + 1, 8), // Hash of IP or user ID
                            ];
                        }
                    }
                }

                // Sort by timestamp in descending order
                usort($recent_ratings_data, function($a, $b) {
                    return $b['timestamp'] - $a['timestamp'];
                });

                // Get top 10 recent ratings
                $recent_ratings = array_slice($recent_ratings_data, 0, 10);
                
                if (!empty($recent_ratings)) {
                    echo '<table class="widefat">';
                    echo '<thead><tr><th>Game</th><th>Rating</th><th>Date</th><th>User/IP Hash</th></tr></thead>';
                    echo '<tbody>';
                    
                    foreach ($recent_ratings as $rating_data) {
                        $game_title = get_the_title($rating_data['game_id']);
                        $rating_stars = str_repeat('★', $rating_data['rating']) . str_repeat('☆', 5 - $rating_data['rating']);
                        $date = date('M j, Y H:i', $rating_data['timestamp']);
                        
                        echo '<tr>';
                        echo '<td><strong>' . esc_html($game_title) . '</strong></td>';
                        echo '<td><span style="color: #ffa500;">' . $rating_stars . '</span> (' . $rating_data['rating'] . '/5)</td>';
                        echo '<td>' . $date . '</td>';
                        echo '<td><code>' . $rating_data['identifier_hash'] . '...</code></td>';
                        echo '</tr>';
                    }
                    
                    echo '</tbody></table>';
                } else {
                    echo '<p>No recent user rating activity.</p>';
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
}

// Use if (!function_exists()) to prevent redeclaration errors if this function is also defined elsewhere.
if (!function_exists('lofygame_export_ratings')) {
    /**
     * Export ratings functionality
     */
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
    add_action('admin_post_lofygame_export_ratings', 'lofygame_export_ratings'); // Hook for export form
}

// Use if (!function_exists()) to prevent redeclaration errors if this function is also defined elsewhere.
if (!function_exists('lofygame_import_ratings')) {
    /**
     * Import ratings functionality
     */
    function lofygame_import_ratings() {
        if (!current_user_can('manage_options') || !wp_verify_nonce($_POST['_wpnonce'], 'lofygame_import_ratings_nonce')) { // Added nonce check
            wp_die('Unauthorized');
        }
        
        $file = $_FILES['ratings_file']['tmp_name'];
        $imported = 0;
        $errors = array();
        
        if (($handle = fopen($file, 'r')) !== FALSE) {
            $header = fgetcsv($handle); // Read header row
            
            // Map data to expected columns based on header
            $game_id_col = array_search('game_id', $header);
            $avg_rating_col = array_search('avg_rating', $header);
            $rating_count_col = array_search('rating_count', $header);
            $best_rating_col = array_search('best_rating', $header);
            $worst_rating_col = array_search('worst_rating', $header);
            $enable_user_ratings_col = array_search('enable_user_ratings', $header);

            $game_id = isset($data[$game_id_col]) ? intval($data[$game_id_col]) : 0;
            $avg_rating = isset($data[$avg_rating_col]) ? floatval($data[$avg_rating_col]) : 0;
            $rating_count = isset($data[$rating_count_col]) ? intval($data[$rating_count_col]) : 0;
            $best_rating = isset($data[$best_rating_col]) ? floatval($data[$best_rating_col]) : 5;
            $worst_rating = isset($data[$worst_rating_col]) ? floatval($data[$worst_rating_col]) : 1;
            $enable_user_ratings = isset($data[$enable_user_ratings_col]) && ($data[$enable_user_ratings_col] === 'yes') ? 1 : 0;
            
            while (($data = fgetcsv($handle)) !== FALSE) {
                if ($game_id && get_post_type($game_id) === 'game') {
                    if ($avg_rating >= 0 && $avg_rating <= 5 && $rating_count >= 0) { // Allow 0 count for new games
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
                    $errors[] = "Game not found or invalid ID: {$game_id}";
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
    add_action('admin_post_lofygame_import_ratings', 'lofygame_import_ratings'); // Hook for import form
}





