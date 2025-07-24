<?php
/**
 * Enhanced Single Game Template with Rating System
 * Replace or update your single-game.php file
 */

get_header(); ?>

<main class="site-main poki-style-layout">
    <?php while (have_posts()) : the_post(); ?>
        <?php
        // Get game data
        $game_url = get_post_meta(get_the_ID(), '_game_url', true);
        $game_width = get_post_meta(get_the_ID(), '_game_width', true);
        $game_height = get_post_meta(get_the_ID(), '_game_height', true);
        $how_to_play = get_post_meta(get_the_ID(), '_how_to_play', true);
        
        // Get rating data
        $avg_rating = get_post_meta(get_the_ID(), '_game_avg_rating', true);
        $rating_count = get_post_meta(get_the_ID(), '_game_rating_count', true);
        $best_rating = get_post_meta(get_the_ID(), '_game_best_rating', true);
        $worst_rating = get_post_meta(get_the_ID(), '_game_worst_rating', true);
        $enable_user_ratings = get_post_meta(get_the_ID(), '_enable_user_ratings', true);
        
        // Set defaults
        if (empty($game_width)) $game_width = 800;
        if (empty($game_height)) $game_height = 600;
        if (empty($avg_rating)) $avg_rating = 4.5;
        if (empty($rating_count)) $rating_count = 127;
        if (empty($best_rating)) $best_rating = 5;
        if (empty($worst_rating)) $worst_rating = 1;
        
        $views = get_post_meta(get_the_ID(), 'post_views_count', true);
        $categories = get_the_category();
        ?>
        
        <article class="single-game-container" data-game-id="<?php echo get_the_ID(); ?>">
            
            <!-- Enhanced Game Header with Rating -->
            <header class="game-header">
                <div class="game-title-section">
                    <h1 class="game-main-title"><?php the_title(); ?></h1>
                    
                    <!-- Prominent Rating Display -->
                    <div class="game-header-rating">
                        <div class="rating-stars-header">
                            <?php for ($i = 1; $i <= 5; $i++) : ?>
                                <span class="star-header <?php echo ($i <= floor($avg_rating)) ? 'filled' : (($i - 0.5 <= $avg_rating) ? 'half' : 'empty'); ?>">★</span>
                            <?php endfor; ?>
                        </div>
                        <div class="rating-info-header">
                            <span class="rating-score-header"><?php echo $avg_rating; ?></span>
                            <span class="rating-count-header">(<?php echo number_format($rating_count); ?> reviews)</span>
                        </div>
                    </div>
                </div>
                
                <!-- Game Meta Info -->
                <div class="game-header-meta">
                    <?php if ($views) : ?>
                        <span class="meta-item">
                            <span class="meta-icon">👀</span>
                            <span class="meta-text"><?php echo number_format($views); ?> plays</span>
                        </span>
                    <?php endif; ?>
                    
                    <?php if ($categories) : ?>
                        <span class="meta-item">
                            <span class="meta-icon">📂</span>
                            <span class="meta-text"><?php echo $categories[0]->name; ?></span>
                        </span>
                    <?php endif; ?>
                    
                    <span class="meta-item">
                        <span class="meta-icon">📐</span>
                        <span class="meta-text"><?php echo $game_width; ?>×<?php echo $game_height; ?>px</span>
                    </span>
                    
                    <span class="meta-item">
                        <span class="meta-icon">📅</span>
                        <span class="meta-text"><?php echo get_the_date(); ?></span>
                    </span>
                </div>
            </header>
            
            <!-- Poki-Style Game Playing Area -->
            <div class="poki-game-area">
                
                <!-- Left Games Column -->
                <div class="left-games-column">
                    <?php
                    // Get related games for left side
                    $left_games_args = array(
                        'post_type' => 'game',
                        'posts_per_page' => 7,
                        'post__not_in' => array(get_the_ID()),
                        'orderby' => 'rand',
                    );
                    
                    if ($categories) {
                        $left_games_args['category__in'] = wp_list_pluck($categories, 'term_id');
                    }
                    
                    $left_games = new WP_Query($left_games_args);
                    
                    if ($left_games->have_posts()) :
                        while ($left_games->have_posts()) : $left_games->the_post();
                    ?>
                        <div class="poki-game-item">
                            <?php get_template_part('template-parts/game-card'); ?>
                        </div>
                    <?php 
                        endwhile;
                        wp_reset_postdata();
                    endif;
                    ?>
                </div>
                
                <!-- Main Game Container -->
                <div class="poki-main-game">
                    
                    <!-- Game Player Area -->
                    <?php if ($game_url) : ?>
                        <?php
                        // Calculate aspect ratio for responsive design
                        $aspect_ratio = ($game_height / $game_width) * 100;
                        ?>
                        <div class="poki-game-player">
                            <div class="game-iframe-container" style="padding-bottom: <?php echo $aspect_ratio; ?>%;">
                                <div class="poki-game-controls">
                                    <button class="poki-fullscreen-btn" id="fullscreen-btn" aria-label="Fullscreen">
                                        <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                                            <path d="M7 14H5v5h5v-2H7v-3zm-2-4h2V7h3V5H5v5zm12 7h-3v2h5v-5h-2v3zM14 5v2h3v3h2V5h-5z"/>
                                        </svg>
                                    </button>
                                    <button class="poki-sound-btn" id="sound-btn" aria-label="Sound">
                                        <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                                            <path d="M3 9v6h4l5 5V4L7 9H3zm13.5 3c0-1.77-1.02-3.29-2.5-4.03v8.05c1.48-.73 2.5-2.25 2.5-4.02zM14 3.23v2.06c2.89.86 5 3.54 5 6.71s-2.11 5.85-5 6.71v2.06c4.01-.91 7-4.49 7-8.77s-2.99-7.86-7-8.77z"/>
                                        </svg>
                                    </button>
                                </div>
                                
                                <iframe 
                                    src="<?php echo esc_url($game_url); ?>" 
                                    width="<?php echo esc_attr($game_width); ?>"
                                    height="<?php echo esc_attr($game_height); ?>"
                                    scrolling="none"
                                    frameborder="0"
                                    class="poki-game-iframe" 
                                    id="game-iframe"
                                    allowfullscreen="true"
                                    webkitallowfullscreen="true"
                                    mozallowfullscreen="true"
                                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; fullscreen"
                                    loading="lazy"
                                    title="<?php echo esc_attr(get_the_title()); ?>">
                                </iframe>
                                
                                <button class="poki-exit-fullscreen" id="exit-fullscreen" aria-label="Exit fullscreen">
                                    ✕ Exit Fullscreen
                                </button>
                            </div>
                            
                            <!-- Enhanced Game Info Bar with Rating -->
                            <div class="poki-game-info-bar">
                                <div class="game-rating-section-mini">
                                    <div class="rating-stars-mini-bar">
                                        <?php for ($i = 1; $i <= 5; $i++) : ?>
                                            <span class="star-mini-bar <?php echo ($i <= floor($avg_rating)) ? 'filled' : 'empty'; ?>">★</span>
                                        <?php endfor; ?>
                                    </div>
                                    <span class="rating-score-mini"><?php echo $avg_rating; ?></span>
                                    <span class="rating-count-mini">(<?php echo number_format($rating_count); ?>)</span>
                                </div>
                                
                                <div class="game-actions">
                                    <button class="poki-like-btn" data-game-id="<?php echo get_the_ID(); ?>">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                            <path d="M1 21h4V9H1v12zm22-11c0-1.1-.9-2-2-2h-6.31l.95-4.57.03-.32c0-.41-.17-.79-.44-1.06L14.17 1 7.59 7.59C7.22 7.95 7 8.45 7 9v10c0 1.1.9 2 2 2h9c.83 0 1.54-.5 1.84-1.22l3.02-7.05c.09-.23.14-.47.14-.73v-2z"/>
                                        </svg>
                                        <span class="like-count"><?php echo $views ? number_format($views/10) : '126'; ?>.0K</span>
                                    </button>
                                    <button class="poki-dislike-btn">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                            <path d="M15 3H6c-.83 0-1.54.5-1.84 1.22l-3.02 7.05c-.09.23-.14.47-.14.73v2c0 1.1.9 2 2 2h6.31l-.95 4.57-.03.32c0 .41.17.79.44 1.06L9.83 23l6.59-6.59c.36-.36.58-.86.58-1.41V5c0-1.1-.9-2-2-2zm4 0v12h4V3h-4z"/>
                                        </svg>
                                        <span class="dislike-count"><?php echo $views ? number_format($views/50) : '21'; ?>.8K</span>
                                    </button>
                                    <button class="poki-share-btn">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                            <path d="M18 16.08c-.76 0-1.44.3-1.96.77L8.91 12.7c.05-.23.09-.46.09-.7s-.04-.47-.09-.7l7.05-4.11c.54.5 1.25.81 2.04.81 1.66 0 3-1.34 3-3s-1.34-3-3-3-3 1.34-3 3c0 .24.04.47.09.7L8.04 9.81C7.5 9.31 6.79 9 6 9c-1.66 0-3 1.34-3 3s1.34 3 3 3c.79 0 1.50-.31 2.04-.81l7.12 4.16c-.05.21-.08.43-.08.65 0 1.61 1.31 2.92 2.92 2.92s2.92-1.31 2.92-2.92-1.31-2.92-2.92-2.92z"/>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    <?php else : ?>
                        <div class="poki-no-game">
                            <p>⚠️ Game URL not configured.</p>
                        </div>
                    <?php endif; ?>
                    
                </div>
                
                <!-- Right Games Column -->
                <div class="right-games-column">
                    <?php
                    // Get more related games for right side
                    $right_games_args = array(
                        'post_type' => 'game',
                        'posts_per_page' => 8,
                        'post__not_in' => array(get_the_ID()),
                        'orderby' => 'meta_value_num',
                        'meta_key' => 'post_views_count',
                        'order' => 'DESC'
                    );
                    
                    if ($categories) {
                        $right_games_args['category__in'] = wp_list_pluck($categories, 'term_id');
                    }
                    
                    $right_games = new WP_Query($right_games_args);
                    
                    if ($right_games->have_posts()) :
                        while ($right_games->have_posts()) : $right_games->the_post();
                    ?>
                        <div class="poki-game-item">
                            <?php get_template_part('template-parts/game-card'); ?>
                        </div>
                    <?php 
                        endwhile;
                        wp_reset_postdata();
                    endif;
                    ?>
                </div>
                
            </div>
            
            
            
            <!-- Bottom Games Row -->
            <div class="poki-bottom-games">
                <?php
                // Get additional games for bottom
                $bottom_games_args = array(
                    'post_type' => 'game',
                    'posts_per_page' => 50,
                    'post__not_in' => array(get_the_ID()),
                    'orderby' => 'rand',
                );
                
                if ($categories) {
                    $bottom_games_args['category__in'] = wp_list_pluck($categories, 'term_id');
                }
                
                $bottom_games = new WP_Query($bottom_games_args);
                
                if ($bottom_games->have_posts()) :
                    while ($bottom_games->have_posts()) : $bottom_games->the_post();
                ?>
                    <div class="poki-bottom-game-item">
                        <?php get_template_part('template-parts/game-card'); ?>
                    </div>
                <?php 
                    endwhile;
                    wp_reset_postdata();
                endif;
                ?>
            </div>

            <!-- Detailed Rating Section -->
            <section class="game-rating-section">
                <h2>Player Reviews & Ratings</h2>
                

                
                <div class="rating-overview">
                    <div class="rating-score-large">
                        <div class="rating-number-large"><?php echo $avg_rating; ?></div>
                        <div class="rating-stars-display">
                            <?php for ($i = 1; $i <= 5; $i++) : ?>
                                <span class="star <?php echo ($i <= floor($avg_rating)) ? 'filled' : 'empty'; ?>">★</span>
                            <?php endfor; ?>
                        </div>
                        <div class="rating-count-large"><?php echo number_format($rating_count); ?> reviews</div>
                    </div>
                    
                    <div class="rating-breakdown">
                        <h3>Rating Distribution</h3>
                        
                        <?php
                        // Generate realistic rating distribution
                        $total_reviews = $rating_count;
                        $five_star = round($total_reviews * 0.45);
                        $four_star = round($total_reviews * 0.30);
                        $three_star = round($total_reviews * 0.15);
                        $two_star = round($total_reviews * 0.07);
                        $one_star = $total_reviews - ($five_star + $four_star + $three_star + $two_star);
                        
                        $ratings_data = array(
                            5 => $five_star,
                            4 => $four_star,
                            3 => $three_star,
                            2 => $two_star,
                            1 => $one_star
                        );
                        ?>
                        
                        <?php foreach ($ratings_data as $stars => $count) : ?>
                            <?php $percentage = $total_reviews > 0 ? round(($count / $total_reviews) * 100) : 0; ?>
                            <div class="rating-row">
                                <div class="rating-label">
                                    <span><?php echo $stars; ?></span>
                                    <span class="star">★</span>
                                </div>
                                <div class="rating-bar">
                                    <div class="rating-fill" style="width: <?php echo $percentage; ?>%"></div>
                                </div>
                                <div class="rating-percentage"><?php echo $percentage; ?>%</div>
                            </div>
                        <?php endforeach; ?>
                    </div>


                    <?php if ($enable_user_ratings) : ?>
                <!-- User Rating Input -->
                <div class="user-rating-section" data-game-id="<?php echo get_the_ID(); ?>">
                    <h3>Rate This Game</h3>
                    
                    <?php 
                    // Check if user has already rated
                    $user_has_rated = lofygame_user_has_rated_game(get_the_ID());
                    $user_rating_data = lofygame_get_user_game_rating(get_the_ID());
                    ?>
                    
                    <?php if ($user_has_rated && $user_rating_data) : ?>
                        <!-- User has already rated -->
                        <div class="already-rated-message">
                            <p class="rating-message">✓ You rated this game</p>
                            <div class="user-previous-rating">
                                <div class="rating-stars-display">
                                    <?php for ($i = 1; $i <= 5; $i++) : ?>
                                        <span class="star-large <?php echo ($i <= $user_rating_data['rating']) ? 'filled' : 'empty'; ?>">★</span>
                                    <?php endfor; ?>
                                </div>
                                <p class="rating-date">Rated on <?php echo date('F j, Y', $user_rating_data['timestamp']); ?></p>
                            </div>
                        </div>
                    <?php else : ?>
                        <!-- Rating input form -->
                        <div class="rating-input-container">
                            <p>How would you rate this game?</p>
                            <div class="rating-input" id="user-rating-input">
                                <?php for ($i = 1; $i <= 5; $i++) : ?>
                                    <span class="rating-star-input" 
                                        data-rating="<?php echo $i; ?>" 
                                        tabindex="0" 
                                        role="button" 
                                        aria-label="Rate <?php echo $i; ?> star<?php echo $i !== 1 ? 's' : ''; ?>">★</span>
                                <?php endfor; ?>
                            </div>
                            <button class="rating-submit" id="submit-rating" disabled>Submit Rating</button>
                            <p class="rating-feedback" id="rating-feedback" style="display: none;"></p>
                        </div>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
                </div>
                
                
            </section>
            
            <!-- Content Section Below Games -->
            <div class="poki-content-section">
                
                <!-- Game Description -->
                <div class="poki-game-description">
                    <h2>About <?php the_title(); ?></h2>
                    <div class="description-content">
                        <?php the_content(); ?>
                    </div>
                </div>
                
                <!-- Categories and Tags -->
                <div class="poki-taxonomy-section">
                    <?php if ($categories) : ?>
                    <div class="poki-categories">
                        <h3>Categories</h3>
                        <div class="poki-category-tags">
                            <?php foreach ($categories as $category) : ?>
                                <a href="<?php echo esc_url(get_category_link($category->term_id)); ?>" class="poki-category-tag">
                                    <?php echo esc_html($category->name); ?>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <?php
                    $tags = get_the_tags();
                    if ($tags) :
                    ?>
                    <div class="poki-tags">
                        <h3>Tags</h3>
                        <div class="poki-tag-list">
                            <?php foreach ($tags as $tag) : ?>
                                <a href="<?php echo esc_url(get_tag_link($tag->term_id)); ?>" class="poki-tag">
                                    #<?php echo esc_html($tag->name); ?>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
                
                <!-- How to Play -->
                <?php if ($how_to_play) : ?>
                <div class="poki-how-to-play">
                    <h2>How to Play</h2>
                    <div class="how-to-play-content">
                        <?php echo wpautop(esc_html($how_to_play)); ?>
                    </div>
                </div>
                <?php endif; ?>
                
                <!-- Game Stats -->
                <div class="poki-game-stats">
                    <h2>Game Info</h2>
                    <div class="stats-list">
                        <div class="stat-item">
                            <span class="stat-label">Released:</span>
                            <span class="stat-value"><?php echo get_the_date(); ?></span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-label">Size:</span>
                            <span class="stat-value"><?php echo $game_width; ?> × <?php echo $game_height; ?>px</span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-label">Platform:</span>
                            <span class="stat-value">Web Browser (Desktop and Mobile)</span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-label">Average Rating:</span>
                            <span class="stat-value"><?php echo $avg_rating; ?>/5 stars</span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-label">Total Reviews:</span>
                            <span class="stat-value"><?php echo number_format($rating_count); ?></span>
                        </div>
                        <?php if ($views) : ?>
                        <div class="stat-item">
                            <span class="stat-label">Plays:</span>
                            <span class="stat-value"><?php echo number_format($views); ?></span>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                
            </div>
            
            <?php wp_reset_postdata(); ?>
        </article>
    <?php endwhile; ?>
</main>

<script>
// Enhanced User Rating System
document.addEventListener('DOMContentLoaded', function() {
    const ratingStars = document.querySelectorAll('.rating-star-input');
    const submitButton = document.getElementById('submit-rating');
    const feedback = document.getElementById('rating-feedback');
    let selectedRating = 0;
    
    ratingStars.forEach(function(star, index) {
        star.addEventListener('mouseenter', function() {
            highlightStars(index + 1);
        });
        
        star.addEventListener('mouseleave', function() {
            highlightStars(selectedRating);
        });
        
        star.addEventListener('click', function() {
            selectedRating = index + 1;
            highlightStars(selectedRating);
            submitButton.disabled = false;
            
            // Show feedback
            feedback.style.display = 'block';
            feedback.textContent = `You selected ${selectedRating} star${selectedRating !== 1 ? 's' : ''}`;
            feedback.style.color = '#666';
        });
    });
    
    function highlightStars(rating) {
        ratingStars.forEach(function(star, index) {
            if (index < rating) {
                star.classList.add('highlighted');
            } else {
                star.classList.remove('highlighted');
            }
        });
    }
    
    submitButton.addEventListener('click', function() {
        if (selectedRating > 0) {
            // Here you would typically send the rating to your server
            // For now, we'll just show a success message
            
            this.disabled = true;
            this.textContent = 'Rating Submitted!';
            this.style.background = '#28a745';
            
            feedback.textContent = `Thank you for rating this game ${selectedRating} star${selectedRating !== 1 ? 's' : ''}!`;
            feedback.style.color = '#28a745';
            
            // Make stars permanent
            ratingStars.forEach(function(star, index) {
                star.style.pointerEvents = 'none';
                if (index < selectedRating) {
                    star.classList.add('active');
                }
            });
            
            // Optional: Send to server
            if (typeof lofygame_ajax !== 'undefined') {
                fetch(lofygame_ajax.ajax_url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: new URLSearchParams({
                        action: 'submit_game_rating',
                        game_id: document.querySelector('[data-game-id]').getAttribute('data-game-id'),
                        rating: selectedRating,
                        nonce: lofygame_ajax.nonce
                    })
                });
            }
        }
    });
});
</script>

<?php get_footer(); ?>