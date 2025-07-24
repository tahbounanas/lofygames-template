<?php
/**
 * Category Card Template
 * Template part for displaying category cards with image and info
 */

$category = $args['category'] ?? null;
if (!$category) return;

$category_image = get_term_meta($category->term_id, 'category_image', true);
$category_link = get_category_link($category->term_id);
$games_count = $category->count;

// Fallback image if no category image is set
if (empty($category_image)) {
    $category_image = get_template_directory_uri() . '/images/placeholder-category.jpg';
}

// Get category icon based on category name (you can customize this)
$category_icons = array(
    'action' => '⚡',
    'adventure' => '🗺️',
    'puzzle' => '🧩',
    'strategy' => '🎯',
    'racing' => '🏎️',
    'sports' => '⚽',
    'arcade' => '🕹️',
    'platform' => '🏃',
    'shooting' => '🎯',
    'simulation' => '🌐',
    'rpg' => '⚔️',
    'horror' => '👻',
    'casual' => '😊',
    'multiplayer' => '👥',
    'kids' => '👶',
    'retro' => '📟'
);

$category_slug = strtolower($category->slug);
$category_icon = '🎮'; // Default icon

// Find matching icon
foreach ($category_icons as $key => $icon) {
    if (strpos($category_slug, $key) !== false) {
        $category_icon = $icon;
        break;
    }
}
?>

<article class="category-card" data-category-id="<?php echo $category->term_id; ?>">
    <a href="<?php echo esc_url($category_link); ?>" class="category-card-link">
        <div class="category-card-content">
            <!-- Category Image (Left 40%) -->
            <div class="category-image">
                <img src="<?php echo esc_url($category_image); ?>" 
                     alt="<?php echo esc_attr($category->name); ?>" 
                     class="category-thumbnail"
                     loading="lazy">
                <div class="category-overlay">
                    <div class="category-icon"><?php echo $category_icon; ?></div>
                </div>
            </div>
            
            <!-- Category Info (Right 60%) -->
            <div class="category-info">
                <div class="category-header">
                    <h3 class="category-name"><?php echo esc_html($category->name); ?></h3>
                    <span class="games-count">
                        <?php echo $games_count; ?> 
                        <?php echo _n('game', 'games', $games_count, 'lofygame'); ?>
                    </span>
                </div>
                
                <div class="category-description">
                    <?php if (!empty($category->description)) : ?>
                        <p><?php echo esc_html(wp_trim_words($category->description, 15)); ?></p>
                    <?php else : ?>
                        <p>Discover amazing <?php echo esc_html(strtolower($category->name)); ?> games</p>
                    <?php endif; ?>
                </div>
                
                <div class="category-footer">
                    <span class="play-now-text">Play Now</span>
                    <span class="category-arrow">→</span>
                </div>
            </div>
        </div>
        
        <!-- Hover Effect -->
        <div class="category-hover-effect">
            <div class="hover-content">
                <span class="hover-icon">🎮</span>
                <span class="hover-text">Browse Games</span>
            </div>
        </div>
    </a>
</article>