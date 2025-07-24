<?php get_header(); ?>

<main class="site-main">
    <?php while (have_posts()) : the_post(); ?>
        <article class="single-post-container">
            <header class="post-header">
                <div class="container">
                    <div class="post-meta">
                        <span class="post-date"><?php echo get_the_date(); ?></span>
                        <span class="post-author">by <?php the_author(); ?></span>
                        <?php
                        $categories = get_the_category();
                        if ($categories) {
                            echo '<span class="post-categories">';
                            foreach ($categories as $category) {
                                echo '<a href="' . get_category_link($category->term_id) . '" class="post-category">' . $category->name . '</a>';
                            }
                            echo '</span>';
                        }
                        ?>
                    </div>
                    <h1 class="post-title"><?php the_title(); ?></h1>
                    <?php if (has_excerpt()) : ?>
                        <div class="post-excerpt"><?php the_excerpt(); ?></div>
                    <?php endif; ?>
                </div>
            </header>
            
            <?php if (has_post_thumbnail()) : ?>
                <div class="post-featured-image">
                    <?php the_post_thumbnail('large'); ?>
                </div>
            <?php endif; ?>
            
            <div class="post-content-wrapper">
                <div class="container">
                    <div class="post-content">
                        <?php the_content(); ?>
                        
                        <?php
                        wp_link_pages(array(
                            'before' => '<div class="page-links">',
                            'after' => '</div>',
                        ));
                        ?>
                    </div>
                    
                    <footer class="post-footer">
                        <?php
                        $tags = get_the_tags();
                        if ($tags) {
                            echo '<div class="post-tags">';
                            echo '<h4>Tags:</h4>';
                            foreach ($tags as $tag) {
                                echo '<a href="' . get_tag_link($tag->term_id) . '" class="post-tag">' . $tag->name . '</a>';
                            }
                            echo '</div>';
                        }
                        ?>
                        
                        <div class="post-navigation">
                            <?php
                            $prev_post = get_previous_post();
                            $next_post = get_next_post();
                            
                            if ($prev_post) {
                                echo '<div class="nav-previous">';
                                echo '<a href="' . get_permalink($prev_post->ID) . '">';
                                echo '<span class="nav-label">Previous Post</span>';
                                echo '<span class="nav-title">' . get_the_title($prev_post->ID) . '</span>';
                                echo '</a>';
                                echo '</div>';
                            }
                            
                            if ($next_post) {
                                echo '<div class="nav-next">';
                                echo '<a href="' . get_permalink($next_post->ID) . '">';
                                echo '<span class="nav-label">Next Post</span>';
                                echo '<span class="nav-title">' . get_the_title($next_post->ID) . '</span>';
                                echo '</a>';
                                echo '</div>';
                            }
                            ?>
                        </div>
                        
                        <div class="social-share">
                            <h4>Share this post:</h4>
                            <div class="share-buttons">
                                <a href="#" class="share-button" data-platform="facebook" data-url="<?php echo get_permalink(); ?>" data-title="<?php echo get_the_title(); ?>">Facebook</a>
                                <a href="#" class="share-button" data-platform="twitter" data-url="<?php echo get_permalink(); ?>" data-title="<?php echo get_the_title(); ?>">Twitter</a>
                                <a href="#" class="share-button" data-platform="pinterest" data-url="<?php echo get_permalink(); ?>" data-title="<?php echo get_the_title(); ?>">Pinterest</a>
                            </div>
                        </div>
                    </footer>
                </div>
            </div>
            
            <?php
            if (comments_open() || get_comments_number()) :
                comments_template();
            endif;
            ?>
        </article>
    <?php endwhile; ?>
</main>

<?php get_footer(); ?>