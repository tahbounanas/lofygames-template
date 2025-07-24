<?php get_header(); ?>

<main class="site-main">
    <section class="error-404 not-found">
        <div class="container">
            <header class="page-header">
                <h1 class="page-title">Game Not Found</h1>
            </header>
            
            <div class="page-content">
                <p>Sorry, the game you're looking for doesn't exist. But don't worry, we have plenty of other amazing games for you to play!</p>
                
                <div class="error-404-actions">
                    <a href="<?php echo esc_url(home_url('/games')); ?>" class="button">Browse All Games</a>
                    <a href="<?php echo esc_url(home_url('/')); ?>" class="button button-secondary">Go Home</a>
                </div>
                
                <div class="popular-games">
                    <h2>Popular Games</h2>
                    <?php
                    $popular_games = new WP_Query(array(
                        'post_type' => 'game',
                        'posts_per_page' => 6,
                        'meta_key' => 'post_views_count',
                        'orderby' => 'meta_value_num',
                        'order' => 'DESC',
                    ));
                    
                    if ($popular_games->have_posts()) :
                        echo '<div class="games-grid">';
                        while ($popular_games->have_posts()) : $popular_games->the_post();
                            get_template_part('template-parts/game-card');
                        endwhile;
                        echo '</div>';
                        wp_reset_postdata();
                    endif;
                    ?>
                </div>
            </div>
        </div>
    </section>
</main>

<?php get_footer(); ?>