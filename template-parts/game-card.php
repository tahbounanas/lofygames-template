<?php
/**
 * Enhanced Minimal Game Card Template with Star Ratings
 * Replace the content of template-parts/game-card.php
 */

$preview_video_url = get_post_meta(get_the_ID(), '_preview_video_url', true);
$game_width = get_post_meta(get_the_ID(), '_game_width', true);
$game_height = get_post_meta(get_the_ID(), '_game_height', true);

// Get rating data
$avg_rating = get_post_meta(get_the_ID(), '_game_avg_rating', true);
$rating_count = get_post_meta(get_the_ID(), '_game_rating_count', true);

// Set defaults
if (empty($game_width)) $game_width = 800;
if (empty($game_height)) $game_height = 600;
if (empty($avg_rating)) $avg_rating = 4.5;
if (empty($rating_count)) $rating_count = 127;

// Get categories for badge
$categories = get_the_category();
$main_category = $categories ? $categories[0]->name : 'Game';
?>

<article class="game-card minimal-card" 
         data-has-video="<?php echo !empty($preview_video_url) ? 'true' : 'false'; ?>"
         data-game-id="<?php echo get_the_ID(); ?>"
         data-rating="<?php echo esc_attr($avg_rating); ?>">
    
    <a href="<?php the_permalink(); ?>" class="game-card-link">
        <div class="game-card-image minimal-image" data-video-url="<?php echo esc_attr($preview_video_url); ?>">
            <!-- Main Game Image - Always Visible -->
            <?php if (has_post_thumbnail()) : ?>
                <?php the_post_thumbnail('game-thumbnail', array(
                    'class' => 'game-thumbnail',
                    'alt' => get_the_title(),
                    'loading' => 'lazy'
                )); ?>
            <?php else : ?>
                <?php
                // Try to get first image from content if no featured image
                $post_content = get_the_content();
                preg_match('/<img[^>]+src=[\'"]([^\'"]+)[\'"][^>]*>/i', $post_content, $matches);
                $first_image = !empty($matches[1]) ? $matches[1] : get_template_directory_uri() . '/images/placeholder-game.jpg';
                ?>
                <img src="<?php echo esc_url($first_image); ?>" 
                     alt="<?php echo esc_attr(get_the_title()); ?>" 
                     class="game-thumbnail"
                     loading="lazy">
            <?php endif; ?>
            
            <!-- Video Preview (if available) - Hidden by default -->
            <?php if (!empty($preview_video_url)) : ?>
                <video 
                    class="game-preview-video" 
                    muted 
                    loop 
                    preload="none"
                    data-src="<?php echo esc_url($preview_video_url); ?>"
                    style="display: none; opacity: 0;"
                    poster="<?php echo get_the_post_thumbnail_url(get_the_ID(), 'game-thumbnail'); ?>">
                    <source data-src="<?php echo esc_url($preview_video_url); ?>" type="video/mp4">
                    Your browser does not support the video tag.
                </video>
                
                <!-- Video Play Indicator -->
                <div class="video-play-indicator">
                    <span class="play-icon">▶</span>
                    <span class="video-text">Preview</span>
                </div>
            <?php endif; ?>
            
            <!-- Category Badge (Top Left) -->
            <div class="game-category-badge">
                <?php echo esc_html($main_category); ?>
            </div>
            
            <!-- Rating Badge (Top Right) -->
            <div class="game-rating-badge">
                <div class="rating-stars-mini">
                    <?php for ($i = 1; $i <= 5; $i++) : ?>
                        <span class="star-mini <?php echo ($i <= floor($avg_rating)) ? 'filled' : (($i - 0.5 <= $avg_rating) ? 'half' : 'empty'); ?>">★</span>
                    <?php endfor; ?>
                </div>
                <span class="rating-value-mini"><?php echo $avg_rating; ?></span>
            </div>
            
            <!-- Title Overlay (shows on hover) -->
            <div class="minimal-title-overlay">
                <h3 class="minimal-game-title"><?php the_title(); ?></h3>
                
                <!-- Detailed Rating Info in Overlay -->
                <div class="game-rating-overlay">
                    <div class="rating-stars-large">
                        <?php for ($i = 1; $i <= 5; $i++) : ?>
                            <span class="star-large <?php echo ($i <= floor($avg_rating)) ? 'filled' : (($i - 0.5 <= $avg_rating) ? 'half' : 'empty'); ?>">★</span>
                        <?php endfor; ?>
                    </div>
                    <div class="rating-details">
                        <span class="rating-score"><?php echo $avg_rating; ?>/5</span>
                        <span class="rating-count">(<?php echo number_format($rating_count); ?> reviews)</span>
                    </div>
                </div>
                
                <!-- Game Meta Information -->
                <div class="game-meta-overlay">
                    <span class="game-size-info"><?php echo $game_width; ?>×<?php echo $game_height; ?></span>
                    <?php if (!empty($preview_video_url)) : ?>
                        <span class="video-available">📹 Video</span>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </a>
</article>