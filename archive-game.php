<?php get_header(); ?>

<main class="site-main">
    <?php
    $term = get_queried_object();
    $is_category = is_category();
    $is_tag = is_tag();
    $is_games_archive = is_post_type_archive('game');
    ?>
    
    <!-- Enhanced Archive Header with SEO -->
    <header class="archive-header enhanced-archive-header">
        <div class="container">
            <!-- Breadcrumbs -->
            <?php echo lofygame_enhanced_breadcrumbs(); ?>
            
            <!-- Main Title -->
            <h1 class="archive-title seo-title">
                <?php echo lofygame_get_archive_title($term); ?>
            </h1>
            
            <!-- Games Count and Archive Info -->
            <div class="archive-meta">
                <div class="games-count-badge">
                    <?php if ($is_games_archive) : ?>
                        <?php $total_games = wp_count_posts('game')->publish; ?>
                        <span class="count-number"><?php echo $total_games; ?></span>
                        <span class="count-label"><?php echo _n('Game', 'Games', $total_games, 'lofygame'); ?></span>
                    <?php elseif (isset($term->count)) : ?>
                        <span class="count-number"><?php echo $term->count; ?></span>
                        <span class="count-label"><?php echo _n('Game', 'Games', $term->count, 'lofygame'); ?></span>
                    <?php endif; ?>
                </div>
                
                <div class="archive-type">
                    <?php if ($is_games_archive) : ?>
                        <span class="archive-type-badge games-badge">🎮 All Games</span>
                    <?php elseif ($is_category) : ?>
                        <span class="archive-type-badge category-badge">📂 Category</span>
                    <?php elseif ($is_tag) : ?>
                        <span class="archive-type-badge tag-badge">🏷️ Tag</span>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Category Image if available -->
            <?php if ($is_category && isset($term->term_id)) : 
                $category_image = get_term_meta($term->term_id, 'category_image', true);
                if ($category_image) : ?>
                    <div class="archive-image">
                        <img src="<?php echo esc_url($category_image); ?>" 
                             alt="<?php echo esc_attr($term->name); ?> games" 
                             class="archive-featured-image">
                    </div>
                <?php endif;
            endif; ?>
        </div>
    </header>

    <!-- Games Grid Section -->
    <section class="games-section archive-games-section">
        <div class="container">
            <?php if (have_posts()) : ?>
                <div class="games-grid games-grid-12-col" id="games-grid">
                    <?php while (have_posts()) : the_post(); ?>
                        <?php get_template_part('template-parts/game-card'); ?>
                    <?php endwhile; ?>
                </div>
                
                <?php if (get_next_posts_link() || get_previous_posts_link()) : ?>
                    <?php lofygame_modern_pagination(); ?>
                <?php endif; ?>
                
            <?php else : ?>
                <div class="no-games">
                    <h2>No games found</h2>
                    <p>Sorry, no games were found<?php if (!$is_games_archive) echo ' in this ' . ($is_category ? 'category' : 'tag'); ?>.</p>
                    <a href="<?php echo get_post_type_archive_link('game'); ?>" class="button">Browse All Games</a>
                </div>
            <?php endif; ?>
        </div>
    </section>
    
    <!-- Browse by Category Section (Same as index) -->
    <section class="categories-showcase archive-categories">
        <div class="container">
            <div class="categories-header">
                <h2 class="categories-title">Browse by Category</h2>
                <p class="categories-subtitle">Discover more amazing game categories</p>
            </div>
            
            <div class="categories-grid">
                <?php
                // Get other categories (excluding current if we're on a category page)
                $current_category_id = ($is_category && isset($term->term_id)) ? $term->term_id : null;
                $other_categories = lofygame_get_other_categories($current_category_id, 8);
                
                if ($other_categories) :
                    foreach ($other_categories as $category) :
                ?>
                    <?php get_template_part('template-parts/category-card', null, array('category' => $category)); ?>
                <?php 
                    endforeach;
                endif;
                ?>
            </div>
            
            <div class="categories-footer">
                <a href="<?php echo get_post_type_archive_link('game'); ?>" class="view-all-categories-btn">
                    View All Games
                    <span class="btn-arrow">→</span>
                </a>
            </div>
        </div>
    </section>
    
    <!-- SEO Description Section -->
    <section class="archive-description-section seo-content">
        <div class="container">
            <div class="description-content">
                <?php if ($is_games_archive) : ?>
                    <h2 class="description-title">About Our Free Online Games</h2>
                    
                    <div class="description-text">
                        <?php echo wpautop(esc_html(lofygame_get_archive_description($term))); ?>
                    </div>
                    
                    <!-- Additional SEO Content for Games Archive -->
                    <div class="seo-features">
                        <h3>Why Choose Our Gaming Platform?</h3>
                        <ul class="features-list">
                            <li>🎮 <strong>Huge Collection:</strong> Thousands of free games across all genres</li>
                            <li>🚀 <strong>Instant Play:</strong> No downloads, installations, or registrations required</li>
                            <li>📱 <strong>Cross-Platform:</strong> Play on desktop, tablet, or mobile devices</li>
                            <li>🔄 <strong>Daily Updates:</strong> New games added regularly to keep content fresh</li>
                            <li>⭐ <strong>Quality Guaranteed:</strong> Every game is tested and curated for the best experience</li>
                            <li>🔒 <strong>Safe & Secure:</strong> Family-friendly environment with safe gameplay</li>
                        </ul>
                    </div>
                    
                <?php elseif (($is_category || $is_tag) && isset($term->name)) : ?>
                    <h2 class="description-title">
                        About <?php echo esc_html($term->name); ?> Games
                    </h2>
                    
                    <div class="description-text">
                        <?php 
                        $description = lofygame_get_archive_description($term);
                        echo wpautop(esc_html($description));
                        ?>
                    </div>
                    
                    <!-- Additional SEO Content for Categories/Tags -->
                    <div class="seo-features">
                        <h3>Why Play <?php echo esc_html($term->name); ?> Games?</h3>
                        <ul class="features-list">
                            <li>🎮 <strong>Free to Play:</strong> All games are completely free with no hidden costs</li>
                            <li>🚀 <strong>Instant Play:</strong> No downloads or installations required</li>
                            <li>📱 <strong>Mobile Friendly:</strong> Play on any device - desktop, tablet, or phone</li>
                            <li>🔄 <strong>Regular Updates:</strong> New <?php echo strtolower($term->name); ?> games added frequently</li>
                            <li>⭐ <strong>High Quality:</strong> Carefully curated selection of the best games</li>
                        </ul>
                    </div>
                <?php endif; ?>
                
                <!-- Related Keywords for SEO -->
                <div class="related-terms">
                    <h4>Related Gaming Categories:</h4>
                    <div class="terms-cloud">
                        <?php
                        // Get related categories/tags
                        $exclude_ids = array();
                        if (isset($term->term_id)) {
                            $exclude_ids[] = $term->term_id;
                        }
                        
                        $related_terms = get_terms(array(
                            'taxonomy' => array('category', 'post_tag'),
                            'hide_empty' => true,
                            'number' => 10,
                            'exclude' => $exclude_ids,
                            'orderby' => 'count',
                            'order' => 'DESC'
                        ));
                        
                        if ($related_terms && !is_wp_error($related_terms)) :
                            foreach ($related_terms as $related_term) :
                        ?>
                            <a href="<?php echo get_term_link($related_term); ?>" class="term-link">
                                <?php echo esc_html($related_term->name); ?>
                            </a>
                        <?php 
                            endforeach;
                        endif;
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>

<?php get_footer(); ?>