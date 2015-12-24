<?php
/**
 * Feature Posts
 *
 * @package Aglee Lite
 */

/**
 * Adds aglee_lite_Preview_Post widget.
 */
add_action( 'widgets_init', 'register_services_widget' );
function register_services_widget() {
    register_widget( 'aglee_lite_services_widget' );
}
class Aglee_Lite_Services_Widget extends WP_Widget {

	/**
	 * Register widget with WordPress.
	 */
	public function __construct() {
		parent::__construct(
	 		'aglee_lite_services',
			'Aglee : Services',
			array(
				'description' => __( 'A widget To Display Services', 'aglee-lite' )
			)
		);
	}

	/**
	 * Helper function that holds widget fields
	 * Array is used in update and form functions
	 */
	 private function widget_fields() {
		$fields = array(
			'service_post_1' => array(
                'aglee_widgets_name' => 'service_post_1',
                'aglee_widgets_title' => __('Post','aglee-lite'),
                'aglee_widgets_field_type' => 'selectpost'
            ),
            'service_post_2' => array(
                'aglee_widgets_name' => 'service_post_2',
                'aglee_widgets_title' => __('Post','aglee-lite'),
                'aglee_widgets_field_type' => 'selectpost'
            ),
            'service_post_3' => array(
                'aglee_widgets_name' => 'service_post_3',
                'aglee_widgets_title' => __('Post','aglee-lite'),
                'aglee_widgets_field_type' => 'selectpost'
            ),
            'service_post_4' => array(
                'aglee_widgets_name' => 'service_post_4',
                'aglee_widgets_title' => __('Post','aglee-lite'),
                'aglee_widgets_field_type' => 'selectpost'
            )
		);
		
		return $fields;
	 }


	/**
	 * Front-end display of widget.
	 *
	 * @see WP_Widget::widget()
	 *
	 * @param array $args     Widget arguments.
	 * @param array $instance Saved values from database.
	 */
	public function widget( $args, $instance ) {
        //print_r($args);
		extract( $args );
        
        $services_readmore_text = get_theme_mod('home_page_moreinfo');//$aglee_settings['services_readmore_text'];

		$service_posts = array_values($instance);
  
        if(!empty($service_posts)) :
            
            echo $before_widget;
            
            $args = array(
                'posts_per_page' => 4,
                'post_type' => array('Post'),
                'post__in' => $service_posts,
                'orderby' => 'post__in'
            );
            
            $feat_query = new WP_Query($args);
            
            if($feat_query->have_posts()) :
                ?>
                <div class="service-post-wrapper-block clearfix">
                <h1><?php _e('Our services','aglee-lite'); ?></h1>
                <?php
                while($feat_query->have_posts()) : $feat_query->the_post();
                    $img = wp_get_attachment_image_src(get_post_thumbnail_id(),'services-thumb', true);
                    $img_src = $img[0];
                    ?>
                    <div class="service-post-wrap clearfix">
                        <figure class="services-post-thumbnail">
                            <a href="<?php the_permalink(); ?>">
                                <?php if(has_post_thumbnail()) : ?>
                                    <img src="<?php echo esc_url($img_src); ?>" title="<?php the_title_attribute(); ?>" alt="<?php the_title_attribute(); ?>" />
                                <?php else : ?>
                                    <img src="<?php echo esc_url(get_template_directory_uri().'/images/no-services-thumbnail.png'); ?>" />
                                <?php endif; ?>
                            </a>
                            <figcaption> 
                                 <a href="<?php the_permalink(); ?>"><i class="fa fa-chain-broken"></i></a>
                            </figcaption>
                        </figure>
                        <div class="services_caption_wrap">
                            <a href="<?php the_permalink(); ?>">
                            <h5 class="services-post-title">
                                <?php echo get_the_title(); ?>
                            </h5>
                            </a>
                            <div class="services-post-excerpt">
                                <?php echo wp_trim_words(get_the_content(),8); ?>
                            </div>
                            <a class="services_readmore-button readmore-button" href="<?php the_permalink(); ?>">
                                <?php if(empty($services_readmore_text)) : ?>
                                    <?php _e('More Info...','aglee-lite'); ?>
                                <?php else : ?>
                                    <?php echo esc_attr($services_readmore_text); ?>
                                <?php endif; ?>
                            </a>
                            </div>
                    </div>
                    <?php
                endwhile;
                ?>
                </div>
                <?php
            endif; // if feature query has posts
            
            echo $after_widget;
        endif; // Feature posts empty
	}

	/**
	 * Sanitize widget form values as they are saved.
	 *
	 * @see WP_Widget::update()
	 *
	 * @param	array	$new_instance	Values just sent to be saved.
	 * @param	array	$old_instance	Previously saved values from database.
	 *
	 * @uses	aglee_lite_widgets_updated_field_value()		defined in widget-fields.php
	 *
	 * @return	array Updated safe values to be saved.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		$widget_fields = $this->widget_fields();

		// Loop through fields
		foreach( $widget_fields as $widget_field ) {

			extract( $widget_field );
	
			// Use helper function to get updated field values
			$instance[$aglee_widgets_name] = aglee_lite_widgets_updated_field_value( $widget_field, $new_instance[$aglee_widgets_name] );
			echo $instance[$aglee_widgets_name];
			
		}
				
		return $instance;
	}

	/**
	 * Back-end widget form.
	 *
	 * @see WP_Widget::form()
	 *
	 * @param	array $instance Previously saved values from database.
	 *
	 * @uses	aglee_lite_widgets_show_widget_field()		defined in widget-fields.php
	 */
	public function form( $instance ) {
		$widget_fields = $this->widget_fields();

		// Loop through fields
		foreach( $widget_fields as $widget_field ) {
		
			// Make array elements available as variables
			extract( $widget_field );
			$aglee_widgets_field_value = isset( $instance[$aglee_widgets_name] ) ? esc_attr( $instance[$aglee_widgets_name] ) : '';
			aglee_lite_widgets_show_widget_field( $this, $widget_field, $aglee_widgets_field_value );
		
		}	
	}

}