<?php
/**
 * Meta Boxes and Custom Fields
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

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
        $("#video_walkthrough_enabled").change(function() {
            if ($(this).is(":checked")) {
                $("#video_id_row").show();
            } else {
                $("#video_id_row").hide();
            }
        });
        
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
?>