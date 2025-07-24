<?php
/**
 * Custom Post Types
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
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
?>