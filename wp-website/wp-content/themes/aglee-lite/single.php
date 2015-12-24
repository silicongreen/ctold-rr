<?php
/**
 * The template for displaying all single posts.
 *
 * @package Aglee Lite
 */

get_header(); ?>
<?php
    global $post;
    $aglee_lite_single_post_layout = get_post_meta($post->ID,'agleelite_page_layout');
    $aglee_lite_default_post_layout ='';
    foreach($aglee_lite_single_post_layout as $aglee_lite_row){
       if($aglee_lite_row == 'default_layout'){
            $aglee_lite_default_post_layout = get_theme_mod('layout_default_post', 'no_sidebar_wide');
       }else{
            $aglee_lite_default_post_layout = $aglee_lite_row;
       }
    }    
    // Dynamically Generating Classes for #primary on the basis of page layout
    $aglee_lite_content_class = '';
    switch($aglee_lite_default_post_layout){
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
?>	
<?php while ( have_posts() ) : the_post(); ?>
	<main id="main" class="site-main <?php echo esc_attr($aglee_lite_content_class); ?>" role="main">
        <div class="ap-container">
            <?php if($aglee_lite_default_post_layout == 'both_sidebar') : ?>
                <div id="primary-wrap" class="clearfix">
            <?php endif; ?>
                <div id="primary" class="content-area">
            
            			<?php get_template_part( 'template-parts/content', 'single' ); ?>
            
            			<?php //the_post_navigation(); ?>
            
                        <?php if(($enable_comments_post = get_theme_mod('post_comment') ) == 1) : ?>
            			<?php
            				// If comments are open or we have at least one comment, load up the comment template
            				if ( comments_open() || get_comments_number() ) :
            					comments_template();
            				endif;
            			?>
                        <?php endif; ?>
            
                </div><!-- #primary -->
                <?php if($aglee_lite_default_post_layout == 'left_sidebar' || $aglee_lite_default_post_layout == 'both_sidebar') : ?>
                    <?php get_sidebar('left'); ?>
                <?php endif; ?>
            <?php if($aglee_lite_default_post_layout == 'both_sidebar') : ?>
                </div> <!-- #primary-wrap -->
            <?php endif; ?>
            
            <?php if($aglee_lite_default_post_layout == 'right_sidebar' || $aglee_lite_default_post_layout == 'both_sidebar') : ?>
                <?php get_sidebar('right'); ?>
            <?php endif; ?>
        </div><!-- ap-container -->
	</main><!-- #main -->
<?php endwhile; // end of the loop. ?>    
<?php get_footer(); ?>

