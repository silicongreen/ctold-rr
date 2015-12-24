<?php
/**
 * Preview post/page widget
 *
 * @package Aglee Lite
 */

/**
 * Adds aglee_lite_Preview_Post widget.
 */
add_action( 'widgets_init', 'register_toggle_widget' );
function register_toggle_widget() {
    register_widget( 'aglee_lite_toggle_widget' );
}
class Aglee_Lite_Toggle_Widget extends WP_Widget {

	/**
	 * Register widget with WordPress.
	 */
	public function __construct() {
		parent::__construct(
	 		'aglee_lite_toggle',
			'Aglee : Toggle',
			array(
				'description'	=> __( 'A widget To Display Toggle', 'aglee-lite' )
			)
		);
	}

	/**
	 * Helper function that holds widget fields
	 * Array is used in update and form functions
	 */
	 private function widget_fields() {
	   $status = array(
        'close' => 'close',
        'open' => 'open'
       );
       
		$fields = array(
			'toggle_title' => array(
                'aglee_widgets_name' => 'toggle_title',
                'aglee_widgets_title' => __('Toggle Title','aglee-lite'),
                'aglee_widgets_field_type' => 'text'
            ),
            'toggle_content' => array(
                'aglee_widgets_name' => 'toggle_content',
                'aglee_widgets_title' => __('Toggle Content','aglee-lite'),
                'aglee_widgets_field_type' => 'textarea'
            ),
            'toggle_status' => array(
                'aglee_widgets_name' => 'toggle_status',
                'aglee_widgets_title' => __('Toggle Status','aglee-lite'),
                'aglee_widgets_field_type' => 'select',
                'aglee_widgets_field_options' => $status
            ),
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
		$toggle_title = empty($instance['toggle_title']) ? false : $instance['toggle_title'];
        $toggle_content = empty($instance['toggle_content']) ? false : $instance['toggle_content'];
        $toggle_status = empty($instance['toggle_status']) ? false : $instance['toggle_status'];
        
        echo $before_widget;
        ?>
        <?php if(!empty($toggle_title)) : ?>
            <div class="ap_toggle <?php echo esc_attr($toggle_status); ?>">
                <?php if(!empty($toggle_title)) : ?>
                    <div class="ap_toggle_title"><?php echo esc_attr($toggle_title); ?></div>           
                <?php endif; ?>
                <?php if(!empty($toggle_content)) : ?>   
                    <div class="ap_toggle_content clearfix"><?php echo esc_textarea($toggle_content); ?></div>
                <?php endif; ?>
            </div>
        <?php endif; ?>
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