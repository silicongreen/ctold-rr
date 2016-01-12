<?php

/**
 * @package Citrix_Meeting_API
 * @version 0.0.1
 */
/*
  Plugin Name: Citrix API
  Plugin URI:  http://www.classtune.com
  Description: Citrix API for Go To Meeting, Go To Training, Go To Webinar
  Version:     0.0.1
  Author:      Md. Nurul Islam
  Author URI:  http://www.classtune.com
  License:     GPL2
  License URI: https://www.gnu.org/licenses/gpl-2.0.html
  Domain Path: /citrix
  Text Domain: citrix
 */

// Make sure we don't expose any info if called directly
if (!function_exists('add_action')) {
    echo 'Hi there!  I\'m just a plugin, not much I can do when called directly.';
    exit;
}

define('CITRIX__MEETING_VERSION', '0.0.1');
define('CITRIX__MINIMUM_WP_VERSION', '4.0');
define('CITRIX__PLUGIN_URL', plugin_dir_url(__FILE__));
define('CITRIX__PLUGIN_DIR', plugin_dir_path(__FILE__));

register_activation_hook(__FILE__, array('Citrix', 'plugin_activation'));
register_deactivation_hook(__FILE__, array('Citrix', 'plugin_deactivation'));

require_once( CITRIX__PLUGIN_DIR . 'libs/class.citrix.php' );
//require_once( CITRIX__PLUGIN_DIR . 'class.akismet-widget.php' );

add_action('init', array('Citrix', 'init'));

if (is_admin()) {
    require_once( CITRIX__PLUGIN_DIR . 'libs/class.citrix-admin.php' );
    add_action('init', array('Citrix_admin', 'init'));
}

//add wrapper class around deprecated akismet functions that are referenced elsewhere
//require_once( AKISMET__PLUGIN_DIR . 'wrapper.php' );


