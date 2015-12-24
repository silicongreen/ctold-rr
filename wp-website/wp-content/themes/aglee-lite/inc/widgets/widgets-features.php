<?php
/**
 * Feature Posts
 *
 * @package Aglee Lite
 */

/**
 * Adds aglee_lite_Preview_Post widget.
 */
add_action( 'widgets_init', 'register_features_widget' );
function register_features_widget() {
    register_widget( 'aglee_lite_features_widget' );
}
class Aglee_Lite_Features_Widget extends WP_Widget {

	/**
	 * Register widget with WordPress.
	 */
	public function __construct() {
		parent::__construct(
	 		'aglee_lite_features',
			'Aglee : Features',
			array(
				'description'	=> __( 'A widget To Display Feature Posts', 'aglee-lite' )
			)
		);
	}

	/**
	 * Helper function that holds widget fields
	 * Array is used in update and form functions
	 */
	 private function widget_fields() {
		$fields = array(
			'feature_post_1' => array(
                'aglee_widgets_name' => 'feature_post_1',
                'aglee_widgets_title' => __('Page','aglee-lite'),
                'aglee_widgets_field_type' => 'selectpage'
            ),
            'feature_post_2' => array(
                'aglee_widgets_name' => 'feature_post_2',
                'aglee_widgets_title' => __('Page','aglee-lite'),
                'aglee_widgets_field_type' => 'selectpage'
            ),
            'feature_post_3' => array(
                'aglee_widgets_name' => 'feature_post_3',
                'aglee_widgets_title' => __('Page','aglee-lite'),
                'aglee_widgets_field_type' => 'selectpage'
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
        global $aglee_options;
        //$aglee_settings = get_option('aglee_options',$aglee_options);
        $features_readmore_text = get_theme_mod('home_page_translation');
		$feature_posts = array_values($instance);

        if(!empty($feature_posts)) :
            
            echo $before_widget;
            
            $args = array(
                'posts_per_page' => -1,
                'post_type' => array('page'),
                'post__in' => $feature_posts,
                'orderby' => 'post__in'
            );
            
            $feat_query = new WP_Query($args);
            $count = 1;
            if($feat_query->have_posts()) :
                ?>
                <div class="aglee-container clearfix">
                <div class="feature-post-wrap-block clearfix">
                <?php
                while($feat_query->have_posts()) : $feat_query->the_post();
                    $img = wp_get_attachment_image_src(get_post_thumbnail_id(),'feature-posts-home');
                    $img_src = $img[0];
                    ?>
                    <div class="feature-post-wrap">
                        <figure class="feature-post-thumbnail">
                            <a href="<?php the_permalink(); ?>">
                                <?php if(has_post_thumbnail()) : ?>
                                    <img src="<?php echo esc_url($img_src); ?>" title="<?php the_title_attribute(); ?>" alt="<?php the_title_attribute(); ?>" />
                                <?php else : ?>
                                    <img src="<?php echo esc_url(get_template_directory_uri().'/images/no-feature-post-thumbnail.png'); ?>" title="<?php the_title_attribute(); ?>" alt="<?php the_title_attribute(); ?>" />
                                <?php endif; ?>
                            </a>
                            <figcaption> 
                                 <a href="<?php the_permalink(); ?>"><i class="fa fa-external-link"></i></a>
                            </figcaption>
                        </figure>
                        <a href="<?php the_permalink(); ?>">
                            <h2><?php echo get_the_title(); ?></h2>
                        </a>
                        <div class="feature-post-excerpt">
                            <?php echo wp_trim_words(get_the_content(),35,'...'); ?>
                        </div>
                        <a class="feat_readmore-button readmore-button" href="<?php the_permalink(); ?>">
                            <?php if(empty($features_readmore_text)) : ?>
                                <?php _e('Read More...','aglee-lite'); ?>
                            <?php else : ?>
                                <?php echo esc_attr($features_readmore_text); ?>
                            <?php endif; ?>
                        </a>
                    </div>
                    <?php if($count%3 == 0) : ?>
                        <div class="clearfix"></div>
                    <?php endif; ?>
                    <?php
                    $count++;
                endwhile;
                ?>
                </div>
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