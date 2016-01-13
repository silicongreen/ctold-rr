<?php

/**
 * @package Create School
 * @version 0.0.1
 */
/*
  Plugin Name: Create School
  Plugin URI:  http://www.classtune.com
  Description: Plugin to create paid or free school
  Version:     0.0.1
  Author:      Md. Nurul Islam
  Author URI:  http://www.classtune.com
  License:     GPL2
  License URI: https://www.gnu.org/licenses/gpl-2.0.html
  Domain Path: /create_school
  Text Domain: create_school
 */

// Make sure we don't expose any info if called directly
if (!function_exists('add_action')) {
    echo 'Hi there!  I\'m just a plugin, not much I can do when called directly.';
    exit;
}

define('CREATE_SCHOOL', '0.0.1');
define('CREATE_SCHOOL_WP_VERSION', '4.0');
define('CREATE_SCHOOL_PLUGIN_URL', plugin_dir_url(__FILE__));
define('CREATE_SCHOOL_PLUGIN_DIR', plugin_dir_path(__FILE__));

register_activation_hook(__FILE__, array('CreateSchool', 'plugin_activation'));
register_deactivation_hook(__FILE__, array('CreateSchool', 'plugin_deactivation'));

require_once( CREATE_SCHOOL_PLUGIN_DIR . 'libs/class.CreateSchool.php' );

add_action('init', array('CreateSchool', 'init'));

if (is_admin()) {
    require_once( CREATE_SCHOOL_PLUGIN_DIR . 'libs/class.CreateSchool-admin.php' );
    add_action('init', array('CreateSchoolAdmin', 'init'));
}

//add wrapper class around deprecated akismet functions that are referenced elsewhere
//require_once( AKISMET__PLUGIN_DIR . 'wrapper.php' );


