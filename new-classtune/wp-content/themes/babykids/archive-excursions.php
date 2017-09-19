<?php get_header(); ?>

<!--start header parallax image-->
<?php if ($redux_demo['archive_excursions_header_img_display'] == 1){ ?>

	<section id="nicdark_archive_parallax" class="nicdark_section nicdark_imgparallax" style="background:url(<?php echo esc_url( $redux_demo['archive_excursions_header_img']['url'] ); ?>) 50% 0 fixed; background-size:cover;">

	    <div class="nicdark_filter <?php echo $redux_demo['archive_excursions_header_filter']; ?>">

	        <!--start nicdark_container-->
	        <div class="nicdark_container nicdark_clearfix">

	            <div class="grid grid_12">
	                <div class="nicdark_space<?php echo $redux_demo['archive_excursions_header_margintop']; ?>"></div>
	                <h1 class="white subtitle"><?php echo $redux_demo['archive_excursions_header_title']; ?></h1>
	                <div class="nicdark_space20"></div>
	                <div class="nicdark_divider left big"><span class="nicdark_bg_white nicdark_radius"></span></div>
	                <div class="nicdark_space<?php echo $redux_demo['archive_excursions_header_marginbottom']; ?>"></div>
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



<?php


if(isset($_GET['advsearch'])) { $advsearch = $_GET['advsearch']; } else { $advsearch = ''; }
if(isset($_GET['keyword'])) { $keyword = $_GET['keyword']; } else { $keyword = ''; }
if(isset($_GET['taxonomy'])) { $taxonomy = $_GET['taxonomy']; } else { $taxonomy = ''; }
if(isset($_GET[''.$taxonomy.''])) { $term = $_GET[''.$taxonomy.'']; } else { $term = ''; }
if(isset($_GET['posttype'])) { $posttype = $_GET['posttype']; } else { $posttype = ''; }
if(isset($_GET['date'])) { $date = $_GET['date']; } else { $date = ''; }


//$term = $_GET[''.$taxonomy.''];


if ( $advsearch == 'true' ) { 

	
	//decide args with date or not
	if ( $date == '' ) { 

		$args = array(
		  'post_type' => ''.$posttype.'',
		  's' => ''.$keyword.'',
		  'nopaging' => 'true',
		  ''.$taxonomy.'' => ''.$term.''
		);

	}else{

		$args = array(
		  'post_type' => ''.$posttype.'',
		  's' => ''.$keyword.'',
		  'nopaging' => 'true',
		  ''.$taxonomy.'' => ''.$term.'',
		  'meta_query' => array( array( 'key' => 'metabox_excursion_date', 'value' => ''.$date.'' ))
		);

	}
	//end decide args with date or not


	$the_query = new WP_Query( $args ); ?>


	<!--start section-->
	<section class="nicdark_section">

	    <!--start nicdark_container-->
	    <div class="nicdark_container nicdark_clearfix">

	    	<!--start no results-->
        	<?php $noresultstext = __('We colud not find any results for your search! Please try again :)','babykids'); ?>
			<?php echo ($the_query->found_posts > 0) ? '' : '
				<div class="grid grid_12">
					<div class="nicdark_alerts nicdark_bg_blue nicdark_radius nicdark_shadow">
					    <p class="white nicdark_size_medium"><i class="icon-cancel-circled-outline iconclose"></i>&nbsp;&nbsp;&nbsp;<strong>'.__('INFO','babykids').':</strong>&nbsp;&nbsp;&nbsp;'.$noresultstext.'</p>
					    <i class="icon-info-outline nicdark_iconbg right medium blue"></i>
					</div>
				</div>
			';?>
			<!--end no results-->
	    		
	    	<!--start nicdark_masonry_container-->
	        <div class="nicdark_masonry_container">

				<?php
					while ( $the_query->have_posts() ) : $the_query->the_post();

						include 'include/excursion/archive-preview-excursion.php';
				 
					endwhile;

					wp_reset_postdata();
				?>

			</div>
	        <!--end nicdark_masonry_container-->

	        <div class="nicdark_space50"></div>

		</div>
		<!--end container-->

	</section>
	<!--end section-->


	

<?php } else { ?>



<!--start section-->
<section class="nicdark_section">

    <!--start nicdark_container-->
    <div class="nicdark_container nicdark_clearfix">
    	
    	<!--start nicdark_masonry_container-->
        <div class="nicdark_masonry_container">

    		<?php if(have_posts()) : ?>
						
				<?php while(have_posts()) : the_post(); ?>

					<?php include 'include/excursion/archive-preview-excursion.php'; ?>
						
				<?php endwhile; ?>
						
			
			<?php endif; ?>

		</div>
        <!--end nicdark_masonry_container-->

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


<?php } ?>


<?php get_footer(); ?>