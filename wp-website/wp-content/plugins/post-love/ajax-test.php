<?php
/**
 * Plugin Name: Post Love
 * Plugin URI: http://danielpataki.com
 * Description: Allows users to love your posts
 * Version: 1.0.0
 * Author: Daniel Pataki
 * Author URI: http://danielpataki.com
 * License: GPL2
 */

add_action( 'wp_enqueue_scripts', 'ajax_test_enqueue_scripts' );
function ajax_test_enqueue_scripts() {
	if( is_single() ) {
		wp_enqueue_style( 'love', plugins_url( '/love.css', __FILE__ ) );
	}

	wp_enqueue_script( 'love', plugins_url( '/love.js', __FILE__ ), array('jquery'), '1.0', true );

	wp_localize_script( 'love', 'postlove', array(
		'ajax_url' => admin_url( 'admin-ajax.php' )
	));

}

add_filter( 'the_content', 'post_love_display', 99 );
function post_love_display( $content ) {
	$love_text = '';

	if ( is_single() ) {
		
		$love = get_post_meta( get_the_ID(), 'post_love', true );
		$love = ( empty( $love ) ) ? 0 : $love;

		$love_text = '<p class="love-received"><a class="love-button" href="' . admin_url( 'admin-ajax.php?action=post_love_add_love&post_id=' . get_the_ID() ) . '" data-id="' . get_the_ID() . '">give love</a><span id="love-count">' . $love . '</span></p>'; 
	
	}

	return $content . $love_text;

}

add_action( 'wp_ajax_nopriv_post_love_add_love', 'post_love_add_love' );
add_action( 'wp_ajax_post_love_add_love', 'post_love_add_love' );

function post_love_add_love() {
	$love = get_post_meta( $_REQUEST['post_id'], 'post_love', true );
	$love++;
	update_post_meta( $_REQUEST['post_id'], 'post_love', $love );
	if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) { 
		echo $love;
		die();
	}
	else {
		wp_redirect( get_permalink( $_REQUEST['post_id'] ) );
		exit();
	}
}