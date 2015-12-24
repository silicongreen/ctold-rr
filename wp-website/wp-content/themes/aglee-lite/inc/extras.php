<?php
/**
 * Custom functions that act independently of the theme templates
 *
 * Eventually, some of the functionality here could be replaced by core features
 *
 * @package Aglee Lite
 */

/**
 * Adds custom classes to the array of body classes.
 *
 * @param array $classes Classes for the body element.
 * @return array
 */
function aglee_lite_body_classes( $aglee_lite_classes ) {
	// Adds a class of group-blog to blogs with more than 1 published author.
	if ( is_multi_author() ) {
		$aglee_lite_classes[] = 'group-blog';
	}

	return $aglee_lite_classes;
}
add_filter( 'body_class', 'aglee_lite_body_classes' );

if ( version_compare( $GLOBALS['wp_version'], '4.1', '<' ) ) :
	/**
	 * Filters wp_title to print a neat <title> tag based on what is being viewed.
	 *
	 * @param string $title Default title text for current view.
	 * @param string $sep Optional separator.
	 * @return string The filtered title.
	 */
	function aglee_lite_wp_title( $aglee_lite_title, $aglee_lite_sep ) {
		if ( is_feed() ) {
			return $aglee_lite_title;
		}

		global $page, $paged;

		// Add the blog name.
		$aglee_lite_title .= get_bloginfo( 'name', 'display' );

		// Add the blog description for the home/front page.
		$aglee_lite_site_description = get_bloginfo( 'description', 'display' );
		if ( $aglee_lite_site_description && ( is_home() || is_front_page() ) ) {
			$aglee_lite_title .= " $aglee_lite_sep $aglee_lite_site_description";
		}

		// Add a page number if necessary.
		if ( ( $paged >= 2 || $page >= 2 ) && ! is_404() ) {
			$aglee_lite_title .= " $aglee_lite_sep " . sprintf( esc_html__( 'Page %s', 'aglee-lite' ), max( $paged, $page ) );
		}

		return $aglee_lite_title;
	}
	add_filter( 'wp_title', 'aglee_lite_wp_title', 10, 2 );

	/**
	 * Title shim for sites older than WordPress 4.1.
	 *
	 * @link https://make.wordpress.org/core/2014/10/29/title-tags-in-4-1/
	 * @todo Remove this function when WordPress 4.3 is released.
	 */
	function aglee_lite_render_title() {
		?>
		<title><?php wp_title( '|', true, 'right' ); ?></title>
		<?php
	}
	add_action( 'wp_head', 'aglee_lite_render_title' );
endif;

// added code for slider

    function aglee_lite_slidercb(){
        $aglee_lite_mode = get_theme_mod('slider_mode_setting','horizontal');
        $aglee_lite_slider_select = get_theme_mod('slider_type_choose','option1');
        $aglee_lite_readmore_option = get_theme_mod('readmore_slider_setting', 'Read More');
        if($aglee_lite_slider_select == 'option1'){
            $aglee_lite_slider_one = get_theme_mod('slider_one');
            if(empty($aglee_lite_slider_one)){
                $aglee_lite_slider_one = 0;
            }
            $aglee_lite_slider_two = get_theme_mod('slider_two');
            if(empty($aglee_lite_slider_two)){
                $aglee_lite_slider_two = 0;
            }
            $aglee_lite_slider_three = get_theme_mod ('slider_three');
            if(empty($aglee_lite_slider_three)){
                $aglee_lite_slider_three = 0;
            }
            $aglee_lite_slider_four = get_theme_mod('slider_four');
            if(empty($aglee_lite_slider_four)){
                $aglee_lite_slider_four = 0;
            }
            $aglee_lite_all_slider = array($aglee_lite_slider_one, $aglee_lite_slider_two, $aglee_lite_slider_three, $aglee_lite_slider_four); 
            $aglee_lite_remove = array(0);
            $aglee_lite_result = array_diff($aglee_lite_all_slider, $aglee_lite_remove);           
        }else{
            $aglee_lite_slider_cat = get_theme_mod('slider_category');
        }
        ?>
          
                    <?php
                    if($aglee_lite_slider_select == 'option1'){
                        echo '<ul class="aglee-home-slider">';
                        foreach($aglee_lite_result as $aglee_lite_rowslide){
                                $aglee_lite_image = wp_get_attachment_image_src( get_post_thumbnail_id( $aglee_lite_rowslide ), 'aglee-lite-home-slider', false );
                                $aglee_lite_content_post = get_post($aglee_lite_rowslide);
                                ?>
                                <li>
                                    <?php if(has_post_thumbnail($aglee_lite_rowslide)){ ?>
                                        <img src="<?php echo esc_url($aglee_lite_image[0]); ?>" />
                                    <?php } ?>
                                   
                                        <div class="caption_wrap">
                                            <div class="slider_title"><?php echo $aglee_lite_content_post->post_title; ?></div>
                                            <div class="slider_cont"><?php echo $aglee_lite_content_post->post_excerpt; ?></div>
                                            <?php if(get_theme_mod('readmore_slider_setting') != '1'){ ?>
                                            <a href="<?php echo get_the_permalink($aglee_lite_rowslide); ?>"><?php _e('Read More','aglee-lite'); ?></a>
                                            <?php } ?>
                                    </div>
                                </li>
                            <?php 
                        }
                        echo '</ul>';
                    }
                                              
                    if($aglee_lite_slider_select == 'option2'){
                        echo '<ul class="aglee-home-slider">';
                        if(!empty($aglee_lite_slider_cat)){
                          $aglee_lite_catquery = new WP_Query( 'cat='.$aglee_lite_slider_cat.'&posts_per_page=10' );
                           while($aglee_lite_catquery->have_posts()){
                                $aglee_lite_catquery->the_post(); 
                                $aglee_lite_post_id = get_the_ID();
                                $aglee_lite_image = wp_get_attachment_image_src( get_post_thumbnail_id( $aglee_lite_post_id ), 'aglee-lite-home-slider', false );
                                ?>
                                <li>
                                    <?php if(has_post_thumbnail()){ ?>
                                        <img src="<?php echo esc_url($aglee_lite_image[0]); ?>" />
                                    <?php } ?>
                                                                       
                                    <div class="caption_wrap">
                                        <div class="slider_title"><?php the_title(); ?></div>
                                        <div class="slider_cont"><?php the_excerpt(); ?></div>
                                        <?php if(get_theme_mod('readmore_slider_setting') != '1'){ ?>
                                        <a href="<?php the_permalink(); ?>"><?php _e('Read More', 'aglee-lite'); ?></a>
                                        <?php } ?>
                                    </div>                                    
                                </li>
                           <?php }
                       }
                       echo '</ul>';
                    }
                    ?>
                      <script type="text/javascript">
                jQuery(document).ready(function ($){
                    $(".aglee-home-slider").bxSlider({
                        pager: true,
                        auto: true,
                        mode: '<?php echo esc_attr($aglee_lite_mode); ?>'
                    });
                });
            </script>
                    <?php 
    }
    add_action('aglee_lite_slider','aglee_lite_slidercb',10);
    
    function aglee_lite_testimonial_slider_cb(){
        $aglee_lite_category_testimonial = get_theme_mod('slider_testimonial_category');
        if(!empty($aglee_lite_category_testimonial)){
        ?>
        <script type="text/javascript">
                jQuery(document).ready(function ($){
                    $(".aglee-testimonial-slider").bxSlider({
                        pager: true,
                        auto: true,
                        mode: 'horizontal'
                    });
                });
        </script>
        <?php
        $aglee_lite_args = array(
                'posts_per_page' => -1,
                'category' => $aglee_lite_category_testimonial,
                'post_type' => 'post',
                'post_status' => 'publish'
                );
        $aglee_lite_posts_array = get_posts( $aglee_lite_args );
        $aglee_lite_no_of_testimonial = sizeof($aglee_lite_posts_array);
        $aglee_lite_loop_no = round($aglee_lite_no_of_testimonial/2);
        ?>
        <h1><?php _e('What Our Clients Say','aglee-lite'); ?></h1>
        <ul class="aglee-testimonial-slider">
        <?php
            $aglee_lite_offset_element = 0;
            for($aglee_lite_i=1; $aglee_lite_i<=$aglee_lite_loop_no; $aglee_lite_i++){  
            $aglee_lite_args = array(
                    'post_type' => 'post',
                    'cat' => $aglee_lite_category_testimonial,
                    'posts_per_page' => 2,
                    'offset' => $aglee_lite_offset_element
            );
            $aglee_lite_cat_testmonial_query = new WP_Query($aglee_lite_args);
                if($aglee_lite_cat_testmonial_query->have_posts()){
                echo '<li>';
                while($aglee_lite_cat_testmonial_query->have_posts()){
                    $aglee_lite_cat_testmonial_query->the_post();
                   $aglee_lite_testimonial_image = wp_get_attachment_image_src( get_post_thumbnail_id(), 'aglee-lite-testimonial-img', false );
                    ?>
                    <div class="testimonial_content">
                        <?php if (has_post_thumbnail()){ ?>
                        <div class="testimonial_img">
                            <img src="<?php echo esc_url($aglee_lite_testimonial_image[0]); ?>" />
                        </div>
                        <?php } ?>
                        
                        <div class="testimonial_designation">
                            <?php echo '<p>'.wp_trim_words(get_the_content(),20).'</p>'; ?>
                            <div class="testimonial_name"><?php the_title(); ?></div>
                            <div class="testimonial_designation"><?php the_excerpt(); ?></div> 
                        </div>       
                    </div>           
            <?php } 
                echo '</li>'; 
            }
            $aglee_lite_offset_element = $aglee_lite_offset_element+2;
        } // end of for loop
            ?>
        </ul>
        <?php }
    }
    
    add_action('aglee_lite_testimonial_slider','aglee_lite_testimonial_slider_cb',10);