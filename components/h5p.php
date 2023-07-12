<?php

/**
 * H5P Extensions & Adjustments
 *
 * @package     PressbooksHfHPackage
 * @author      Matthias Nötzli
 * @copyright   2022 HFH
 * @license     GPL-2.0+
 */

// function hfh_h5pmods_alter_semantics(&$semantics, $library_name)
// {
// 	if ($library_name === 'H5P.MultiChoice') {
// 		foreach ($semantics as $semantic_field) {
// 			if ($semantic_field->name === 'overallFeedback') {
// 				// https://github.com/h5p/h5p-multi-choice/blob/master/semantics.json#L155
// 				$overall_feedback_fields = $semantic_field->fields;
// 				foreach ($overall_feedback_fields as $overall_feedback_field) {
// 					if ($overall_feedback_field->name === 'overallFeedback') {
// 						// https://github.com/h5p/h5p-multi-choice/blob/master/semantics.json#L162
// 						$overall_feedback_elements = $overall_feedback_field->field->fields;
// 						foreach ($overall_feedback_elements as $overall_feedback_element) {

// 							if ($overall_feedback_element->name === 'feedback') {
// 								// https://github.com/h5p/h5p-multi-choice/blob/master/semantics.json#L200
// 								$overall_feedback_element->maxLength = 1000;
// 							}
// 						}
// 					}
// 				}
// 			}
// 		}
// 	}
// }
// add_action('h5p_alter_library_semantics', 'hfh_h5pmods_alter_semantics', 10, 4);

add_action('admin_enqueue_scripts', 'hfh_enqueue_h5p_fix', 11);

function hfh_enqueue_h5p_fix()
{
    wp_enqueue_style('hfh-h5p-fix',  HFH_PLUGIN_URL . 'components/css/h5pfix.css');
}

/**
 * Automatically enables H5P autosaving for a new site
 */
function hfh_h5p_autosave_settings($new_site)
{
    if (is_plugin_active_for_network('h5p/h5p.php')) {
        update_blog_option($new_site->blog_id, 'h5p_save_content_state', 1);
        update_blog_option($new_site->blog_id, 'h5p_save_content_frequency', 3);
    }
}

add_action('wp_initialize_site', 'hfh_h5p_autosave_settings');
