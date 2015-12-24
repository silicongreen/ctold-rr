<?php
/**
 * Template part for displaying single posts.
 *
 * @package Aglee Lite
 */

?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<header class="entry-header">
		<?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>

		<div class="entry-meta">
			<?php aglee_lite_posted_on(); ?>
		</div><!-- .entry-meta -->
	</header><!-- .entry-header -->
    
    <?php if(has_post_thumbnail()){
        $aglee_lite_image = wp_get_attachment_image_src( get_post_thumbnail_id(), 'aglee-lite-home-slider', false );
        ?>
    <div class="post_img">
        <img src="<?php echo esc_url($aglee_lite_image[0]); ?>" />
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
		<?php //aglee_lite_entry_footer(); ?>
	</footer><!-- .entry-footer -->
</article><!-- #post-## -->

