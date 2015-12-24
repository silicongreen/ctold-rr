<?php
/**
 * The category template
 *
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @package Aglee Theme
 */
 

 $aglee_lite_default_layout = get_theme_mod('layout_default_page');
 
 $aglee_lite_blog_display_type = get_theme_mod('blog_post_layout');
 
 $aglee_lite_blog_display_class = '';
 switch($aglee_lite_blog_display_type){
    case 'blog_image_large' :
        $aglee_lite_blog_display_class = 'blog-image-large';       
        break;
    case 'blog_image_medium' :
        $aglee_lite_blog_display_class = 'blog-image-medium';       
        break;
    case 'blog_image_alternate_medium' :
        $aglee_lite_blog_display_class = 'blog-image-alternate-medium';       
        break;
    case 'blog_full_content' :
        $aglee_lite_blog_display_class = 'blog-full-content';       
        break;
 }
 
 // Dynamically Generating Classes for #primary on the basis of page layout
 $aglee_lite_content_class = '';
    switch($aglee_lite_default_layout){
        case 'left_sidebar':
            $aglee_lite_content_class = 'left-sidebar';
            break;
        case 'right_sidebar':
            $aglee_lite_content_class = 'right-sidebar';
            break;
        case 'both_sidebar':
            $aglee_lite_content_class = 'both-sidebar';
            break;
        case 'no_sidebar_wide':
            $aglee_lite_content_class = 'no-sidebar-wide';
            break;
        case 'no_sidebar_narrow':
            $aglee_lite_content_class = 'no-sidebar-narraow';
            break;
    }
 
get_header(); ?>
	<main id="main" class="site-main <?php echo esc_attr($aglee_lite_content_class).' '.esc_attr($aglee_lite_blog_display_class); ?>" role="main">
        <div class="ap-container">
        <?php if($aglee_lite_default_layout == 'both_sidebar') : ?>
            <div id="primary-wrap" class="clearfix">
        <?php endif; ?>
            <div id="primary" class="content-area">
        
        			<?php /* Start the Loop */ ?>
        			<?php while ( have_posts() ) : the_post(); ?>
        
        				<?php
        					/* Include the Post-Format-specific template for the content.
        					 * If you want to override this in a child theme, then include a file
        					 * called content-___.php (where ___ is the Post Format name) and that will be used instead.
        					 */
        					get_template_part( 'template-parts/content', get_post_format() );
        				?>
        
        			<?php endwhile; ?>
        
        			<?php //the_posts_navigation(); ?>
        
        		<?php if ( !have_posts() ) : ?>
        
        			<?php get_template_part( 'content', 'none' ); ?>
        
        		<?php endif; ?>
            </div><!-- #primary -->
            <?php if($aglee_lite_default_layout == 'left_sidebar' || $aglee_lite_default_layout == 'both_sidebar') : ?>
                <?php get_sidebar('left'); ?>
            <?php endif; ?>
        <?php if($aglee_lite_default_layout == 'both_sidebar') : ?>
            </div>
        <?php endif; ?>
        <?php if($aglee_lite_default_layout == 'right_sidebar' || $aglee_lite_default_layout == 'both_sidebar') : ?>
            <?php get_sidebar('right'); ?>
        <?php endif; ?>
        </div>
	</main><!-- #main -->
<?php get_footer(); ?>