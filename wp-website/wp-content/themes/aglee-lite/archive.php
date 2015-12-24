<?php
/**
 * The template for displaying archive pages.
 *
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @package Aglee Lite
 */
 
 // Dynamically Generating Classes for #primary on the basis of page layout
 $aglee_lite_default_layout = get_theme_mod('layout_category_blog');  
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
        default:
            $aglee_lite_content_class = 'no-sidebar-wide';
    }
 
get_header(); ?>

	<main id="main" class="site-main <?php echo esc_attr($aglee_lite_content_class); ?>" role="main">
        <div class="ap-container">
        <?php if($aglee_lite_default_layout == 'both_sidebar') : ?>
            <div id="primary-wrap" class="clearfix">
        <?php endif; ?>
            <div id="primary" class="content-area">
        
        			<?php /* Start the Loop */ ?>
        			<?php while ( have_posts() ) : the_post(); ?>
                        <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                            	<header class="entry-header">
                            		<?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>
                            	</header><!-- .entry-header -->
                                
                                <?php if(has_post_thumbnail()){
                                    $image = wp_get_attachment_image_src( get_post_thumbnail_id(), 'aglee-lite-home-slider', false );
                                    ?>
                                <div class="page_img">
                                    <img src="<?php echo esc_url($image[0]); ?>" />
                                </div>
                                <?php } ?>
                                
                            	<div class="entry-content">
                            		<?php the_content(); ?>
                            		<?php
                            			wp_link_pages( array(
                            				'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'aglee-lite' ),
                            				'after'  => '</div>',
                            			) );
                            		?>
                            	</div><!-- .entry-content -->
                            
                            	<footer class="entry-footer">
                            		<?php edit_post_link( esc_html__( 'Edit', 'aglee-lite' ), '<span class="edit-link">', '</span>' ); ?>
                            	</footer><!-- .entry-footer -->
                          </article><!-- #post-## -->
        
        			<?php endwhile; ?>
        
        			<?php the_posts_navigation(); ?>
        
        		<?php if ( !have_posts() ) : ?>
        
        			<?php get_template_part( 'template-parts/content', 'none' ); ?>
        
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
