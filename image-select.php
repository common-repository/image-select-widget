<?php 
/*
Plugin Name: Image Select Widget
Plugin URI: http://marcel-online.magix.net/wordpress/image-select-widget/
Description: Image Select Widget is a Widget where you can save images in a list and category. With a shortcode you can add a theme to a post or a page. Furthermore you have the possibility to change a lot of settings. For maximum optic install <a href="http://wordpress.org/plugins/wp-jquery-lightbox/" target="_blank">jQuery Lightbox for Wordpress</a>.
Version: 1.1
Author: Marcel Birkholz
Author URI: http://marcel-online.magix.net/
Min WP Version 3.8
License: GPLv2 or later

Text Domain: image-select
Domain Path: /languages/
*/

/*
This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

$plugin_header_translate = array(
__(	'Image Select Widget is a Widget where you can save images in a list and category. With a shortcode you can add a theme to a post or a page. Furthermore you have the possibility to change a lot of settings. For maximum optic install <a href="http://wordpress.org/plugins/wp-jquery-lightbox/" target="_blank">jQuery Lightbox for Wordpress</a>.', 'image-select'),
	'Marcel Birkholz',
	'http://marcel-online.magix.net/wordpress/',
__(	'Image Select Widget', 'image-select'));

function iws_plugin_language() {
	load_plugin_textdomain( 'image-select', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
}
add_action('plugins_loaded', 'iws_plugin_language');

function image_select_constants() {
	global $wpdb;
	
	define('is_THEME', $wpdb->prefix . "image_select_theme");
	define('is_SHORT', $wpdb->prefix . "image_select_short");
	define('is_CONTENT', $wpdb->prefix . "image_select_content");
}

image_select_constants();

require_once ( dirname(__FILE__) . '/register.php' );

register_activation_hook(  __FILE__, 'image_select_install'   );
register_uninstall_hook(__FILE__, 'image_select_uninstall' );

require_once ( dirname( __FILE__ ) . '/settings.php');

?>