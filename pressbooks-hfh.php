<?php
/*
Plugin Name: Pressbooks HfH
Description: HfH additions for Pressbooks
Version: 1.0.9
Author: Sarah Frederickx, Stephan Müller, Lukas Kaiser, Matthias Nötzli
Copyright: © 2017, ETH Zurich, D-HEST, Stephan J. Müller, Lukas Kaiser, © 2022, HfH, DLC, Matthias Nötzli
License: GPLv2
Text-Domain: pressbooks-hfh
*/

use Hfh\Pressbooks\AdminMenu;
use HfH\Pressbooks\BookPostsPassword;
use HfH\Pressbooks\ChapterCategories;
use HfH\Pressbooks\CourseProgress;
use HfH\Pressbooks\Shortcodes;

if (!defined('ABSPATH')) {
	return;
}

// Enable errors and warnings for debugging.
// if ( false ) {
// ini_set( 'display_errors', 1 );
// ini_set( 'display_startup_errors', 1 );
// error_reporting( E_ALL );
// }


define('HFH_PLUGIN_URL', plugin_dir_url(__FILE__));
define('HFH_PLUGIN_DIR', plugin_dir_path(__FILE__));

// add_action( 'init', '_pb_session_start', 1 );
// add_action( 'wp_logout', '_pb_session_kill' );
// add_action( 'wp_login', '_pb_session_kill' );


require_once 'components/quicktags.php';
require_once 'components/h5p.php';
require_once 'components/admin_menu.php';
AdminMenu::get_instance();
require_once 'components/book_posts_password.php';
BookPostsPassword::get_instance();
require_once 'components/chapter_categories.php';
ChapterCategories::get_instance();
require_once 'components/course_progress.php';
CourseProgress::get_instance();
require_once 'components/shortcodes.php';
Shortcodes::get_instance();

/**
 * Remove references to pressbooks.com..
 */
// $GLOBALS['PB_SECRET_SAUCE']['TURN_OFF_FREEBIE_NOTICES_EPUB'] = 'not_created_on_pb_com';
// $GLOBALS['PB_SECRET_SAUCE']['TURN_OFF_FREEBIE_NOTICES_PDF'] = 'not_created_on_pb_com';



/**
 * REST API stuff (sarah)
 * https://kau-boys.de/4194/wordpress/benutzer-in-wordpress-mit-der-rest-api-erstellen
 **/

function app_rest_headers_allowed_cors_headers($allow_headers)
{
	return array(
		'Origin',
		'Content-Type',
		'X-Auth-Token',
		'Accept',
		'Authorization',
		'X-Request-With',
		'Access-Control-Request-Method',
		'Access-Control-Request-Headers',
	);
}
add_filter('rest_allowed_cors_headers', 'app_rest_headers_allowed_cors_headers');


function app_rest_headers_pre_serve_request($value)
{
	header('Access-Control-Allow-Origin: *', true);

	return $value;
}
add_filter('rest_pre_serve_request', 'app_rest_headers_pre_serve_request', 11);


/**
 * Hide admin bar correctly in newer themes
 */

if (is_admin() === false) {
	add_filter('show_admin_bar', '__return_false');
}

/**
 * Add PB admin style fix for overlapping problem introduced by 3D Viewer Plugin
 */
function hfh_pb_admin_fix()
{
	wp_enqueue_style('hfh_pb_admin_fix',  HFH_PLUGIN_URL . '/components/css/pb_admin_fix.css');
}

add_action('admin_enqueue_scripts', 'hfh_pb_admin_fix');

/**
 * Add details h5p iframe resize fix
 */
function hfh_details_h5p_iframe_resize_fix()
{
	wp_enqueue_script(
		'hfh_details_h5p_iframe_resize',
		HFH_PLUGIN_URL . 'components/js/details_h5p_iframe_resize.js',
	);
}
add_action('admin_enqueue_scripts', 'hfh_details_h5p_iframe_resize_fix');

// /**
//  * Replace default cover url.
//  *
//  * @param string $default_url The url of the default image.
//  * @param string $suffix The size suffixcase '-100x100', '-65x0', -225x0, or none.
//  */
// function hfh_pb_default_cover_url( $default_url, $suffix ) {
// 	return get_stylesheet_directory_uri() . "/images/default-book-cover${suffix}.jpg";
// }
// add_filter( 'pb_default_cover_url', 'hfh_pb_default_cover_url', 10, 2 );

/**
 * Replace default cover path.
 *
 * @param string $default_path The path of the default image.
 * @param string $suffix The size suffixcase '-100x100', '-65x0', -225x0, or none.
 */
function hfh_pb_default_cover_path($default_path, $suffix)
{
	return get_stylesheet_directory() . "/images/default-book-cover${suffix}.jpg";
}
add_filter('pb_default_cover_path', 'hfh_pb_default_cover_path', 10, 2);

function hfh_hide_loco_translate_menus()
{
	if (!is_super_admin()) {
		//Hide "Loco Translate".
		remove_menu_page('loco');
		//Hide "Loco Translate → Home".
		remove_submenu_page('loco', 'loco');
		//Hide "Loco Translate → Themes".
		remove_submenu_page('loco', 'loco-theme');
		//Hide "Loco Translate → Plugins".
		remove_submenu_page('loco', 'loco-plugin');
		//Hide "Loco Translate → WordPress".
		remove_submenu_page('loco', 'loco-core');
		//Hide "Loco Translate → Languages".
		remove_submenu_page('loco', 'loco-lang');
		//Hide "Loco Translate → Settings".
		remove_submenu_page('loco', 'loco-config');
	}
}
add_action('admin_menu', 'hfh_hide_loco_translate_menus', 12);

function hfh_redirect_loco_translate_pages()
{
	if (is_super_admin()) {
		return;
	}

	global $pagenow;

	if (($pagenow == 'admin.php' && isset($_GET['page']) && $_GET['page'] == 'loco')) {
		wp_redirect(admin_url());
	}
}
add_action('admin_init', 'hfh_redirect_loco_translate_pages');


function hfh_load_textdomain($domain)
{
	load_plugin_textdomain('pressbooks-hfh', false, dirname(plugin_basename(__FILE__)) . '/languages');
}

add_action('init', 'hfh_load_textdomain');
