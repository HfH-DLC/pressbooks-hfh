<?php

/**
 * Custom buttons for text editor
 *
 * @package     PressbooksHfHPackage
 * @author      Matthias Nötzli
 * @copyright   2021 HfH
 * @license     GPL-2.0+
 */

add_action('admin_enqueue_scripts', 'hfh_enqueue_quicktag_script');

function hfh_enqueue_quicktag_script()
{

    // Access the wp_scripts global to get the jquery-ui-core version used.
    global $wp_scripts;
    // Create a handle for the jquery-ui-core css.
    $handle = 'jquery-ui';
    // Path to stylesheet, based on the jquery-ui-core version used in core.
    $src = "https://ajax.googleapis.com/ajax/libs/jqueryui/{$wp_scripts->registered['jquery-ui-core']->ver}/themes/base/{$handle}.css";
    // Required dependencies
    $deps = array();
    // Add stylesheet version.
    $ver = $wp_scripts->registered['jquery-ui-core']->ver;
    // Register the stylesheet handle.
    wp_register_style($handle, $src, $deps, $ver);
    // Enqueue the style.
    wp_enqueue_style('jquery-ui');
    wp_enqueue_style('hfh-quicktags',  plugins_url('components/css/quicktags.css', dirname(__FILE__)), array(), HFH_PLUGIN_VERSION);
    wp_enqueue_script('jquery-ui-dialog');
    wp_enqueue_script('hfh_quicktags', plugins_url('components/js/quicktags.js', dirname(__FILE__)), array('jquery', 'quicktags'), HFH_PLUGIN_VERSION, true);
}
