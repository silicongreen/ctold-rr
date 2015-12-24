<?php
/**
 * The template for displaying all pages.
 * Template Name: Home Page
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages
 * and that other 'pages' on your WordPress site will use a
 * different template.
 *
 * @package Aglee Lite
 */

get_header(); ?>
	<div id="primary" class="content-area">
		<main id="main" class="site-main" role="main">
            
			<?php if(is_active_sidebar('aglee_featured_section')) : ?>
                <div id="featured-post-container" class="clearfix">
                    <?php dynamic_sidebar('aglee_featured_section'); ?>
                </div>
            <?php endif; ?>
                
            <?php if(is_active_sidebar('aglee_cta_section')) : ?>
                <div id="cta-container">
                    <?php dynamic_sidebar('aglee_cta_section'); ?>
                </div>
            <?php endif; ?>
            
            <?php if(is_active_sidebar('icon_text_block_section')) : ?>
                <div id="icon-text-block-container" class="clearfix">
                    <div class="ag-container">
                        <div class="icon-text-block-wrapper clearfix">
                            <?php dynamic_sidebar('icon_text_block_section'); ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
            
            <?php if(is_active_sidebar('aglee_toggle_section')) : ?>
                <div id="toggle-feat-page-container" class='clearfix'>
                    <div class="ag-container clearfix">
                        <?php if(is_active_sidebar('aglee_toggle_section')) : ?>
                            <div id="toggle-container">
                                <h1><?php _e('Accordion','aglee-lite'); ?></h1>
                                <?php dynamic_sidebar('aglee_toggle_section'); ?>
                            </div>
                        <?php endif; ?>
                        
                        <div id="testimonial-container">
                           <?php do_action('aglee_lite_testimonial_slider'); ?>
                        </div>                      
                    </div>
                </div>
            <?php endif; ?>
            
            <?php if(is_active_sidebar('aglee_featured_page_section') || is_active_sidebar('aglee_services_section')) : ?>
                <div id="test-services-container">
                    <div class="ap-container">
                        <?php if(is_active_sidebar('aglee_services_section')) : ?>
                            <div id="services-container">
                                <?php dynamic_sidebar('aglee_services_section'); ?>
                            </div>
                        <?php endif; ?>
                        
                        <?php if(is_active_sidebar('aglee_featured_page_section')) : ?>
                            <div id="featured-page-container">
                                <?php dynamic_sidebar('aglee_featured_page_section'); ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>

		</main><!-- #main -->
	</div><!-- #primary -->

<?php //get_sidebar(); ?>
<?php get_footer(); ?>
