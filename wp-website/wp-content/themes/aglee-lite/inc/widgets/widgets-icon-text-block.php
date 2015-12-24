<?php
/**
 * Preview post/page widget
 *
 * @package Aglee Lite
 */

/**
 * Adds aglee_lite_Preview_Post widget.
 */
add_action( 'widgets_init', 'register_icon_text_widget' );
function register_icon_text_widget() {
    register_widget( 'aglee_lite_icon_text_widget' );
}
class Aglee_Lite_Icon_Text_Widget extends WP_Widget {

	/**
	 * Register widget with WordPress.
	 */
	public function __construct() {
		parent::__construct(
	 		'aglee_lite_icon_text',
			'Aglee : Icon Text Block',
			array(
				'description' => __( 'A widget To Display Icon Text Block', 'aglee-lite' )
			)
		);
	}

	/**
	 * Helper function that holds widget fields
	 * Array is used in update and form functions
	 */
	 private function widget_fields() {
		$fields = array(
			'icon_title' => array(
                'aglee_widgets_name' => 'icon_title',
                'aglee_widgets_title' => __('Title','aglee-lite'),
                'aglee_widgets_field_type' => 'text'
            ),
            'icon_detail' => array(
                'aglee_widgets_name' => 'icon_detail',
                'aglee_widgets_title' => __('Details','aglee-lite'),
                'aglee_widgets_field_type' => 'textarea'
            ),
            'icon_fa_class' => array(
                'aglee_widgets_name' => 'icon_fa_class',
                'aglee_widgets_title' => __('Fa - Icon Class','aglee-lite'),
                'aglee_widgets_field_type' => 'text',
                'aglee_widgets_description' => 'e.g. fa-diamond <a href="'.esc_url('http://fortawesome.github.io/Font-Awesome/icons/').'" target="_blank">get featured icon class</a>'
            ),
            'icon_readmore_text' => array(
                'aglee_widgets_name' => 'icon_readmore_text',
                'aglee_widgets_title' => __('Read More Text','aglee-lite'),
                'aglee_widgets_field_type' => 'text'
            ),
            'icon_readmore_link' => array(
                'aglee_widgets_name' => 'icon_readmore_link',
                'aglee_widgets_title' => __('Read More Link','aglee-lite'),
                'aglee_widgets_field_type' => 'text'
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
        $icon_title = empty($instance['icon_title']) ? false : $instance['icon_title'];
        $icon_detail = empty($instance['icon_detail']) ? false : wp_trim_words($instance['icon_detail'],34);
        $icon_fa_class = empty($instance['icon_fa_class']) ? false : $instance['icon_fa_class'];
        $icon_readmore_text = empty($instance['icon_readmore_text']) ? __('Read More','aglee-lite') : $instance['icon_readmore_text']; 
        $icon_readmore_link = empty($instance['icon_readmore_link']) ? false : $instance['icon_readmore_link'];
        static $ic_count = 1;
        echo $before_widget;
            ?>
            <?php if(!empty($icon_title) && !empty($icon_detail)) : ?>
                <div class="icon-text-wrap">
        
                    <?php if(empty($icon_fa_class)) : $icon_fa_class = 'fa-globe'; endif; ?>
                    <a href="<?php echo esc_url($icon_readmore_link); ?>" class="icon-image">
                        <i class="fa <?php echo esc_attr($icon_fa_class); ?>"></i>                                            
                    </a>
                    <div class="icon-wrap">
                        <?php if(!empty($icon_title)) : ?>
                        <a href="<?php echo esc_url($icon_readmore_link); ?>">
                        <h5 class="icon-block-title"><?php echo esc_attr($icon_title); ?></h5>
                        </a>
                        <?php endif; ?>
                        
                        <?php if(!empty($icon_detail)) : ?>
                        <div class="icon-details">
                            <?php echo esc_textarea($icon_detail); ?>
                        </div>
                        <?php endif; ?>
                        <?php if(!empty($icon_readmore_link) && !empty($icon_readmore_text)) : ?>
                            <a class="icon_readmore-button readmore-button" href="<?php echo esc_url($icon_readmore_link); ?>" target="_blank"><?php echo esc_attr($icon_readmore_text); ?></a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>
            <?php
        echo $after_widget;
        $ic_count++;        
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