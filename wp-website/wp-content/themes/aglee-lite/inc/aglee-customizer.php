<?php

/** customizer start ***/

function aglee_lite_customizer( $wp_customize ) {
    $wp_customize->add_panel( 
        'aglee_lite_general_panel',
         array(
            'priority' => 10,
            'capability' => 'edit_theme_options',
            'theme_supports' => '',
            'title' => __( 'General Panel', 'aglee-lite' ),
            'description' => __( 'Default section provided by wordpress customizer.', 'aglee-lite' ),
        ) 
    );  
    
    $wp_customize->get_section( 'colors' )->panel         = 'aglee_lite_general_panel';
    $wp_customize->get_section( 'background_image' )->panel         = 'aglee_lite_general_panel';
    $wp_customize->get_section( 'static_front_page' )->panel         = 'aglee_lite_general_panel';  
    

/**
 * 
 * Adding Heading Setting Panel in customizer
 * 
 */
    $wp_customize->add_panel( 
        'header_setting',
         array(
            'priority' => 20,
            'capability' => 'edit_theme_options',
            'theme_supports' => '',
            'title' => __( 'Header Setting', 'aglee-lite' ),
        ) 
    );
    
    $wp_customize->get_section( 'title_tagline' )->panel         = 'header_setting';
    $wp_customize->get_section( 'header_image' )->panel         = 'header_setting';

    
    $wp_customize->add_section(
        'header_text_disp_option',
        array(
            'title' => __('Header Display Option', 'aglee-lite'),
            'priority' => 60,
            'panel' => 'header_setting'
        )
    );
    
    $wp_customize->add_setting(
        'header_text_image_display',
        array(
            'default' => __('show_both','aglee-lite'),
            'transport' => 'refresh',
            'sanitize_callback' => 'sanitize_header_part_option',
        )
    );    
    
    // function to get webpage sanitize
        function sanitize_header_part_option($input) {
    		$valid_keys = array(
    			'header_logo_only' => __('header_logo_only', 'aglee-lite'),
    			'header_text_only' => __('header_text_only', 'aglee-lite'),
                'show_both' => __('show_both', 'aglee-lite'),
                'disable' => __('disable', 'aglee-lite')
    			);
    		if ( array_key_exists( $input, $valid_keys)) {
    			return $input;
    		} else {
    			return '';
    		}
	   }
    
    $wp_customize->add_control(
        'header_text_image_display',
        array(
            'section' => 'header_text_disp_option',
            'label' => __('Show', 'aglee-lite'),
            'type' => 'radio',
            'choices' => array(
                            'header_logo_only' => __('Header Logo Only','aglee-lite'),
                            'header_text_only' => __('Header Text Only','aglee-lite'),
                            'show_both' => __('Show Both','aglee-lite'),
                            'disable' => __('Hide Both', 'aglee-lite')
                        )
        )
    ); 
    
// Top header text
    $wp_customize->add_section(
        'header_text_display_section',
        array(
            'title' => __('Top Header Content Options','aglee-lite'),
            'priorty' => 60,
            'panel' => 'header_setting'
        )
    );
    
    $wp_customize->add_setting(
        'header_text_setting',
        array(
            'default' => __('Call Us: +1-123-123-45-78','aglee-lite'),
            'sanitize_callback' => 'sanitize_text', 
        )
    );
    
    $wp_customize->add_control(
        'header_text_setting',
        array(
            'section' => 'header_text_display_section',
            'label' => __('Header Right Text','aglee-lite'),
            'type' => 'textarea'
        )
    );
    
    $wp_customize->add_setting(
        'show_social',
        array(
            'default' => '',
            'transport'=>'refresh',
            'sanitize_callback' => 'sanitize_checkbox'
        )
    );
    
    $wp_customize->add_control(
        'show_social',
            array(
                'label'=>__('Hide Social Icons in Header','aglee-lite'),
                'section'=>'header_text_display_section',
                'type'=>'checkbox'
            )
    );
    
    $wp_customize->add_setting(
        'show_search',
        array(
            'default'=>'',
            'transport'=>'refresh',
            'sanitize_callback' => 'sanitize_checkbox'
        )
    );
    
    $wp_customize->add_control(
        'show_search',
        array(
            'label'=>__('Hide Search in Header','aglee-lite'),
            'section'=>'header_text_display_section',
            'type'=>'checkbox'
        )
    );
/** End of header text option section **/        
 
 
    // Footer text
    $wp_customize->add_section(
        'aglee_lite_footertext_section',
        array(
            'title' => __('Footer Text','aglee-lite'),
            'priority' => 60,
            'panel' => 'header_setting'
        )
    );
    
    $wp_customize->add_setting(
        'aglee_lite_footertext',
        array(
            'default'=>__('AgleeLite','aglee-lite'),
            'transport' => 'refresh',
            'sanitize_callback' => 'sanitize_text' 
        )
    );
    
    $wp_customize->add_control(
        'aglee_lite_footertext',
        array(
            'label' => __('Footer Text','aglee-lite'),
            'section'=>'aglee_lite_footertext_section',
            'type'=>'text'
        )
    ); 
    
/** third pannel design setting **/

/**
 * 
 * Adding Basic Setting Panel in customizer
 * 
 */
    $wp_customize->add_panel( 
        'design_setting',
         array(
            'priority' => 20,
            'capability' => 'edit_theme_options',
            'theme_supports' => '',
            'title' => __( 'Design Setting', 'aglee-lite' ),
        ) 
    );
    
    // Responsive setting checkbox
    $wp_customize->add_section(
        'site_layout_section',
        array(
            'title' => __('Site Layout','aglee-lite'),
            'description' => __('Site Layout Option','aglee-lite'),
            'priority' => 10,
            'panel' => 'design_setting',
        )
    );
    
    $wp_customize->add_setting(
        'site_layout_setting',
        array(
            'default' => __('full_width','aglee-lite'),
            'sanitize_callback' => 'webpage_layout_radio_sanitize',
            )
    );
    
    $wp_customize -> add_control(
        'site_layout_setting',
        array(
            'label' => __('Site Layout','aglee-lite'),
            'section' => 'site_layout_section',
            'type' => 'radio',
            'choices' => array(
                            'full_width' => __('Fullwidth','aglee-lite'),
                            'boxed' => __('Boxed','aglee-lite'),
                        )
        )
    );
    
        // function to get webpage sanitize
        function webpage_layout_radio_sanitize($input) {
    		$valid_keys = array(
    			'fullwidth' => __('fullwidth', 'aglee-lite'),
    			'boxed' => __('boxed', 'aglee-lite')
    			);
    		if ( array_key_exists( $input, $valid_keys)) {
    			return $input;
    		} else {
    			return '';
    		}
	   }
       
    // pattern selection for box layout
        $wp_customize->add_section(
        'site_pattern_section',
        array(
            'title' => __('Background Image','aglee-lite'),
            'description' => __('Select Box layout above to use this functionality','aglee-lite'),
            'priority' => 10,
            'panel' => 'design_setting',
        )
    );
    
    $wp_customize->add_setting(
        'site_pattern_setting',
        array(
            'default' => '',
            'sanitize_callback' => 'webpage_pattern_radio_sanitize',
            )
    );
    
    $wp_customize -> add_control(
        'site_pattern_setting',
        array(
            'label' => __('Site Layout','aglee-lite'),
            'section' => 'site_pattern_section',
            'type' => 'radio',
            'choices' => array(
                            'pattern0' => __('None','aglee-lite'),
                            'pattern1' => __('Pattern 1','aglee-lite'),
                            'pattern3' => __('Pattern 2','aglee-lite'),
                            'pattern4' => __('Pattern 3','aglee-lite'),
                            'pattern5' => __('Pattern 4', 'aglee-lite'),
                        )
        )
    );
    
        // function to get webpage sanitize
        function webpage_pattern_radio_sanitize($input) {
    		$valid_keys = array(
    			'pattern0' => __('pattern0', 'aglee-lite'),
    			'pattern1' => __('pattern1', 'aglee-lite'),
                'pattern3' => __('pattern3', 'aglee-lite'),
    			'pattern4' => __('pattern4', 'aglee-lite'),
                'pattern5' => __('pattern5', 'aglee-lite'),
    			);
    		if ( array_key_exists( $input, $valid_keys)) {
    			return $input;
    		} else {
    			return '';
    		}
	   }
    
    
    
    //Layout of category/blog
    $wp_customize -> add_section(
        'layout_category_blog_section',
        array(
            'title' => __('Default Layout (Category/Blog)','aglee-lite'),
            'priority' => 10,
            'panel' => 'design_setting'
        )
    );
    
    $wp_customize -> add_setting(
        'layout_category_blog',
        array(
            'default' => __('no_sidebar_wide','aglee-lite'),
            'transport' => 'refresh',
            'sanitize_callback' => 'webpage_sanitize_category_blog'
        )
    );
    
    $wp_customize -> add_control(
        'layout_category_blog',
        array(
            'label' => __('Default Layout (Category/Blog)','aglee-lite'),
            'section' => 'layout_category_blog_section',
            'type' => 'radio',
            'choices' => array(
                            'left_sidebar' => __('Left Sidebar','aglee-lite'),
                            'right_sidebar' => __('Right Sidebar','aglee-lite'),
                            'both_sidebar' => __('Both Sidebar','aglee-lite'),
                            'no_sidebar_wide' => __('No Sidebar Wide','aglee-lite'),
                            'no_sidebar_narrow' => __('No Sidebar Narrow','aglee-lite')
                        )
        )
    );
    
 
     //Layout of Default Layout page only
    $wp_customize -> add_section(
        'layout_default_page_section',
        array(
            'title' => __('Default Layout (Pages Only)','aglee-lite'),
            'priority' => 10,
            'panel' => 'design_setting'
        )
    );
    
    $wp_customize -> add_setting(
        'layout_default_page',
        array(
            'default' => __('no_sidebar_wide','aglee-lite'),
            'transport' => 'refresh',
            'sanitize_callback' => 'webpage_sanitize_category_blog'
        )
    );
    
    $wp_customize -> add_control(
        'layout_default_page',
        array(
            'label' => __('Default Layout (Pages Only)','aglee-lite'),
            'section' => 'layout_default_page_section',
            'type' => 'radio',
            'choices' => array(
                            'left_sidebar' => __('Left Sidebar','aglee-lite'),
                            'right_sidebar' => __('Right Sidebar', 'aglee-lite'),
                            'both_sidebar' => __('Both Sidebar', 'aglee-lite'),
                            'no_sidebar_wide' => __('No Sidebar Wide','aglee-lite'),
                            'no_sidebar_narrow' => __('No Sidebar Narrow','aglee-lite')
                        )
        )
    );
    
     //Layout of Default Layout post
    $wp_customize -> add_section(
        'layout_default_post_section',
        array(
            'title' => __('Default Layout (Post Only)','aglee-lite'),
            'priority' => 10,
            'panel' => 'design_setting'
        )
    );
    
    $wp_customize -> add_setting(
        'layout_default_post',
        array(
            'default' => __('no_sidebar_wide','aglee-lite'),
            'transport' => 'refresh',
            'sanitize_callback' => 'webpage_sanitize_category_blog'
        )
    );
    
    $wp_customize -> add_control(
        'layout_default_post',
        array(
            'label' => __('Default Layout (Post Only)','aglee-lite'),
            'section' => 'layout_default_post_section',
            'type' => 'radio',
            'choices' => array(
                            'left_sidebar' => __('Left Sidebar','aglee-lite'),
                            'right_sidebar' => __('Right Sidebar', 'aglee-lite'),
                            'both_sidebar' => __('Both Sidebar', 'aglee-lite'),
                            'no_sidebar_wide' => __('No Sidebar Wide','aglee-lite'),
                            'no_sidebar_narrow' => __('No Sidebar Narrow','aglee-lite')
                        )
        )
    );   
    
        // function to get webpage sanitize
        function webpage_sanitize_category_blog($input) {
    		$valid_keys = array(
    			'no_sidebar_wide' => __('no_sidebar_wide', 'aglee-lite'),
    			'left_sidebar' => __('left_sidebar', 'aglee-lite'),
                'right_sidebar' => __('right_sidebar', 'aglee-lite'),
                'both_sidebar' => __('both_sidebar', 'aglee-lite'),
                'no_sidebar_narrow' => __('no_sidebar_narrow', 'aglee-lite'),
    			);
    		if ( array_key_exists( $input, $valid_keys)) {
    			return $input;
    		} else {
    			return '';
    		}
	   }

       
    /* to enable and disable footer featured widget */

    $wp_customize->add_section(
        'footer_widget_section',
        array(
            'title' => __('Enable/Disable (Footer Featured Widgets)','aglee-lite'),
            'priorty' => 10,
            'panel' => 'design_setting'
        )
    );
    
    $wp_customize->add_setting(
        'footer_widget',
        array(
            'default' => '',
            'transport'=>'refresh',
            'sanitize_callback' => 'sanitize_checkbox'
        )
    );
    
    $wp_customize->add_control(
        'footer_widget',
            array(
                'label'=>__('Show Footer Featured Widget Section','aglee-lite'),
                'section'=>'footer_widget_section',
                'type'=>'checkbox'
            )
    );
    
    /* to enable and disable page comment */

    $wp_customize->add_section(
        'page_comment_section',
        array(
            'title' => __('Enable/Disable (Page Comments)','aglee-lite'),
            'priorty' => 10,
            'panel' => 'design_setting'
        )
    );
    
    $wp_customize->add_setting(
        'page_comment',
        array(
            'default' => '',
            'transport'=>'refresh',
            'sanitize_callback' => 'sanitize_checkbox'
        )
    );
    
    $wp_customize->add_control(
        'page_comment',
            array(
                'label'=>__('Show Comments in Page','aglee-lite'),
                'section'=>'page_comment_section',
                'type'=>'checkbox'
            )
    );
    
    /* to enable and disable post comment */

    $wp_customize->add_section(
        'post_comment_section',
        array(
            'title' => __('Enable/Disable (Post Comments)','aglee-lite'),
            'priorty' => 10,
            'panel' => 'design_setting'
        )
    );
    
    $wp_customize->add_setting(
        'post_comment',
        array(
            'default' => '',
            'transport'=>'refresh',
            'sanitize_callback' => 'sanitize_checkbox'
        )
    );
    
    $wp_customize->add_control(
        'post_comment',
            array(
                'label'=>__('Show Comments in Post','aglee-lite'),
                'section'=>'post_comment_section',
                'type'=>'checkbox'
            )
    );
    
/**
 * 
 * Adding Slider Setting Panel in customizer
 * 
 */
    $wp_customize->add_panel( 
        'slider_setting_pannel',
         array(
            'priority' => 20,
            'capability' => 'edit_theme_options',
            'theme_supports' => '',
            'title' => __( 'Slider Settings', 'aglee-lite' ),
        ) 
    );
    
    // Slider type option post as a slider or category as a slider ..........................
    $wp_customize->add_section(
        'slider_type',
        array(
            'title' => __('Slider Settings','aglee-lite'),
            'priority' => 20,
            'panel' => 'slider_setting_pannel',
        )
    );
    
    $wp_customize->add_setting(
        'slider_type_choose',
        array(
            'default' => __('option1','aglee-lite'),
            'sanitize_callback' => 'slider_choose_radio_sanitize'
            )
    );
    
    $wp_customize->add_control(
        'slider_type_choose',
        array(
            'label' => __('Choose Slider Type','aglee-lite'),
            'section' => 'slider_type',
            'type' => 'radio',
            'choices' => array(
                             'option1' => __('Single Posts as a Slider','aglee-lite'),
                             'option2' => __('Category Posts as a Slider','aglee-lite'),
                             )
        )
    );
    
    function slider_choose_radio_sanitize($input) {
		$valid_keys = array(
			'option1' => __('option1', 'aglee-lite'),
			'option2' => __('option2', 'aglee-lite')
			);
		if ( array_key_exists( $input, $valid_keys)) {
			return $input;
		} else {
			return 'option1';
		}
	}
    
    
    //Slider Post Choose options ..................
    $wp_customize->add_section(
        'slider_page_choose_section',
        array(
            'title' => __('Slider Selection(Posts)','aglee-lite'),
            'priority' => 20,
            'description' => __('Enable single post as slider in slider setting section.','aglee-lite'),
            'panel' => 'slider_setting_pannel',
        )
    );
    
    $wp_customize->add_setting(
        'slider_one',
        array(
            'sanitize_callback' => 'aglee_sanitize_dropdown_general',
            )
    );
    $wp_customize->add_control( new Post_Dropdown( $wp_customize, 'slider_one',
        array(
            'label' => __('Choose Slider 1','aglee-lite'),
            'section' => 'slider_page_choose_section',
            'type' => 'select',
        )
    ) );
    
    $wp_customize->add_setting(
        'slider_two',
        array(
            'sanitize_callback' => 'aglee_sanitize_dropdown_general',
            )
    );
    $wp_customize->add_control( new Post_Dropdown( $wp_customize, 'slider_two',
        array(
            'label' => __('Choose Slider 2','aglee-lite'),
            'section' => 'slider_page_choose_section',
            'type' => 'select',
        )
    ) );
    
    $wp_customize->add_setting(
        'slider_three',
        array(
            'sanitize_callback' => 'aglee_sanitize_dropdown_general',
            )
    );
    $wp_customize->add_control( new Post_Dropdown( $wp_customize, 'slider_three',
        array(
            'label' => __('Choose Slider 3','aglee-lite'),
            'section' => 'slider_page_choose_section',
            'type' => 'select',
        )
    ) );
    
    $wp_customize->add_setting(
        'slider_four',
        array(
            'sanitize_callback' => 'aglee_sanitize_dropdown_general',
            )
    );
    $wp_customize->add_control( new Post_Dropdown( $wp_customize, 'slider_four',
        array(
            'label' => __('Choose Slider 4','aglee-lite'),
            'section' => 'slider_page_choose_section',
            'type' => 'select',
        )
    ) );
    
    //slider category choose
    $wp_customize->add_section(
        'slider_category_choose_section',
        array(
            'title' => __('Slider Selection(Category)','aglee-lite'),
            'priority' => 20,
            'description' => __('Enable category as slider in slider setting section.','aglee-lite'),
            'panel' => 'slider_setting_pannel',
        )
    );
    
    $wp_customize->add_setting(
        'slider_category',
        array(
            'sanitize_callback' => 'aglee_sanitize_dropdown_general'
            )
    );
    
    $wp_customize->add_control( new Category_Dropdown( $wp_customize, 'slider_category',
        array(
            'label' => __('Choose Slider category ', 'aglee-lite'),
            'section' => 'slider_category_choose_section',
            'type' => 'select',
        )
    ) );   
    
    
    // slider display option
    $wp_customize->add_section(
        'slider_show_option',
        array(
            'title' => __('Show Slider','aglee-lite'),
            'priorty' => 20,
            'panel' => 'slider_setting_pannel'
        )
    );
    
    $wp_customize->add_setting(
        'slider_setting',
        array(
            'default' => '',
            'transport'=>'refresh',
            'sanitize_callback' => 'sanitize_checkbox'
        )
    );
    
    $wp_customize->add_control(
        'slider_setting',
            array(
                'label'=>__('Show Slider','aglee-lite'),
                'section'=>'slider_show_option',
                'type'=>'checkbox'
            )
    );
    

    
    // slider display setting in single page
    $wp_customize -> add_section(
        'slider_show_post_section',
        array(
            'title' => __('Enable/Disable slider (Post Page)','aglee-lite'),
            'priority' => 30,
            'panel' => 'slider_setting_pannel'
        )
    );
    
    $wp_customize -> add_setting(
        'slider_show_post',
        array(
            'default' => '',
            'transport' => 'refresh',
            'sanitize_callback' => 'sanitize_checkbox'
        )
    );
    
    $wp_customize -> add_control(
        'slider_show_post',
        array(
            'label' => __('Enable Slider in Post','aglee-lite'),
            'section' => 'slider_show_post_section',
            'type' =>'checkbox',
        )
    );
    
    // for slider mode change
    $wp_customize -> add_section(
        'slider_mode_section',
        array(
            'title' => __('Slider Mode','aglee-lite'),
            'priority' => 30,
            'panel' => 'slider_setting_pannel'
        )
    );
    
    $wp_customize -> add_setting(
        'slider_mode_setting',
        array(
            'default' => __('horizontal','aglee-lite'),
            'transport' => 'refresh',
            'sanitize_callback' => 'aglee_lite_san_slidermode'
            
        )
    );
    
    
        function aglee_lite_san_slidermode ($input){
        		$valid_keys = array(
        			'horizontal' => __('horizontal', 'aglee-lite'),
        			'fade' => __('fade', 'aglee-lite'),
        			);
        		if ( array_key_exists( $input, $valid_keys)) {
        			return $input;
        		} else {
        			return '';
        		}
        }
    $wp_customize -> add_control(
        'slider_mode_setting',
        array(
            'label' => __('Slider Mode','aglee-lite'),
            'section' => 'slider_mode_section',
            'type' => 'radio',
            'choices' => array(
                            'horizontal' => __('Horizontal','aglee-lite'),
                            'fade' => __('Fade','aglee-lite'),
                        )
        )
    );
    
    
    // Hide readmore in home slider
    $wp_customize->add_section(
        'homeslider_readmore_show_option',
        array(
            'title' => __('Readmore Display Option','aglee-lite'),
            'priorty' => 20,
            'panel' => 'slider_setting_pannel'
        )
    );
    
    $wp_customize->add_setting(
        'readmore_slider_setting',
        array(
            'default' => '0',
            'transport'=>'refresh',
            'sanitize_callback' => 'sanitize_checkbox'
        )
    );
    
    $wp_customize->add_control(
        'readmore_slider_setting',
            array(
                'label'=>__('Click To Hide Readmore In Slider','aglee-lite'),
                'section'=>'homeslider_readmore_show_option',
                'type'=>'checkbox'
            )
    );
    
    
    /** Testimonial panel **/
       $wp_customize -> add_panel(
        'testimonial_select_panel',
        array(
            'priority' => 20,
            'capability' => 'edit_theme_options',
            'theme_supports' => '',
            'title' => __('Testimonial Slider', 'aglee-lite')
        )
       ); 
    
    // choose category of slider   
    $wp_customize->add_section(
        'testimonial_category_choose_section',
        array(
            'title' => __('Slider Selection(Category)','aglee-lite'),
            'priority' => 20,
            'description' => __('Choose Category for testimonial slider','aglee-lite'),
            'panel' => 'testimonial_select_panel',
        )
    );
    
    $wp_customize->add_setting(
        'slider_testimonial_category',
        array(
            'sanitize_callback' => 'aglee_sanitize_dropdown_general'
            )
    );
    
    $wp_customize->add_control( new Category_Dropdown( $wp_customize, 'slider_testimonial_category',
        array(
            'label' => __('Choose Testimonial Category', 'aglee-lite'),
            'section' => 'testimonial_category_choose_section',
            'type' => 'select',
        )
    ) ); 
    /**  end of testimonial panel **/       
      
       
    /** For Blog page selection **/
    
    $wp_customize -> add_panel( 
        'blogpage_setting_pannel',
         array(
            'priority' => 20,
            'capability' => 'edit_theme_options',
            'theme_supports' => '',
            'title' => __( 'Blog Page Option', 'aglee-lite' ),
        ) 
      );
      
      $wp_customize -> add_section(
        'blog_category_select_section',
        array(
            'title' => __('Choose Category To Use As Blog','aglee-lite'),
            'priority' => 30,
            'panel' => 'blogpage_setting_pannel'
        )        
      );
      
      $wp_customize -> add_setting(
        'blog_category_select_setting',
        array(
            'sanitize_callback' => 'aglee_sanitize_dropdown_general'
        )
      );
      
      $wp_customize -> add_control(new Category_Dropdown( $wp_customize, 'blog_category_select_setting',
        array(
            'label' => __('Choose Blog Category', 'aglee-lite'),
            'section' => 'blog_category_select_section',
            'type' => 'select',
        )
    ));      
    
    
      // Blog Post display Layout
    $wp_customize -> add_section(
        'blog_post_layout_section',
        array(
            'title' => __('Blog Post Display Layout','aglee-lite'),
            'description' => '',
            'priority' => 10,
            'panel' => 'blogpage_setting_pannel'
        )
    );
    
    $wp_customize -> add_setting(
        'blog_post_layout',
        array(
            'default' => __('blog_image_large','aglee-lite'),
            'sanitize_callback' => 'webpage_sanitize_blog_layout',
            'transport' => 'refresh'
        )
    );
    
    $wp_customize -> add_control(
        'blog_post_layout',
        array(
            'label' => __('Blog Posts Display Layout','aglee-lite'),
            'section' => 'blog_post_layout_section',
            'type' => 'radio',
            'choices' => array(
                            'blog_image_large' => __('Blog Image Large','aglee-lite'),
                            'blog_image_medium' => __('Blog Image Medium', 'aglee-lite'),
                            'blog_image_alternate_medium' => __('Blog Image Alternate Medium', 'aglee-lite'),
                            'blog_full_content' => __('Blog Full Content', 'aglee-lite'),
                        )            
        )
    );
    
    
        // function to get webpage sanitize
        function webpage_sanitize_blog_layout($input) {
    		$valid_keys = array(
    			'blog_image_large' => __('blog_image_large', 'aglee-lite'),
    			'blog_image_medium' => __('blog_image_medium', 'aglee-lite'),
                'blog_image_alternate_medium' => __('blog_image_alternate_medium', 'aglee-lite'),
                'blog_full_content' => __('blog_full_content', 'aglee-lite')
    			);
    		if ( array_key_exists( $input, $valid_keys)) {
    			return $input;
    		} else {
    			return '';
    		}
	   }
    /** End of Blog Page selection **/

      
     /** Translation panel **/
     
     $wp_customize -> add_panel( 
        'translation_setting_pannel',
         array(
            'priority' => 20,
            'capability' => 'edit_theme_options',
            'theme_supports' => '',
            'title' => __( 'Translations', 'aglee-lite' ),
        ) 
      );
      
      $wp_customize -> add_section(
        'home_page_translation_section',
        array(
            'title' => __('Home Page','aglee-lite'),
            'priority' => 30,
            'panel' => 'translation_setting_pannel'
        )
      );
      $wp_customize -> add_setting(
        'home_page_translation',
        array(
            'default' => '',
            'transport' => 'refresh',
            'sanitize_callback' => 'sanitize_text'
        )
      );
      $wp_customize -> add_control(
        'home_page_translation',
        array(
            'label' => __('Read More... (Features)','aglee-lite'),
            'section' => 'home_page_translation_section',
            'type' => 'text'
        )
      );
      
      $wp_customize -> add_setting(
        'home_page_moreinfo',
        array(
            'default' => '',
            'transport' => 'refresh',
            'sanitize_callback' => 'sanitize_text'
        )
      );
      $wp_customize -> add_control(
        'home_page_moreinfo',
        array(
            'label' => __('More Info... (Services)','aglee-lite'),
            'section' => 'home_page_translation_section',
            'type' => 'text'
        )
      );
      
      // blog page readmore text change
      $wp_customize -> add_section(
        'blog_page_translation_section',
        array(
            'title' => __('Blog/Archive','aglee-lite'),
            'priority' => 30,
            'panel' => 'translation_setting_pannel'
        )
      );
      $wp_customize -> add_setting(
        'blog_page_translation',
        array(
            'default' => '',
            'transport' => 'refresh',
            'sanitize_callback' => 'sanitize_text'
        )
      );
      $wp_customize -> add_control(
        'blog_page_translation',
        array(
            'label' => __('Read More... ','aglee-lite'),
            'section' => 'blog_page_translation_section',
            'type' => 'text'
        )
      );
      
      // Single Post Page
      $wp_customize -> add_section(
        'single_post_page_section',
        array(
            'title' => __('Single Post Page','aglee-lite'),
            'priority' => 30,
            'panel' => 'translation_setting_pannel'
        )
      );
      
      $wp_customize -> add_setting(
        'tagged_post_page',
        array(
            'default' => '',
            'transport' => 'refresh',
            'sanitize_callback' => 'sanitize_text'
        )
      );
      
      $wp_customize -> add_control(
        'tagged_post_page',
        array(
            'label' => __('Tagged','aglee-lite'),
            'section' => 'single_post_page_section',
            'type' => 'text'
        )
      );
      
      $wp_customize -> add_setting(
        'postedon_post_page',
        array(
            'default' => '',
            'transport' => 'refresh',
            'sanitize_callback' => 'sanitize_text'
        )
      );
      $wp_customize -> add_control(
        'postedon_post_page',
        array(
            'label' => __('Posted On .. by ..','aglee-lite'),
            'section' => 'single_post_page_section',
            'type' => 'text'
        )
      );
      
      $wp_customize -> add_setting(
        'by_post_page',
        array(
            'default' => '',
            'transport' => 'refresh',
            'sanitize_callback' => 'sanitize_text'
        )
      );
      $wp_customize -> add_control(
        'by_post_page',
        array(
            'label' => ' ',
            'section' => 'single_post_page_section',
            'type' => 'text',
            'placeholder' => __('by','aglee-lite')
        )
      );
      
      $wp_customize -> add_setting(
        'in_post_page',
        array(
            'default' => '',
            'transport' => 'refresh',
            'sanitize_callback' => 'sanitize_text'
        )
      );
      $wp_customize -> add_control(
        'in_post_page',
        array(
            'label' => __('Posted In','aglee-lite'),
            'section' => 'single_post_page_section',
            'type' => 'text'
        )
      );
      
      // Search result for 
      $wp_customize -> add_section(
        'search_page_section',
        array(
            'title' => __('Search Page','aglee-lite'),
            'priority' => 30,
            'panel' => 'translation_setting_pannel'
        )
      );
      $wp_customize -> add_setting(
        'search_page_setting',
        array(
            'default' => '',
            'transport' => 'refresh',
            'sanitize_callback' => 'sanitize_text'
        )
      );
      $wp_customize -> add_control(
        'search_page_setting',
        array(
            'label' => __('Search Results For','aglee-lite'),
            'section' => 'search_page_section',
            'type' => 'text'
        )
      );
    
    //General dropdown sanitize for integer value
    function aglee_sanitize_dropdown_general( $input ) {
        return absint( $input );
    }
    
    //Sanitize input text general
    function sanitize_text( $input ){
        return wp_kses_post( force_balance_tags( $input ) );
    } 
    
    //Checkbox sanitization customizer
    function sanitize_checkbox( $input ) {
        if ( $input == 1 ) {
            return 1;
        } else {
            return '';
        }
    }
    
    }
    add_action( 'customize_register', 'aglee_lite_customizer' );

?>