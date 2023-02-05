<?php

/*

Plugin Name:  PDF Generator

Plugin URI:   https://anandavak.com 

Description:  Generate PDF from CSV files 

Version:      1.2

Author:       Speed 

Author URI:   https://anandavak.com

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