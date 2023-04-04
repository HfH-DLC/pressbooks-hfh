<?php

/**
 * Chapter categories settings page and metabox
 *
 * @package     PressbooksHfHPackage
 * @author      Matthias Nötzli
 * @copyright   2021 HfH
 * @license     GPL-2.0+
 */

namespace HfH\Pressbooks;

use function Pressbooks\Utility\str_starts_with;

class ChapterCategories
{
    private static $instance = false;

    public static function get_instance()
    {
        if (!self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct()
    {
        add_action('admin_menu', array($this, 'admin_menu'));
        add_filter('parent_file', array($this, 'fix_parent_file'));
        add_action('init', array($this, 'attach_category_to_chapter'), 99);
        add_action('add_meta_boxes', array($this, 'add_meta_boxes'));
    }

    public function admin_menu()
    {
        add_submenu_page(
            'pb_organize',
            __('Chapter Categories', 'pressbooks-hfh'),
            __('Chapter Categories', 'pressbooks-hfh'),
            'manage_network',
            'edit-tags.php?taxonomy=category&amp;post_type=chapter',
            '',
            7
        );
    }

    public function fix_parent_file($file)
    {
        global $submenu_file;
        //Move submenu under Organize
        if (str_starts_with($submenu_file, 'edit-tags.php?taxonomy=category')) {
            return 'pb_organize';
        }
        return $file;
    }

    public function attach_category_to_chapter()
    {
        register_taxonomy_for_object_type('category', 'chapter');
    }

    public function add_meta_boxes()
    {
        add_meta_box("categorydiv", __('Categories'), 'post_categories_meta_box', 'chapter', 'side');
    }
}
