<!--start nicdark_masonry_container-->
<div class="nicdark_masonry_container">

	<?php if(have_posts()) : ?>
				
		<?php while(have_posts()) : the_post(); ?>
			
			
			<!--#post-->
			<!--prevew-->
			<div class="grid grid_4 nicdark_masonry_item">
				<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

					<div class="nicdark_archive1 nicdark_bg_<?php echo $redux_demo['metabox_posts_color']; ?> nicdark_radius nicdark_shadow">
                    
	                    <?php if (has_post_thumbnail()): ?>
	                    	<a href="<?php the_permalink(); ?>" class="nicdark_zoom nicdark_btn_icon nicdark_bg_<?php echo $redux_demo['metabox_posts_color']; ?> nicdark_border_<?php echo $redux_demo['metabox_posts_color']; ?>dark white medium nicdark_radius_circle nicdark_absolute_left"><i class="icon-link-outline"></i></a>
							<div class="nicdark_featured_image"><?php the_post_thumbnail('large'); ?></div>
						<?php endif ?>
	                    
	                    <div class="nicdark_margin20 nicdark_post_archive">
	                        <h4 class="white"><?php the_title(); ?></h4>
	                        <div class="nicdark_space20"></div>
	                        <div class="nicdark_divider left small"><span class="nicdark_bg_white nicdark_radius"></span></div>
	                        <div class="nicdark_space20"></div>
	                        <p class="white"><?php the_excerpt(); ?></p>
	                        <div class="nicdark_space20"></div>
	                        <a href="<?php the_permalink(); ?>" class="white nicdark_btn"><i class="icon-doc-text-1 "></i> <?php _e('Read More','babykids'); ?></a>                        
	                    </div>

	                    <i class="icon-pencil-1 nicdark_iconbg right medium <?php echo $redux_demo['metabox_posts_color']; ?>"></i>

	                </div>

				</div>
			</div>
			<!--#post-->


			<div class="nicdark_space50"></div>
				
				
		<?php endwhile; ?>
				
	<?php else: ?>
	
		<?php $nicdark_search_message = __('NOTHING FOUND: Search again','babykids'); ?>
	    <div class="nicdark_alerts nicdark_bg_orange nicdark_radius nicdark_shadow">
	        <p class="white nicdark_size_big"><i class="icon-cancel-circled-outline iconclose"></i>&nbsp;&nbsp;&nbsp;<?php echo $nicdark_search_message; ?></p>
	        <i class="icon-warning-empty nicdark_iconbg right big orange"></i>
	    </div>

	<?php endif; ?>

</div>
<!--end nicdark_masonry_container-->