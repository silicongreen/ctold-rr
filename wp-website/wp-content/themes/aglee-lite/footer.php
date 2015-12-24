<?php
/**
 * The template for displaying the footer.
 *
 * Contains the closing of the #content div and all content after
 *
 * @package Aglee Lite
 */
?>

	</div><!-- #content -->
	<footer id="colophon" class="site-footer" role="contentinfo">
        <?php if(($aglee_lite_show_footer_featured_section = get_theme_mod('footer_widget')) == 1) : ?>
            <div class="footer-featured-section">
                <div class="ap-container clearfix">
                    <div class="featured-footer-wrap">
                        <?php if(is_active_sidebar('aglee_footer_one')) : ?>
                            <div class="featured-footer-1 featured-footer">
                                <?php dynamic_sidebar('aglee_footer_one'); ?>
                            </div>
                        <?php endif; ?>
                        <?php if(is_active_sidebar('aglee_footer_two')) : ?>
                            <div class="featured-footer-2 featured-footer">
                                <?php dynamic_sidebar('aglee_footer_two'); ?>
                            </div>
                        <?php endif; ?>
                        <?php if(is_active_sidebar('aglee_footer_three')) : ?>
                            <div class="featured-footer-3 featured-footer">
                                <?php dynamic_sidebar('aglee_footer_three'); ?>
                            </div>
                        <?php endif; ?>
                        <?php if(is_active_sidebar('aglee_footer_four')) : ?>
                            <div class="featured-footer-4 featured-footer">
                                <?php dynamic_sidebar('aglee_footer_four'); ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>  
        <?php endif; ?>
        
		<div class="site-info">
            <div class="ap-container clearfix">
                <div class="copyright-info">
                    Copyright &copy; <?php the_date( 'Y' ); ?> <a href="<?php get_home_url(); ?>">
                        <?php 
                        $aglee_lite_footer_text = get_theme_mod('aglee_lite_footertext','AgleeLite');
                        if(!empty($aglee_lite_footer_text)){
                            echo esc_attr($aglee_lite_footer_text);                              
                        }else{
                            echo bloginfo('name');
                        }
                        ?>
                    </a>
                    <span class="sep"> | </span>
                        <a target="_blank" href="<?php echo esc_url( __( 'http://wordpress.org/', 'aglee-lite' ) ); ?>"><?php printf( __( 'Proudly powered by %s', 'aglee-lite' ), 'WordPress' ); ?></a>
        			<span class="sep"> | </span>
                        <?php _e( 'Theme: ', 'aglee-lite' ); ?><a href="<?php esc_url('http://ww38.themes.com/'); ?>" target="_blank" rel="designer"><?php _e('Aglee Lite', 'aglee-lite'); ?></a>
                </div>
                <?php if(is_active_sidebar('aglee_footer_social_links')) : ?>
                <div class="footer-socials">
                    <?php dynamic_sidebar('aglee_footer_social_links');  ?>
                </div>
                <?php endif; ?>    
            </div>
		</div><!-- .site-info -->
	</footer><!-- #colophon -->
</div><!-- #page -->
<div id="go-top"><a href="#page"><i class="fa fa-arrow-up"></i></a></div>
<?php wp_footer(); ?>
</body>
</html>
