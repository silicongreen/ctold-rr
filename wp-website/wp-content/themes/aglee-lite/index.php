<?php
/**
 * The main template
 *
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @package Aglee Theme
 */

get_header(); 
?>
<main id="main" class="site-main right-sidebar" role="main">
    <div class="ap-container">

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

        <?php the_posts_navigation(); ?>

        <?php if ( !have_posts() ) : ?>

         <?php get_template_part( 'content', 'none' ); ?>

     <?php endif; ?>
 </div><!-- #primary -->
 <?php get_sidebar(); ?>
</div>
</main><!-- #main -->
<?php get_footer(); ?>