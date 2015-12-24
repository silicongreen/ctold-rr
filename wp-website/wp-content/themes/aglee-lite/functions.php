<?php
/**
 * Aglee Lite functions and definitions
 *
 * @package Aglee Lite
 */

if ( ! function_exists( 'aglee_lite_setup' ) ) :
/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which
 * runs before the init hook. The init hook is too late for some features, such
 * as indicating support for post thumbnails.
 */
function aglee_lite_setup() {
	/*
	 * Make theme available for translation.
	 * Translations can be filed in the /languages/ directory.
	 * If you're building a theme based on Aglee Lite, use a find and replace
	 * to change 'aglee-lite' to the name of your theme in all the template files
	 */
	load_theme_textdomain( 'aglee-lite', get_template_directory() . '/languages' );

	// Add default posts and comments RSS feed links to head.
	add_theme_support( 'automatic-feed-links' );

	/*
	 * Let WordPress manage the document title.
	 * By adding theme support, we declare that this theme does not use a
	 * hard-coded <title> tag in the document head, and expect WordPress to
	 * provide it for us.
	 */
	add_theme_support( 'title-tag' );

	/*
	 * Enable support for Post Thumbnails on posts and pages.
	 *
	 * @link http://codex.wordpress.org/Function_Reference/add_theme_support#Post_Thumbnails
	 */
	add_theme_support( 'post-thumbnails' );

	// This theme uses wp_nav_menu() in one location.
	register_nav_menus( array(
		'primary' => esc_html__( 'Primary Menu', 'aglee-lite' ),
	) );

	/*
	 * Switch default core markup for search form, comment form, and comments
	 * to output valid HTML5.
	 */
	add_theme_support( 'html5', array(
		'search-form',
		'comment-form',
		'comment-list',
		'gallery',
		'caption',
	) );

	/*
	 * Enable support for Post Formats.
	 * See http://codex.wordpress.org/Post_Formats
	 */
	add_theme_support( 'post-formats', array(
		'aside',
		'image',
		'video',
		'quote',
		'link',
	) );

	// Set up the WordPress core custom background feature.
	add_theme_support( 'custom-background', apply_filters( 'aglee_lite_custom_background_args', array(
		'default-color' => 'ffffff',
		'default-image' => '',
	) ) );
}
endif; // aglee_lite_setup
add_action( 'after_setup_theme', 'aglee_lite_setup' );

/**
 * Set the content width in pixels, based on the theme's design and stylesheet.
 *
 * Priority 0 to make it available to lower priority callbacks.
 *
 * @global int $content_width
 */
function aglee_lite_content_width() {
	$GLOBALS['content_width'] = apply_filters( 'aglee_lite_content_width', 640 );
}
add_action( 'after_setup_theme', 'aglee_lite_content_width', 0 );

if ( ! isset( $content_width ) ) {
	$content_width = 640;
}


/**
 * Register widget area.
 *
 * @link http://codex.wordpress.org/Function_Reference/register_sidebar
 */
function aglee_lite_widgets_init() {
	register_sidebar( array(
		'name'          => __( 'Sidebar', 'aglee-lite' ),
		'id'            => 'sidebar-1',
		'description'   => '',
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h1 class="widget-title">',
		'after_title'   => '</h1>',
	) );
}
add_action( 'widgets_init', 'aglee_lite_widgets_init' );

/**
 * Enqueue scripts and styles.
 */
function aglee_lite_scripts() {
    $aglee_lite_query_args = array(
          'family' => 'Oswald:400,300,700|Raleway:400,300,300italic,400italic,600,600italic,700,700italic,800,800italic,',
    ); 
    wp_enqueue_style( 'aglee-lite-superfish-css', get_template_directory_uri() . '/css/superfish.css');
	wp_enqueue_style('aglee-lite-google-fonts-css', add_query_arg($aglee_lite_query_args, "//fonts.googleapis.com/css"));
	wp_enqueue_style( 'aglee-lite-style', get_stylesheet_uri() );
    wp_enqueue_style( 'aglee-lite-respond', get_template_directory_uri() . '/css/responsive.css', array() );

	wp_enqueue_script( 'aglee-lite-navigation', get_template_directory_uri() . '/js/navigation.js', array(), '20120206', true );
    wp_enqueue_script( 'aglee-lite-superfish', get_template_directory_uri() . '/js/superfish.js', array('jquery','hoverIntent'));
    
	wp_enqueue_script( 'aglee-lite-skip-link-focus-fix', get_template_directory_uri() . '/js/skip-link-focus-fix.js', array(), '20130115', true );
    wp_enqueue_script( 'aglee-lite-aglee_lite_theme-js', get_template_directory_uri() . '/js/custom.js', array('jquery') );
    
	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'aglee_lite_scripts' );


/**
 * For image size
 */
 add_image_size( 'aglee-lite-home-slider', 1920, 764, true); //Portfolio Image	
 
 add_image_size ( 'aglee-lite-services-thumb', 108, 91, true); // services thumb image
 add_image_size ( 'aglee-lite-feature-page-home', 364, 175, true); // featured post home page display img
 add_image_size ( 'aglee-lite-testimonial-img', 70, 70, true); // testimonial image display in slider
 add_image_size ( 'aglee-lite-feature-posts-home', 363,269, true); // services home page display size 
 
 add_image_size ( 'aglee-lite-blog-full-width', 1170, 355, true); // blog full content
 add_image_size ( 'aglee-lite-blog-large', 805, 355, true); // blog full content
 add_image_size ( 'aglee-lite-blog-medium', 380, 252, true); // blog medium content
 
 
/**
 * Implement the Custom Header feature.
 */
require get_template_directory() . '/inc/custom-header.php';

/**
 * Custom template tags for this theme.
 */
require get_template_directory() . '/inc/template-tags.php';

/**
 * Custom functions that act independently of the theme templates.
 */
require get_template_directory() . '/inc/extras.php';

/**
 * Customizer additions.
 */
require get_template_directory() . '/inc/customizer.php';

/**
 * Load Jetpack compatibility file.
 */
require get_template_directory() . '/inc/jetpack.php';

/**
 * Load Widgets fields 
 */
require get_template_directory() . '/inc/agleelite-widgets.php';


/**
 * To select the category for sliders
 */
 require get_template_directory() . '/inc/category-dropdown.php'; 
/**
 * Customizer_Options additions.
 *
 * @since Aglee Lite
 */
require get_template_directory() . '/inc/post-dropdown.php';


/**
 * Load Widgets fields 
 */
require get_template_directory() . '/inc/aglee-customizer.php';

/**
 * Load aglee lite function
 */
 require get_template_directory() . '/inc/agleelite-functions.php';
 
 /**
 * Load Aglee Lite Metabox
 */
require get_template_directory() . '/inc/aglee-custom-metabox.php';
 /**
 * Load Aglee Lite Theme Information
 */
require get_template_directory() . '/inc/theme-info.php';
