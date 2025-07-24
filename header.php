<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="profile" href="https://gmpg.org/xfn/11">
    <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<header class="site-header">
    <div class="header-container">
        <a href="<?php echo esc_url(home_url('/')); ?>" class="site-title">
            <?php bloginfo('name'); ?>
        </a>
        
        



        <nav class="main-navigation">
            <?php
            wp_nav_menu(array(
                'theme_location' => 'header-menu',
                'menu_class' => 'header-menu',
                'container' => false,
                'fallback_cb' => 'lofygame_fallback_menu',
            ));
            ?>
        </nav>



        
        <div class="header-controls">
            <button class="theme-toggle" id="theme-toggle" aria-label="Toggle dark mode">
                <span class="theme-toggle-icon">🌙</span>
            </button>
        </div>
    </div>
</header>

<?php if (is_home() || is_post_type_archive('game')) : ?>
<section class="game-filters">
    <select id="category-filter" class="filter-select">
        <option value="all">All Categories</option>
        <?php
        $categories = get_categories(array(
            'taxonomy' => 'category',
            'hide_empty' => true,
        ));
        foreach ($categories as $category) {
            echo '<option value="' . esc_attr($category->term_id) . '">' . esc_html($category->name) . '</option>';
        }
        ?>
    </select>
    
    <select id="tag-filter" class="filter-select">
        <option value="all">All Tags</option>
        <?php
        $tags = get_tags(array(
            'hide_empty' => true,
        ));
        foreach ($tags as $tag) {
            echo '<option value="' . esc_attr($tag->term_id) . '">' . esc_html($tag->name) . '</option>';
        }
        ?>
    </select>
</section>
<?php endif; ?>

<div id="content" class="site-content">