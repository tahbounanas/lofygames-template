<?php get_header(); ?>

<main class="site-main">
    <?php
    // Get current page number - FIXED FOR HOME PAGE
    $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
    
    // Query games with pagination - 12 per row × 17 rows = 204 games per page
    $games_query = new WP_Query(array(
        'post_type' => 'game',
        'posts_per_page' => 204, // 12 games × 17 rows
        'paged' => $paged,
        'post_status' => 'publish',
        'orderby' => 'date',
        'order' => 'DESC'
    ));
    ?>
    
    <?php if ($games_query->have_posts()) : ?>
        <section class="games-section">
            <div class="games-grid games-grid-12-col" id="games-grid">
                <?php while ($games_query->have_posts()) : $games_query->the_post(); ?>
                    <?php get_template_part('template-parts/game-card'); ?>
                <?php endwhile; ?>
            </div>
        </section>
        
        <?php if ($games_query->max_num_pages > 1) : ?>
            <?php lofygame_modern_pagination($games_query); ?>
        <?php endif; ?>
        
    <?php else : ?>
        <section class="no-content">
            <div class="container">
                <h2>No games found</h2>
                <p>Sorry, no games were found. Please try again later.</p>
                <a href="<?php echo admin_url('post-new.php?post_type=game'); ?>" class="button">Add Your First Game</a>
            </div>
        </section>
    <?php endif; ?>
    
    <?php wp_reset_postdata(); ?>
    
    <!-- Categories Section -->
    <section class="categories-showcase">
        <div class="container">
            <div class="categories-header">
                <h2 class="categories-title">Browse by Category</h2>
                <p class="categories-subtitle">Discover games by your favorite genres</p>
            </div>
            
            <div class="categories-grid">
                <?php
                $top_categories = lofygame_get_top_categories(8);
                if ($top_categories) :
                    foreach ($top_categories as $category) :
                ?>
                    <?php get_template_part('template-parts/category-card', null, array('category' => $category)); ?>
                <?php 
                    endforeach;
                endif;
                ?>
            </div>
            
            <div class="categories-footer">
                <a href="<?php echo get_post_type_archive_link('game'); ?>" class="view-all-categories-btn">
                    View All Categories
                    <span class="btn-arrow">→</span>
                </a>
            </div>
        </div>
    </section>
</main>

<?php get_footer(); ?>