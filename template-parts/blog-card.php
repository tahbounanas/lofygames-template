<article class="blog-card">
    <div class="blog-card-image">
        <a href="<?php the_permalink(); ?>">
            <?php if (has_post_thumbnail()) : ?>
                <?php the_post_thumbnail('medium', array('class' => 'blog-thumbnail', 'alt' => get_the_title())); ?>
            <?php else : ?>
                <img src="<?php echo get_template_directory_uri(); ?>/images/placeholder-blog.jpg" alt="<?php the_title(); ?>" class="blog-thumbnail">
            <?php endif; ?>
        </a>
        <div class="blog-card-date">
            <?php echo get_the_date('M j'); ?>
        </div>
    </div>
    
    <div class="blog-card-content">
        <div class="blog-card-meta">
            <?php
            $categories = get_the_category();
            if ($categories) {
                echo '<a href="' . get_category_link($categories[0]->term_id) . '" class="blog-category">' . $categories[0]->name . '</a>';
            }
            ?>
            <span class="blog-author">by <?php the_author(); ?></span>
        </div>
        
        <h3 class="blog-title">
            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
        </h3>
        
        <p class="blog-excerpt"><?php echo wp_trim_words(get_the_excerpt(), 20); ?></p>
        
        <div class="blog-card-footer">
            <a href="<?php the_permalink(); ?>" class="read-more">Read More</a>
            <div class="blog-stats">
                <span class="comment-count"><?php comments_number('0', '1', '%'); ?></span>
            </div>
        </div>
    </div>
</article>