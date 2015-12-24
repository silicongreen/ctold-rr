<?php
/**
 * Template Name: Blog Template
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages
 * and that other 'pages' on your WordPress site will use a
 * different template.
 *
 * @package Aglee Lite
 */

get_header(); ?>

<?php
    global $post;
    $aglee_lite_page_layout =  get_post_meta($post->ID,'agleelite_page_layout');
    $aglee_lite_default_page_layout = '';
    foreach($aglee_lite_page_layout as $aglee_lite_row){
       if($aglee_lite_row == 'default_layout'){
            $aglee_lite_default_page_layout = get_theme_mod('layout_category_blog', 'no_sidebar_wide');
       }else{
            $aglee_lite_default_page_layout = $aglee_lite_row;
       }
    }
    $aglee_lite_content_class = '';
    switch($aglee_lite_default_page_layout){
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
    
$aglee_lite_blog_display_type = get_theme_mod('blog_post_layout','blog_image_large');
 
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
    default:
        $aglee_lite_blog_display_class = 'blog-full-content';
    
 }  
?>
<?php while ( have_posts() ) : the_post(); ?>
	<main id="main" class="site-main <?php echo esc_attr($aglee_lite_content_class).' '.esc_attr($aglee_lite_blog_display_class); ?>" role="main">
        <div class="ag-container">
        <?php if($aglee_lite_default_page_layout == 'both_sidebar') : ?>
            <div id="primary-wrap" class="clearfix">
        <?php endif; ?> 
        
            <div id="primary" class="content-area">
				<?php get_template_part( 'template-parts/content', 'blog' ); ?>
            </div><!-- #primary -->
            
            <?php if($aglee_lite_default_page_layout == 'left_sidebar' || $aglee_lite_default_page_layout == 'both_sidebar') : ?>
                <?php get_sidebar('left'); ?>
            <?php endif; ?>
            
        <?php if($aglee_lite_default_page_layout == 'both_sidebar') : ?>
            </div> <!-- #primary-wrap -->
        <?php endif; ?>
        
        <?php if($aglee_lite_default_page_layout == 'right_sidebar' || $aglee_lite_default_page_layout == 'both_sidebar') : ?>
            <?php get_sidebar('right'); ?>
        <?php endif; ?>
    </div>
	</main><!-- #main -->
<?php endwhile; // end of the loop. ?>  	
<?php get_footer(); ?>