<?php get_header(); ?>

<!--start header parallax image-->
<?php if ($redux_demo['archive_post_header_img_display'] == 1){ ?>

	<section id="nicdark_archive_parallax" class="nicdark_section nicdark_imgparallax" style="background:url(<?php echo esc_url( $redux_demo['archive_post_header_img']['url'] ); ?>) 50% 0 fixed; background-size:cover;">

	    <div class="nicdark_filter <?php echo $redux_demo['archive_post_header_filter']; ?>">

	        <!--start nicdark_container-->
	        <div class="nicdark_container nicdark_clearfix">

	            <div class="grid grid_12">
	                <div class="nicdark_space<?php echo $redux_demo['archive_post_header_margintop']; ?>"></div>
	                
	            
	                <?php if (is_category()): ?>
						<h1 class="white subtitle"><?php _e('CATEGORY','babykids'); ?></h1><div class="nicdark_space10"></div><h3 class="subtitle white"><?php single_cat_title(); ?></h3>
					<?php elseif (is_tag()): ?>
						<h1 class="white subtitle"><?php _e('TAG','babykids'); ?></h1><div class="nicdark_space10"></div><h3 class="subtitle white"><?php single_tag_title() ?></h3>
					<?php elseif (is_month()): ?>
						<h1 class="white subtitle"><?php _e('ARCHIVE FOR','babykids'); ?></h1><div class="nicdark_space10"></div><h3 class="subtitle white"><?php single_month_title(); ?></h3>
					<?php elseif (is_author()): ?>
						<h1 class="white subtitle"><?php _e('AUTHOR','babykids'); ?></h1><div class="nicdark_space10"></div><h3 class="subtitle white"><?php the_author(); ?></h3>
					<?php elseif (is_search()): ?>
						<h1 class="white subtitle"><?php _e('SEARCH FOR','babykids'); ?></h1><div class="nicdark_space10"></div><h3 class="subtitle white">" <?php the_search_query(); ?> "</h3>
					<?php else: ?>
						<h1 class="white subtitle"><?php _e('ARCHIVE','babykids'); ?></h1>
					<?php endif ?>


					<div class="nicdark_space20"></div>
					<div class="nicdark_divider left big"><span class="nicdark_bg_white nicdark_radius"></span></div>
	                <div class="nicdark_space<?php echo $redux_demo['archive_post_header_marginbottom']; ?>"></div>
	            </div>

	        </div>
	        <!--end nicdark_container-->

	    </div>
	     
	</section>

	<div class="nicdark_space50"></div>

	<script type="text/javascript">(function($) { "use strict"; $("#nicdark_archive_parallax").parallax("50%", 0.3); })(jQuery);</script>

<?php }else{ ?>

	<div class="nicdark_space160"></div>

<?php } ?>
<!--end header parallax image-->


<!--start section-->
<section class="nicdark_section">

    <!--start nicdark_container-->
    <div class="nicdark_container nicdark_clearfix">


    	<?php if ( $redux_demo['archive_post_style'] == 'masonry' ){

    		include 'archive-masonry.php';

    	} else { ?>

			
			<!--start all posts previews-->
	    	<div class="grid grid_8">
	    	
	    		<?php if(have_posts()) : ?>
							
					<?php while(have_posts()) : the_post(); ?>
						
						<!--#post-->
						<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

								<!--START PREVIEW-->
								<?php if (has_post_thumbnail()): ?>
									<div class="nicdark_featured_image"><?php the_post_thumbnail('archive-image'); ?></div>
								<?php endif ?>
					            <div class="nicdark_textevidence nicdark_bg_<?php echo $redux_demo['metabox_posts_color']; ?> nicdark_radius_bottom nicdark_shadow">
					                <div class="nicdark_size_big">
					                    <p class="white"><i class="icon-calendar-1 nicdark_marginright10"></i><?php the_time('j') ?> <?php the_time('M') ?> <span class="nicdark_margin010">·</span> <i class="icon-user-1 nicdark_marginright10"></i><?php the_author(); ?> <span class="nicdark_margin010">·</span> <i class="icon-chat nicdark_marginright10"></i><?php comments_number(__('No Comments','babykids'),__('One Comment','babykids'),__( '% Comments','babykids') );?></p>
					                </div>
					                <i class="icon-info-outline nicdark_iconbg right medium <?php echo $redux_demo['metabox_posts_color']; ?>"></i>
					            </div>
					            <div class="nicdark_space20"></div>
					            <h1 class="subtitle greydark"><?php the_title(); ?></h1>
					            <div class="nicdark_space20"></div>
					            <div class="nicdark_divider left small"><span class="nicdark_bg_<?php echo $redux_demo['metabox_posts_color']; ?> nicdark_radius"></span></div>
					            <div class="nicdark_space20"></div>
					            <p><?php the_excerpt(); ?></p>
					            <div class="nicdark_space20"></div>
					            <a class="nicdark_btn nicdark_press nicdark_bg_<?php echo $redux_demo['metabox_posts_color']; ?> white medium nicdark_radius nicdark_shadow" href="<?php the_permalink(); ?>"><?php _e('READ MORE','babykids'); ?></a>
								<!--END PREVIEW-->

						</div>
						<!--#post-->

						<div class="nicdark_space50"></div>
							
							
					<?php endwhile; ?>
							
				
				<?php endif; ?>

	    	</div>
	    	<!--end all posts previews-->

	    	<!--sidebar-->
	    	<div class="grid grid_4">
	    		<?php if ( ! dynamic_sidebar( 'Sidebar' ) ) : ?><?php endif ?>
	    	</div>
	    	<!--end sidebar-->

    	<?php } ?>


	</div>
	<!--end container-->

</section>
<!--end section-->


<!--start pagination-->
<div class="nicdark_section">

    <!--start nicdark_container-->
    <div class="nicdark_container nicdark_clearfix">

        <div class="nicdark_space40"></div>

        <div class="grid grid_6 nicdark_aligncenter_iphoneland nicdark_aligncenter_iphonepotr">
        	<?php previous_posts_link(__('PREV', 'babykids')); ?>
        </div>
        <div class="grid grid_6 nicdark_aligncenter_iphoneland nicdark_aligncenter_iphonepotr">
        	<?php next_posts_link(__('NEXT', 'babykids')); ?>
        </div>

        <div class="nicdark_space50"></div>

    </div>
    <!--end nicdark_container-->
            
</div>
<!--end pagination-->


<?php get_footer(); ?>