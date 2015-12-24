<?php
/**
 * 
 * Aglee Lite Custom Sidebar
 *  For Widgets
 * 
 */ 
 add_action('widgets_init','aglee_lite_additional_widgets');
 
 function aglee_lite_additional_widgets(){
    // Registering main right sidebar
	register_sidebar( array(
		'name' 				=> __( 'Right Sidebar', 'aglee-lite' ),
		'id' 					=> 'aglee_right_sidebar',
		'description'   	=> __( 'Shows widgets at Right side.', 'aglee-lite' ),
		'before_widget' 	=> '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  	=> '</aside>',
		'before_title'  	=> '<h3 class="widget-title"><span>',
		'after_title'   	=> '</span></h3>'
	) );
    
    // Registering main left sidebar
	register_sidebar( array(
		'name' 				=> __( 'Left Sidebar', 'aglee-lite' ),
		'id' 					=> 'aglee_left_sidebar',
		'description'   	=> __( 'Shows widgets at Left side.', 'aglee-lite' ),
		'before_widget' 	=> '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  	=> '</aside>',
		'before_title'  	=> '<h3 class="widget-title"><span>',
		'after_title'   	=> '</span></h3>'
	) );
    
    // Registering featured section
	register_sidebar( array(
		'name' 				=> __( 'Featured Section', 'aglee-lite' ),
		'id' 					=> 'aglee_featured_section',
		'description'   	=> __( 'Shows widgets at Featured Post.', 'aglee-lite' ),
		'before_widget' 	=> '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  	=> '</aside>',
		'before_title'  	=> '<h3 class="widget-title"><span>',
		'after_title'   	=> '</span></h3>'
	) );
    
    // Registering call to action section
	register_sidebar( array(
		'name' 				=> __( 'Call To Action Section', 'aglee-lite' ),
		'id' 					=> 'aglee_cta_section',
		'description'   	=> __( 'Shows widgets at Call To Action.', 'aglee-lite' ),
		'before_widget' 	=> '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  	=> '</aside>',
		'before_title'  	=> '<h3 class="widget-title"><span>',
		'after_title'   	=> '</span></h3>'
	) );
    
    // Registering testimonial section
	register_sidebar( array(
		'name' 				=> __( 'Icon Text Block Section', 'aglee-lite' ),
		'id' 					=> 'icon_text_block_section',
		'description'   	=> __( 'Shows widgets at Icon Text Block Section.', 'aglee-lite' ),
		'before_widget' 	=> '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  	=> '</aside>',
		'before_title'  	=> '<h3 class="widget-title"><span>',
		'after_title'   	=> '</span></h3>'
	) );
    
    // Registering toggle section
	register_sidebar( array(
		'name' 				=> __( 'Toggle Section', 'aglee-lite' ),
		'id' 					=> 'aglee_toggle_section',
		'description'   	=> __( 'Shows Toggles at Toggle Section.', 'aglee-lite' ),
		'before_widget' 	=> '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  	=> '</aside>',
		'before_title'  	=> '<h3 class="widget-title"><span>',
		'after_title'   	=> '</span></h3>'
	) );
    
    // Registering toggle section
	register_sidebar( array(
		'name' 				=> __( 'Featured Page Section', 'aglee-lite' ),
		'id' 					=> 'aglee_featured_page_section',
		'description'   	=> __( 'Shows Featured Page In Featured Page Section.', 'aglee-lite' ),
		'before_widget' 	=> '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  	=> '</aside>',
		'before_title'  	=> '<h3 class="widget-title"><span>',
		'after_title'   	=> '</span></h3>'
	) );
    
    // Registering services section
	register_sidebar( array(
		'name' 				=> __( 'Services Section', 'aglee-lite' ),
		'id' 					=> 'aglee_services_section',
		'description'   	=> __( 'Shows widgets at Services.', 'aglee-lite' ),
		'before_widget' 	=> '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  	=> '</aside>',
		'before_title'  	=> '<h3 class="widget-title"><span>',
		'after_title'   	=> '</span></h3>'
	) );
    
    // Registering Header Text Widget
	register_sidebar( array(
		'name' 				=> __( 'Header Text Section', 'aglee-lite' ),
		'id' 					=> 'aglee_header_text',
		'description'   	=> __( 'Shows widgets at Header Text Section.', 'aglee-lite' ),
		'before_widget' 	=> '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  	=> '</aside>',
		'before_title'  	=> '<h3 class="widget-title"><span>',
		'after_title'   	=> '</span></h3>'
	) );
    
    // Registering Header Social Links Widget
	register_sidebar( array(
		'name' 				=> __( 'Header Social Links Section', 'aglee-lite' ),
		'id' 					=> 'aglee_header_social_links',
		'description'   	=> __( 'Shows widgets at Header Text Section.', 'aglee-lite' ),
		'before_widget' 	=> '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  	=> '</aside>',
		'before_title'  	=> '<h3 class="widget-title"><span>',
		'after_title'   	=> '</span></h3>'
	) );
    
    // Registering Footer Social Links Widget
	register_sidebar( array(
		'name' 				=> __( 'Footer Social Links Section', 'aglee-lite' ),
		'id' 					=> 'aglee_footer_social_links',
		'description'   	=> __( 'Shows widgets at Footer.', 'aglee-lite' ),
		'before_widget' 	=> '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  	=> '</aside>',
		'before_title'  	=> '<h3 class="widget-title"><span>',
		'after_title'   	=> '</span></h3>'
	) );
    
    // Registering footer one section
	register_sidebar( array(
		'name' 				=> __( 'Footer One', 'aglee-lite' ),
		'id' 					=> 'aglee_footer_one',
		'description'   	=> __( 'Shows widgets at Footer First Section .', 'aglee-lite' ),
		'before_widget' 	=> '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  	=> '</aside>',
		'before_title'  	=> '<h3 class="widget-title"><span>',
		'after_title'   	=> '</span></h3>'
	) );
    
    // Registering footer two section
	register_sidebar( array(
		'name' 				=> __( 'Footer Two', 'aglee-lite' ),
		'id' 					=> 'aglee_footer_two',
		'description'   	=> __( 'Shows widgets at Footer Second Section.', 'aglee-lite' ),
		'before_widget' 	=> '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  	=> '</aside>',
		'before_title'  	=> '<h3 class="widget-title"><span>',
		'after_title'   	=> '</span></h3>'
	) );
    
    // Registering footer three section
	register_sidebar( array(
		'name' 				=> __( 'Footer Three', 'aglee-lite' ),
		'id' 					=> 'aglee_footer_three',
		'description'   	=> __( 'Shows widgets at Footer Third Section.', 'aglee-lite' ),
		'before_widget' 	=> '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  	=> '</aside>',
		'before_title'  	=> '<h3 class="widget-title"><span>',
		'after_title'   	=> '</span></h3>'
	) ); 
    
    // Registering footer four section
	register_sidebar( array(
		'name' 				=> __( 'Footer Four', 'aglee-lite' ),
		'id' 					=> 'aglee_footer_four',
		'description'   	=> __( 'Shows widgets at Footer Fourth Section.', 'aglee-lite' ),
		'before_widget' 	=> '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  	=> '</aside>',
		'before_title'  	=> '<h3 class="widget-title"><span>',
		'after_title'   	=> '</span></h3>'
	) );
    
 } // END OF AGLEE LITE REGISTER SIDEBAR FUNCTION

/**
 * Aglee Lite Custom Widgets
 *
 * @package Aglee Lite Basic
 */

function aglee_lite_widgets_updated_field_value( $widget_field, $new_field_value ) {
    
	extract( $widget_field );
	
	// Allow only integers in number fields
	if( $aglite_widgets_field_type == 'number' ) {
		return absint( $new_field_value );
		
	// Allow some tags in textareas
	} elseif( $aglite_widgets_field_type == 'textarea' ) {
		// Check if field array specifed allowed tags
		if( !isset( $aglite_widgets_allowed_tags ) ) {
			// If not, fallback to default tags
			$aglite_widgets_allowed_tags = '<p><strong><em><a>';
		}
		return strip_tags( $new_field_value, $aglite_widgets_allowed_tags );
		
	// No allowed tags for all other fields
	} else {
		return strip_tags( $new_field_value );
	}

}

/**
 * Include helper functions that display widget fields in the dashboard
 *
 * @since Aglee Lite Widget Pack 1.0
 */
require get_template_directory() . '/inc/widgets/widgets-fields.php';

/**
 * Register Post Preview Widget
 *
 * @since Aglee Lite Widget Pack 1.0
 */
require get_template_directory() . '/inc/widgets/widgets-testimonials.php';

/**
 * Register Post Feature Posts
 *
 * @since Aglee Lite Widget Pack 1.0
 */
require get_template_directory() . '/inc/widgets/widgets-features.php';

/**
 * Register Post Services Posts
 *
 * @since Aglee Lite Widget Pack 1.0
 */
require get_template_directory() . '/inc/widgets/widgets-services.php';

/**
 * Register Post Services Posts
 *
 * @since Aglee Lite Widget Pack 1.0
 */
 require get_template_directory() . '/inc/widgets/widgets-cta.php';

/**
 * Register Icon Text Block
 *
 * @since Aglee Lite Widget Pack 1.0
 */
require get_template_directory() . '/inc/widgets/widgets-icon-text-block.php';

/**
 * Register Toggle
 *
 * @since Aglee Lite Widget Pack 1.0
 */
require get_template_directory() . '/inc/widgets/widgets-toggle.php';

/**
 * Register Featured Page
 *
 * @since Aglee Lite Widget Pack 1.0
 */
 require get_template_directory() . '/inc/widgets/widgets-featured-page.php';