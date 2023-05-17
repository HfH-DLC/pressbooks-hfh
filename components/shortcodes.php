<?php

/**
 * Custom shortcodes
 *
 * @package     PressbooksHfHPackage
 * @author      Matthias Nötzli
 * @copyright   2023 HfH
 * @license     GPL-2.0+
 */

namespace HfH\Pressbooks;

class Shortcodes
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
        add_action('init', array($this, 'init'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue'));
    }

    public function init()
    {
        add_shortcode('hfh_parts', array($this, 'parts_shortcode'));
        add_shortcode('hfh_chapters', array($this, 'chapters_shortcode'));
    }

    public function enqueue()
    {
        wp_register_style('pressbooks_hfh_shortcodes_style', HFH_PLUGIN_URL . 'components/css/shortcodes.css');
        wp_register_script('pressbooks_hfh_shortcodes_script', HFH_PLUGIN_URL . 'components/js/shortcodes.js', array("jquery"));
    }

    public function parts_shortcode()
    {
        wp_enqueue_script('pressbooks_hfh_shortcodes_script');
        wp_enqueue_style('pressbooks_hfh_shortcodes_style');

        $book_structure = pb_get_book_structure();
        $parts = $book_structure['part'];
        $current_id = get_the_ID();
        $current_part = null;

        foreach ($parts as $index => $part) {
            if ($current_part == null) {
                foreach ($part["chapters"] as $index => $chapter) {
                    if ($current_id == $chapter["ID"]) {
                        $current_part = $part;
                        break;
                    }
                }
            }
        }

        ob_start();
        include('templates/parts_shortcode.php');
        return ob_get_clean();
    }

    public function chapters_shortcode()
    {
        wp_enqueue_script('pressbooks_hfh_shortcodes_script');
        wp_enqueue_style('pressbooks_hfh_shortcodes_style');

        $book_structure = pb_get_book_structure();
        $parts = $book_structure['part'];
        $current_id = get_the_ID();
        $current_index = null;
        $current_part = null;

        foreach ($parts as $index => $part) {
            if ($current_part == null) {
                foreach ($part["chapters"] as $index => $chapter) {
                    if ($current_id == $chapter["ID"]) {
                        $current_index = $index;
                        $current_part = $part;
                        break;
                    }
                }
            }
        }

        $chapters = [$current_part["chapters"][$current_index]];
        if ($current_index + 1 < count($current_part["chapters"])) {
            $chapters[] = $current_part["chapters"][$current_index + 1];
        }
        if ($current_index + 2 < count($current_part["chapters"])) {
            $chapters[] = $current_part["chapters"][$current_index + 2];
        }

        ob_start();
        include('templates/chapters_shortcode.php');
        return ob_get_clean();
    }
}
