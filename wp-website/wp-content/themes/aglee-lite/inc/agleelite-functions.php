<?php
    /**
     * 
     * Aglee Lite Function 
     * 
     */
     
     function aglee_lite_additional_scripts() {
    	wp_enqueue_style( 'aglee-lite-font-awesome', get_template_directory_uri().'/css/fawesome/css/font-awesome.css' );
    	wp_enqueue_script( 'aglee-lite-jquery-bxslider-js', get_template_directory_uri() . '/js/jquery.bxslider.js', array('jquery'), '1.11.2' );
    }
    add_action( 'wp_enqueue_scripts', 'aglee_lite_additional_scripts' );
    
    
   add_action( 'admin_enqueue_scripts', 'aglee_lite_media_uploader' );

    function aglee_lite_media_uploader( $hook )
    {
        if( 'widgets.php' != $hook )
            return;
    
        wp_enqueue_script( 
                'uploader-script', 
                get_template_directory_uri().'/inc/admin-panel/js/media-uploader.js', 
                array(), // dependencies
                false, // version
                true // on footer
        );
        wp_enqueue_media();
    }
    
    // add classes for alternate posts left and right
    function aglee_lite_assign_alt_classes( $aglee_lite_classes ) {
    	global $post;
    	
        static $aglee_lite_flag = true;
        
        if($aglee_lite_flag){
            $aglee_lite_classes[] = 'alt-left';
            $aglee_lite_flag = false;
        }else{
            $aglee_lite_classes[] = 'alt-right';
            $aglee_lite_flag = true;
        }
        
    	return $aglee_lite_classes;
    }
    
    if(($aglee_lite_setting = get_theme_mod('blog_post_layout')) == 'blog_image_alternate_medium'){
        add_filter( 'post_class', 'aglee_lite_assign_alt_classes' );
    }
    
    // Adding custom dynamic styles to the site
    function aglee_lite_custom_dynamic_styles(){
       $aglee_lite_background_image = get_theme_mod('site_pattern_setting');
        
        $aglee_lite_bg_img = get_template_directory_uri().'/inc/admin-panel/images/'.$aglee_lite_background_image.'.png';
        
        if(($aglee_lite_aglee_lite_layout = get_theme_mod('site_layout_setting')) == 'boxed') :
        ?>
            <style type="text/css" rel="stylesheet">
                <?php if($aglee_lite_background_image == 'pattern0') : ?>
                    body{
                        background: none;
                    }
                <?php else : ?>
                    body{
                        background: url(<?php echo $aglee_lite_bg_img; ?>);
                    }
                <?php endif; ?>                
            </style>
        <?php
        endif;
    }
    add_action('wp_head','aglee_lite_custom_dynamic_styles');
    
    
    // Filter for excerpt read more
    function aglee_lite_custom_excerpt_more( $aglee_lite_more ) {
    	return '...';
    }
    add_filter( 'excerpt_more', 'aglee_lite_custom_excerpt_more' );
    
    /**
     * Since I'm already doing a tutorial, I'm not going to include comments to
     * this code, but if you want, you can check out the "example.php" file
     * inside the ZIP you downloaded - it has a very detailed documentation.
     */

if ( is_admin() ) : // Load only if we are viewing an admin page
function aglee_lite_admin_scripts() {
    wp_enqueue_media();
    wp_enqueue_script( 'agleelite_custom_js', get_template_directory_uri().'/inc/admin-panel/js/admin.js', array( 'jquery' ),'',true );
    wp_enqueue_style( 'agleelite_admin_style',get_template_directory_uri().'/inc/admin-panel/css/admin.css', '1.0', 'screen' );
}
add_action('admin_enqueue_scripts', 'aglee_lite_admin_scripts');
endif;

    /**
 * 8Degree More Themes
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
}

// Add stylesheet and JS for upsell page.
function aglee_lite_upsell_style() {
    wp_enqueue_style( 'upsell-style', get_template_directory_uri() . '/css/upsell.css');
}

// Add upsell page to the menu.
function aglee_lite_add_upsell() {
    $page = add_theme_page(
        __( 'More Themes', 'aglee-lite' ),
        __( 'More Themes', 'aglee-lite' ),
        'administrator',
        'aglee-lite-themes',
        'aglee_lite_display_upsell'
    );

    add_action( 'admin_print_styles-' . $page, 'aglee_lite_upsell_style' );
}
add_action( 'admin_menu', 'aglee_lite_add_upsell', 11 );

// Define markup for the upsell page.
function aglee_lite_display_upsell() {

    // Set template directory uri
    $directory_uri = get_template_directory_uri();
    ?>
    <div class="wrap">
        <div class="container-fluid">
            <div id="upsell_container">  
                <div class="row">
                    <div id="upsell_header" class="col-md-12">
                        <h2>
                            <a href="http://8degreethemes.com/" target="_blank">
                                <img src="http://8degreethemes.com/wp-content/uploads/2015/05/logo.png"/>
                            </a>
                        </h2>

                        <h3><?php _e( 'Product of 8Degree Themes', 'aglee-lite' ); ?></h3>
                    </div>
                </div>

                <div id="upsell_themes" class="row">
                    <?php
                    // Set the argument array with author name.
                    $args = array(
                        'author' => '8degreethemes',
                    );

                    // Set the $request array.
                    $request = array(
                        'body' => array(
                            'action'  => 'query_themes',
                            'request' => serialize( (object)$args )
                        )
                    );
                    $themes = aglee_lite_get_themes( $request );
                    $active_theme = wp_get_theme()->get( 'Name' );
                    $counter = 1;

                    // For currently active theme.
                    foreach ( $themes->themes as $theme ) {
                        if( $active_theme == $theme->name ) {?>

                            <div id="<?php echo $theme->slug; ?>" class="theme-container col-md-6 col-lg-4">
                                <div class="image-container">
                                    <img class="theme-screenshot" src="<?php echo $theme->screenshot_url ?>"/>

                                    <div class="theme-description">
                                        <p><?php echo $theme->description; ?></p>
                                    </div>
                                </div>
                                <div class="theme-details active">
                                    <span class="theme-name"><?php echo $theme->name . ':' . __( 'Current theme', 'aglee-lite' ); ?></span>
                                    <a class="button button-secondary customize right" target="_blank" href="<?php echo get_site_url(). '/wp-admin/customize.php' ?>">Customize</a>
                                </div>
                            </div>

                        <?php
                        $counter++;
                        break;
                        }
                    }

                    // For all other themes.
                    foreach ( $themes->themes as $theme ) {
                        if( $active_theme != $theme->name ) {

                            // Set the argument array with author name.
                            $args = array(
                                'slug' => $theme->slug,
                            );

                            // Set the $request array.
                            $request = array(
                                'body' => array(
                                    'action'  => 'theme_information',
                                    'request' => serialize( (object)$args )
                                )
                            );

                            $theme_details = aglee_lite_get_themes( $request );
                            ?>

                            <div id="<?php echo $theme->slug; ?>" class="theme-container col-md-6 col-lg-4 <?php echo $counter % 3 == 1 ? 'no-left-megin' : ""; ?>">
                                <div class="image-container">
                                    <img class="theme-screenshot" src="<?php echo $theme->screenshot_url ?>"/>

                                    <div class="theme-description">
                                        <p><?php echo $theme->description; ?></p>
                                    </div>
                                </div>
                                <div class="theme-details">
                                    <span class="theme-name"><?php echo $theme->name; ?></span>

                                    <!-- Check if the theme is installed -->
                                    <?php if( wp_get_theme( $theme->slug )->exists() ) { ?>

                                        <!-- Show the tick image notifying the theme is already installed. -->
                                        <img data-toggle="tooltip" title="<?php _e( 'Already installed', 'aglee-lite' ); ?>" data-placement="bottom" class="theme-exists" src="<?php echo $directory_uri ?>/images/8dt-right.png"/>

                                        <!-- Activate Button -->
                                        <a  class="button button-primary activate right"
                                            href="<?php echo wp_nonce_url( admin_url( 'themes.php?action=activate&amp;stylesheet=' . urlencode( $theme->slug ) ), 'switch-theme_' . $theme->slug );?>" >Activate</a>
                                    <?php }
                                    else {

                                        // Set the install url for the theme.
                                        $install_url = add_query_arg( array(
                                                'action' => 'install-theme',
                                                'theme'  => $theme->slug,
                                            ), self_admin_url( 'update.php' ) );
                                    ?>
                                        <!-- Install Button -->
                                        <a data-toggle="tooltip" data-placement="bottom" title="<?php echo 'Downloaded ' . number_format( $theme_details->downloaded ) . ' times'; ?>" class="button button-primary install right" href="<?php echo esc_url( wp_nonce_url( $install_url, 'install-theme_' . $theme->slug ) ); ?>" ><?php _e( 'Install Now', 'aglee-lite' ); ?></a>
                                    <?php } ?>

                                    <!-- Preview button -->
                                    <a class="button button-secondary preview right" target="_blank" href="<?php echo $theme->preview_url; ?>"><?php _e( 'Live Preview', 'aglee-lite' ); ?></a>
                                </div>
                            </div>
                            <?php
                            $counter++;
                        }
                    }?>
                </div>
            </div>
        </div>
    </div>
<?php
}

// Get all 8Degree themes by using API.
function aglee_lite_get_themes( $request ) {

    // Generate a cache key that would hold the response for this request:
    $key = 'aglee-lite_' . md5( serialize( $request ) );

    // Check transient. If it's there - use that, if not re fetch the theme
    if ( false === ( $themes = get_transient( $key ) ) ) {

        // Transient expired/does not exist. Send request to the API.
        $response = wp_remote_post( 'http://api.wordpress.org/themes/info/1.0/', $request );

        // Check for the error.
        if ( !is_wp_error( $response ) ) {

            $themes = unserialize( wp_remote_retrieve_body( $response ) );

            if ( !is_object( $themes ) && !is_array( $themes ) ) {

                // Response body does not contain an object/array
                return new WP_Error( 'theme_api_error', 'An unexpected error has occurred' );
            }

            // Set transient for next time... keep it for 24 hours should be good
            set_transient( $key, $themes, 60 * 60 * 24 );
        }
        else {
            // Error object returned
            return $response;
        }
    }
    return $themes;
}
