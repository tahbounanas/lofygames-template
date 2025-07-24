<?php
/*
Template Name: Blog
*/
get_header(); ?>

<main class="site-main blog-main">
    <header class="blog-header">
        <div class="container">
            <h1 class="blog-title">Gaming Blog</h1>
            <p class="blog-description">Latest gaming news, reviews, and tips</p>
        </div>
    </header>

    <section class="blog-section">
        <div class="blog-container">
            <div class="blog-content">
                <?php
                $blog_query = new WP_Query(array(
                    'post_type' => 'post',
                    'posts_per_page' => 10,
                    'post_status' => 'publish',
                    'orderby' => 'date',
                    'order' => 'DESC'
                ));
                ?>
                
                <?php if ($blog_query->have_posts()) : ?>
                    <div class="blog-grid">
                        <?php while ($blog_query->have_posts()) : $blog_query->the_post(); ?>
                            <?php get_template_part('template-parts/blog-card'); ?>
                        <?php endwhile; ?>
                    </div>
                    
                    <div class="blog-pagination">
                        <?php
                        echo paginate_links(array(
                            'total' => $blog_query->max_num_pages,
                            'prev_text' => '← Previous Posts',
                            'next_text' => 'More Posts →'
                        ));
                        ?>
                    </div>
                <?php else : ?>
                    <div class="no-posts">
                        <h2>No blog posts found</h2>
                        <p>Start sharing your gaming insights!</p>
                        <?php if (current_user_can('publish_posts')) : ?>
                            <a href="<?php echo admin_url('post-new.php'); ?>" class="button">Write First Post</a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
                
                <?php wp_reset_postdata(); ?>
            </div>
            
            <aside class="blog-sidebar">
                <div class="sidebar-widget">
                    <h3>Categories</h3>
                    <ul class="category-list">
                        <?php
                        $categories = get_categories(array('hide_empty' => true));
                        foreach ($categories as $category) {
                            echo '<li><a href="' . get_category_link($category->term_id) . '">' . $category->name . ' (' . $category->count . ')</a></li>';
                        }
                        ?>
                    </ul>
                </div>
                
                <div class="sidebar-widget">
                    <h3>Recent Posts</h3>
                    <?php
                    $recent_posts = wp_get_recent_posts(array(
                        'numberposts' => 5,
                        'post_status' => 'publish'
                    ));
                    
                    if ($recent_posts) {
                        echo '<ul class="recent-posts">';
                        foreach ($recent_posts as $post_item) {
                            echo '<li><a href="' . get_permalink($post_item['ID']) . '">' . $post_item['post_title'] . '</a></li>';
                        }
                        echo '</ul>';
                    }
                    ?>
                </div>
                
                <div class="sidebar-widget">
                    <h3>Popular Games</h3>
                    <?php
                    $popular_games = new WP_Query(array(
                        'post_type' => 'game',
                        'posts_per_page' => 5,
                        'meta_key' => 'post_views_count',
                        'orderby' => 'meta_value_num',
                        'order' => 'DESC'
                    ));
                    
                    if ($popular_games->have_posts()) {
                        echo '<ul class="popular-games">';
                        while ($popular_games->have_posts()) {
                            $popular_games->the_post();
                            echo '<li><a href="' . get_permalink() . '">' . get_the_title() . '</a></li>';
                        }
                        echo '</ul>';
                    }
                    wp_reset_postdata();
                    ?>
                </div>
            </aside>
        </div>
    </section>
</main>

<?php get_footer(); ?>