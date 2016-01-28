<?php
/*
Plugin Name: CLASSTUNE FAQ Plugin
Plugin URI: http://classtune.com/
Description: CLASSTUNE FAQ Plugin
Version: 1.0.0
Author: LIKHON REZOAN
Author URI: http://www.classtune.com
*/

//Custom FAQ Post Type 
function classtune_wp_awesome_faq_post_type() {
    $labels = array(
        'name'               => _x( 'FAQ', 'post type general name' ),
        'singular_name'      => _x( 'FAQ', 'post type singular name' ),
        'add_new'            => _x( 'Add New', 'book' ),
        'add_new_item'       => __( 'Add New FAQ' ),
        'edit_item'          => __( 'Edit FAQ' ),
        'new_item'           => __( 'New FAQ Items' ),
        'all_items'          => __( 'All FAQ\'s' ),
        'view_item'          => __( 'View FAQ' ),
        'search_items'       => __( 'Search FAQ' ),
        'not_found'          => __( 'No FAQ Items found' ),
        'not_found_in_trash' => __( 'No FAQ Items found in the Trash' ), 
        'parent_item_colon'  => '',
        'menu_name'          => 'FAQ'
    );
    $args = array(
        'labels'        => $labels,
        'description'   => 'Holds FAQ specific data',
        'public'        => true,
        'show_ui'       => true,
        'show_in_menu'  => true,
        'query_var'     => true,
        'rewrite'       => array('slug' => 'faq'),
        'capability_type'=> 'post',
        'has_archive'   => true,
        'hierarchical'  => false,
        'menu_position' => 5,
        'supports'      => array( 'title', 'editor'),
        'menu_icon' => 'dashicons-welcome-write-blog'
    );

    register_post_type( 'faq', $args ); 

        // Add new taxonomy, make it hierarchical (like categories)
        $labels = array(
            'name'              => _x( 'FAQ Categories', 'taxonomy general name' ),
            'singular_name'     => _x( 'FAQ Category', 'taxonomy singular name' ),
            'search_items'      =>  __( 'Search FAQ Categories' ),
            'all_items'         => __( 'All FAQ Category' ),
            'parent_item'       => __( 'Parent FAQ Category' ),
            'parent_item_colon' => __( 'Parent FAQ Category:' ),
            'edit_item'         => __( 'Edit FAQ Category' ),
            'update_item'       => __( 'Update FAQ Category' ),
            'add_new_item'      => __( 'Add New FAQ Category' ),
            'new_item_name'     => __( 'New FAQ Category Name' ),
            'menu_name'         => __( 'FAQ Category' ),
        );
    
        register_taxonomy('faq_cat',array('faq'), array(
            'hierarchical' => true,
            'labels'       => $labels,
            'show_ui'      => true,
            'query_var'    => true,
            'rewrite'      => array( 'slug' => 'faq_cat' ),
        ));
}

add_action( 'init', 'classtune_wp_awesome_faq_post_type' );

function classtune_wp_awesome_faq_enqueue_scripts(){
     if(!is_admin()){
        wp_register_style('classtune-jquery-ui-style',plugins_url('/jquery-ui.css', __FILE__ ));
        wp_enqueue_style('classtune-jquery-ui-style');
        wp_enqueue_script('jquery');
        wp_enqueue_script('jquery-ui-core');
        wp_register_script('classtune-custom-js', plugins_url('/accordion.js', __FILE__ ), array('jquery-ui-accordion'),true);
        wp_enqueue_script('classtune-custom-js');
    }   
}
add_action( 'init', 'classtune_wp_awesome_faq_enqueue_scripts' );


function classtune_wp_awesome_faq_shortcode($atts, $content= null) { 
    
    extract( shortcode_atts(
        array(
           'id' => '',
            'content'  => '',
            "cat_id" => '',
            "image" => '',
            ), $atts )
    );


    // WP_Query arguments
    if( $cat_id == '' ) :
        $args = array (
            'posts_per_page'        => -1,
            'post_type'             => 'faq',
            'p'                     => $id,
            'order' =>"DESC"
            );
    else:
        $args = array (
            'posts_per_page'        => -1,
            'post_type'             => 'faq',
            'p'                     => $id,
            'tax_query' => array(
                array(
                    'taxonomy' => 'faq_cat',
                    'field'    => 'id',
                    'terms'    => array( $cat_id ),
                    ),
                ),

            'order' =>"DESC"
            );
    endif;

    $query = new WP_Query( $args );

    ob_start();

    global $faq;

    $count = 0; 
    $accordion = 'accordion-' . time() . rand();
	$i = 1;
    ?>
        <div class="accordion" id="<?php echo $accordion .  $count;?>">
            <?php if( $query->have_posts() ) { while ( $query->have_posts() ) { $query->the_post(); ?>
				<div class="panel panel-default">
					<div class="panel-heading">
						<h4 class="panel-title" style=''>
							<a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapse<?php the_id();?>">
								<?php the_title();?>
							</a>
						</h4>
					</div>
					<div id="collapse<?php the_id();?>" class="panel-collapse collapse">
						<div class="panel-body">
							<?php if($image){ ?>
								<img src="<?php echo $image;?>">
							<?php } ?>

							<?php the_content();?>
						</div>    
					</div>    
				</div>
                <?php $i++; } //end while
            } else{
                echo "<p>No FAQ Items. Please add some Items</p>";
                } ?>
        </div>
    <?php
        //Reset the query
    wp_reset_query();
    wp_reset_postdata();
        $output = ob_get_contents(); // end output buffering
        ob_end_clean(); // grab the buffer contents and empty the buffer
        return $output;
}
add_shortcode('faq', 'classtune_wp_awesome_faq_shortcode');




/* Display a notice that can be dismissed */

add_action('admin_notices', 'classtune_wp_awesome_faq_admin_notice');

function classtune_wp_awesome_faq_admin_notice() {
    global $current_user ;
        $user_id = $current_user->ID;
    if ( ! get_user_meta($user_id, 'classtune_ignore_notice') ) {
        //echo '<div class="updated"><p>';         
       // printf(__('<h4 style="font-size: 20px; color: #5FA52A; font-weight: normal; margin-bottom: 10px; margin-top: 5px;"><a href="http://classtune.com/product/wp-awesome-faq-pro/" target="_blank">Get WP Awesome FAQ PRO Today!</a></h4>Check out Premium Features of <a href="http://classtune.com/product/wp-awesome-faq-pro/" target="_blank">WP Awesome FAQ</a> Plugin. Compare Why this Plugin is really awesome !!! <br>
        //    Jewel Theme, always express the power of WordPress. We are one of the best Team for creating stunning WordPress Themes - Plugins and Website Templates. <br>
        //    Check all of our <a href="http://classtune.com/product-category/wordpress-themes/" target="_blank">Free and Premium WordPress Themes</a> and <a href="http://classtune.com/product-category/wordpress-plugins/" target="_blank">WordPress Plugins </a>'), '?classtune_ignore=0');
        //echo "</p></div>";
    }
}
add_action('admin_init', 'classtune_wp_awesome_faq_ignore');


function classtune_wp_awesome_faq_ignore() {
    global $current_user;
        $user_id = $current_user->ID;
        if ( isset($_GET['classtune_ignore']) && '0' == $_GET['classtune_ignore'] ) {
             add_user_meta($user_id, 'classtune_ignore_notice', 'true', true);
    }
}





// Manage Category Shortcode Columns
add_filter("manage_faq_cat_custom_column", 'classtune_wp_awesome_faq_cat_columns', 10, 3);
add_filter("manage_edit-faq_cat_columns", 'classtune_wp_awesome_faq_cat_manage_columns'); 
 
function classtune_wp_awesome_faq_cat_manage_columns($theme_columns) {
    $new_columns = array(
            'cb' => '<input type="checkbox" />',
            'name' => __('Name'),
            'faq_category_shortcode' => __( 'Category Shortcode', 'classtune' ),
            'slug' => __('Slug'),
            'posts' => __('Posts')
        );
    return $new_columns;

}


function classtune_wp_awesome_faq_cat_columns($out, $column_name, $theme_id) {
    $theme = get_term($theme_id, 'faq_cat');
    switch ($column_name) {
        
        case 'title':
            echo get_the_title();
        break;

        case 'faq_category_shortcode':             
             echo '[faq cat_id="' . $theme_id. '"]';
        break;
 
        default:
            break;
    }
    return $out;    
}






add_action('admin_head', 'classtune_wp_awesome_faq_tinymce_button');

function classtune_wp_awesome_faq_tinymce_button() {
    global $typenow;
    
    // check user permissions
    if ( !current_user_can('edit_posts') && !current_user_can('edit_pages') ) {
    return;
    }
    
    // verify the post type
    if( ! in_array( $typenow, array( 'post', 'page' ) ) )
        return;

    // check if WYSIWYG is enabled
    if ( get_user_option('rich_editing') == 'true') {
        add_filter("mce_external_plugins", "classtune_wp_awesome_faq_tinymce_plugin");
        add_filter('mce_buttons', 'classtune_wp_awesome_faq_register_tinymce_button');
    }
}

function classtune_wp_awesome_faq_tinymce_plugin($plugin_array) {
    $plugin_array['classtune_faq_button'] = plugins_url( '/editor-button.js', __FILE__ ); 
    return $plugin_array;
}

function classtune_wp_awesome_faq_register_tinymce_button($buttons) {
   array_push($buttons, "classtune_faq_button");
   return $buttons;
}

function admin_inline_js(){ ?>
    <style>
        i.mce-ico.mce-i-faq-icon {
            background-image: url('<?php echo  plugins_url( 'icon.png', __FILE__ );?>');
        }
    </style>
<?php }
add_action( 'admin_print_scripts', 'admin_inline_js' );


