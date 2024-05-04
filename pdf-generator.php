<?php

/*

Plugin Name:  PDF Generator

Plugin URI:   https://github.com/Li8ning/PDF-Generator/ 

Description:  Generate PDF from CSV files 

Version:      1.2.2

Author:       Speed 

Author URI:   https://dharmrajsinhjadeja.com

License:      GPL2

License URI:  https://www.gnu.org/licenses/gpl-2.0.html

*/

defined("ABSPATH") or die("Hey, you can't access this file!!");

/**
* Register a custom menu page.
*/
function register_custom_menu_page() {
  add_menu_page('PDF Generator','PDF Generator','manage_options','pdf-generator','pdf_generator_function');
}
add_action( 'admin_menu', 'register_custom_menu_page' );

function pdf_generator_function(){
  $pluginDirPath = plugin_dir_path(__FILE__);
  require($pluginDirPath.'generate.php');
}
$pluginDirPath = plugin_dir_path(__FILE__);
require($pluginDirPath.'functions.php');

// Hook to register AJAX action that will call functions from functions.php file
function pdf_generator_register_ajax_action() {
  wp_register_script('pdf-generator-ajax-script', plugins_url('/pdf-generator-ajax-script.js', __FILE__), array('jquery'));
  wp_localize_script('pdf-generator-ajax-script', 'pdf_generator_ajax_params', array(
    'ajax_url' => admin_url('admin-ajax.php')
  ));
  wp_enqueue_script('pdf-generator-ajax-script');
}
add_action('admin_enqueue_scripts', 'pdf_generator_register_ajax_action');
