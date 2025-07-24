</div><!-- #content -->

<footer class="site-footer">
    <div class="footer-content">
        <nav class="footer-navigation">
            <?php
            wp_nav_menu(array(
                'theme_location' => 'footer-menu',
                'menu_class' => 'footer-menu',
                'container' => false,
                'fallback_cb' => false,
            ));
            ?>
        </nav>
        
        <div class="footer-info">
            <p>&copy; <?php echo date('Y'); ?> <?php bloginfo('name'); ?>. All rights reserved.</p>
            <p>Play free online games - No downloads required!</p>
        </div>
    </div>
</footer>

<?php wp_footer(); ?>
</body>
</html>