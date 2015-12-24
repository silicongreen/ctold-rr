<?php
/**
 * Preview post/page widget
 *
 * @package Aglee Lite
 */

/**
 * Adds aglee_lite_Preview_Post widget.
 */
add_action( 'widgets_init', 'register_testimonial_widget' );
function register_testimonial_widget() {
    register_widget( 'aglee_lite_testimonial_widget' );
}
class Aglee_Lite_Testimonial_Widget extends WP_Widget {

	/**
	 * Register widget with WordPress.
	 **/
	public function __construct() {
		parent::__construct(
	 		'aglee_lite_testimonial',
			'Aglee : Testimonial',
			array(
				'description' => __( 'A widget To Display Testimonial', 'aglee-lite' )
			)
		);
	}

	/**
	 * Helper function that holds widget fields
	 * Array is used in update and form functions
	 */
	 private function widget_fields() {
		$fields = array(
			'client_image' => array(
                'aglee_widgets_name' => 'client_image',
                'aglee_widgets_title' => __('Clients Image','aglee-lite'),
                'aglee_widgets_field_type' => 'upload'
            ),
            'client_name' => array(
                'aglee_widgets_name' => 'client_name',
                'aglee_widgets_title' => __('Client Name','aglee-lite'),
                'aglee_widgets_field_type' => 'text'
            ),
            'client_designation' => array(
                'aglee_widgets_name' => 'client_designation',
                'aglee_widgets_title' => __('Designation','aglee-lite'),
                'aglee_widgets_field_type' => 'text'
            ),
            'client_testimonial' => array(
                'aglee_widgets_name' => 'client_testimonial',
                'aglee_widgets_title' => __('Testimonial','aglee-lite'),
                'aglee_widgets_field_type' => 'textarea'
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
		extract( $args );
        extract($instance);
        $client_image = empty($instance['client_image']) ? false : $instance['client_image'];
        $client_name = empty($instance['client_name']) ? false : $instance['client_name'];
        $client_designation = empty($instance['client_designation']) ? false : $instance['client_designation'];
        $client_testimonial = empty($instance['client_testimonial']) ? false : $instance['client_testimonial'];
        
        $img_id = attachment_url_to_postid($client_image);
        $image = wp_get_attachment_image_src($img_id,'aglee-lite-testimonial-thumbnail');
        
        echo $before_widget;
            ?>
                <div class="testimonials-wrap clearfix">
                    <figure class="testimonial-image-wrap">
                            <?php if(!empty($image[0])) : ?>
                                <div class="testimonial_img">
                                <img src="<?php echo esc_url($image[0]); ?>" />
                            <?php else : ?>
                                <img src="<?php echo esc_url(get_template_directory_uri().'/images/no-testimonial-thumbnail.png'); ?>" />
                            <?php endif; ?>
                        </div>                           
                    </figure>
                    <div class="testimonial_content">
                        <?php echo esc_textarea($client_testimonial); ?>
                    </div>
                    <div class="testimonial_info">
                        <span class="client-name"><?php echo esc_attr($client_name); ?></span>
                        <span class="client-designation"><?php echo esc_attr($client_designation); ?></span> 
                    </div>
                </div>
            <?php
        echo $after_widget;        
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