<?php
/**
 * Call To Action
 *
 * @package Aglee Lite
 */

/**
 * Adds aglee_lite_Preview_Post widget.
 */
add_action( 'widgets_init', 'register_cta_widget' );
function register_cta_widget() {
    register_widget( 'aglee_lite_cta_widget' );
}
class Aglee_Lite_Cta_Widget extends WP_Widget {

	/**
	 * Register widget with WordPress.
	 */
	public function __construct() {
		parent::__construct(
	 		'aglee_lite_cta',
			'Aglee : Call To Action',
			array(
				'description'	=> __( 'A widget To Display Call To Action', 'aglee-lite' )
			)
		);
	}

	/**
	 * Helper function that holds widget fields
	 * Array is used in update and form functions
	 */
	 private function widget_fields() {
		$fields = array(
			'cta_title' => array(
                'aglee_widgets_name' => 'cta_title',
                'aglee_widgets_title' => __('Title','aglee-lite'),
                'aglee_widgets_field_type' => 'text'
            ),
            'cta_descr' => array(
                'aglee_widgets_name' => 'cta_descr',
                'aglee_widgets_title' => __('Description','aglee-lite'),
                'aglee_widgets_field_type' => 'textarea'
            ),
            'cta_readmore_text' => array(
                'aglee_widgets_name' => 'cta_readmore_text',
                'aglee_widgets_title' => __('Read More Text','aglee-lite'),
                'aglee_widgets_field_type' => 'text'
            ),
            'cta_readmore_link' => array(
                'aglee_widgets_name' => 'cta_readmore_link',
                'aglee_widgets_title' => __('Read More Link','aglee-lite'),
                'aglee_widgets_field_type' => 'text'
            ),
            'cta_fa_icon' => array(
                'aglee_widgets_name' => 'cta_fa_icon',
                'aglee_widgets_title' => __('Read More Button Icon','aglee-lite'),
                'aglee_widgets_field_type' => 'text',
                'aglee_widgets_description' => 'e.g. fa-diamond <a href="'.esc_url('http://fortawesome.github.io/Font-Awesome/icons/').'" target="_blank">get featured icon class</a>'
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
        extract($args);
        
        $cta_title = empty($instance['cta_title']) ? false : $instance['cta_title'];
        $cta_descr = empty($instance['cta_descr']) ? false : $instance['cta_descr'];
        $cta_readmore_link = empty($instance['cta_readmore_link']) ? false : $instance['cta_readmore_link'];
        $cta_readmore_text = empty($instance['cta_readmore_text']) ? false : $instance['cta_readmore_text'];
        $cta_fa_icon = empty($instance['cta_fa_icon']) ? false : $instance['cta_fa_icon'];
        
        echo $before_widget;
            ?>
            <div class="cta-wrap clearfix">
                <div class="ap-container">
                	<div class="cta-desc-wrap">
                    <h2 class="cta_title">
                        <?php echo esc_attr($cta_title); ?>
                    </h2>
                    <div class="cta_descr">
                        <?php echo esc_textarea($cta_descr); ?>
                    </div>
                	</div>
                    <div class="cta-btn-wrap">
                    	<a href="<?php echo esc_url($cta_readmore_link); ?>" target="_blank">
                            <?php if(!empty($cta_fa_icon)) : ?>
                                <i class="fa <?php echo esc_attr($cta_fa_icon); ?>"></i>
                            <?php endif; ?>
                            <?php echo esc_attr($cta_readmore_text); ?>
                        </a>
               	 	</div>
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